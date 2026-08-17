<?php
    header("Content-Type: application/json");
    require_once "conexao.php";
    $metodo = $_SERVER["REQUEST_METHOD"];//Informa se a requisição é GET, POST, PUT, PATCH ou DELETE
    if($metodo === "GET"){
        //BUSCAR
        $sql = "SELECT * FROM usuarios";
        $stmt = $pdo->query($sql);
        $usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
        echo json_encode($usuarios);
    }else if($metodo === "POST"){
        //CADASTRAR
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
        $id = $_GET["id"];

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

        echo json_encode([
            "mensagem" => "Usuário atualizado com sucesso"
        ]);
    }else if($metodo === "DELETE"){
        //EXCLUIR/APAGAR
        $id = $_GET["id"];

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

        echo_json_encode([
            "erro" => "Método não permitido"
        ]);
    }
?>
