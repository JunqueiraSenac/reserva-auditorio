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
    <meta name="description" content="Cadastro - Sistema de Reserva de Auditório SENAC Umuarama">
    <meta name="theme-color" content="#004A8D">
    <title>SENAC Umuarama - Cadastro</title>

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

        .gradient-senac-warm {
            background: linear-gradient(135deg, #F26C21 0%, #FF8C42 100%);
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

        /* Password strength indicator */
        .strength-meter {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-weak { width: 33%; background: #ef4444; }
        .strength-medium { width: 66%; background: #f59e0b; }
        .strength-strong { width: 100%; background: #10b981; }
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

    <!-- Register Container -->
    <div class="relative z-10 w-full max-w-2xl mx-auto animate-slide-up">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 glass rounded-3xl shadow-2xl mb-6 animate-float">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo" class="h-20 w-auto">
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                Junte-se a nós!
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                SENAC Umuarama - Paraná
            </p>
        </div>

        <!-- Register Card -->
        <div class="glass rounded-3xl shadow-2xl p-8 border border-white/20">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-user-plus text-senac-orange mr-3"></i>
                    Criar Nova Conta
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Preencha os dados para solicitar acesso ao sistema
                </p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET["erro"])): ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-lg animate-slide-up">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-400">Erro no cadastro</p>
                            <p class="text-sm text-red-600 dark:text-red-300">Este email já está cadastrado ou houve um erro. Tente novamente.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Register Form -->
            <form action="../controller/UsuarioController.php" method="POST" id="cadastroForm" class="space-y-6">
                <input type="hidden" name="action" value="cadastrar">

                <!-- Name Field -->
                <div class="space-y-2">
                    <label for="nome" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-user text-senac-blue dark:text-senac-orange mr-2"></i>
                        Nome Completo
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            required
                            minlength="3"
                            placeholder="Digite seu nome completo"
                            class="input-focus w-full px-4 py-3 pl-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-envelope text-senac-blue dark:text-senac-orange mr-2"></i>
                        Email Institucional
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            placeholder="seu.email@exemplo.com"
                            class="input-focus w-full px-4 py-3 pl-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-at absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>

                <!-- Phone Field -->
                <div class="space-y-2">
                    <label for="telefone" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-phone text-senac-blue dark:text-senac-orange mr-2"></i>
                        Telefone (WhatsApp)
                    </label>
                    <div class="relative">
                        <input
                            type="tel"
                            id="telefone"
                            name="telefone"
                            required
                            placeholder="(44) 99999-9999"
                            maxlength="15"
                            class="input-focus w-full px-4 py-3 pl-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fab fa-whatsapp absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Necessário para receber notificações
                    </p>
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
                            placeholder="••••••••"
                            onkeyup="checkPasswordStrength()"
                            class="input-focus w-full px-4 py-3 pl-12 pr-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                        <button
                            type="button"
                            onclick="togglePassword('senha')"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-senac-blue dark:hover:text-senac-orange transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIconSenha"></i>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div id="strengthMeter" class="strength-meter" style="width: 0%;"></div>
                        </div>
                        <p id="strengthText" class="text-xs mt-1 text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                    <label for="confirmar_senha" class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-lock text-senac-blue dark:text-senac-orange mr-2"></i>
                        Confirmar Senha
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            required
                            minlength="6"
                            placeholder="••••••••"
                            onkeyup="checkPasswordMatch()"
                            class="input-focus w-full px-4 py-3 pl-12 pr-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-senac-blue dark:focus:border-senac-orange focus:ring-4 focus:ring-senac-blue/10 dark:focus:ring-senac-orange/10 outline-none transition-all duration-300"
                        >
                        <i class="fas fa-check-double absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                        <button
                            type="button"
                            onclick="togglePassword('confirmar_senha')"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-senac-blue dark:hover:text-senac-orange transition-colors"
                        >
                            <i class="fas fa-eye" id="toggleIconConfirmar"></i>
                        </button>
                    </div>
                    <p id="matchText" class="text-xs mt-1"></p>
                </div>

                <!-- Access Request Checkbox -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <label class="flex items-start space-x-3 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="solicitar_instrutor"
                            value="1"
                            checked
                            class="mt-1 w-5 h-5 rounded border-gray-300 text-senac-blue focus:ring-senac-blue dark:bg-gray-700 dark:border-gray-600"
                        >
                        <div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white block mb-1">
                                <i class="fas fa-clipboard-check text-senac-blue dark:text-senac-orange mr-2"></i>
                                Solicitar acesso ao sistema
                            </span>
                            <span class="text-xs text-gray-600 dark:text-gray-300">
                                Marque esta opção para poder criar reservas após aprovação
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                    <label class="flex items-start space-x-3 cursor-pointer group">
                        <input
                            type="checkbox"
                            required
                            class="mt-1 w-5 h-5 rounded border-gray-300 text-senac-blue focus:ring-senac-blue dark:bg-gray-700 dark:border-gray-600"
                        >
                        <span class="text-xs text-gray-600 dark:text-gray-300">
                            Eu li e concordo com os
                            <a href="#" class="text-senac-blue dark:text-senac-orange hover:underline font-semibold">termos de uso</a>
                            e a
                            <a href="#" class="text-senac-blue dark:text-senac-orange hover:underline font-semibold">política de privacidade</a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="btn-ripple w-full py-4 px-6 gradient-senac-warm text-white font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center space-x-3 group"
                >
                    <i class="fas fa-user-plus group-hover:rotate-12 transition-transform"></i>
                    <span>Criar Conta</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            Já tem uma conta?
                        </span>
                    </div>
                </div>

                <!-- Login Link -->
                <a
                    href="login.php"
                    class="block w-full py-4 px-6 bg-white dark:bg-gray-800 text-senac-blue dark:text-senac-orange border-2 border-senac-blue dark:border-senac-orange font-bold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-center"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Fazer Login
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
                    <i class="fas fa-user-check mr-1"></i>
                    Verificado
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
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById('toggleIcon' + fieldId.charAt(0).toUpperCase() + fieldId.slice(1).replace('_', ''));

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

        // Check Password Strength
        function checkPasswordStrength() {
            const password = document.getElementById('senha').value;
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;

            strengthMeter.className = 'strength-meter';

            if (strength <= 2) {
                strengthMeter.classList.add('strength-weak');
                strengthText.textContent = 'Senha fraca';
                strengthText.className = 'text-xs mt-1 text-red-500';
            } else if (strength <= 4) {
                strengthMeter.classList.add('strength-medium');
                strengthText.textContent = 'Senha média';
                strengthText.className = 'text-xs mt-1 text-yellow-500';
            } else {
                strengthMeter.classList.add('strength-strong');
                strengthText.textContent = 'Senha forte';
                strengthText.className = 'text-xs mt-1 text-green-500';
            }

            checkPasswordMatch();
        }

        // Check Password Match
        function checkPasswordMatch() {
            const senha = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar_senha').value;
            const matchText = document.getElementById('matchText');

            if (confirmar.length > 0) {
                if (senha === confirmar) {
                    matchText.innerHTML = '<i class="fas fa-check-circle mr-1"></i> As senhas coincidem';
                    matchText.className = 'text-xs mt-1 text-green-500';
                } else {
                    matchText.innerHTML = '<i class="fas fa-times-circle mr-1"></i> As senhas não coincidem';
                    matchText.className = 'text-xs mt-1 text-red-500';
                }
            } else {
                matchText.textContent = '';
            }
        }

        // Phone Mask - Otimizado
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length > 11) {
                value = value.slice(0, 11);
            }

            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }

            e.target.value = value;
        });

        // Form Validation - Otimizada
        document.getElementById('cadastroForm').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const telefone = document.getElementById('telefone').value.trim();
            const senha = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar_senha').value;

            // Validação do nome
            if (nome.length < 3) {
                e.preventDefault();
                alert('Nome deve ter pelo menos 3 caracteres!');
                document.getElementById('nome').focus();
                return false;
            }

            // Validação do email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Email inválido!');
                document.getElementById('email').focus();
                return false;
            }

            // Validação do telefone
            const telefoneNumeros = telefone.replace(/\D/g, '');
            if (telefoneNumeros.length < 10) {
                e.preventDefault();
                alert('Telefone inválido! Digite um número completo.');
                document.getElementById('telefone').focus();
                return false;
            }

            // Validação das senhas
            if (senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres!');
                document.getElementById('senha').focus();
                return false;
            }

            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                document.getElementById('confirmar_senha').focus();
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Criando conta...';

            // Timeout de segurança (15 segundos)
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            }, 15000);
        });

        // Auto-focus no nome e scroll suave
        document.addEventListener('DOMContentLoaded', function() {
            // Garantir scroll no topo
            window.scrollTo(0, 0);

            // Focus no primeiro campo
            setTimeout(() => {
                document.getElementById('nome').focus();
            }, 100);

            console.log('🏛️ SENAC Umuarama - Sistema de Cadastro carregado');
            console.log('📱 Altura da página:', document.body.scrollHeight, 'px');
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
