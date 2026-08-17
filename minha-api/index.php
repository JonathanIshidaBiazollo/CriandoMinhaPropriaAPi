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
    <button id="listar">Listar</button>
    <ul id="lista"></ul>
    <script>
     const lista = document.querySelector("#lista");
     const listar = document.querySelector("#listar");
     listar.addEventListener("click", async()=>{
      const resposta = await fetch("http://localhost/projetos/minha-api/usuarios.php");
      const usuarios = await resposta.json();
      console.log(usuarios);
      lista.innerHTML = "";
      usuarios.forEach(usuario=>{
        lista.innerHTML+=`
          <li data-id="${usuario.id}">
            <p>${usuario.nome}</p>
            <p>${usuario.email}</p>
            <button data-id="${usuario.id}" class="excluir">excluir</button>
            <button data-id="${usuario.id}" class="editar">Editar</button>
          </li>
        `;
      })
     });

    lista.addEventListener("click", async(evento)=>{
      if(evento.target.classList.contains("excluir")){
        const id = evento.target.dataset.id;
        console.log(id);
        const resposta = await fetch(`http://localhost/projetos/minha-api/usuarios.php/${id}`, {
          method: "DELETE"
        });
        evento.target.parentElement.remove();
        console.log(resposta);
      }else if(evento.target){
        alert("Ainda não criei o evento de editar")
      }
      
      const cadastrar = document.querySelector("#cadastrar");
    });
    </script>
  </body>
</html>