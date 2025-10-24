<?php
session_start();
require_once 'controller/ReservaController.php';

$reservaController = new ReservaController();
$reservasPublicas = $reservaController->listarReservasPublicas();

// Organizar reservas por data
$reservasPorData = [];
foreach ($reservasPublicas as $reserva) {
    $data = $reserva['data'];
    if (!isset($reservasPorData[$data])) {
        $reservasPorData[$data] = [];
    }
    $reservasPorData[$data][] = $reserva;
}

// Função para obter o nome do mês em português
function getNomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes];
}

// Função para obter o nome do dia da semana
function getNomeDia($dia) {
    $dias = [
        0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua',
        4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'
    ];
    return $dias[$dia];
}

// Obter mês atual e próximo mês
$mesAtual = date('n');
$anoAtual = date('Y');
$proximoMes = $mesAtual + 1;
$anoProximoMes = $anoAtual;
if ($proximoMes > 12) {
    $proximoMes = 1;
    $anoProximoMes++;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Calendário de Eventos</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="icon" type="image/png" href="public/images/logo-senac.png">
    <style>
        .calendar-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #004A8D 0%, #F26C21 100%);
            padding: 20px;
        }
        
        .calendar-header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .calendar-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            color: #004A8D;
        }
        
        .calendar-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            color: #F26C21;
            font-weight: bold;
        }
        
        .calendar-section {
            background: #1a1f2e;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: #e2e8f0;
            margin-bottom: 30px;
        }
        
        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .calendar-nav h2 {
            color: #e2e8f0;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            background: #004A8D;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover {
            background: #003366;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #2d3748;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-day-header {
            background: #004A8D;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .calendar-day {
            background: #1e2433;
            min-height: 120px;
            padding: 10px;
            border: 1px solid #2d3748;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .calendar-day:hover {
            background: #252b3b;
        }
        
        .calendar-day.today {
            background: #004A8D;
            color: white;
        }
        
        .calendar-day.other-month {
            background: #0f1419;
            color: #64748b;
        }
        
        .day-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .event-item {
            background: #F26C21;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-bottom: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .event-item:hover {
            background: #d45a1a;
            transform: scale(1.05);
        }
        
        .event-item.aprovada {
            background: #10b981;
        }
        
        .event-item.pendente {
            background: #f59e0b;
        }
        
        .back-button {
            background: #F26C21;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: #d45a1a;
            transform: translateY(-2px);
        }
        
        .event-details {
            background: #1e2433;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #004A8D;
        }
        
        .event-details h3 {
            color: #e2e8f0;
            margin-bottom: 15px;
        }
        
        .event-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .event-info-item {
            background: #252b3b;
            padding: 15px;
            border-radius: 8px;
        }
        
        .event-info-item strong {
            color: #F26C21;
            display: block;
            margin-bottom: 5px;
        }
        
        .event-info-item span {
            color: #e2e8f0;
        }
        
        @media (max-width: 768px) {
            .calendar-nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .calendar-day {
                min-height: 80px;
                padding: 5px;
            }
            
            .event-item {
                font-size: 0.7rem;
                padding: 1px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="logo-container" style="margin-bottom: 20px;">
                <img src="public/images/logo-senac.png" alt="SENAC Logo" style="max-height: 80px;">
            </div>
            <h1>Calendário de Eventos</h1>
            <p>SENAC - Serviço Nacional de Aprendizagem Comercial</p>
        </div>
        
        <a href="home.php" class="back-button">Voltar para Agenda</a>
        
        <div class="calendar-section">
            <div class="calendar-nav">
                <button class="nav-btn" onclick="changeMonth(-1)">Mês Anterior</button>
                <h2 id="current-month"><?php echo getNomeMes($mesAtual) . ' ' . $anoAtual; ?></h2>
                <button class="nav-btn" onclick="changeMonth(1)">Próximo Mês</button>
            </div>
            
            <div class="calendar-grid">
                <!-- Cabeçalhos dos dias -->
                <div class="calendar-day-header">Dom</div>
                <div class="calendar-day-header">Seg</div>
                <div class="calendar-day-header">Ter</div>
                <div class="calendar-day-header">Qua</div>
                <div class="calendar-day-header">Qui</div>
                <div class="calendar-day-header">Sex</div>
                <div class="calendar-day-header">Sáb</div>
                
                <?php
                // Obter primeiro dia do mês e quantos dias tem o mês
                $primeiroDia = mktime(0, 0, 0, $mesAtual, 1, $anoAtual);
                $ultimoDia = mktime(0, 0, 0, $mesAtual + 1, 0, $anoAtual);
                $diasNoMes = date('t', $primeiroDia);
                $diaSemana = date('w', $primeiroDia);
                
                // Dias do mês anterior
                $mesAnterior = $mesAtual - 1;
                $anoAnterior = $anoAtual;
                if ($mesAnterior < 1) {
                    $mesAnterior = 12;
                    $anoAnterior--;
                }
                $diasMesAnterior = date('t', mktime(0, 0, 0, $mesAnterior, 1, $anoAnterior));
                
                // Preencher dias do mês anterior
                for ($i = $diaSemana - 1; $i >= 0; $i--) {
                    $dia = $diasMesAnterior - $i;
                    echo '<div class="calendar-day other-month">';
                    echo '<div class="day-number">' . $dia . '</div>';
                    echo '</div>';
                }
                
                // Dias do mês atual
                for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                    $dataCompleta = sprintf('%04d-%02d-%02d', $anoAtual, $mesAtual, $dia);
                    $hoje = date('Y-m-d');
                    $classe = ($dataCompleta === $hoje) ? 'today' : '';
                    
                    echo '<div class="calendar-day ' . $classe . '">';
                    echo '<div class="day-number">' . $dia . '</div>';
                    
                    // Mostrar eventos do dia
                    if (isset($reservasPorData[$dataCompleta])) {
                        foreach ($reservasPorData[$dataCompleta] as $reserva) {
                            $horario = substr($reserva['hora_inicio'], 0, 5);
                            $status = $reserva['status'];
                            echo '<div class="event-item ' . $status . '" onclick="showEventDetails(' . $reserva['id'] . ')">';
                            echo $horario . ' - ' . htmlspecialchars(substr($reserva['descricao'], 0, 20)) . '...';
                            echo '</div>';
                        }
                    }
                    
                    echo '</div>';
                }
                
                // Preencher dias do próximo mês
                $diasRestantes = 42 - ($diaSemana + $diasNoMes);
                for ($dia = 1; $dia <= $diasRestantes; $dia++) {
                    echo '<div class="calendar-day other-month">';
                    echo '<div class="day-number">' . $dia . '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <div id="event-details" class="event-details" style="display: none;">
            <h3>Detalhes do Evento</h3>
            <div id="event-info" class="event-info"></div>
        </div>
    </div>
    
    <script>
        let currentMonth = <?php echo $mesAtual; ?>;
        let currentYear = <?php echo $anoAtual; ?>;
        
        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            } else if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            
            // Recarregar a página com o novo mês
            window.location.href = `calendario.php?mes=${currentMonth}&ano=${currentYear}`;
        }
        
        function showEventDetails(eventId) {
            // Aqui você pode implementar uma modal ou expandir os detalhes
            console.log('Mostrar detalhes do evento:', eventId);
        }
        
        // Adicionar animações suaves
        document.addEventListener('DOMContentLoaded', function() {
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach((day, index) => {
                day.style.animationDelay = `${index * 0.02}s`;
                day.style.animation = 'fadeInUp 0.5s ease forwards';
            });
        });
        
        // CSS para animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
