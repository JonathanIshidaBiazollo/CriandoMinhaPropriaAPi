<?php
    header("Content-Type: application/json");
    require_once "conexao.php";
    $metodo = $_SERVER["REQUEST_METHOD"];//Informa se a requisição é GET, POST, PUT, PATCH ou DELETE
    if($metodo === "GET"){
        //BUSCAR
        if(isset($_GET["id"])){
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
                http_response_code(404);//Recurso não encontrado

                echo json_encode([
                    "erro" => "Usuário não encontrado"
                ]);
                exit;
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

        $nome = $dados["nome"] ?? "";
        $email = $dados["email"] ?? "";

        if(empty($dados["nome"]) || empty($dados["email"])){
            http_response_code(400);

            echo json_encode([
                "erro" => "Nome e email são obrigatórios"
            ]);

            exit;
        }

        //não basta colocar só o type email no html, pois o usuário pode mudar lá, tem que tratar aqui tbm
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            http_response_code(400);

            echo json_encode([
                "erro" => "Email inválido"
            ]);

            exit;
        }

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

        http_response_code(201);//A requisição foi criada e um novo recurso foi criado

        echo json_encode([
            "mensagem" => "Usuário cadastrado com sucesso",
            "id" => $pdo->lastInsertId(),
            "nome" => $nome,
            "email" => $email
        ]);

        
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
            http_response_code(400);

            echo json_encode([
                "erro" => "ID do usuário é obrigatório"
            ]);
            exit;
        }

        //Pra depois pegar o valor e atribuir a uma variável e poder usar 
        $id = $_GET["id"];

        //Agora vou verificar se esse id existe no banco de dados pra poder editá-lo
        $sql = "SELECT id
                FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if(!$stmt->fetch()){
            http_response_code(404);

            echo json_encode([
                "erro" => "Usuário não encontrado"
            ]);

            exit;
        }

        $dados = json_decode(
            file_get_contents("php://input"),
            true
        );

        if(empty($dados["nome"]) || empty($dados["email"])){
            http_response_code(400);

            echo json_encode([
                "erro" => "Nome e email são obrigatórios"
            ]);

            exit;
        }

        $nome = $dados["nome"];
        $email = $dados["email"];

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            http_response_code(400);

            echo json_encode([
                "erro" => "Email inválido"
            ]);

            exit;
        }

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

        if($stmt->rowCount() === 0){//indica qtas linhas foram afetadas, ou seja, se o id não existir, não será afetado e portanto não será dada a msg de Usuário atualizado, mas o ideal seria dar um select e depois o update, pois se vc alterar com as mesmas informações ele pode retornar que nenhuma linha foi afetada a depender do banco
            http_response_code(400);

            echo json_encode([
                "erro" => "Usuário não encontrado"
            ]);

            exit;
        }

        echo json_encode([
            "mensagem" => "Usuário atualizado com sucesso",
            "id" => $id,
            "nome" => $nome,
            "email" => $email
        ]);
    }else if($metodo === "DELETE"){
        //EXCLUIR/APAGAR

        //Igual no PATCH vamos verficar se mandaram um id
        if(!isset($_GET["id"])){
            http_response_code(400);

            echo json_encode([
                "erro" => "ID do usuário é obrigatório"
            ]);

            exit;
        }

        //Pra depois colocar em uma variável e usá-lo
        $id = $_GET["id"];

        //e depois verificar se ele existe no banco
        $sql = "SELECT id
                FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        if(!$stmt->fetch()){
            http_response_code(404);

            echo json_encode([
                "erro" => "Usuário não encontrado"
            ]);
            exit;
        }

        $sql = "DELETE FROM usuarios
                WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id
        ]);

        echo json_encode([
            "mensagem" => "Usuário deletado com sucesso"
        ]);
    }else{
        http_response_code(405);

        echo json_encode([
            "erro" => "Método não permitido"
        ]);
    }
?>
