<?php
session_start();
require_once "../controller/LoginController.php";
require_once "../controller/ReservaController.php";
require_once "../controller/UsuarioController.php";

$loginController = new LoginController();
$loginController->verificarAutenticacao();

if ($_SESSION["tipo_usuario"] === "admin") {
    header("Location: painel_admin.php");
    exit();
}

$reservaController = new ReservaController();
$reservas = $reservaController->listarPorUsuario($_SESSION["usuario_id"]);

// Verifica se o usuário está aprovado para fazer reservas
$usuarioController = new UsuarioController();
$usuario = $usuarioController->buscarPorId($_SESSION["usuario_id"]);
$usuario_aprovado = $usuario && $usuario["status_aprovacao"] === "aprovado";

// Mensagens de feedback
$sucesso = isset($_GET["sucesso"]) ? $_GET["sucesso"] : "";
$erro = isset($_GET["erro"]) ? $_GET["erro"] : "";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENAC - Painel do Instrutor</title>
    <link rel="icon" type="image/png" href="../public/images/logo-senac.png">


    <!-- Tailwind CSS (opcional) -->

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../public/css/global-optimized.css">


    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'senac-blue': '#004A8D',
                        'senac-orange': '#F26C21',
                        'senac-gold': '#C9A961',
                        'senac-light-blue': '#E8F2FF',
                        'senac-dark-blue': '#003366'
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-senac-light-blue to-white">
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <img src="../public/images/logo-senac.png" alt="SENAC Logo" class="logo-img">
                <div class="hidden md:block">
                    <h1 class="header-title">Painel do Instrutor</h1>
                    <p class="text-sm text-gray-600">Sistema de Reserva do Auditório</p>
                </div>
            </div>

            <div class="nav">
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-user text-senac-blue"></i>
                        <span>Olá, <?php echo htmlspecialchars(
                            $_SESSION["usuario_nome"],
                        ); ?>!</span>
                    </div>


                    <a href="../home.php" class="btn btn-outline">

                        <i class="fas fa-home mr-2"></i>

                        Início

                    </a>
                    <button id="theme-toggle" class="btn btn-outline" title="Alternar tema" onclick="(function(){var h=document.documentElement;var d=h.classList.toggle('dark');localStorage.setItem('theme', d?'dark':'light');})();">
                        <i class="fas fa-moon"></i>
                    </button>


                    <form action="../controller/LoginController.php" method="POST" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Title -->
    <div class="block md:hidden text-center py-6 px-4">
        <h1 class="text-2xl font-bold text-senac-blue">Painel do Instrutor</h1>
        <p class="text-senac-orange">Olá, <?php echo htmlspecialchars(
            $_SESSION["usuario_nome"],
        ); ?>!</p>
    </div>

    <!-- Main Content -->
    <main class="container py-8">
        <?php if (!$usuario_aprovado): ?>
            <!-- Status de Aprovação -->
            <div class="card mb-8 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-4xl text-yellow-500"></i>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            Aguardando Aprovação
                        </h3>
                        <p class="text-gray-600 mb-4">
                            Seu cadastro foi recebido e está sendo analisado pela administração.
                            Você poderá criar reservas após a aprovação.
                        </p>
                        <div class="flex items-center text-sm text-yellow-700 bg-yellow-100 px-4 py-2 rounded-lg">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Status atual: Aguardando aprovação</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="grid-4 mb-8">
            <div class="card text-center">
                <div class="text-3xl font-bold text-senac-blue mb-2">
                    <?php echo count($reservas); ?>
                </div>
                <div class="text-gray-600">Total de Reservas</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-green-500 mb-2">
                    <?php echo count(
                        array_filter($reservas, function ($r) {
                            return $r["status"] === "aprovada";
                        }),
                    ); ?>
                </div>
                <div class="text-gray-600">Aprovadas</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-yellow-500 mb-2">
                    <?php echo count(
                        array_filter($reservas, function ($r) {
                            return $r["status"] === "pendente";
                        }),
                    ); ?>
                </div>
                <div class="text-gray-600">Pendentes</div>
            </div>

            <div class="card text-center">
                <div class="text-3xl font-bold text-red-500 mb-2">
                    <?php echo count(
                        array_filter($reservas, function ($r) {
                            return $r["status"] === "rejeitada";
                        }),
                    ); ?>
                </div>
                <div class="text-gray-600">Rejeitadas</div>
            </div>
        </div>

        <!-- Nova Reserva -->
        <div class="card mb-8">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title">
                            <i class="fas fa-plus-circle text-senac-blue mr-3"></i>
                            Nova Reserva
                        </h2>
                        <p class="card-subtitle">Solicite uma reserva do auditório</p>
                    </div>
                    <?php if (!$usuario_aprovado): ?>
                        <div class="badge badge-warning">
                            <i class="fas fa-lock mr-1"></i>
                            Bloqueado
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($usuario_aprovado): ?>
                <form id="form-reserva" action="../controller/ReservaController.php" method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="criar">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="data" class="form-label">
                                <i class="fas fa-calendar-alt mr-2 text-senac-blue"></i>
                                Data da Reserva
                            </label>
                            <input type="date"
                                   id="data"
                                   name="data"
                                   class="form-input"
                                   required
                                   min="<?php echo date("Y-m-d"); ?>"
                                   onchange="validarConflito()">
                            <p class="text-sm text-gray-500 mt-1">Selecione a data do evento</p>
                        </div>

                        <div class="form-group">
                            <label for="descricao" class="form-label">
                                <i class="fas fa-edit mr-2 text-senac-orange"></i>
                                Descrição do Evento
                            </label>
                            <input type="text"
                                   id="descricao"
                                   name="descricao"
                                   class="form-input"
                                   placeholder="Ex: Palestra sobre Marketing Digital"
                                   required
                                   minlength="10">
                            <p class="text-sm text-gray-500 mt-1">Mínimo de 10 caracteres</p>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio" class="form-label">
                                <i class="fas fa-clock mr-2 text-senac-gold"></i>
                                Hora de Início
                            </label>
                            <input type="time"
                                   id="hora_inicio"
                                   name="hora_inicio"
                                   class="form-input"
                                   required
                                   min="08:00"
                                   max="21:30"
                                   onchange="validarConflito()">
                            <p class="text-sm text-gray-500 mt-1">Entre 08:00 e 21:30</p>
                        </div>

                        <div class="form-group">
                            <label for="hora_fim" class="form-label">
                                <i class="fas fa-clock mr-2 text-senac-gold"></i>
                                Hora de Término
                            </label>
                            <input type="time"
                                   id="hora_fim"
                                   name="hora_fim"
                                   class="form-input"
                                   required
                                   min="08:30"
                                   max="22:00"
                                   onchange="validarConflito()">
                            <p class="text-sm text-gray-500 mt-1">Entre 08:30 e 22:00</p>
                        </div>
                    </div>

                    <!-- Status de Validação -->
                    <div id="status-validacao" class="hidden">
                        <!-- Será preenchido via JavaScript -->
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="btn btn-primary px-8 py-3">
                            <i class="fas fa-save mr-2"></i>
                            Solicitar Reserva
                        </button>

                        <button type="button" onclick="limparFormulario()" class="btn btn-outline px-8 py-3">
                            <i class="fas fa-undo mr-2"></i>
                            Limpar
                        </button>

                        <button type="button" onclick="verificarDisponibilidade()" class="btn btn-secondary px-8 py-3">
                            <i class="fas fa-search mr-2"></i>
                            Verificar Disponibilidade
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-lock text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Formulário Bloqueado</h3>
                    <p>Aguarde a aprovação do seu cadastro para criar reservas.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Minhas Reservas -->
        <div class="card">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="card-title">
                            <i class="fas fa-list text-senac-blue mr-3"></i>
                            Minhas Reservas
                        </h2>
                        <p class="card-subtitle">Histórico e status das suas solicitações</p>
                    </div>

                    <!-- Filtros -->
                    <div class="flex items-center space-x-2">
                        <select id="filtro-status" class="form-select text-sm" onchange="filtrarReservas()">
                            <option value="">Todos os Status</option>
                            <option value="aprovada">Aprovadas</option>
                            <option value="pendente">Pendentes</option>
                            <option value="rejeitada">Rejeitadas</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if (empty($reservas)): ?>
                <div class="text-center py-12">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhuma reserva encontrada</h3>
                    <p class="text-gray-500 mb-6">
                        Você ainda não possui reservas. Faça sua primeira solicitação acima!
                    </p>
                    <?php if ($usuario_aprovado): ?>
                        <button onclick="document.getElementById('data').focus()" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Criar Primeira Reserva
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data/Horário
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tabela-reservas">
                            <?php foreach ($reservas as $reserva): ?>
                                <tr class="reserva-row hover:bg-gray-50" data-status="<?php echo $reserva[
                                    "status"
                                ]; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo date(
                                                    "d/m/Y",
                                                    strtotime($reserva["data"]),
                                                ); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 ml-2">
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
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            <?php
                                            $dias = [
                                                "Dom",
                                                "Seg",
                                                "Ter",
                                                "Qua",
                                                "Qui",
                                                "Sex",
                                                "Sáb",
                                            ];
                                            echo $dias[
                                                date(
                                                    "w",
                                                    strtotime($reserva["data"]),
                                                )
                                            ];
                                            ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">
                                            <?php echo htmlspecialchars(
                                                $reserva["descricao"],
                                            ); ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            ID: #<?php echo $reserva["id"]; ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusClass = "";
                                        $statusIcon = "";
                                        switch ($reserva["status"]) {
                                            case "aprovada":
                                                $statusClass = "badge-success";
                                                $statusIcon =
                                                    "fas fa-check-circle";
                                                break;
                                            case "pendente":
                                                $statusClass = "badge-warning";
                                                $statusIcon = "fas fa-clock";
                                                break;
                                            case "rejeitada":
                                                $statusClass = "badge-danger";
                                                $statusIcon =
                                                    "fas fa-times-circle";
                                                break;
                                            default:
                                                $statusClass = "badge-info";
                                                $statusIcon =
                                                    "fas fa-info-circle";
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <i class="<?php echo $statusIcon; ?> mr-1"></i>
                                            <?php echo ucfirst(
                                                $reserva["status"],
                                            ); ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <?php if (
                                                $reserva["status"] ===
                                                "pendente"
                                            ): ?>
                                                <form action="../controller/ReservaController.php" method="POST" class="inline"
                                                      onsubmit="return confirm('Deseja cancelar esta reserva?')">
                                                    <input type="hidden" name="action" value="cancelar">
                                                    <input type="hidden" name="id" value="<?php echo $reserva[
                                                        "id"
                                                    ]; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Cancelar">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <button onclick="verDetalhes(<?php echo htmlspecialchars(
                                                json_encode($reserva),
                                            ); ?>)"
                                                    class="text-senac-blue hover:text-senac-dark-blue" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal de Detalhes -->
    <div id="modal-detalhes" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">Detalhes da Reserva</h3>
                    <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="p-6" id="conteudo-modal">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../public/js/config.js"></script>
    <script src="../public/js/app.js"></script>

    <script>
        // Aguardar configurações serem carregadas
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se configurações estão disponíveis
            if (!window.configManager) {
                console.error('ConfigManager não foi carregado');
                return;
            }

            // Feedback messages
            <?php if ($sucesso): ?>
                const successDuration = getConfig('ui.toast.duration.success') || 5000;
                toast.success('Reserva solicitada com sucesso! Aguarde a aprovação da administração.', null, successDuration);
            <?php endif; ?>

            <?php if ($erro): ?>
                const errorDuration = getConfig('ui.toast.duration.error') || 7000;
                toast.error('Erro ao processar a reserva. Verifique os dados e tente novamente.', null, errorDuration);
            <?php endif; ?>

            // Debug em desenvolvimento
            if (configManager.isDevelopment()) {
                console.log('👨‍🏫 Painel do instrutor carregado');
                console.log('📊 Minhas reservas:', <?php echo count(
                    $reservas,
                ); ?>);
            }
        });

        // Validação em tempo real de conflitos
        let timeoutValidacao = null;

        function validarConflito() {
            const data = document.getElementById('data').value;
            const horaInicio = document.getElementById('hora_inicio').value;
            const horaFim = document.getElementById('hora_fim').value;

            if (!data || !horaInicio || !horaFim) {
                document.getElementById('status-validacao').classList.add('hidden');
                return;
            }

            // Debounce para evitar muitas requisições
            clearTimeout(timeoutValidacao);
            timeoutValidacao = setTimeout(() => {
                verificarConflitoAPI(data, horaInicio, horaFim);
            }, 500);
        }

        async function verificarConflitoAPI(data, horaInicio, horaFim) {
            try {
                const apiUrl = getApiUrl('reservas');
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'validar_reserva',
                        data: data,
                        hora_inicio: horaInicio,
                        hora_fim: horaFim,
                        descricao: 'Validação'
                    })
                });

                const resultado = await response.json();
                mostrarStatusValidacao(resultado);

            } catch (error) {
                console.error('Erro ao validar:', error);
            }
        }

        function mostrarStatusValidacao(resultado) {
            const statusDiv = document.getElementById('status-validacao');
            statusDiv.classList.remove('hidden');

            if (resultado.success && resultado.data.valida) {
                statusDiv.innerHTML = `
                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 font-medium">
                                    Horário disponível! Duração: ${resultado.data.duracao_minutos} minutos
                                </p>
                                ${resultado.data.avisos.length > 0 ?
                                    '<ul class="mt-2 text-sm text-yellow-700">' +
                                    resultado.data.avisos.map(aviso => '<li>• ' + aviso + '</li>').join('') +
                                    '</ul>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusDiv.innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-times-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">Problemas encontrados:</p>
                                <ul class="mt-2 text-sm text-red-700">
                                    ${resultado.data?.erros?.map(erro => '<li>• ' + erro + '</li>').join('') ||
                                      '<li>• ' + (resultado.error?.message || 'Erro desconhecido') + '</li>'}
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function limparFormulario() {
            document.getElementById('form-reserva').reset();
            document.getElementById('status-validacao').classList.add('hidden');
        }

        async function verificarDisponibilidade() {
            const data = document.getElementById('data').value;

            if (!data) {
                toast.warning('Selecione uma data primeiro');
                return;
            }

            loading.show('Verificando disponibilidade...');

            try {
                const apiUrl = getApiUrl('reservas');
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'verificar_disponibilidade',
                        data_inicio: data,
                        data_fim: data
                    })
                });

                const resultado = await response.json();
                loading.hide();

                if (resultado.success) {
                    const disponibilidade = resultado.data.disponibilidade[0];

                    if (disponibilidade.disponivel) {
                        toast.success('Data disponível! Nenhuma reserva encontrada para este dia.');
                    } else {
                        const reservas = disponibilidade.reservas;
                        let mensagem = `Encontradas ${reservas.length} reservas para este dia:\n`;
                        reservas.forEach(r => {
                            mensagem += `• ${r.hora_inicio.substring(0,5)}-${r.hora_fim.substring(0,5)}: ${r.descricao}\n`;
                        });
                        toast.info(mensagem);
                    }
                } else {
                    toast.error('Erro ao verificar disponibilidade');
                }

            } catch (error) {
                loading.hide();
                toast.error('Erro de conexão ao verificar disponibilidade');
            }
        }

        function filtrarReservas() {
            const filtro = document.getElementById('filtro-status').value;
            const linhas = document.querySelectorAll('.reserva-row');

            linhas.forEach(linha => {
                if (!filtro || linha.dataset.status === filtro) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        }

        function verDetalhes(reserva) {
            const modal = document.getElementById('modal-detalhes');
            const conteudo = document.getElementById('conteudo-modal');

            const statusClass = reserva.status === 'aprovada' ? 'text-green-600' :
                               reserva.status === 'pendente' ? 'text-yellow-600' : 'text-red-600';

            conteudo.innerHTML = `
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            ${reserva.descricao}
                        </h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass} bg-gray-100">
                            ${reserva.status.charAt(0).toUpperCase() + reserva.status.slice(1)}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-alt text-senac-blue mr-2"></i>
                                <span class="font-medium text-gray-700">Data</span>
                            </div>
                            <div class="text-gray-600">
                                ${new Date(reserva.data + 'T00:00:00').toLocaleDateString('pt-BR', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-clock text-senac-orange mr-2"></i>
                                <span class="font-medium text-gray-700">Horário</span>
                            </div>
                            <div class="text-gray-600">
                                ${reserva.hora_inicio.substring(0, 5)} - ${reserva.hora_fim.substring(0, 5)}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-hashtag text-senac-gold mr-2"></i>
                                <span class="font-medium text-gray-700">ID da Reserva</span>
                            </div>
                            <div class="text-gray-600">#${reserva.id}</div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-map-marker-alt text-senac-blue mr-2"></i>
                                <span class="font-medium text-gray-700">Local</span>
                            </div>
                            <div class="text-gray-600">Auditório Principal</div>
                        </div>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
        }

        function fecharModal() {
            document.getElementById('modal-detalhes').classList.add('hidden');
        }

        // Fecha modal clicando fora
        document.getElementById('modal-detalhes').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModal();
            }
        });

        // Validação de horários
        document.getElementById('hora_inicio').addEventListener('change', function() {
            const horaFim = document.getElementById('hora_fim');
            const horaInicio = this.value;

            if (horaInicio) {
                const [h, m] = horaInicio.split(':');
                const novoMinimo = new Date();
                novoMinimo.setHours(parseInt(h), parseInt(m) + 30, 0, 0);

                const horaMinima = String(novoMinimo.getHours()).padStart(2, '0') + ':' +
                                 String(novoMinimo.getMinutes()).padStart(2, '0');

                horaFim.min = horaMinima;

                if (horaFim.value && horaFim.value <= horaInicio) {
                    horaFim.value = horaMinima;
                }
            }
        });

        // Auto-focus no primeiro campo quando aprovado
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($usuario_aprovado): ?>
            // Scroll suave para o formulário se houver erro ou sucesso
            <?php if ($sucesso || $erro): ?>
            document.getElementById('form-reserva').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            <?php endif; ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
