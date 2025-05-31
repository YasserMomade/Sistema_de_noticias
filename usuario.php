<?php
abstract class Usaurio{

    protected $nome;
    protected $email;
    protected $senha;
    protected $perfil;


    public function __construct($nome,$email,$senha)
    {
        $this -> nome = $nome;
        $this -> email = $email;
        $this -> senha = $senha;
    }

    public function getnome(){ return $this->nome;}
    public function getEmail() { return $this->email; }
    public function getSenha() { return $this->senha; }
    public function getPerfil() { return $this->perfil; }

}
