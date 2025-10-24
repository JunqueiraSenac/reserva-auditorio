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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard">
        <header class="dashboard-header">
            <div class="header-logo">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo">
                <h1>SENAC - Painel do Administrador</h1>
            </div>
            <div class="user-info">
                <span>Bem-vindo, <?php echo htmlspecialchars(
                    $_SESSION["usuario_nome"],
                ); ?>!</span>
                <form action="../controller/LoginController.php" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-secondary">Sair</button>
                </form>
            </div>
        </header>

        <main class="dashboard-content">
            <!-- Added statistics cards -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">Total</div>
                    <div class="stat-info">
                        <h3>Total de Reservas</h3>
                        <p class="stat-number"><?php echo $estatisticas[
                            "total"
                        ]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-pending">
                    <div class="stat-icon">Pendentes</div>
                    <div class="stat-info">
                        <h3>Pendentes</h3>
                        <p class="stat-number"><?php echo $estatisticas[
                            "pendentes"
                        ]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-approved">
                    <div class="stat-icon">Aprovadas</div>
                    <div class="stat-info">
                        <h3>Aprovadas</h3>
                        <p class="stat-number"><?php echo $estatisticas[
                            "aprovadas"
                        ]; ?></p>
                    </div>
                </div>

                <div class="stat-card stat-rejected">
                    <div class="stat-icon">Rejeitadas</div>
                    <div class="stat-info">
                        <h3>Rejeitadas</h3>
                        <p class="stat-number"><?php echo $estatisticas[
                            "rejeitadas"
                        ]; ?></p>
                    </div>
                </div>
            </section>

            <!-- Added charts section -->
            <section class="charts-section">
                <div class="card chart-card">
                    <h2>Reservas por Mês</h2>
                    <canvas id="reservasChart"></canvas>
                </div>

                <div class="card chart-card">
                    <h2>Status das Reservas</h2>
                    <canvas id="statusChart"></canvas>
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
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma reserva encontrada</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservas as $reserva): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(
                                            $reserva["usuario_nome"],
                                        ); ?></td>
                                        <!-- Added telefone column -->
                                        <td><?php echo htmlspecialchars(
                                            $reserva["usuario_telefone"] ??
                                                "N/A",
                                        ); ?></td>
                                        <td><?php echo date(
                                            "d/m/Y",
                                            strtotime($reserva["data"]),
                                        ); ?></td>
                                        <td><?php echo substr(
                                            $reserva["hora_inicio"],
                                            0,
                                            5,
                                        ) .
                                            " - " .
                                            substr(
                                                $reserva["hora_fim"],
                                                0,
                                                5,
                                            ); ?></td>
                                        <td><?php echo htmlspecialchars(
                                            $reserva["descricao"],
                                        ); ?></td>
                                        <td>
                                            <span class="status status-<?php echo $reserva[
                                                "status"
                                            ]; ?>">
                                                <?php echo ucfirst(
                                                    $reserva["status"],
                                                ); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (
                                                $reserva["status"] ===
                                                "pendente"
                                            ): ?>
                                                <form action="../controller/ReservaController.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="aprovar">
                                                    <input type="hidden" name="id" value="<?php echo $reserva[
                                                        "id"
                                                    ]; ?>">
                                                    <button type="submit" class="btn btn-small btn-success">Aprovar</button>
                                                </form>
                                                <form action="../controller/ReservaController.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="rejeitar">
                                                    <input type="hidden" name="id" value="<?php echo $reserva[
                                                        "id"
                                                    ]; ?>">
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

    <script>
        console.log('[v0] Initializing charts...');

        // Reservas por Mês Chart
        const reservasPorMes = <?php echo json_encode($reservasPorMes); ?>;
        console.log('[v0] Reservas por mes data:', reservasPorMes);

        const meses = reservasPorMes.map(item => {
            const [ano, mes] = item.mes.split('-');
            const mesesNomes = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            return mesesNomes[parseInt(mes) - 1] + '/' + ano.slice(2);
        });
        const totais = reservasPorMes.map(item => item.total);
        const aprovadas = reservasPorMes.map(item => item.aprovadas);

        const chartElement = document.getElementById('reservasChart');
        console.log('[v0] Chart element found:', chartElement);

        if (chartElement) {
            new Chart(chartElement, {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Total de Reservas',
                        data: totais,
                        borderColor: '#004A8D',
                        backgroundColor: 'rgba(0, 74, 141, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Aprovadas',
                        data: aprovadas,
                        borderColor: '#F26C21',
                        backgroundColor: 'rgba(242, 108, 33, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#e0e0e0' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#e0e0e0' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        x: {
                            ticks: { color: '#e0e0e0' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    }
                }
            });
            console.log('[v0] Line chart created successfully');
        }

        // Status Chart
        const stats = <?php echo json_encode($estatisticas); ?>;
        console.log('[v0] Statistics data:', stats);

        const statusChartElement = document.getElementById('statusChart');
        console.log('[v0] Status chart element found:', statusChartElement);

        if (statusChartElement) {
            new Chart(statusChartElement, {
                type: 'doughnut',
                data: {
                    labels: ['Pendentes', 'Aprovadas', 'Rejeitadas', 'Canceladas'],
                    datasets: [{
                        data: [stats.pendentes, stats.aprovadas, stats.rejeitadas, stats.canceladas],
                        backgroundColor: ['#FFA726', '#66BB6A', '#EF5350', '#78909C'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#e0e0e0', padding: 15 }
                        }
                    }
                }
            });
            console.log('[v0] Doughnut chart created successfully');
        }

        console.log('[v0] Charts initialization complete');
    </script>
</body>
</html>
