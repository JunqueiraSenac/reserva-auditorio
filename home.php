<?php
// Inicializar sistema
session_start();

// Verificar conexão e buscar reservas
$setupComplete = file_exists("setup_complete.txt");
$reservasPublicas = [];
$conexaoOk = false;
$erroConexao = null;

try {
    require_once "controller/ReservaController.php";
    $reservaController = new ReservaController();
    $reservasPublicas = $reservaController->listarReservasPublicas();
    $conexaoOk = true;
} catch (Exception $e) {
    $erroConexao = $e->getMessage();
}

// Calcular estatísticas
$totalEventos = count($reservasPublicas);
$eventosAprovados = count(
    array_filter($reservasPublicas, fn($r) => $r["status"] === "aprovada"),
);
$eventosPendentes = count(
    array_filter($reservasPublicas, fn($r) => $r["status"] === "pendente"),
);

// Define page title for header
$page_title = "Sistema de Reserva de Auditório";

// Include header
include "includes/header.php";
?>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-32 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 animate-gradient opacity-10 dark:opacity-5"></div>

        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-senac-blue/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-senac-orange/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center animate-slide-up">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-senac-blue/10 dark:bg-senac-orange/10 text-senac-blue dark:text-senac-orange text-sm font-semibold mb-8">
                    <i class="fas fa-bolt mr-2"></i>
                    Sistema Profissional de Gestão
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                    Gerencie as reservas do<br>
                    <span class="text-transparent bg-clip-text gradient-senac">Auditório SENAC</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 mb-12 max-w-3xl mx-auto leading-relaxed">
                    Sistema completo para agendamento e gerenciamento de eventos.
                    Interface moderna, notificações automáticas via WhatsApp e controle total de disponibilidade.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="view/login.php" class="btn-primary group w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white gradient-senac rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-3 group-hover:rotate-12 transition-transform"></i>
                        Acessar Sistema
                        <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="calendario.php" class="group w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-senac-blue dark:text-white bg-white dark:bg-gray-800 border-2 border-senac-blue dark:border-senac-orange rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-calendar-alt mr-3 group-hover:scale-110 transition-transform"></i>
                        Ver Calendário
                    </a>

                    <a href="view/cadastro.php" class="group w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white gradient-senac-warm rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-user-plus mr-3 group-hover:scale-110 transition-transform"></i>
                        Solicitar Acesso
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center items-center gap-8 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>100% Gratuito</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-500"></i>
                        <span>Seguro & Confiável</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-mobile-alt text-purple-500"></i>
                        <span>Totalmente Responsivo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        <span>Rápido & Eficiente</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16 animate-slide-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Funcionalidades Poderosas
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Tudo que você precisa para gerenciar reservas de forma profissional
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="card-hover bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-2xl bg-senac-blue text-white text-2xl shadow-lg">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Agendamento Fácil</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Interface intuitiva para criar, editar e gerenciar reservas rapidamente
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="card-hover bg-gradient-to-br from-orange-50 to-orange-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-2xl bg-senac-orange text-white text-2xl shadow-lg">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">100% Responsivo</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Acesse de qualquer dispositivo - desktop, tablet ou smartphone
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="card-hover bg-gradient-to-br from-green-50 to-green-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-2xl bg-green-600 text-white text-2xl shadow-lg">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Notificações WhatsApp</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Receba confirmações automáticas via WhatsApp quando aprovado
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="card-hover bg-gradient-to-br from-purple-50 to-purple-100 dark:from-gray-700 dark:to-gray-600 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-6 flex items-center justify-center rounded-2xl bg-purple-600 text-white text-2xl shadow-lg">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Sistema Seguro</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Controle de acesso, validação de dados e proteção contra conflitos
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-20 gradient-senac text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="animate-scale-in">
                    <div class="text-5xl font-black mb-2" id="counter-eventos"><?php echo $totalEventos; ?></div>
                    <div class="text-blue-200">Eventos Programados</div>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.1s;">
                    <div class="text-5xl font-black mb-2"><?php echo $eventosAprovados; ?></div>
                    <div class="text-blue-200">Eventos Confirmados</div>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.2s;">
                    <div class="text-5xl font-black mb-2">100%</div>
                    <div class="text-blue-200">Disponibilidade</div>
                </div>
                <div class="animate-scale-in" style="animation-delay: 0.3s;">
                    <div class="text-5xl font-black mb-2">24/7</div>
                    <div class="text-blue-200">Suporte Online</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Próximos Eventos
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Confira a programação do auditório
                    </p>
                </div>
                <a href="calendario.php" class="mt-6 md:mt-0 inline-flex items-center px-6 py-3 text-base font-semibold text-senac-blue dark:text-white bg-white dark:bg-gray-800 border-2 border-senac-blue dark:border-senac-orange rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Ver Calendário Completo
                </a>
            </div>

            <?php if (!$conexaoOk): ?>
                <!-- Connection Error -->
                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-2xl p-12 text-center">
                    <div class="text-6xl text-red-400 mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-red-900 dark:text-red-400 mb-2">Erro de Conexão</h3>
                    <p class="text-red-700 dark:text-red-500 mb-6">
                        Não foi possível conectar ao banco de dados.
                        <?php if (
                            !$setupComplete
                        ): ?>Execute o setup primeiro.<?php endif; ?>
                    </p>
                    <?php if (!$setupComplete): ?>
                        <a href="setup.php" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-cog mr-2"></i>
                            Executar Setup
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif (empty($reservasPublicas)): ?>
                <!-- No Events -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center shadow-lg">
                    <div class="text-6xl text-gray-300 dark:text-gray-600 mb-4">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Nenhum evento agendado</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        Não há eventos programados para os próximos dias
                    </p>

                                        <?php if (
                                            !isset($_SESSION["usuario_id"])
                                        ): ?>
                                        <a href="view/login.php" class="inline-flex items-center px-6 py-3 gradient-senac text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">

                                            <i class="fas fa-plus mr-2"></i>

                                            Criar Nova Reserva

                                        </a>
                                        <?php endif; ?>

                </div>
            <?php else: ?>
                <!-- Events Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach (
                        array_slice($reservasPublicas, 0, 6)
                        as $index => $reserva
                    ): ?>
                        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg animate-slide-up" style="animation-delay: <?php echo $index *
                            0.1; ?>s;">
                            <!-- Event Header -->
                            <div class="relative h-32 gradient-senac flex items-center justify-center">
                                <div class="absolute top-4 right-4">
                                    <?php
                                    $statusConfig = [
                                        "aprovada" => [
                                            "class" => "bg-green-500",
                                            "icon" => "fa-check-circle",
                                            "text" => "Aprovada",
                                        ],
                                        "pendente" => [
                                            "class" => "bg-yellow-500",
                                            "icon" => "fa-clock",
                                            "text" => "Pendente",
                                        ],
                                        "rejeitada" => [
                                            "class" => "bg-red-500",
                                            "icon" => "fa-times-circle",
                                            "text" => "Rejeitada",
                                        ],
                                    ];
                                    $status =
                                        $statusConfig[$reserva["status"]] ??
                                        $statusConfig["pendente"];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full <?php echo $status[
                                        "class"
                                    ]; ?> text-white text-xs font-semibold">
                                        <i class="fas <?php echo $status[
                                            "icon"
                                        ]; ?> mr-1"></i>
                                        <?php echo $status["text"]; ?>
                                    </span>
                                </div>
                                <div class="text-center text-white">
                                    <div class="text-4xl font-bold mb-1">
                                        <?php echo date(
                                            "d",
                                            strtotime($reserva["data"]),
                                        ); ?>
                                    </div>
                                    <div class="text-sm opacity-90">
                                        <?php echo strftime(
                                            "%B %Y",
                                            strtotime($reserva["data"]),
                                        ); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Body -->
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 line-clamp-2">
                                    <?php echo htmlspecialchars(
                                        $reserva["descricao"],
                                    ); ?>
                                </h3>

                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock w-5 text-senac-orange"></i>
                                        <span class="ml-3 text-sm">
                                            <?php echo substr(
                                                $reserva["hora_inicio"],
                                                0,
                                                5,
                                            ) .
                                                " - " .
                                                substr(
                                                    $reserva["hora_fim"],
                                                    0,
                                                    5,
                                                ); ?>
                                        </span>
                                    </div>

                                    <?php if (
                                        !empty($reserva["instrutor_nome"])
                                    ): ?>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-user w-5 text-senac-blue"></i>
                                        <span class="ml-3 text-sm">
                                            <?php echo htmlspecialchars(
                                                $reserva["instrutor_nome"],
                                            ); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-map-marker-alt w-5 text-senac-gold"></i>
                                        <span class="ml-3 text-sm">Auditório Principal</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($reservasPublicas) > 6): ?>
                <div class="text-center mt-12">
                    <a href="calendario.php" class="inline-flex items-center px-6 py-3 text-base font-semibold text-white gradient-senac rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Ver Todos os Eventos
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 gradient-senac opacity-95"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <h2 class="text-3xl md:text-5xl font-black mb-6 animate-slide-up">
                Pronto para começar?
            </h2>
            <p class="text-xl md:text-2xl mb-12 opacity-90 animate-slide-up" style="animation-delay: 0.1s;">
                Solicite seu acesso e comece a gerenciar reservas de forma profissional
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up" style="animation-delay: 0.2s;">
                <a href="view/cadastro.php" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold bg-white text-senac-blue rounded-xl shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-user-plus mr-3 group-hover:rotate-12 transition-transform"></i>
                    Solicitar Acesso
                </a>

                                <?php if (!isset($_SESSION["usuario_id"])): ?>
                                <a href="view/login.php" class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold bg-transparent text-white border-2 border-white rounded-xl hover:bg-white hover:text-senac-blue shadow-2xl transform hover:scale-105 transition-all duration-300">

                                    <i class="fas fa-sign-in-alt mr-3 group-hover:translate-x-1 transition-transform"></i>

                                    Já tenho conta

                                </a>
                                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Counter Animation
        document.addEventListener('DOMContentLoaded', function() {
            const counter = document.getElementById('counter-eventos');
            if (counter) {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 30);
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        mobileMenu.classList.add('hidden');
                    }
                });
            });

            // Console logs
            console.log('🏛️ Sistema SENAC carregado com sucesso!');
            console.log('📊 Total de eventos:', <?php echo $totalEventos; ?>);
            console.log('✅ Conexão:', <?php echo $conexaoOk
                ? "OK"
                : "ERRO"; ?>);
            console.log('⚙️ Setup completo:', <?php echo $setupComplete
                ? "Sim"
                : "Não"; ?>);

            <?php if (!$conexaoOk): ?>
            console.warn('⚠️ Sistema sem conexão com banco de dados!');
            console.log('🔧 Execute: ' + window.location.origin + window.location.pathname.replace('home.php', 'setup.php'));
            <?php endif; ?>
        });

        // Lazy load animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.animate-slide-up, .animate-fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(el);
            });
        }

        // Prevenir zoom em mobile
        if (window.innerWidth < 768) {
            const meta = document.querySelector('meta[name="viewport"]');
            if (meta) {
                meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
            }
        }
    </script>

<?php include "includes/footer.php"; ?>
