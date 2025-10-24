<?php
session_start();
if (isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - Sistema de Reserva de Auditório SENAC Umuarama">
    <meta name="theme-color" content="#004A8D">
    <title>SENAC Umuarama - Login</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../public/images/logo-senac.png">


    <!-- Tailwind CSS -->

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CSS local (fallback e estilos globais) -->
    <link rel="stylesheet" href="../public/css/global-optimized.css">


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
                            'blue-darker': '#002244',
                            'blue-light': '#0066CC',
                            'orange': '#F26C21',
                            'orange-dark': '#D45A1A',
                            'orange-light': '#FF8C42',
                            'gold': '#C9A961',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
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
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
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

        body {
            transition: background-color 0.3s ease;
        }

        /* Dark mode com azul escuro SENAC */
        .dark {
            background: linear-gradient(135deg, #002244 0%, #003366 50%, #004A8D 100%);
        }

        /* Gradient backgrounds */
        .gradient-senac {
            background: linear-gradient(135deg, #004A8D 0%, #0066CC 100%);
        }

        .gradient-senac-dark {
            background: linear-gradient(135deg, #002244 0%, #003366 100%);
        }

        /* Animated gradient background */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-gradient {
            background: linear-gradient(-45deg, #004A8D, #0066CC, #F26C21, #FF8C42);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        /* Glass morphism */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .dark .glass {
            background: rgba(0, 34, 68, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Input focus effects */
        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 74, 141, 0.15);
        }

        /* Button ripple effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }

        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-ripple:active::after {
            width: 300px;
            height: 300px;
        }

        /* Floating shapes */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .dark .floating-shape {
            opacity: 0.05;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-orange-50 dark:from-senac-blue-darker dark:to-senac-blue-dark py-8 px-4 relative overflow-x-hidden overflow-y-auto">

    <!-- Floating Background Shapes -->
    <div class="floating-shape w-96 h-96 bg-senac-blue top-10 left-10" style="animation-delay: 0s;"></div>
    <div class="floating-shape w-72 h-72 bg-senac-orange bottom-10 right-10" style="animation-delay: 2s;"></div>
    <div class="floating-shape w-64 h-64 bg-senac-gold top-1/2 left-1/2" style="animation-delay: 4s;"></div>

    <!-- Dark Mode Toggle -->
    <button id="theme-toggle" class="fixed top-6 right-6 z-50 p-3 rounded-full glass shadow-lg hover:shadow-2xl transform hover:scale-110 transition-all duration-300 group">
        <i class="fas fa-moon dark:hidden text-senac-blue text-xl group-hover:rotate-12 transition-transform"></i>
        <i class="fas fa-sun hidden dark:inline text-yellow-400 text-xl group-hover:rotate-180 transition-transform"></i>
    </button>

    <!-- Back to Home Button -->
    <a href="../home.php" class="fixed top-6 left-6 z-50 flex items-center space-x-2 px-4 py-2 glass rounded-full shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-senac-blue dark:text-white font-medium">
        <i class="fas fa-arrow-left"></i>
        <span class="hidden sm:inline">Voltar</span>
    </a>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md mx-auto animate-slide-up">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 glass rounded-3xl shadow-2xl mb-6 animate-float">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo" class="h-20 w-auto">
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                Bem-vindo de volta!
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                SENAC Umuarama - Paraná
            </p>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-3xl shadow-2xl p-8 border border-white/20">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-sign-in-alt text-senac-orange mr-3"></i>
                    Entrar no Sistema
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Acesse o sistema de reserva do auditório
                </p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET["erro"])): ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-lg animate-slide-up">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-400">Erro ao fazer login</p>
                            <p class="text-sm text-red-600 dark:text-red-300">Email ou senha incorretos. Tente novamente.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (
                isset($_GET["cadastro"]) &&
                $_GET["cadastro"] === "sucesso"
            ): ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-lg animate-slide-up">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-green-800 dark:text-green-400">Cadastro realizado!</p>
                            <p class="text-sm text-green-600 dark:text-green-300">Faça login para acessar o sistema.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="../controller/LoginController.php" method="POST" class="space-y-6">
                <input type="hidden" name="action" value="login">

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-envelope text-senac-blue dark:text-senac-orange mr-2"></i>
                        Email
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            placeholder="seu.email@exemplo.com"
                            class="input-focus w-full px-4 py-3 pl-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="senha" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-lock text-senac-blue dark:text-senac-orange mr-2"></i>
                        Senha
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            required
                            minlength="6"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-focus w-full px-4 py-3 pl-12 pr-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-senac-blue dark:hover:text-senac-orange transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-gray-300 text-senac-blue focus:ring-senac-blue dark:bg-gray-700 dark:border-gray-600"
                        >
                        <span class="text-gray-600 dark:text-gray-300 group-hover:text-senac-blue dark:group-hover:text-senac-orange transition-colors">
                            Lembrar-me
                        </span>
                    </label>
                    <a href="#" class="text-senac-blue dark:text-senac-orange hover:underline font-medium">
                        Esqueceu a senha?
                    </a>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="btn-ripple w-full py-4 px-6 gradient-senac text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center space-x-3 group"
                >
                    <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform"></i>
                    <span>Entrar no Sistema</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            Não tem uma conta?
                        </span>
                    </div>
                </div>

                <!-- Register Link -->
                <a
                    href="cadastro.php"
                    class="block w-full py-4 px-6 bg-white dark:bg-gray-800 text-senac-blue dark:text-senac-orange border-2 border-senac-blue dark:border-senac-orange font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-center"
                >
                    <i class="fas fa-user-plus mr-2"></i>
                    Criar nova conta
                </a>
            </form>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Sistema de Reserva de Auditório
            </p>
            <div class="flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                <span>
                    <i class="fas fa-shield-alt mr-1"></i>
                    Seguro
                </span>
                <span>•</span>
                <span>
                    <i class="fas fa-clock mr-1"></i>
                    24/7
                </span>
                <span>•</span>
                <span>
                    <i class="fas fa-lock mr-1"></i>
                    Privado
                </span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Dark Mode Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // Verificar tema salvo ou usar dark mode por padrão
        const currentTheme = localStorage.getItem('theme') || 'dark';
        html.classList.toggle('dark', currentTheme === 'dark');

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const theme = html.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });

        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('senha');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form Validation - Otimizada
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;

            // Validação de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor, digite um email válido!');
                document.getElementById('email').focus();
                return false;
            }

            // Validação de senha
            if (!senha || senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres!');
                document.getElementById('senha').focus();
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Entrando...';

            // Timeout de segurança (10 segundos)
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 10000);
        });

        // Auto-focus no email e scroll suave
        document.addEventListener('DOMContentLoaded', function() {
            // Garantir scroll no topo
            window.scrollTo(0, 0);

            // Focus no primeiro campo
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 100);

            console.log('🏛️ SENAC Umuarama - Sistema de Login carregado');
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + D para toggle dark mode
            if (e.altKey && e.key === 'd') {
                e.preventDefault();
                themeToggle.click();
            }

            // Esc para scroll to top
            if (e.key === 'Escape') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Prevenir zoom em mobile nos inputs
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                if (window.innerWidth < 768) {
                    document.body.style.zoom = '1.0';
                }
            });
        });
    </script>
</body>
</html>
