<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../controller/LoginController.php";
require_once "../controller/ReservaController.php";

$loginController = new LoginController();
$loginController->verificarAutenticacao();

if ($_SESSION["tipo_usuario"] !== "admin") {
    header("Location: painel_instrutor.php");
    exit();
}

$reservaController = new ReservaController();

$reservas = $reservaController->listarTodas();
$estatisticas = $reservaController->obterEstatisticas();
$reservasPorMes = $reservaController->obterReservasPorMes();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Painel do Administrador</title>
    <link rel="icon" type="image/png" href="../public/images/logo-senac.png">
    <link rel="stylesheet" href="../public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <header class="dashboard-header">
            <div class="header-logo">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo">
                <h1>SENAC - Painel do Administrador</h1>
            </div>
            <div class="user-info">
                <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION["usuario_nome"]); ?>!</span>
                <button id="theme-toggle" class="btn" style="margin-right: 10px;" title="Alternar tema escuro/claro (Alt+D)">
                    <i class="fas fa-moon"></i>
                </button>
                <form action="../controller/LoginController.php" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-exit">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </div>
        </header>

        <main class="dashboard-content">
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3>Total de Reservas</h3>
                        <p class="stat-number"><?php echo $estatisticas["total"]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-pending">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3>Pendentes</h3>
                        <p class="stat-number"><?php echo $estatisticas["pendentes"]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-approved">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3>Aprovadas</h3>
                        <p class="stat-number"><?php echo $estatisticas["aprovadas"]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-rejected">
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-info">
                        <h3>Rejeitadas</h3>
                        <p class="stat-number"><?php echo $estatisticas["rejeitadas"]; ?></p>
                    </div>
                </div>
            </section>

            <section class="charts-section">
                <div class="card chart-card">
                    <h2>Reservas por Mês</h2>
                    <div class="static-chart reservas-chart">
                        <?php if (empty($reservasPorMes)): ?>
                            <div class="no-data-message">
                                <i class="fas fa-chart-bar"></i>
                                <p>Não há dados disponíveis</p>
                            </div>
                        <?php else: ?>
                            <?php 
                            $limitedData = array_slice($reservasPorMes, -12);
                            $maxValue = 0;
                            foreach ($limitedData as $item) {
                                $maxValue = max($maxValue, $item['total']);
                            }
                            ?>
                            <div class="chart-bars">
                                <?php foreach ($limitedData as $item): ?>
                                    <?php 
                                    $mes = explode('-', $item['mes'])[1];
                                    $ano = explode('-', $item['mes'])[0];
                                    $nomesMes = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                                    $altura = $maxValue > 0 ? ($item['total'] / $maxValue) * 100 : 0;
                                    $alturaAprovadas = $maxValue > 0 ? ($item['aprovadas'] / $maxValue) * 100 : 0;
                                    ?>
                                    <div class="chart-bar-group">
                                        <div class="chart-bar-wrapper">
                                            <div class="chart-bar total" style="height: <?= $altura ?>%">
                                                <span class="chart-value"><?= $item['total'] ?></span>
                                            </div>
                                            <div class="chart-bar approved" style="height: <?= $alturaAprovadas ?>%"></div>
                                        </div>
                                        <div class="chart-label"><?= $nomesMes[intval($mes)-1] ?>/<?= substr($ano, 2) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <span class="legend-color total"></span>
                                    <span class="legend-text">Total de Reservas</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color approved"></span>
                                    <span class="legend-text">Aprovadas</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card chart-card">
                    <h2>Status das Reservas</h2>
                    <div class="static-chart status-chart">
                        <?php if ($estatisticas["pendentes"] == 0 && $estatisticas["aprovadas"] == 0 && $estatisticas["rejeitadas"] == 0 && $estatisticas["canceladas"] == 0): ?>
                            <div class="no-data-message">
                                <i class="fas fa-chart-pie"></i>
                                <p>Não há dados disponíveis</p>
                            </div>
                        <?php else: ?>
                            <?php
                            $total = $estatisticas["pendentes"] + $estatisticas["aprovadas"] + $estatisticas["rejeitadas"] + $estatisticas["canceladas"];
                            $percentPendentes = $total > 0 ? ($estatisticas["pendentes"] / $total) * 100 : 0;
                            $percentAprovadas = $total > 0 ? ($estatisticas["aprovadas"] / $total) * 100 : 0;
                            $percentRejeitadas = $total > 0 ? ($estatisticas["rejeitadas"] / $total) * 100 : 0;
                            $percentCanceladas = $total > 0 ? ($estatisticas["canceladas"] / $total) * 100 : 0;
                            ?>
                            <div class="donut-chart">
                                <div class="donut-segment" style="--percent: <?= $percentPendentes ?>; --color: #FFA726; --offset: 0;"></div>
                                <div class="donut-segment" style="--percent: <?= $percentAprovadas ?>; --color: #66BB6A; --offset: <?= $percentPendentes ?>;"></div>
                                <div class="donut-segment" style="--percent: <?= $percentRejeitadas ?>; --color: #EF5350; --offset: <?= $percentPendentes + $percentAprovadas ?>;"></div>
                                <div class="donut-segment" style="--percent: <?= $percentCanceladas ?>; --color: #78909C; --offset: <?= $percentPendentes + $percentAprovadas + $percentRejeitadas ?>;"></div>
                                <div class="donut-hole"></div>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #FFA726;"></span>
                                    <span class="legend-text">Pendentes (<?= $estatisticas["pendentes"] ?>)</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #66BB6A;"></span>
                                    <span class="legend-text">Aprovadas (<?= $estatisticas["aprovadas"] ?>)</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #EF5350;"></span>
                                    <span class="legend-text">Rejeitadas (<?= $estatisticas["rejeitadas"] ?>)</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #78909C;"></span>
                                    <span class="legend-text">Canceladas (<?= $estatisticas["canceladas"] ?>)</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="card">
                <h2>Todas as Reservas</h2>
                <div class="table-responsive">
                    <table class="reservas-table">
                        <thead>
                            <tr>
                                <th>Instrutor</th>
                                <th>Telefone</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservas)): ?>
                                <tr><td colspan="7" class="text-center">Nenhuma reserva encontrada</td></tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $reserva): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($reserva["usuario_nome"]) ?></td>
                                        <td><?= htmlspecialchars($reserva["usuario_telefone"] ?? "N/A") ?></td>
                                        <td><?= date("d/m/Y", strtotime($reserva["data"])) ?></td>
                                        <td><?= substr($reserva["hora_inicio"], 0, 5) . " - " . substr($reserva["hora_fim"], 0, 5) ?></td>
                                        <td><?= htmlspecialchars($reserva["descricao"]) ?></td>
                                        <td><span class="status status-<?= $reserva["status"] ?>"><?= ucfirst($reserva["status"]) ?></span></td>
                                        <td>
                                            <?php if ($reserva["status"] === "pendente"): ?>
                                                <form action="../controller/ReservaController.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="aprovar">
                                                    <input type="hidden" name="id" value="<?= $reserva["id"] ?>">
                                                    <button type="submit" class="btn btn-small btn-success">Aprovar</button>
                                                </form>
                                                <form action="../controller/ReservaController.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="rejeitar">
                                                    <input type="hidden" name="id" value="<?= $reserva["id"] ?>">
                                                    <button type="submit" class="btn btn-small btn-danger">Rejeitar</button>
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

    <!-- Dark Mode Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            const icon = document.querySelector('#theme-toggle i');
            if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('dark', theme === 'dark');
            const icon = document.querySelector('#theme-toggle i');
            if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            const toggle = document.getElementById('theme-toggle');
            if (toggle) toggle.addEventListener('click', toggleTheme);
            document.addEventListener('keydown', function(e) {
                if (e.altKey && e.key === 'd') { e.preventDefault(); toggleTheme(); }
            });
        });
    </script>
</body>
</html>
