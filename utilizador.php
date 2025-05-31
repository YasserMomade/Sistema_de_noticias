<?php

require_once 'usuario.php';

class Utilizador extends Usaurio{
    public function __construct($nome, $email, $senha) {
        parent::__construct($nome, $email, $senha);
        $this->perfil = "utilizador"; }

}