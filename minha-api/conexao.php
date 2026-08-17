<?php
    $host = "localhost";
    $db = "minha_api";
    $user = "root";
    $senha = "";

    try{
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8",
            $user,
            $senha
        );

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }catch(PDOException $erro){
        http_response_code(500);
        echo json_encode([
            "erro" => "Erro ao conectar com o banco"
        ]);

        exit;
    }
?>