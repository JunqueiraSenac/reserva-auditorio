<?php
// Versão simplificada da home para teste de conexão
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Teste básico de conexão
try {
    require_once 'model/Conexao.php';
    $conexao = Conexao::getInstance();
    $conn = $conexao->getConnection();

    // Buscar reservas de forma simples
    $stmt = $conn->prepare("SELECT * FROM reservas WHERE data >= CURDATE() ORDER BY data ASC LIMIT 10");
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin: 10px; border-radius: 5px;'>";
    echo "<strong>Erro de conexão:</strong> " . $e->getMessage();
    echo "</div>";
    $reservas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Sistema de Reserva (Teste)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #004A8D 0%, #F26C21 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #004A8D;
            margin: 0;
        }
        .header p {
            color: #F26C21;
            font-weight: bold;
        }
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #004A8D;
            color: white;
        }
        .btn-primary:hover {
            background: #003366;
        }
        .btn-secondary {
            background: #F26C21;
            color: white;
        }
        .btn-secondary:hover {
            background: #d45a1a;
        }
        .events {
            margin-top: 40px;
        }
        .event-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .event-date {
            font-weight: bold;
            color: #004A8D;
        }
        .event-time {
            color: #666;
            font-size: 14px;
        }
        .event-desc {
            margin-top: 5px;
        }
        .event-status {
            float: right;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-aprovada {
            background: #d4edda;
            color: #155724;
        }
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        .status-rejeitada {
            background: #f8d7da;
            color: #721c24;
        }
        .no-events {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SENAC - Sistema de Reserva de Auditório</h1>
            <p>Versão de Teste - Verificando Conexão</p>
        </div>

        <div class="buttons">
            <a href="view/login.php" class="btn btn-primary">Fazer Login</a>
            <a href="view/cadastro.php" class="btn btn-secondary">Cadastrar-se</a>
            <a href="calendario.php" class="btn btn-primary">Ver Calendário</a>
        </div>

        <div class="events">
            <h2>Próximos Eventos</h2>

            <?php if (empty($reservas)): ?>
                <div class="no-events">
                    <h3>Nenhum evento encontrado</h3>
                    <p>Não há eventos programados ou houve problema na conexão com o banco.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reservas as $reserva): ?>
                    <div class="event-card">
                        <span class="event-status status-<?php echo htmlspecialchars($reserva['status']); ?>">
                            <?php echo ucfirst($reserva['status']); ?>
                        </span>
                        <div class="event-date">
                            <?php echo date('d/m/Y', strtotime($reserva['data'])); ?>
                        </div>
                        <div class="event-time">
                            <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fim'], 0, 5); ?>
                        </div>
                        <div class="event-desc">
                            <?php echo htmlspecialchars($reserva['descricao']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-center;">
            <p><strong>Status da Conexão:</strong>
                <?php if (isset($conn)): ?>
                    <span style="color: green;">✅ Conectado</span>
                <?php else: ?>
                    <span style="color: red;">❌ Erro na conexão</span>
                <?php endif; ?>
            </p>
            <p><strong>Total de eventos:</strong> <?php echo count($reservas); ?></p>
            <p><a href="test_connection.php">🔧 Teste de Conexão Completo</a></p>
        </div>
    </div>

    <script>
        // JavaScript básico sem dependências
        document.addEventListener('DOMContentLoaded', function() {
            console.log('SENAC Sistema carregado - Versão de teste');
            console.log('Total de eventos carregados:', <?php echo count($reservas); ?>);

            // Testar se API funciona
            fetch('api/test.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Teste da API:', data);
                    if (data.success) {
                        console.log('✅ API funcionando');
                    } else {
                        console.log('❌ API com problema:', data.error);
                    }
                })
                .catch(error => {
                    console.log('❌ Erro ao chamar API:', error);
                });
        });
    </script>
</body>
</html>
