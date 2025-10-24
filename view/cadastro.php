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
    <title>SENAC - Cadastro</title>
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
            <h3>Cadastro</h3>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-error">Erro ao cadastrar. Tente novamente.</div>
            <?php endif; ?>
            
            <form action="../controller/UsuarioController.php" method="POST" class="login-form">
                <input type="hidden" name="action" value="cadastrar">
                
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <!-- Added telefone field for WhatsApp notifications -->
                <div class="form-group">
                    <label for="telefone">Telefone (WhatsApp)</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(41) 99999-9999" required>
                    <small>Formato: (DDD) 99999-9999</small>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required minlength="6">
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="solicitar_instrutor" name="solicitar_instrutor" value="1" checked>
                        <span class="checkmark"></span>
                        Solicitar acesso ao sistema de reservas
                    </label>
                    <small style="color: #94a3b8; margin-top: 5px; display: block;">
                        Você poderá fazer login normalmente, mas precisará de aprovação para fazer reservas.
                    </small>
                </div>
                
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
            
            <p class="text-center">
                Já tem uma conta? <a href="login.php">Faça login</a>
            </p>
        </div>
    </div>
    
    <script src="../public/js/gsap.min.js"></script>
    <script src="../public/js/animations.js"></script>
    <!-- Added phone mask script -->
    <script>
        // Phone mask
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html>
