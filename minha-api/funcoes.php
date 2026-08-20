<?php
    //Funções
    //400 -> requisição inválida
    //404 -> recurso não encontrado
    /*
    | Operação             | Status |
    | -------------------- | -----: |
    | GET encontrado       |  `200` |
    | GET não encontrado   |  `404` |
    | POST criado          |  `201` |
    | PATCH atualizado     |  `200` |
    | DELETE realizado     |  `200` |
    | Dados inválidos      |  `400` |
    | Método não permitido |  `405` |
    */

   
    function responder($dados, $status = 200){//se eu não passar nada fica por padrão o código 200 que significa que o status está OK
            
        http_response_code($status);

        echo json_encode($dados);

        exit;
    }

    function validarUsuario($dados){
        if(empty($dados["nome"]) || empty($dados["email"])){
            responder([
                "erro" => "Nome e email são obrigatórios"
            ], 400);
        }

        if(!filter_var($dados["email"], FILTER_VALIDATE_EMAIL)){//não basta colocar só o type email no html, pois o usuário pode mudar lá, tem que tratar aqui tbm
            responder([
                "erro" => "Email inválido"
            ], 400);
        }
    }
?>