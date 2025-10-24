<?php
class Conexao {
    private static $instance = null;
    private $conn;
    
    private $host = 'sql312.infinityfree.com';
    private $dbname = 'if0_40245163_reserva_auditorio';
    private $username = 'if0_40245163';
    private $password = 'SUA_SENHA_AQUI'; // Substitua pela senha fornecida pelo InfinityFree
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Conexao();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
?>
