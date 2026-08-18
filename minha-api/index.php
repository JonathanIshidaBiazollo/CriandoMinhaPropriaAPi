<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
    <form action="" id="formulario">
      <input type="text" id="nome">
      <input type="email" id="email">
      <button id="cadastrar">Cadastrar</button>
    </form>
    <button id="buscar">Listar</button>
    <ul id="lista"></ul>
    <script>
      const API_URL = "http://localhost/projetos/minha-api/usuarios.php";
      let usuarioEditando = null;//let não const pq preciso que o valor dessa variável mude conforme o usuario que eu escolher mude
  
      //Listar todos os usuários
      const lista = document.querySelector("#lista");
      const botao = document.querySelector("#buscar");

      botao.addEventListener("click", async()=>{
        try{
          const resposta = await fetch(API_URL);

          if(!resposta.ok){
            throw new Error("Erro ao buscar usuários");
          }

          const usuarios = await resposta.json();
          console.log(usuarios);

          lista.innerHTML = "";
          usuarios.forEach(usuario => {
            lista.innerHTML+=`
              <li data-id="${usuario.id}">
                <h2>${usuario.nome}</h2>
                <p>${usuario.email}</p>
                <button data-id="${usuario.id}" class="excluir">excluir</button>
                <button data-id="${usuario.id}" class="editar">Editar</button>
              </li>
            `;
          })
        }catch(erro){
          console.error(erro);
          lista.innerHTML = `
            <li>Erro ao carregar usuários</li>
          `;
        }
      });

      //Adicionar mais usuários
      const formulario = document.querySelector("#formulario");

      formulario.addEventListener("submit", async(evento) => {
        evento.preventDefault();//Por padrão o botão de submit envia o formulário e nesse caso eu quero evitar isso

        const metodo = usuarioEditando ? "PATCH" : "POST";
        const URL = usuarioEditando
                    ? `${API_URL}?id=${usuarioEditando}`//essa linha faz isso -> http://localhost/projetos/minha-api/usuarios.php?id=8
                    : API_URL;
        
        const nome = document.querySelector("#nome").value;
        const email = document.querySelector("#email").value;

        try{

          const resposta = await fetch(URL, {
            method: metodo,
            headers: {
              "Content-Type": "application/json"
            },

            body: JSON.stringify({//bem parecido com a estrutura que se usa em testes no POSTMAN, porém aqui é necessário fazer a conversão, coisa que o POSTMAN faria por mim automaticamente
              nome: nome,
              email: email
            })
          });

          if(!resposta.ok){
            throw new Error("Erro ao cadastrar usuário");
          }

          const dados = await resposta.json();
          if(usuarioEditando){
            const li = document.querySelector(
              `li[data-id="${usuarioEditando}"]`
            );
            li.innerHTML = `
              <h2>${dados.nome}</h2>
              <p>${dados.email}</p>
              <button data-id="${dados.id}" class="excluir">Excluir</button>
              <button data-id="${dados.id}" class="editar">Editar</button>
            `;
            usuarioEditando = null;
          }else{
            lista.innerHTML += `
              <li data-id="${dados.id}">
                <h2>${dados.nome}</h2>
                <p>${dados.email}</p>
                <button data-id="${dados.id}" class="excluir">Excluir</button>
                <button data-id="${dados.id}" class="editar">Editar</button>
              </li>
            `;
          }
          //Com essa inserção do html, não éw necessário fazer outro get pra atualizar a lista, pois o POST já devolveu os dados que foram cadastrados a pouco, então é só usá-los diretamente
          console.log(dados);
          if (metodo === "PATCH") {
              alert("Usuário atualizado com sucesso!");
          } else {
              alert("Usuário cadastrado com sucesso!");
          }
          formulario.reset();
        }catch(erro){
          console.error(erro);
          if(metodo === "PATCH"){
              alert("Erro ao atualizar usuário");
          }else{
              alert("Erro ao cadastrar usuário"); 
          }
        }
      });

      
      lista.addEventListener("click", async(evento) => {
        if(evento.target.classList.contains("excluir")){
          //Apagar usuários
          const id = evento.target.dataset.id;
          console.log("ID a ser excluído: ", id);
          try{
            const resposta = await fetch(`${API_URL}?id=${id}`, {
              method: "DELETE"
            });
            if(!resposta.ok){
              throw new Error("Erro ao excluir usuário");
            }

            const dados = await resposta.json();
            console.log(dados);
            evento.target.parentElement.remove();
          }catch(erro){
            console.error(erro);
            alert("Erro ao excluir usuário");
          }
          
        }else if(evento.target.classList.contains("editar")){
          //Editar usuários
          const id = evento.target.dataset.id;
          usuarioEditando = id;
          console.log("Usuário sendo editado: ", usuarioEditando)
          try {
            const resposta = await fetch(`${API_URL}?id=${id}`);
            if(!resposta.ok){
              throw new Error("Erro ao buscar usuário");
            }
            const usuario = await resposta.json();
            console.log(usuario);

            document.querySelector("#nome").value = usuario.nome;
            document.querySelector("#email").value = usuario.email;
          } catch (erro) {
            console.error(erro);
            alert("Erro ao buscar usuário");
            
          }
        }
      });

    </script>
  </body>
</html>
