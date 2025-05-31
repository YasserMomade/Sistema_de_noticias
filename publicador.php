<?php

require_once 'usuario.php';

class Publicador extends Usaurio{
    private $topico;

    public function __construct($nome, $email, $senha, $topico) {
        parent::__construct($nome, $email, $senha);
        $this->perfil = "publicador";
        $this->topico = $topico;
    }

    public function getTopico(){
        return $this -> topico;
    }


}