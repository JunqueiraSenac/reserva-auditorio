<?php
session_start();

// Se não estiver autenticado, mostra a página inicial com agenda pública
if (!isset($_SESSION["usuario_id"])) {
    header("Location: home.php");
    exit();
}

// Se estiver autenticado, redireciona conforme o tipo de usuário (admin, instrutor, comum)
if ($_SESSION["tipo_usuario"] === "admin") {
    header("Location: view/painel_admin.php");

    exit();
} elseif ($_SESSION["tipo_usuario"] === "instrutor") {
    header("Location: view/painel_instrutor.php");

    exit();
} else {
    // Perfil 'comum' (ou outros): mantém navegação pública
    header("Location: home.php");
    exit();
}

?>
