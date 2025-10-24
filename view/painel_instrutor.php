<?php
session_start();
require_once '../controller/LoginController.php';
require_once '../controller/ReservaController.php';
require_once '../controller/UsuarioController.php';

$loginController = new LoginController();
$loginController->verificarAutenticacao();

if ($_SESSION['tipo_usuario'] === 'admin') {
    header('Location: painel_admin.php');
    exit;
}

$reservaController = new ReservaController();
$reservas = $reservaController->listarPorUsuario($_SESSION['usuario_id']);

// Verifica se o usuário está aprovado para fazer reservas
$usuarioController = new UsuarioController();
$usuario = $usuarioController->buscarPorId($_SESSION['usuario_id']);
$usuario_aprovado = $usuario && $usuario['status_aprovacao'] === 'aprovado';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Painel do Instrutor</title>
    <link rel="icon" type="image/png" href="../public/images/logo-senac.png">
</head>
<body>
    <div class="dashboard">
        <header class="dashboard-header">
            <!-- Added logo and restructured header -->
            <div class="header-logo">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo">
                <h1>SENAC - Painel do Instrutor</h1>
            </div>
            <div class="user-info">
                <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span>
                <form action="../controller/LoginController.php" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-secondary">Sair</button>
                </form>
            </div>
        </header>
        
        <main class="dashboard-content">
            <?php if (!$usuario_aprovado): ?>
                <div class="alert alert-warning">
                    <strong>Atenção:</strong> Sua solicitação de acesso ao sistema de reservas ainda está pendente de aprovação. 
                    Você pode fazer login normalmente, mas não poderá criar reservas até ser aprovado pelo administrador.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success">Reserva criada com sucesso!</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-error">Erro ao criar reserva. Horário pode estar ocupado ou você não tem permissão.</div>
            <?php endif; ?>
            
            <section class="card">
                <h2>Nova Reserva</h2>
                <?php if ($usuario_aprovado): ?>
                    <form action="../controller/ReservaController.php" method="POST" class="reserva-form">
                        <input type="hidden" name="action" value="criar">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data">Data</label>
                                <input type="date" id="data" name="data" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="hora_inicio">Hora Início</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="hora_fim">Hora Fim</label>
                                <input type="time" id="hora_fim" name="hora_fim" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea id="descricao" name="descricao" rows="3" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Criar Reserva</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>Você não pode criar reservas no momento. Aguarde a aprovação do administrador.</p>
                    </div>
                <?php endif; ?>
            </section>
            
            <section class="card">
                <h2>Minhas Reservas</h2>
                <div class="table-responsive">
                    <table class="reservas-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservas)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Nenhuma reserva encontrada</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $reserva): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($reserva['data'])); ?></td>
                                        <td><?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fim'], 0, 5); ?></td>
                                        <td><?php echo htmlspecialchars($reserva['descricao']); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $reserva['status']; ?>">
                                                <?php echo ucfirst($reserva['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($reserva['status'] === 'pendente'): ?>
                                                <form action="../controller/ReservaController.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="cancelar">
                                                    <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                                                    <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Deseja cancelar esta reserva?')">Cancelar</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    
    <script src="../public/js/gsap.min.js"></script>
    <script src="../public/js/animations.js"></script>
</body>
</html>
