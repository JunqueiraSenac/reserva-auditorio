<?php
session_start();

// Se não estiver autenticado, mostra a página inicial com agenda pública
if (!isset($_SESSION['usuario_id'])) {
    header('Location: home.php');
    exit;
}

// Se estiver autenticado, redireciona para o painel apropriado baseado no tipo de usuário
if ($_SESSION['tipo_usuario'] === 'admin') {
    header('Location: view/painel_admin.php');
} else {
    header('Location: view/painel_instrutor.php');
}
exit;
?>
