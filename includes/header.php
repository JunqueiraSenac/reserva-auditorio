<?php
// Inicia sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define o caminho base dependendo de onde o arquivo está sendo chamado
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/view/') !== false) {
    $base_path = '../';
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Reserva de Auditório SENAC - Gerencie eventos e reservas de forma profissional">
    <meta name="theme-color" content="#004A8D">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>SENAC - Sistema de Reserva de Auditório</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>public/images/logo-senac.png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- CSS local (fallback e estilos globais) -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>public/css/global-optimized.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'senac': {
                            'blue': '#004A8D',
                            'blue-dark': '#003366',
                            'blue-light': '#0066CC',
                            'orange': '#F26C21',
                            'orange-dark': '#D45A1A',
                            'orange-light': '#FF8C42',
                            'gold': '#C9A961',
                            'gray-light': '#F8F9FA',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'slide-down': 'slideDown 0.6s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                    },
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        }

        html.dark {
            color-scheme: dark;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #004A8D;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #003366;
        }

        /* Gradientes SENAC */
        .gradient-senac {
            background: linear-gradient(135deg, #004A8D 0%, #0066CC 100%);
        }

        .gradient-senac-warm {
            background: linear-gradient(135deg, #F26C21 0%, #FF8C42 100%);
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(0, 74, 141, 0.95) 0%, rgba(0, 102, 204, 0.95) 100%);
        }

        /* Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Button Styles */
        .btn-primary {
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }
    </style>

    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <!-- Navigation Header -->
    <nav class="fixed w-full top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-4">
                    <a href="<?php echo $base_path; ?>home.php" class="flex items-center space-x-4">
                        <img src="<?php echo $base_path; ?>public/images/logo-senac.png" alt="SENAC Logo" class="h-12 w-auto">
                        <div class="hidden md:block">
                            <h1 class="text-xl font-bold text-senac-blue dark:text-white">Sistema de Reservas</h1>
                            <p class="text-xs text-gray-600 dark:text-gray-400">SENAC Auditório</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="<?php echo $base_path; ?>home.php" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-senac-blue dark:hover:text-senac-orange transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i class="fas fa-home mr-2"></i>Início
                    </a>
                    <a href="<?php echo $base_path; ?>calendario.php" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-senac-blue dark:hover:text-senac-orange transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <i class="fas fa-calendar-alt mr-2"></i>Calendário
                    </a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>view/painel_admin.php" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-senac-blue dark:hover:text-senac-orange transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                <i class="fas fa-cog mr-2"></i>Painel Admin
                            </a>
                        <?php elseif ($_SESSION['tipo_usuario'] === 'instrutor'): ?>
                            <a href="<?php echo $base_path; ?>view/painel_instrutor.php" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-senac-blue dark:hover:text-senac-orange transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Painel Instrutor
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-moon dark:hidden text-gray-700"></i>
                        <i class="fas fa-sun hidden dark:inline text-yellow-400"></i>
                    </button>

                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <!-- User Menu -->
                        <div class="hidden sm:flex items-center space-x-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>
                            </span>
                            <a href="<?php echo $base_path; ?>controller/LoginController.php?action=logout" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>Sair
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Login Button -->
                        <a href="<?php echo $base_path; ?>view/login.php" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-senac-blue dark:text-white border border-senac-blue dark:border-senac-orange rounded-lg hover:bg-senac-blue hover:text-white dark:hover:bg-senac-orange transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>

                        <!-- Register Button -->
                        <a href="<?php echo $base_path; ?>view/cadastro.php" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white gradient-senac-warm rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Cadastrar
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        <i class="fas fa-bars text-gray-700 dark:text-gray-300"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <div class="px-4 py-4 space-y-2">
                <a href="<?php echo $base_path; ?>home.php" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-home mr-2"></i>Início
                </a>
                <a href="<?php echo $base_path; ?>calendario.php" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-calendar-alt mr-2"></i>Calendário
                </a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                        <a href="<?php echo $base_path; ?>view/painel_admin.php" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                            <i class="fas fa-cog mr-2"></i>Painel Admin
                        </a>
                    <?php elseif ($_SESSION['tipo_usuario'] === 'instrutor'): ?>
                        <a href="<?php echo $base_path; ?>view/painel_instrutor.php" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>Painel Instrutor
                        </a>
                    <?php endif; ?>
                    <hr class="my-2 border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>
                    </div>
                    <a href="<?php echo $base_path; ?>controller/LoginController.php?action=logout" class="block px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg text-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                <?php else: ?>
                    <hr class="my-2 border-gray-200 dark:border-gray-700">
                    <a href="<?php echo $base_path; ?>view/login.php" class="block px-4 py-2 text-sm font-medium text-senac-blue dark:text-senac-orange hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Fazer Login
                    </a>
                    <a href="<?php echo $base_path; ?>view/cadastro.php" class="block px-4 py-2 text-sm font-medium text-white gradient-senac-warm rounded-lg text-center">
                        <i class="fas fa-user-plus mr-2"></i>Cadastrar-se
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed nav -->
    <div class="h-20"></div>

    <script>
        // Dark Mode Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // Load saved theme
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', currentTheme === 'dark');

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const theme = html.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
