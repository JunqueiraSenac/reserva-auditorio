<?php
class Conexao
{
    private static $instance = null;
    private $conn;

    // Configurações para diferentes ambientes
    private $configs = [
        "production" => [
            "host" => "sql212.infinityfree.com",
            "dbname" => "if0_40245163_reserva_auditorio",
            "username" => "if0_40245163",
            "password" => "kayo2810",
        ],
        "development" => [
            "host" => "localhost",
            "dbname" => "reserva_auditorio",
            "username" => "root",
            "password" => "",
        ],
    ];

    private function __construct()
    {
        $config = $this->getEnvironmentConfig();

        try {
            $dsn = "mysql:host={$config["host"]};dbname={$config["dbname"]};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND =>
                    "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            $this->conn = new PDO(
                $dsn,
                $config["username"],
                $config["password"],
                $options,
            );
        } catch (PDOException $e) {
            $this->logError("Erro na conexão com banco de dados", $e);
            die(
                "Erro na conexão com o banco de dados. Verifique as configurações."
            );
        }
    }

    /**
     * Detecta automaticamente o ambiente baseado no host
     */
    private function getEnvironmentConfig()
    {
        $host =
            $_SERVER["HTTP_HOST"] ?? ($_SERVER["SERVER_NAME"] ?? "localhost");

        // Se contém infinityfree ou é o domínio de produção, usa config de produção
        if (
            strpos($host, "infinityfree") !== false ||
            strpos($host, "senachub.infinityfree.me") !== false
        ) {
            return $this->configs["production"];
        }

        // Caso contrário, usa desenvolvimento (localhost, xampp, etc)
        return $this->configs["development"];
    }

    /**
     * Retorna a instância única da conexão (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Testa a conexão
     */
    public function testConnection()
    {
        try {
            $this->conn->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            $this->logError("Erro ao testar conexão", $e);
            return false;
        }
    }

    /**
     * Retorna informações sobre a conexão atual (sem dados sensíveis)
     */
    public function getConnectionInfo()
    {
        $config = $this->getEnvironmentConfig();
        $environment =
            strpos($_SERVER["HTTP_HOST"] ?? "localhost", "infinityfree") !==
            false
                ? "production"
                : "development";

        return [
            "host" => $config["host"],
            "database" => $config["dbname"],
            "environment" => $environment,
            "charset" => "utf8mb4",
        ];
    }

    /**
     * Verifica se as tabelas necessárias existem
     */
    public function checkTables()
    {
        $requiredTables = ["usuarios", "reservas"];
        $existingTables = [];

        try {
            $stmt = $this->conn->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($requiredTables as $table) {
                if (in_array($table, $tables)) {
                    $existingTables[$table] = true;
                } else {
                    $existingTables[$table] = false;
                }
            }

            return $existingTables;
        } catch (PDOException $e) {
            $this->logError("Erro ao verificar tabelas", $e);
            return false;
        }
    }

    /**
     * Cria as tabelas necessárias se não existirem
     */
    public function createTables()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL UNIQUE,
            `senha` varchar(255) NOT NULL,
            `telefone` varchar(20) DEFAULT NULL,
            `tipo_usuario` enum('admin','instrutor') DEFAULT 'instrutor',
            `status_aprovacao` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS `reservas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `data` date NOT NULL,
            `hora_inicio` time NOT NULL,
            `hora_fim` time NOT NULL,
            `descricao` text NOT NULL,
            `status` enum('pendente','aprovada','rejeitada','cancelada') DEFAULT 'pendente',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `usuario_id` (`usuario_id`),
            KEY `data` (`data`),
            KEY `status` (`status`),
            CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        INSERT IGNORE INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `tipo_usuario`, `status_aprovacao`) VALUES
        (1, 'Administrador', 'admin@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 99999-9999', 'admin', 'aprovado'),
        (2, 'João Silva', 'joao@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 98888-8888', 'instrutor', 'aprovado');
        ";

        try {
            $this->conn->exec($sql);
            return true;
        } catch (PDOException $e) {
            $this->logError("Erro ao criar tabelas", $e);
            return false;
        }
    }

    /**
     * Log de erros
     */
    private function logError($message, $exception = null)
    {
        $logMessage = date("Y-m-d H:i:s") . " - " . $message;

        if ($exception) {
            $logMessage .= " - " . $exception->getMessage();
            $logMessage .=
                " (Arquivo: " .
                $exception->getFile() .
                ", Linha: " .
                $exception->getLine() .
                ")";
        }

        // Em desenvolvimento, mostra o erro
        if ($this->isDevelopment()) {
            error_log($logMessage);
        }
    }

    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    private function isDevelopment()
    {
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";
        return strpos($host, "localhost") !== false ||
            strpos($host, "127.0.0.1") !== false;
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne deserialização
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
