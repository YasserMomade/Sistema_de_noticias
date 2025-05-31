<?php
require_once 'utilizador.php';
require_once 'publicador.php';

class utilizadorFactory {

    public static function criar($tipo, $dados){

        switch($tipo){
            case 'utilizador':
                return new Utilizador($dados['nome'], $dados['email'], $dados['senha']);
        
            case 'publicador':
                return new Publicador($dados['nome'], $dados['email'], $dados['senha'], $dados['topico']);  
            } 

    }

}