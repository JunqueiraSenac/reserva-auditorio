<?php
// Página de diagnóstico do sistema
// Acesso restrito para verificações técnicas

// Verificar se está em ambiente de desenvolvimento
$isDevelopment = (strpos($_SERVER['HTTP_HOST'] ?? 'localhost', 'localhost') !== false) ||
                 (strpos($_SERVER['HTTP_HOST'] ?? 'localhost', '127.0.0.1') !== false) ||
                 (strpos($_SERVER['HTTP_HOST'] ?? 'localhost', 'xampp') !== false);

// Em produção, requer uma chave de acesso
if (!$isDevelopment) {
    $accessKey = $_GET['key'] ?? '';
    if ($accessKey !== 'senac2024debug') {
        http_response_code(404);
        die('Página não encontrada');
    }
}

require_once 'model/Conexao.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Diagnóstico do Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-ok { color: #10b981; }
        .status-warning { color: #f59e0b; }
        .status-error { color: #ef4444; }
        .status-info { color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <img src="public/images/logo-senac.png" alt="SENAC Logo" class="h-16 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Diagnóstico do Sistema</h1>
                <p class="text-gray-600">Sistema de Reserva de Auditório SENAC</p>
            </div>

            <!-- Informações do Ambiente -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-blue-900 mb-4">
                    <i class="fas fa-server mr-2"></i>
                    Informações do Ambiente
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong>Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'N/A'; ?>
                    </div>
                    <div>
                        <strong>Ambiente:</strong>
                        <span class="<?php echo $isDevelopment ? 'status-warning' : 'status-ok'; ?>">
                            <?php echo $isDevelopment ? 'Desenvolvimento' : 'Produção'; ?>
                        </span>
                    </div>
                    <div>
                        <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                    </div>
                    <div>
                        <strong>Data/Hora:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                    </div>
                </div>
            </div>

            <!-- Teste de Conexão com Banco -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-database mr-2"></i>
                    Conexão com Banco de Dados
                </h2>
                <?php
                try {
                    $conexao = Conexao::getInstance();
                    $conn = $conexao->getConnection();
                    $connectionInfo = $conexao->getConnectionInfo();

                    echo '<div class="status-ok mb-4">';
                    echo '<i class="fas fa-check-circle mr-2"></i>';
                    echo 'Conexão estabelecida com sucesso!';
                    echo '</div>';

                    echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                    echo '<div><strong>Host:</strong> ' . htmlspecialchars($connectionInfo['host']) . '</div>';
                    echo '<div><strong>Database:</strong> ' . htmlspecialchars($connectionInfo['database']) . '</div>';
                    echo '<div><strong>Ambiente:</strong> ' . htmlspecialchars($connectionInfo['environment']) . '</div>';
                    echo '</div>';

                } catch (Exception $e) {
                    echo '<div class="status-error">';
                    echo '<i class="fas fa-times-circle mr-2"></i>';
                    echo 'Erro na conexão: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Verificação de Tabelas -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-table mr-2"></i>
                    Estrutura do Banco de Dados
                </h2>
                <?php
                try {
                    $tabelas = ['usuarios', 'reservas'];

                    foreach ($tabelas as $tabela) {
                        try {
                            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM $tabela");
                            $stmt->execute();
                            $result = $stmt->fetch();

                            echo '<div class="flex justify-between items-center py-2 border-b">';
                            echo '<span><i class="fas fa-table mr-2 status-ok"></i>' . ucfirst($tabela) . '</span>';
                            echo '<span class="status-info">' . $result['total'] . ' registros</span>';
                            echo '</div>';

                        } catch (PDOException $e) {
                            echo '<div class="flex justify-between items-center py-2 border-b">';
                            echo '<span><i class="fas fa-exclamation-triangle mr-2 status-error"></i>' . ucfirst($tabela) . '</span>';
                            echo '<span class="status-error">Erro: ' . htmlspecialchars($e->getMessage()) . '</span>';
                            echo '</div>';
                        }
                    }

                } catch (Exception $e) {
                    echo '<div class="status-error">';
                    echo 'Erro ao verificar tabelas: ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Verificação de Arquivos Críticos -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-code mr-2"></i>
                    Arquivos do Sistema
                </h2>
                <?php
                $arquivos_criticos = [
                    'model/Conexao.php' => 'Classe de Conexão',
                    'model/Usuario.php' => 'Model Usuario',
                    'model/Reserva.php' => 'Model Reserva',
                    'controller/LoginController.php' => 'Controller Login',
                    'controller/ReservaController.php' => 'Controller Reserva',
                    'view/login.php' => 'Página de Login',
                    'view/painel_instrutor.php' => 'Painel Instrutor',
                    'home.php' => 'Página Inicial',
                    'calendario.php' => 'Calendário',
                    'api/reserva.php' => 'API de Reservas',
                    'public/js/app.js' => 'JavaScript Principal',
                    'public/css/tailwind-base.css' => 'CSS Base'
                ];

                foreach ($arquivos_criticos as $arquivo => $descricao) {
                    $existe = file_exists($arquivo);
                    $status = $existe ? 'status-ok' : 'status-error';
                    $icon = $existe ? 'fa-check-circle' : 'fa-times-circle';

                    echo '<div class="flex justify-between items-center py-2 border-b">';
                    echo '<span><i class="fas ' . $icon . ' mr-2 ' . $status . '"></i>' . $descricao . '</span>';
                    echo '<span class="text-sm text-gray-500">' . $arquivo . '</span>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Teste das APIs -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-plug mr-2"></i>
                    Teste das APIs
                </h2>
                <div id="api-tests">
                    <div class="text-gray-600">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Testando APIs...
                    </div>
                </div>
                <button onclick="testarAPIs()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-redo mr-2"></i>
                    Testar Novamente
                </button>
            </div>

            <!-- Configurações Recomendadas -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-yellow-900 mb-4">
                    <i class="fas fa-cog mr-2"></i>
                    Configurações PHP
                </h2>
                <?php
                $configs = [
                    'max_execution_time' => ['atual' => ini_get('max_execution_time'), 'recomendado' => '60'],
                    'memory_limit' => ['atual' => ini_get('memory_limit'), 'recomendado' => '128M'],
                    'upload_max_filesize' => ['atual' => ini_get('upload_max_filesize'), 'recomendado' => '10M'],
                    'post_max_size' => ['atual' => ini_get('post_max_size'), 'recomendado' => '10M']
                ];

                foreach ($configs as $config => $valores) {
                    $status = ($valores['atual'] >= $valores['recomendado']) ? 'status-ok' : 'status-warning';
                    echo '<div class="flex justify-between items-center py-2 border-b">';
                    echo '<span>' . $config . '</span>';
                    echo '<span><span class="' . $status . '">' . $valores['atual'] . '</span> (rec: ' . $valores['recomendado'] . ')</span>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Ações de Manutenção -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tools mr-2"></i>
                    Ações de Manutenção
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="limparCache()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-broom mr-2"></i>
                        Limpar Cache
                    </button>
                    <button onclick="verificarIntegridade()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        <i class="fas fa-check-double mr-2"></i>
                        Verificar Integridade
                    </button>
                    <button onclick="exportarLogs()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        <i class="fas fa-download mr-2"></i>
                        Exportar Logs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testarAPIs() {
            const apiTests = document.getElementById('api-tests');
            apiTests.innerHTML = '<div class="text-gray-600"><i class="fas fa-spinner fa-spin mr-2"></i>Testando APIs...</div>';

            const apis = [
                { url: 'api/reserva.php?action=listar', name: 'Listar Reservas' },
                { url: 'api/reserva.php?action=estatisticas', name: 'Estatísticas' }
            ];

            let results = [];
            let completed = 0;

            apis.forEach(api => {
                fetch(api.url)
                    .then(response => {
                        results.push({
                            name: api.name,
                            status: response.ok ? 'success' : 'error',
                            code: response.status
                        });
                    })
                    .catch(error => {
                        results.push({
                            name: api.name,
                            status: 'error',
                            error: error.message
                        });
                    })
                    .finally(() => {
                        completed++;
                        if (completed === apis.length) {
                            displayAPIResults(results);
                        }
                    });
            });
        }

        function displayAPIResults(results) {
            const apiTests = document.getElementById('api-tests');
            let html = '';

            results.forEach(result => {
                const statusClass = result.status === 'success' ? 'status-ok' : 'status-error';
                const icon = result.status === 'success' ? 'fa-check-circle' : 'fa-times-circle';

                html += `<div class="flex justify-between items-center py-2 border-b">
                    <span><i class="fas ${icon} mr-2 ${statusClass}"></i>${result.name}</span>
                    <span class="${statusClass}">${result.status === 'success' ? 'OK' : 'Erro'}</span>
                </div>`;
            });

            apiTests.innerHTML = html;
        }

        function limparCache() {
            alert('Função de limpeza de cache não implementada nesta versão.');
        }

        function verificarIntegridade() {
            alert('Verificação de integridade não implementada nesta versão.');
        }

        function exportarLogs() {
            alert('Exportação de logs não implementada nesta versão.');
        }

        // Testar APIs automaticamente ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            testarAPIs();
        });
    </script>
</body>
</html>
