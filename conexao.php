<?php
class Conexao {
    private static $instance = null;
    private $pdo;

    
    private function __construct() {
        try {
            //  conexxo 
            $this->pdo = new PDO('mysql:host=localhost;dbname=noticias', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Modo de erro
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            exit;
        }
    }

    // Metodo estatico para obter a instancia da conexão
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Conexao();
        }

        return self::$instance;
    }

    // Metodo para obter a instancia do PDO
    public function getPDO() {
        return $this->pdo;
    }

    
}

// Agora em qualquer lugar do sistema pode se obter a instancia unica da conexo com a base de dados
// Usando: Conexao::getInstance()->getPDO();
?>
