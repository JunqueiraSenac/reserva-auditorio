<?php
/**
 * Script de Criação Automática do Banco de Dados
 * Sistema de Reserva SENAC - XAMPP
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações de conexão para XAMPP
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'reserva_auditorio';

try {
    // Conectar ao MySQL sem especificar o banco (para poder criar)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🏛️ Criação do Banco - Sistema SENAC</h1>";
    echo "<hr>";

    // Criar banco de dados
    echo "<h2>1. Criando banco de dados...</h2>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco de dados '$dbname' criado com sucesso!<br>";

    // Conectar ao banco específico
    $pdo->exec("USE `$dbname`");
    echo "✅ Conectado ao banco '$dbname'<br>";

    // Criar tabela usuarios
    echo "<h2>2. Criando tabela usuarios...</h2>";
    $sql_usuarios = "
    CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nome` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `senha` VARCHAR(255) NOT NULL,
        `telefone` VARCHAR(20) DEFAULT NULL,
        `tipo_usuario` ENUM('admin','instrutor') DEFAULT 'instrutor',
        `status_aprovacao` ENUM('pendente','aprovado','rejeitado') DEFAULT 'pendente',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql_usuarios);
    echo "✅ Tabela 'usuarios' criada com sucesso!<br>";

    // Adicionar índice único para email
    try {
        $pdo->exec("ALTER TABLE `usuarios` ADD UNIQUE KEY `email_unique` (`email`)");
        echo "✅ Índice único para email adicionado<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            echo "⚠️ Aviso ao criar índice: " . $e->getMessage() . "<br>";
        } else {
            echo "ℹ️ Índice para email já existe<br>";
        }
    }

    // Criar tabela reservas
    echo "<h2>3. Criando tabela reservas...</h2>";
    $sql_reservas = "
    CREATE TABLE IF NOT EXISTS `reservas` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `usuario_id` INT(11) NOT NULL,
        `data` DATE NOT NULL,
        `hora_inicio` TIME NOT NULL,
        `hora_fim` TIME NOT NULL,
        `descricao` TEXT NOT NULL,
        `status` ENUM('pendente','aprovada','rejeitada','cancelada') DEFAULT 'pendente',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_usuario` (`usuario_id`),
        KEY `idx_data` (`data`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql_reservas);
    echo "✅ Tabela 'reservas' criada com sucesso!<br>";

    // Adicionar chave estrangeira
    try {
        $pdo->exec("ALTER TABLE `reservas` ADD CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE");
        echo "✅ Chave estrangeira adicionada<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            echo "⚠️ Aviso ao criar chave estrangeira: " . $e->getMessage() . "<br>";
        } else {
            echo "ℹ️ Chave estrangeira já existe<br>";
        }
    }

    // Inserir usuários padrão
    echo "<h2>4. Inserindo usuários padrão...</h2>";
    $usuarios_padrao = [
        [
            'nome' => 'Administrador SENAC',
            'email' => 'admin@senac.com',
            'senha' => password_hash('admin123', PASSWORD_DEFAULT),
            'telefone' => '(11) 99999-9999',
            'tipo_usuario' => 'admin',
            'status_aprovacao' => 'aprovado'
        ],
        [
            'nome' => 'João Silva',
            'email' => 'joao@senac.com',
            'senha' => password_hash('instrutor123', PASSWORD_DEFAULT),
            'telefone' => '(11) 98888-8888',
            'tipo_usuario' => 'instrutor',
            'status_aprovacao' => 'aprovado'
        ],
        [
            'nome' => 'Maria Santos',
            'email' => 'maria@senac.com',
            'senha' => password_hash('instrutor123', PASSWORD_DEFAULT),
            'telefone' => '(11) 97777-7777',
            'tipo_usuario' => 'instrutor',
            'status_aprovacao' => 'aprovado'
        ],
        [
            'nome' => 'Pedro Costa',
            'email' => 'pedro@senac.com',
            'senha' => password_hash('instrutor123', PASSWORD_DEFAULT),
            'telefone' => '(11) 96666-6666',
            'tipo_usuario' => 'instrutor',
            'status_aprovacao' => 'pendente'
        ]
    ];

    $stmt_usuario = $pdo->prepare("
        INSERT IGNORE INTO usuarios (nome, email, senha, telefone, tipo_usuario, status_aprovacao)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $usuarios_inseridos = 0;
    foreach ($usuarios_padrao as $usuario) {
        if ($stmt_usuario->execute([
            $usuario['nome'],
            $usuario['email'],
            $usuario['senha'],
            $usuario['telefone'],
            $usuario['tipo_usuario'],
            $usuario['status_aprovacao']
        ])) {
            if ($stmt_usuario->rowCount() > 0) {
                $usuarios_inseridos++;
            }
        }
    }

    echo "✅ $usuarios_inseridos usuários inseridos (duplicados ignorados)<br>";

    // Inserir reservas de exemplo
    echo "<h2>5. Inserindo reservas de exemplo...</h2>";
    $reservas_exemplo = [
        [
            'usuario_id' => 2, // João
            'data' => date('Y-m-d'),
            'hora_inicio' => '09:00:00',
            'hora_fim' => '11:00:00',
            'descricao' => 'Palestra sobre Marketing Digital - Introdução às estratégias digitais modernas',
            'status' => 'aprovada'
        ],
        [
            'usuario_id' => 2,
            'data' => date('Y-m-d', strtotime('+1 day')),
            'hora_inicio' => '14:00:00',
            'hora_fim' => '16:00:00',
            'descricao' => 'Workshop de Programação Web - HTML, CSS e JavaScript básico',
            'status' => 'aprovada'
        ],
        [
            'usuario_id' => 3, // Maria
            'data' => date('Y-m-d', strtotime('+2 days')),
            'hora_inicio' => '08:00:00',
            'hora_fim' => '12:00:00',
            'descricao' => 'Curso de Gestão de Projetos - Metodologias ágeis e tradicionais',
            'status' => 'pendente'
        ],
        [
            'usuario_id' => 3,
            'data' => date('Y-m-d', strtotime('+3 days')),
            'hora_inicio' => '13:00:00',
            'hora_fim' => '17:00:00',
            'descricao' => 'Seminário de Empreendedorismo - Como iniciar seu próprio negócio',
            'status' => 'aprovada'
        ],
        [
            'usuario_id' => 2,
            'data' => date('Y-m-d', strtotime('+5 days')),
            'hora_inicio' => '10:00:00',
            'hora_fim' => '12:00:00',
            'descricao' => 'Treinamento de Vendas - Técnicas avançadas de negociação',
            'status' => 'pendente'
        ],
        [
            'usuario_id' => 3,
            'data' => date('Y-m-d', strtotime('+7 days')),
            'hora_inicio' => '15:00:00',
            'hora_fim' => '18:00:00',
            'descricao' => 'Workshop de Design Gráfico - Princípios básicos do design',
            'status' => 'aprovada'
        ]
    ];

    $stmt_reserva = $pdo->prepare("
        INSERT IGNORE INTO reservas (usuario_id, data, hora_inicio, hora_fim, descricao, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $reservas_inseridas = 0;
    foreach ($reservas_exemplo as $reserva) {
        if ($stmt_reserva->execute([
            $reserva['usuario_id'],
            $reserva['data'],
            $reserva['hora_inicio'],
            $reserva['hora_fim'],
            $reserva['descricao'],
            $reserva['status']
        ])) {
            if ($stmt_reserva->rowCount() > 0) {
                $reservas_inseridas++;
            }
        }
    }

    echo "✅ $reservas_inseridas reservas de exemplo inseridas<br>";

    // Verificar dados criados
    echo "<h2>6. Verificando dados criados...</h2>";

    $count_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $count_reservas = $pdo->query("SELECT COUNT(*) FROM reservas")->fetchColumn();

    echo "📊 Total de usuários: $count_usuarios<br>";
    echo "📊 Total de reservas: $count_reservas<br>";

    // Marcar setup como concluído
    file_put_contents('setup_complete.txt', date('Y-m-d H:i:s') . ' - Banco criado via create_database.php');
    echo "✅ Setup marcado como concluído<br>";

    echo "<h2>✅ Banco de dados configurado com sucesso!</h2>";

    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🔑 Credenciais de Acesso:</h3>";
    echo "<ul>";
    echo "<li><strong>Administrador:</strong> admin@senac.com / admin123</li>";
    echo "<li><strong>Instrutor:</strong> joao@senac.com / instrutor123</li>";
    echo "<li><strong>Instrutor:</strong> maria@senac.com / instrutor123</li>";
    echo "<li><strong>Instrutor (pendente):</strong> pedro@senac.com / instrutor123</li>";
    echo "</ul>";
    echo "</div>";

    echo "<div style='text-align: center; margin: 30px 0;'>";
    echo "<a href='home.php' style='background: #004A8D; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>🏠 Ir para Home</a>";
    echo "<a href='view/login.php' style='background: #F26C21; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔑 Fazer Login</a>";
    echo "<a href='test_connection.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 10px;'>🔍 Testar Conexão</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h2>❌ Erro ao criar banco de dados:</h2>";
    echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Linha:</strong> " . $e->getLine() . "</p>";
    echo "</div>";

    echo "<h3>🔧 Possíveis soluções:</h3>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando no XAMPP</li>";
    echo "<li>Confirme se o usuário 'root' não tem senha no MySQL</li>";
    echo "<li>Tente executar o SQL manualmente no phpMyAdmin</li>";
    echo "<li>Verifique se não há problemas de permissão</li>";
    echo "</ul>";

    echo "<div style='text-align: center; margin: 20px 0;'>";
    echo "<a href='database_simple.sql' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📄 Baixar SQL Simples</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h2>❌ Erro geral:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Script executado em: " . date('d/m/Y H:i:s') . "</small></p>";
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background: linear-gradient(135deg, #004A8D 0%, #F26C21 100%);
    color: #333;
}

h1 {
    color: #004A8D;
    text-align: center;
}

h2 {
    color: #F26C21;
    margin-top: 20px;
}

div {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin: 10px 0;
}
</style>
