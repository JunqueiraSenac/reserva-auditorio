<?php
// Teste simples de conexão com banco de dados
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão - Sistema SENAC</h1>";
echo "<hr>";

// Teste 1: Verificar se o arquivo de conexão existe
echo "<h2>1. Verificando arquivo de conexão...</h2>";
$conexaoFile = 'model/Conexao.php';
if (file_exists($conexaoFile)) {
    echo "✅ Arquivo $conexaoFile encontrado<br>";
} else {
    echo "❌ Arquivo $conexaoFile NÃO encontrado<br>";
    echo "Caminho atual: " . __DIR__ . "<br>";
    die("Não é possível continuar sem o arquivo de conexão.");
}

// Teste 2: Incluir o arquivo de conexão
echo "<h2>2. Carregando classe de conexão...</h2>";
try {
    require_once $conexaoFile;
    echo "✅ Classe Conexao carregada com sucesso<br>";
} catch (Exception $e) {
    echo "❌ Erro ao carregar classe: " . $e->getMessage() . "<br>";
    die("Não é possível continuar.");
}

// Teste 3: Verificar configurações
echo "<h2>3. Verificando configurações...</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
echo "Host atual: $host<br>";

if (strpos($host, 'infinityfree') !== false) {
    echo "🌐 Ambiente: PRODUÇÃO (InfinityFree)<br>";
} else {
    echo "💻 Ambiente: DESENVOLVIMENTO (Local)<br>";
}

// Teste 4: Tentar criar instância da conexão
echo "<h2>4. Testando conexão com banco...</h2>";
try {
    $conexao = Conexao::getInstance();
    echo "✅ Instância da conexão criada<br>";

    $conn = $conexao->getConnection();
    echo "✅ Objeto PDO obtido<br>";

    // Teste 5: Executar query simples
    echo "<h2>5. Testando query simples...</h2>";
    $stmt = $conn->query("SELECT 1 as teste");
    $resultado = $stmt->fetch();
    if ($resultado['teste'] == 1) {
        echo "✅ Query executada com sucesso<br>";
    } else {
        echo "❌ Resultado da query inesperado<br>";
    }

    // Teste 6: Verificar informações do banco
    echo "<h2>6. Informações do banco...</h2>";
    $info = $conexao->getConnectionInfo();
    echo "Host: " . htmlspecialchars($info['host']) . "<br>";
    echo "Database: " . htmlspecialchars($info['database']) . "<br>";
    echo "Ambiente: " . htmlspecialchars($info['environment']) . "<br>";

    // Teste 7: Verificar tabelas
    echo "<h2>7. Verificando tabelas...</h2>";
    try {
        $stmt = $conn->query("SHOW TABLES");
        $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($tabelas)) {
            echo "⚠️ Nenhuma tabela encontrada no banco<br>";
        } else {
            echo "✅ Tabelas encontradas:<br>";
            foreach ($tabelas as $tabela) {
                echo "&nbsp;&nbsp;- " . htmlspecialchars($tabela) . "<br>";

                // Contar registros em cada tabela
                try {
                    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM `$tabela`");
                    $countStmt->execute();
                    $count = $countStmt->fetch();
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;→ {$count['total']} registros<br>";
                } catch (Exception $e) {
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;→ Erro ao contar: " . $e->getMessage() . "<br>";
                }
            }
        }
    } catch (PDOException $e) {
        echo "❌ Erro ao listar tabelas: " . $e->getMessage() . "<br>";
    }

} catch (Exception $e) {
    echo "❌ ERRO DE CONEXÃO: " . $e->getMessage() . "<br>";
    echo "<br><strong>Detalhes do erro:</strong><br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";

    // Mostrar configurações para debug (sem senha)
    echo "<br><strong>Configurações atuais:</strong><br>";
    if (class_exists('Conexao')) {
        // Tentar obter informações sem criar conexão
        $reflection = new ReflectionClass('Conexao');
        $configs = $reflection->getProperty('configs');
        $configs->setAccessible(true);

        echo "Tentativa de debug das configurações...<br>";
    }
}

echo "<hr>";
echo "<h2>Informações do PHP:</h2>";
echo "Versão PHP: " . PHP_VERSION . "<br>";
echo "PDO MySQL disponível: " . (extension_loaded('pdo_mysql') ? '✅ Sim' : '❌ Não') . "<br>";
echo "Timezone: " . date_default_timezone_get() . "<br>";
echo "Data/Hora atual: " . date('d/m/Y H:i:s') . "<br>";

echo "<hr>";
echo "<p><a href='diagnostic.php'>← Voltar para Diagnóstico Completo</a></p>";
?>
