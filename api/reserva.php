<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../controller/ReservaController.php';
require_once __DIR__ . '/../model/Reserva.php';

class ReservaAPI
{
    private $reservaController;
    private $reservaModel;

    public function __construct()
    {
        $this->reservaController = new ReservaController();
        $this->reservaModel = new Reserva();
    }

    /**
     * Processa as requisições da API
     */
    public function processRequest()
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'];

            switch ($method) {
                case 'GET':
                    return $this->handleGet();
                case 'POST':
                    return $this->handlePost();
                default:
                    return $this->errorResponse('Método não permitido', 405);
            }
        } catch (Exception $e) {
            return $this->errorResponse('Erro interno do servidor: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle GET requests
     */
    private function handleGet()
    {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'listar':
                return $this->listarReservas();
            case 'estatisticas':
                return $this->obterEstatisticas();
            default:
                return $this->errorResponse('Ação não encontrada', 404);
        }
    }

    /**
     * Handle POST requests
     */
    private function handlePost()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        // Fallback para form data se JSON não estiver disponível
        if (!$input) {
            $input = $_POST;
        }

        $action = $input['action'] ?? '';

        switch ($action) {
            case 'verificar_conflito':
                return $this->verificarConflito($input);
            case 'validar_reserva':
                return $this->validarReserva($input);
            case 'verificar_disponibilidade':
                return $this->verificarDisponibilidade($input);
            default:
                return $this->errorResponse('Ação não encontrada', 404);
        }
    }

    /**
     * Verifica conflitos de reserva
     */
    private function verificarConflito($data)
    {
        try {
            $dataReserva = $data['data'] ?? '';
            $horaInicio = $data['hora_inicio'] ?? '';
            $horaFim = $data['hora_fim'] ?? '';
            $auditorio = $data['auditorio'] ?? 'principal';
            $reservaId = $data['reserva_id'] ?? null;

            // Validar dados obrigatórios
            if (empty($dataReserva) || empty($horaInicio) || empty($horaFim)) {
                return $this->errorResponse('Dados obrigatórios não informados', 400);
            }

            // Validar formato da data
            if (!$this->validarData($dataReserva)) {
                return $this->errorResponse('Formato de data inválido', 400);
            }

            // Validar horários
            if (!$this->validarHorario($horaInicio) || !$this->validarHorario($horaFim)) {
                return $this->errorResponse('Formato de horário inválido', 400);
            }

            // Verificar se hora fim é maior que hora início
            if ($horaFim <= $horaInicio) {
                return $this->errorResponse('Hora de fim deve ser maior que hora de início', 400);
            }

            // Verificar se a data não é no passado
            if (strtotime($dataReserva) < strtotime(date('Y-m-d'))) {
                return $this->errorResponse('Não é possível fazer reservas para datas passadas', 400);
            }

            // Verificar conflito no banco de dados
            $conflito = $this->reservaModel->verificarConflito(
                $dataReserva,
                $horaInicio,
                $horaFim,
                $auditorio,
                $reservaId
            );

            if ($conflito) {
                return $this->successResponse([
                    'conflito' => true,
                    'message' => 'Já existe uma reserva para este horário',
                    'detalhes' => [
                        'data' => $dataReserva,
                        'hora_inicio' => $horaInicio,
                        'hora_fim' => $horaFim,
                        'auditorio' => $auditorio
                    ]
                ]);
            }

            return $this->successResponse([
                'conflito' => false,
                'message' => 'Horário disponível',
                'disponivel' => true
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao verificar conflito: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validação completa de uma reserva
     */
    private function validarReserva($data)
    {
        try {
            $erros = [];
            $avisos = [];

            // Validações básicas
            if (empty($data['data'])) {
                $erros[] = 'Data é obrigatória';
            } elseif (!$this->validarData($data['data'])) {
                $erros[] = 'Formato de data inválido (esperado: YYYY-MM-DD)';
            }

            if (empty($data['hora_inicio'])) {
                $erros[] = 'Hora de início é obrigatória';
            } elseif (!$this->validarHorario($data['hora_inicio'])) {
                $erros[] = 'Formato de hora de início inválido (esperado: HH:MM)';
            }

            if (empty($data['hora_fim'])) {
                $erros[] = 'Hora de fim é obrigatória';
            } elseif (!$this->validarHorario($data['hora_fim'])) {
                $erros[] = 'Formato de hora de fim inválido (esperado: HH:MM)';
            }

            if (empty($data['descricao'])) {
                $erros[] = 'Descrição é obrigatória';
            } elseif (strlen($data['descricao']) < 10) {
                $erros[] = 'Descrição deve ter pelo menos 10 caracteres';
            }

            // Se há erros básicos, retorna sem fazer outras validações
            if (!empty($erros)) {
                return $this->errorResponse(implode(', ', $erros), 400);
            }

            // Validações de regras de negócio
            $dataReserva = $data['data'];
            $horaInicio = $data['hora_inicio'];
            $horaFim = $data['hora_fim'];

            // Verificar se data não é no passado
            if (strtotime($dataReserva) < strtotime(date('Y-m-d'))) {
                $erros[] = 'Não é possível fazer reservas para datas passadas';
            }

            // Verificar se é fim de semana
            $diaSemana = date('w', strtotime($dataReserva));
            if ($diaSemana == 0 || $diaSemana == 6) {
                $avisos[] = 'Reserva para fim de semana pode precisar de autorização especial';
            }

            // Verificar horário de funcionamento (08:00 às 22:00)
            if ($horaInicio < '08:00' || $horaFim > '22:00') {
                $avisos[] = 'Horário fora do funcionamento padrão (08:00 às 22:00)';
            }

            // Verificar duração mínima (30 minutos)
            $duracaoMinutos = (strtotime($horaFim) - strtotime($horaInicio)) / 60;
            if ($duracaoMinutos < 30) {
                $erros[] = 'Duração mínima da reserva é de 30 minutos';
            }

            // Verificar duração máxima (8 horas)
            if ($duracaoMinutos > 480) {
                $avisos[] = 'Reserva de longa duração (mais de 8 horas) pode precisar de autorização';
            }

            // Verificar conflito
            $conflito = $this->reservaModel->verificarConflito(
                $dataReserva,
                $horaInicio,
                $horaFim,
                $data['auditorio'] ?? 'principal',
                $data['reserva_id'] ?? null
            );

            if ($conflito) {
                $erros[] = 'Já existe uma reserva para este horário';
            }

            return $this->successResponse([
                'valida' => empty($erros),
                'erros' => $erros,
                'avisos' => $avisos,
                'duracao_minutos' => $duracaoMinutos,
                'fim_de_semana' => ($diaSemana == 0 || $diaSemana == 6),
                'fora_horario_padrao' => ($horaInicio < '08:00' || $horaFim > '22:00')
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao validar reserva: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verifica disponibilidade de um período
     */
    private function verificarDisponibilidade($data)
    {
        try {
            $dataInicio = $data['data_inicio'] ?? date('Y-m-d');
            $dataFim = $data['data_fim'] ?? date('Y-m-d', strtotime('+7 days'));
            $auditorio = $data['auditorio'] ?? 'principal';

            $disponibilidade = $this->reservaModel->verificarDisponibilidadePeriodo(
                $dataInicio,
                $dataFim,
                $auditorio
            );

            return $this->successResponse([
                'periodo' => [
                    'data_inicio' => $dataInicio,
                    'data_fim' => $dataFim
                ],
                'auditorio' => $auditorio,
                'disponibilidade' => $disponibilidade
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao verificar disponibilidade: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Lista reservas públicas
     */
    private function listarReservas()
    {
        try {
            $reservas = $this->reservaController->listarReservasPublicas();

            return $this->successResponse([
                'reservas' => $reservas,
                'total' => count($reservas)
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao listar reservas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtém estatísticas
     */
    private function obterEstatisticas()
    {
        try {
            $estatisticas = $this->reservaController->obterEstatisticas();

            return $this->successResponse($estatisticas);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao obter estatísticas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Valida formato de data (YYYY-MM-DD)
     */
    private function validarData($data)
    {
        $d = DateTime::createFromFormat('Y-m-d', $data);
        return $d && $d->format('Y-m-d') === $data;
    }

    /**
     * Valida formato de horário (HH:MM)
     */
    private function validarHorario($horario)
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $horario);
    }

    /**
     * Retorna resposta de sucesso
     */
    private function successResponse($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        return true;
    }

    /**
     * Retorna resposta de erro
     */
    private function errorResponse($message, $code = 400)
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        return false;
    }
}

// Executar API
$api = new ReservaAPI();
$api->processRequest();
?>
