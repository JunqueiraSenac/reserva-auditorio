<?php
session_start();
require_once "controller/ReservaController.php";

// Define page title for header
$page_title = "Calendário de Eventos";

// Buscar reservas
$reservasPublicas = [];
try {
    $reservaController = new ReservaController();
    $reservasPublicas = $reservaController->listarReservasPublicas();
} catch (Exception $e) {
    $reservasPublicas = [];
}

// Estatísticas
$totalEventos = count($reservasPublicas);
$eventosAprovados = count(
    array_filter($reservasPublicas, fn($r) => $r["status"] === "aprovada"),
);
$eventosPendentes = count(
    array_filter($reservasPublicas, fn($r) => $r["status"] === "pendente"),
);
$eventosRejeitados = count(
    array_filter($reservasPublicas, fn($r) => $r["status"] === "rejeitada"),
);

// Organizar eventos por data
$eventosPorData = [];
foreach ($reservasPublicas as $reserva) {
    $data = $reserva["data"];
    if (!isset($eventosPorData[$data])) {
        $eventosPorData[$data] = [];
    }
    $eventosPorData[$data][] = $reserva;
}

// Extra styles for calendar
$extra_head = '
<style>
    .calendar-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .calendar-day {
        aspect-ratio: 1;
        min-height: 100px;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .calendar-day:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 74, 141, 0.2);
        z-index: 10;
    }

    .calendar-day.other-month {
        opacity: 0.3;
    }

    .calendar-day.today {
        border: 2px solid #F26C21;
        box-shadow: 0 0 0 3px rgba(242, 108, 33, 0.1);
    }

    .day-number {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 4px;
    }

    .event-dots {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 8px;
    }

    .event-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .event-dot.aprovada {
        background-color: #10b981;
    }

    .event-dot.pendente {
        background-color: #f59e0b;
    }

    .event-dot.rejeitada {
        background-color: #ef4444;
    }

    .event-count {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #004A8D;
        color: white;
        font-size: 0.75rem;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 20px;
        text-align: center;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        padding: 32px;
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.3s ease;
    }

    .dark .modal-content {
        background: #1f2937;
        color: white;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        font-size: 24px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        background: #dc2626;
        transform: rotate(90deg);
    }

    .event-item {
        background: #f3f4f6;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 16px;
        border-left: 4px solid #004A8D;
    }

    .dark .event-item {
        background: #374151;
        border-left-color: #F26C21;
    }

    .event-item h3 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #004A8D;
    }

    .dark .event-item h3 {
        color: #F26C21;
    }

    .event-item p {
        font-size: 0.9rem;
        margin: 4px 0;
        color: #6b7280;
    }

    .dark .event-item p {
        color: #9ca3af;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 8px;
    }

    .status-badge.aprovada {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.pendente {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.rejeitada {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .calendar-day {
            min-height: 80px;
            padding: 4px;
        }

        .day-number {
            font-size: 0.9rem;
        }

        .event-count {
            font-size: 0.65rem;
            padding: 2px 4px;
        }
    }
</style>
';

include "includes/header.php";
?>

<!-- Main Content -->
<main class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-12">
    <div class="calendar-container px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <div class="mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-calendar-alt text-senac-orange mr-3"></i>Calendário de Eventos
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Visualize todos os eventos e reservas do auditório</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-slide-up">
            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Total de Eventos</h3>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo $totalEventos; ?></div>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                        <i class="fas fa-calendar text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Aprovados</h3>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400"><?php echo $eventosAprovados; ?></div>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Pendentes</h3>
                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?php echo $eventosPendentes; ?></div>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg">
                        <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">Rejeitados</h3>
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400"><?php echo $eventosRejeitados; ?></div>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900 p-3 rounded-lg">
                        <i class="fas fa-times-circle text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-info-circle mr-2 text-senac-blue dark:text-senac-orange"></i>Legenda
            </h3>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center space-x-2">
                    <span class="event-dot aprovada"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Aprovado</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="event-dot pendente"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Pendente</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="event-dot rejeitada"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Rejeitado</span>
                </div>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex items-center justify-between">
                <button onclick="previousMonth()" class="px-4 py-2 bg-senac-blue dark:bg-senac-orange text-white rounded-lg hover:opacity-80 transition-all">
                    <i class="fas fa-chevron-left mr-2"></i>Anterior
                </button>
                <h2 id="currentMonth" class="text-2xl font-bold text-gray-900 dark:text-white"></h2>
                <button onclick="nextMonth()" class="px-4 py-2 bg-senac-blue dark:bg-senac-orange text-white rounded-lg hover:opacity-80 transition-all">
                    Próximo<i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <!-- Weekday Headers -->
            <div class="calendar-grid mb-2">
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">DOM</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">SEG</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">TER</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">QUA</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">QUI</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">SEX</div>
                <div class="text-center font-bold text-gray-700 dark:text-gray-300 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">SÁB</div>
            </div>

            <!-- Calendar Days -->
            <div id="calendarDays" class="calendar-grid"></div>
        </div>
    </div>
</main>

<!-- Event Modal -->
<div id="eventModal" class="modal" onclick="closeModalOnBackdrop(event)">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">×</button>
        <div id="modalBody"></div>
    </div>
</div>

<script>
    const eventos = <?php echo json_encode($eventosPorData); ?>;
    let currentDate = new Date();

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Atualizar título
        const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                           'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

        // Primeiro dia do mês e último dia do mês
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay();
        const totalDays = lastDay.getDate();

        // Dias do mês anterior
        const prevLastDay = new Date(year, month, 0).getDate();

        const daysContainer = document.getElementById('calendarDays');
        daysContainer.innerHTML = '';

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Dias do mês anterior
        for (let i = startDay - 1; i >= 0; i--) {
            const day = prevLastDay - i;
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day other-month bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600';
            dayDiv.innerHTML = `<div class="day-number text-gray-400 dark:text-gray-500">${day}</div>`;
            daysContainer.appendChild(dayDiv);
        }

        // Dias do mês atual
        for (let day = 1; day <= totalDays; day++) {
            const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayDate = new Date(year, month, day);

            const dayDiv = document.createElement('div');
            let className = 'calendar-day bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600';

            if (dayDate.getTime() === today.getTime()) {
                className += ' today';
            }

            dayDiv.className = className;

            let html = `<div class="day-number text-gray-900 dark:text-white">${day}</div>`;

            // Verificar se há eventos neste dia
            if (eventos[dateKey]) {
                const dayEvents = eventos[dateKey];
                html += `<div class="event-count">${dayEvents.length}</div>`;
                html += '<div class="event-dots">';

                dayEvents.slice(0, 5).forEach(evento => {
                    html += `<span class="event-dot ${evento.status}"></span>`;
                });

                if (dayEvents.length > 5) {
                    html += `<span class="text-xs text-gray-500">+${dayEvents.length - 5}</span>`;
                }

                html += '</div>';
                dayDiv.onclick = () => showEvents(dateKey, dayEvents);
                dayDiv.style.cursor = 'pointer';
            }

            dayDiv.innerHTML = html;
            daysContainer.appendChild(dayDiv);
        }

        // Dias do próximo mês
        const totalCells = daysContainer.children.length;
        const remainingCells = 42 - totalCells; // 6 semanas * 7 dias
        for (let i = 1; i <= remainingCells; i++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day other-month bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600';
            dayDiv.innerHTML = `<div class="day-number text-gray-400 dark:text-gray-500">${i}</div>`;
            daysContainer.appendChild(dayDiv);
        }
    }

    function showEvents(dateKey, events) {
        const [year, month, day] = dateKey.split('-');
        const formattedDate = `${day}/${month}/${year}`;

        let html = `<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Eventos do dia ${formattedDate}</h2>`;

        events.forEach(evento => {
            const statusClass = evento.status;
            const statusText = {
                'aprovada': 'Aprovado',
                'pendente': 'Pendente',
                'rejeitada': 'Rejeitado'
            }[evento.status] || evento.status;

            html += `
                <div class="event-item">
                    <h3>${evento.descricao}</h3>
                    <p><i class="fas fa-clock mr-2"></i><strong>Horário:</strong> ${evento.hora_inicio.substring(0,5)} - ${evento.hora_fim.substring(0,5)}</p>
                    <p><i class="fas fa-user mr-2"></i><strong>Responsável:</strong> ${evento.usuario_nome || 'Não informado'}</p>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
            `;
        });

        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('eventModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('eventModal').classList.remove('show');
    }

    function closeModalOnBackdrop(e) {
        if (e.target === document.getElementById('eventModal')) {
            closeModal();
        }
    }

    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    // Renderizar calendário inicial
    renderCalendar();
</script>

<?php include "includes/footer.php"; ?>
