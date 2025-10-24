<?php
session_start();
require_once 'controller/ReservaController.php';

$reservaController = new ReservaController();
$reservasPublicas = $reservaController->listarReservasPublicas();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Sistema de Reserva de Auditório</title>
    <link rel="icon" type="image/png" href="public/images/logo-senac.png">
    <link rel="stylesheet" href="public/css/modern-style.css">
</head>
<body>
    <div class="home-container">
        <div class="home-header">
            <div class="logo-container">
                <img src="public/images/logo-senac.png" alt="SENAC Logo" class="logo-img">
            </div>
            <h1>Sistema de Reserva de Auditório</h1>
            <p>SENAC - Serviço Nacional de Aprendizagem Comercial</p>
        </div>
        
        <div class="login-section">
            <h2>Área de Acesso</h2>
            <p>Faça login para gerenciar suas reservas ou cadastre-se como instrutor</p>
            <div class="login-buttons">
                <a href="view/login.php" class="btn-login">Fazer Login</a>
                <a href="view/cadastro.php" class="btn-cadastro">Cadastrar-se</a>
                <a href="calendario.php" class="btn-calendario">Ver Calendário</a>
            </div>
        </div>
        
        <div class="agenda-section">
            <h2>Agenda de Eventos</h2>
            
            <?php if (empty($reservasPublicas)): ?>
                <div class="sem-eventos">
                    <p>Nenhum evento agendado no momento.</p>
                </div>
            <?php else: ?>
                <div class="eventos-grid">
                    <?php foreach ($reservasPublicas as $reserva): ?>
                        <div class="evento-card">
                            <div class="evento-data">
                                <?php echo date('d/m/Y', strtotime($reserva['data'])); ?>
                            </div>
                            <div class="evento-horario">
                                <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fim'], 0, 5); ?>
                            </div>
                            <div class="evento-descricao">
                                <?php echo htmlspecialchars($reserva['descricao']); ?>
                            </div>
                            <div class="evento-status status-<?php echo $reserva['status']; ?>">
                                <?php echo ucfirst($reserva['status']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="public/js/gsap.min.js"></script>
    <script src="public/js/animations.js"></script>
</body>
</html>
