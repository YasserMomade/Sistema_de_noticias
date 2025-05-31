<?php

require_once 'conexao.php';

class utilizadorDAO{

private $pdo;

public function __construct() {
    $this->pdo = Conexao::getInstance()->getPDO();
}



public function salvar(Usaurio $usuario){
    $sql = "INSERT INTO utilizador (nome, email, senha, perfil) VALUES (?, ?, ?, ?)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        $usuario->getNome(),
        $usuario->getEmail(),
        $usuario->getSenha(),
        $usuario->getPerfil()
    ]);   


if ($usuario instanceof Publicador) {
    $idPublicador = $this->pdo->lastInsertId(); 
    $sqlTopico = "INSERT INTO topico (topico, id_publicador) VALUES (?, ?)";
    $stmtTopico = $this->pdo->prepare($sqlTopico);
    $stmtTopico->execute([
        $usuario->getTopico(),
        $idPublicador
    ]);
}

}
}