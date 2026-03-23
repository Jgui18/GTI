<?php
/**
 * Arquivo de conexão com o banco de dados MySQL
 * Utiliza PDO para conexão segura
 */

class Conexao {
    private static $instancia = null;
    private $pdo;
    
    // Configurações do banco de dados
    private const HOST = 'localhost';
    private const DB_NAME = 'gti_bd';
    private const USER = 'root';
    private const PASSWORD = ''; // Ajuste a senha conforme necessário
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
            $opcoes = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, self::USER, self::PASSWORD, $opcoes);
        } catch (PDOException $e) {
            // Em produção, não exponha detalhes do erro
            error_log("Erro de conexão: " . $e->getMessage());
            throw new Exception("Erro ao conectar com o banco de dados.");
        }
    }
    
    public static function getInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    
    public function getPDO() {
        return $this->pdo;
    }
    
    // Previne clonagem
    private function __clone() {}
    
    // Previne unserialize
    public function __wakeup() {
        throw new Exception("Não é possível unserialize singleton");
    }
}

// Função auxiliar para obter conexão
function obterConexao() {
    return Conexao::getInstancia()->getPDO();
}
