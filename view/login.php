<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Login</title>
    <link rel="icon" type="image/png" href="../public/images/logo-senac.png">
    <link rel="stylesheet" href="../public/css/modern-style.css">
</head>
<body>
    <!-- Added animated background -->
    <div class="auth-background">
        <div class="auth-shape shape-1"></div>
        <div class="auth-shape shape-2"></div>
        <div class="auth-shape shape-3"></div>
    </div>
    
    <div class="login-container">
        <div class="login-box">
            <div class="logo-container">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo">
            </div>
            
            <h1>SENAC</h1>
            <h2>Sistema de Reserva de Auditório</h2>
            <h3>Login</h3>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-error">Email ou senha incorretos!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                <div class="alert alert-success">Cadastro realizado com sucesso! Faça login.</div>
            <?php endif; ?>
            
            <form action="../controller/LoginController.php" method="POST" class="login-form">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            
            <p class="text-center">
                Não tem uma conta? <a href="cadastro.php">Cadastre-se</a>
            </p>
        </div>
    </div>
    
    <script src="../public/js/gsap.min.js"></script>
    <script src="../public/js/animations.js"></script>
</body>
</html>
