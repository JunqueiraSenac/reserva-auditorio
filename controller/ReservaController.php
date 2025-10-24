<?php
require_once __DIR__ . "/../model/Reserva.php";
require_once __DIR__ . "/../model/Usuario.php";
require_once __DIR__ . "/../services/WhatsAppService.php";

class ReservaController
{
    private $reservaModel;
    private $usuarioModel;
    private $whatsappService;

    public function __construct()
    {
        $this->reservaModel = new Reserva();
        $this->usuarioModel = new Usuario();
        $this->whatsappService = new WhatsAppService();
    }

    /**
     * Verifica se existe conflito de horário para uma reserva
     */
    public function verificarConflito(
        $data,
        $hora_inicio,
        $hora_fim,
        $auditorio = "principal",
        $reserva_id = null,
    ) {
        return $this->reservaModel->verificarConflito(
            $data,
            $hora_inicio,
            $hora_fim,
            $auditorio,
            $reserva_id,
        );
    }

    public function criar(
        $usuario_id,
        $data,
        $hora_inicio,
        $hora_fim,
        $descricao,
    ) {
        // Verifica se o usuário está aprovado para fazer reservas
        $usuario = $this->usuarioModel->buscarPorId($usuario_id);
        if (!$usuario || $usuario["status_aprovacao"] !== "aprovado") {
            return false; // Usuário não aprovado para fazer reservas
        }

        $resultado = $this->reservaModel->criar(
            $usuario_id,
            $data,
            $hora_inicio,
            $hora_fim,
            $descricao,
        );

        if ($resultado) {
            $reservas = $this->reservaModel->listarPorUsuario($usuario_id);
            if (!empty($reservas)) {
                $reserva = $reservas[0]; // Get the most recent reservation
                $this->whatsappService->enviarConfirmacaoReserva($reserva);
            }
        }

        return $resultado;
    }

    public function listarPorUsuario($usuario_id)
    {
        return $this->reservaModel->listarPorUsuario($usuario_id);
    }

    public function listarTodas()
    {
        return $this->reservaModel->listarTodas();
    }

    public function aprovar($id)
    {
        $resultado = $this->reservaModel->atualizarStatus($id, "aprovada");

        if ($resultado) {
            $reserva = $this->reservaModel->buscarPorId($id);
            $this->whatsappService->enviarStatusReserva($reserva, "aprovada");
        }

        return $resultado;
    }

    public function rejeitar($id)
    {
        $resultado = $this->reservaModel->atualizarStatus($id, "rejeitada");

        if ($resultado) {
            $reserva = $this->reservaModel->buscarPorId($id);
            $this->whatsappService->enviarStatusReserva($reserva, "rejeitada");
        }

        return $resultado;
    }

    public function cancelar($id)
    {
        return $this->reservaModel->atualizarStatus($id, "cancelada");
    }

    public function deletar($id)
    {
        return $this->reservaModel->deletar($id);
    }

    public function obterEstatisticas()
    {
        return $this->reservaModel->obterEstatisticas();
    }

    public function obterReservasPorMes()
    {
        return $this->reservaModel->obterReservasPorMes();
    }

    public function listarReservasPublicas()
    {
        return $this->reservaModel->listarReservasPublicas();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    session_start();
    $controller = new ReservaController();

    switch ($_POST["action"]) {
        case "criar":
            $usuario_id = $_SESSION["usuario_id"];
            $data = $_POST["data"] ?? "";
            $hora_inicio = $_POST["hora_inicio"] ?? "";
            $hora_fim = $_POST["hora_fim"] ?? "";
            $descricao = $_POST["descricao"] ?? "";

            if (
                $controller->criar(
                    $usuario_id,
                    $data,
                    $hora_inicio,
                    $hora_fim,
                    $descricao,
                )
            ) {
                header("Location: ../view/painel_instrutor.php?sucesso=1");
            } else {
                header("Location: ../view/painel_instrutor.php?erro=1");
            }
            exit();

        case "aprovar":
            $id = $_POST["id"] ?? 0;
            $controller->aprovar($id);
            header("Location: ../view/painel_admin.php");
            exit();

        case "rejeitar":
            $id = $_POST["id"] ?? 0;
            $controller->rejeitar($id);
            header("Location: ../view/painel_admin.php");
            exit();

        case "cancelar":
            $id = $_POST["id"] ?? 0;
            $controller->cancelar($id);
            header("Location: ../view/painel_instrutor.php");
            exit();
    }
}
?>
