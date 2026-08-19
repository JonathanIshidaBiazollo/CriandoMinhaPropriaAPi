<?php
    header("Content-Type: application/json");
    require_once "conexao.php";
    require_once "funcoes.php";


    $metodo = $_SERVER["REQUEST_METHOD"];//Informa se a requisição é GET, POST, PUT, PATCH ou DELETE
    if($metodo === "GET"){
        //BUSCAR
        if(isset($_GET["id"])){
            if(!is_numeric($_GET["id"])){
                responder([
                    "erro" => "ID inválido"
                ], 400);
            }
            //Buscar um usuário em específico
            $id = $_GET["id"];

            $sql = "SELECT *
                    FROM usuarios
                    WHERE id = ?
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$usuario){
                responder([
                    "erro" => "Usuário não encontrado"
                ], 404);//código de usuário não encontrado
            }
            echo json_encode($usuario);
        }else{
            $sql = "SELECT * FROM usuarios";
            $stmt = $pdo->query($sql);
            $usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
            echo json_encode($usuarios);
        }
    }else if($metodo === "POST"){
        //CADASTRAR
        $dados = json_decode(
            file_get_contents("php://input"), 
            true
        );

        if($dados === null){
            responder([
                "erro" => "JSON inválido"
            ], 400);
        }

        $nome = $dados["nome"] ?? "";
        $email = $dados["email"] ?? "";

        validarUsuario($dados);

        /*
        //Para usar no POSTMAN
        $sql = "INSERT INTO usuarios(
                    nome,
                    email)
                VALUES(
                    :nome,
                    :email);
                ";
        */
        $sql = "INSERT INTO usuarios(
                    nome,
                    email)
                VALUES(
                    ?,
                    ?);
                ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $email
        ]);

        /*
        //Exemplo sem usar a função no início do código
        http_response_code(201);//A requisição foi criada e um novo recurso foi criado

        echo json_encode([
            "mensagem" => "Usuário cadastrado com sucesso",
            "id" => $pdo->lastInsertId(),
            "nome" => $nome,
            "email" => $email
        ]);
        */

        responder([
            "mensagem" => "Usuário cadastrado com sucesso",
            "id" => $pdo->lastInsertId(),
            "nome" => $nome,
            "email" => $email
        ], 201);
        
        /*
        //Para usar no POSTMAN
        $stmt->execute([
            ":nome" => $nome,
            ":email" => $email
        ]);
        
        echo json_encode([
            "mensagem" => "Usuário cadastrado com sucesso",
            "id" => $pdo->lastInsertId()
        ]);
        */
    }else if($metodo === "PATCH"){
        //ATUALIZAR/EDITAR

        //Primeiro verifico se o id não está vazio
        if(!isset($_GET["id"])){
            responder([
                "erro" => "ID do usuário é obrigatório"
            ], 400);
        }

        //Pra depois pegar o valor e atribuir a uma variável e poder usar 
        $id = $_GET["id"];
        if(!is_numeric($id)){
            responder([
                "erro" => "ID inválido"
            ]);
        }

        //Agora vou verificar se esse id existe no banco de dados pra poder editá-lo
        $sql = "SELECT id
                FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if(!$stmt->fetch()){
            responder([
                "erro" => "Usuário não encontrado"
            ], 404);
        }

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        //Faz a mesma coisa dos $dados === null
        /*
        if(json_last_error() !== JSON_ERROR_NONE){
            responder([
                "erro" => "JSON Inválido"
            ], 400);
        }

        */
        if($dados === null){
            responder([
                "erro" => "JSON Inválido"
            ], 400);
        }


        validarUsuario($dados);

        $nome = $dados["nome"];
        $email = $dados["email"];

        $sql = "UPDATE usuarios
                SET 
                    nome = ?,
                    email = ?
                WHERE 
                    id = ?
                ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nome,
            $email,
            $id
        ]);

        /*
        //Fazendo o uso do select antes para verificar se o usuário existe essa linha com rowCount não é mais necessária
        if($stmt->rowCount() === 0){//indica qtas linhas foram afetadas, ou seja, se o id não existir, não será afetado e portanto não será dada a msg de Usuário atualizado, mas o ideal seria dar um select e depois o update, pois se vc alterar com as mesmas informações ele pode retornar que nenhuma linha foi afetada a depender do banco
            responder([
                "erro" => "Usuário não encontrado"
            ], 400);
        }

        */
        //Exemplo sem usar a função
        /*
        echo json_encode([
            "mensagem" => "Usuário atualizado com sucesso",
            "id" => $id,
            "nome" => $nome,
            "email" => $email
        ]);
        */
        responder([
            "mensagem" => "Usuário atualizado com sucesso",
            "id" => $id,
            "nome" => $nome,
            "email" => $email
        ], 200);
    }else if($metodo === "DELETE"){
        //EXCLUIR/APAGAR

        //Igual no PATCH vamos verficar se mandaram um id
        if(!isset($_GET["id"])){
            responder([
                "erro" => "ID do usuário é obrigatório"
            ], 400);
        }

        //Pra depois colocar em uma variável e usá-lo
        $id = $_GET["id"];

        if(!is_numeric($id)){
            responder([
                "erro" => "ID inválido"
            ]);
        }

        //e depois verificar se ele existe no banco
        $sql = "SELECT id
                FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if(!$stmt->fetch()){
            responder([
                "erro" => "Usuário não encontrado"
            ], 404);
        }

        //AO invés de usar o select use o rowcount, diferente do patch ele não dá problema no delete
        /*
            if($stmt->rowCount() === 0){
                responder([
                    "erro" => "Usuário não encontrado"
                ], 404);
            }
        */

        $sql = "DELETE FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id
        ]);

        //sem usar a função responder
        /*
        echo json_encode([
            "mensagem" => "Usuário deletado com sucesso"
        ]);
        */
        responder([
            "mensagem" => "Usuário deletado com sucesso"
        ]);
    }else{
        responder([
            "erro" => "Método não permitido"
        ], 405);
    }
?>
