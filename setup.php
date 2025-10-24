<?php
/**
 * Script de Setup Automático - Sistema de Reserva SENAC
 * Este script configura automaticamente o banco de dados para XAMPP
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se já foi configurado
$configFile = 'setup_complete.txt';
$isConfigured = file_exists($configFile);

// Processar formulário
$setupResult = null;
if ($_POST && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'setup_database':
            $setupResult = setupDatabase();
            break;
        case 'test_connection':
            $setupResult = testConnection();
            break;
        case 'create_admin':
            $setupResult = createAdminUser($_POST);
            break;
        case 'reset_setup':
            if (file_exists($configFile)) {
                unlink($configFile);
            }
            $isConfigured = false;
            $setupResult = ['success' => true, 'message' => 'Setup resetado com sucesso!'];
            break;
    }
}

function testConnection() {
    try {
        require_once 'model/Conexao.php';
        $conexao = Conexao::getInstance();
        $conn = $conexao->getConnection();

        $stmt = $conn->query("SELECT 1 as teste, NOW() as agora");
        $result = $stmt->fetch();

        return [
            'success' => true,
            'message' => 'Conexão estabelecida com sucesso!',
            'data' => $result
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erro na conexão: ' . $e->getMessage()
        ];
    }
}

function setupDatabase() {
    try {
        require_once 'model/Conexao.php';
        $conexao = Conexao::getInstance();

        // Verificar se já existe o banco
        $conn = $conexao->getConnection();

        // Criar tabelas
        if ($conexao->createTables()) {
            // Marcar como configurado
            file_put_contents('setup_complete.txt', date('Y-m-d H:i:s'));

            return [
                'success' => true,
                'message' => 'Banco de dados configurado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao criar tabelas do banco de dados'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erro durante setup: ' . $e->getMessage()
        ];
    }
}

function createAdminUser($data) {
    try {
        require_once 'model/Conexao.php';
        require_once 'model/Usuario.php';

        $usuario = new Usuario();

        $nome = $data['nome'] ?? 'Administrador';
        $email = $data['email'] ?? 'admin@senac.com';
        $senha = $data['senha'] ?? 'admin123';
        $telefone = $data['telefone'] ?? '';

        $result = $usuario->criar($nome, $email, $senha, $telefone, 'admin', 'aprovado');

        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuário administrador criado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao criar usuário administrador (pode já existir)'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erro ao criar admin: ' . $e->getMessage()
        ];
    }
}

function getSystemInfo() {
    $info = [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Desconhecido',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Desconhecido'
    ];

    // Verificar extensões PHP necessárias
    $info['extensions'] = [
        'pdo' => extension_loaded('pdo') ? '✅' : '❌',
        'pdo_mysql' => extension_loaded('pdo_mysql') ? '✅' : '❌',
        'mbstring' => extension_loaded('mbstring') ? '✅' : '❌',
        'openssl' => extension_loaded('openssl') ? '✅' : '❌'
    ];

    return $info;
}

$systemInfo = getSystemInfo();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Sistema de Reserva SENAC</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #004A8D 0%, #F26C21 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #004A8D;
            margin: 0;
            font-size: 2.5em;
        }

        .header p {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 1.1em;
        }

        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: bold;
        }

        .status.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .status.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .status.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .status.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 5px;
            background: #004A8D;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #003366;
            transform: translateY(-2px);
        }

        .btn.secondary {
            background: #F26C21;
        }

        .btn.secondary:hover {
            background: #d45a1a;
        }

        .btn.danger {
            background: #dc3545;
        }

        .btn.danger:hover {
            background: #c82333;
        }

        .form-group {
            margin: 15px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #004A8D;
        }

        .info-item strong {
            color: #004A8D;
        }

        .steps {
            counter-reset: step-counter;
        }

        .step {
            counter-increment: step-counter;
            position: relative;
            padding: 20px;
            margin: 15px 0;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #004A8D;
        }

        .step::before {
            content: counter(step-counter);
            position: absolute;
            left: -15px;
            top: 15px;
            background: #004A8D;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .completed {
            border-left-color: #28a745;
        }

        .completed::before {
            background: #28a745;
            content: "✓";
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🏛️ Setup - Sistema SENAC</h1>
                <p>Configuração Automática para XAMPP</p>
            </div>

            <?php if ($setupResult): ?>
                <div class="status <?php echo $setupResult['success'] ? 'success' : 'error'; ?>">
                    <?php echo $setupResult['message']; ?>
                    <?php if (isset($setupResult['data'])): ?>
                        <br><small>Dados: <?php echo json_encode($setupResult['data']); ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($isConfigured): ?>
                <div class="status success">
                    ✅ Sistema já configurado! Configurado em: <?php echo file_get_contents($configFile); ?>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="home.php" class="btn">🏠 Acessar Sistema</a>
                    <a href="view/login.php" class="btn secondary">🔑 Fazer Login</a>

                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="reset_setup">
                        <button type="submit" class="btn danger" onclick="return confirm('Tem certeza que deseja resetar a configuração?')">
                            🔄 Resetar Setup
                        </button>
                    </form>
                </div>
            <?php else: ?>

                <div class="steps">
                    <div class="step">
                        <h3>1. Informações do Sistema</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>PHP Version:</strong><br>
                                <?php echo $systemInfo['php_version']; ?>
                            </div>
                            <div class="info-item">
                                <strong>Servidor:</strong><br>
                                <?php echo $systemInfo['server_software']; ?>
                            </div>
                            <div class="info-item">
                                <strong>Host:</strong><br>
                                <?php echo $systemInfo['http_host']; ?>
                            </div>
                        </div>

                        <h4>Extensões PHP:</h4>
                        <div class="info-grid">
                            <?php foreach ($systemInfo['extensions'] as $ext => $status): ?>
                                <div class="info-item">
                                    <strong><?php echo strtoupper($ext); ?>:</strong>
                                    <?php echo $status; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="step">
                        <h3>2. Teste de Conexão</h3>
                        <p>Primeiro vamos testar se conseguimos conectar ao MySQL do XAMPP.</p>

                        <form method="post">
                            <input type="hidden" name="action" value="test_connection">
                            <button type="submit" class="btn">🔍 Testar Conexão</button>
                        </form>
                    </div>

                    <div class="step">
                        <h3>3. Configurar Banco de Dados</h3>
                        <p>Criar tabelas e dados iniciais necessários para o sistema.</p>

                        <div class="status info">
                            <strong>Atenção:</strong> Este passo irá criar automaticamente:
                            <ul>
                                <li>Tabela 'usuarios'</li>
                                <li>Tabela 'reservas'</li>
                                <li>Usuário administrador padrão</li>
                                <li>Usuário instrutor de teste</li>
                            </ul>
                        </div>

                        <form method="post">
                            <input type="hidden" name="action" value="setup_database">
                            <button type="submit" class="btn secondary">⚙️ Configurar Banco</button>
                        </form>
                    </div>

                    <div class="step">
                        <h3>4. Criar Usuário Administrador (Opcional)</h3>
                        <p>Se desejar, crie um usuário administrador personalizado.</p>

                        <form method="post">
                            <input type="hidden" name="action" value="create_admin">

                            <div class="form-group">
                                <label>Nome:</label>
                                <input type="text" name="nome" value="Administrador SENAC" required>
                            </div>

                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" value="admin@senac.com" required>
                            </div>

                            <div class="form-group">
                                <label>Senha:</label>
                                <input type="password" name="senha" value="admin123" required>
                            </div>

                            <div class="form-group">
                                <label>Telefone:</label>
                                <input type="tel" name="telefone" value="(11) 99999-9999">
                            </div>

                            <button type="submit" class="btn">👤 Criar Admin</button>
                        </form>
                    </div>
                </div>

                <div class="status warning">
                    <strong>Credenciais Padrão:</strong><br>
                    Admin: admin@senac.com / admin123<br>
                    Instrutor: joao@senac.com / instrutor123
                </div>

            <?php endif; ?>
        </div>

        <div class="card">
            <h3>🛠️ Ferramentas de Diagnóstico</h3>
            <div style="text-align: center;">
                <a href="test_connection.php" class="btn">🔍 Teste de Conexão Detalhado</a>
                <a href="diagnostic.php" class="btn secondary">📊 Diagnóstico Completo</a>
                <a href="home_simple.php" class="btn">🏠 Home Simplificada</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh em algumas situações
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setup do Sistema SENAC carregado');

            // Se acabou de fazer setup, mostrar sucesso
            <?php if ($setupResult && $setupResult['success'] && isset($_POST['action']) && $_POST['action'] === 'setup_database'): ?>
                setTimeout(() => {
                    if (confirm('Setup concluído com sucesso! Deseja ir para a página inicial?')) {
                        window.location.href = 'home.php';
                    }
                }, 2000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
