<?php
require_once "Conexao.php";

class Reserva
{
    private $conn;

    public function __construct()
    {
        $this->conn = Conexao::getInstance()->getConnection();
    }

    public function criar(
        $usuario_id,
        $data,
        $hora_inicio,
        $hora_fim,
        $descricao,
    ) {
        // Verifica se já existe reserva no horário
        if ($this->verificarDisponibilidade($data, $hora_inicio, $hora_fim)) {
            $sql = "INSERT INTO reservas (usuario_id, data, hora_inicio, hora_fim, descricao, status)
                    VALUES (:usuario_id, :data, :hora_inicio, :hora_fim, :descricao, 'pendente')";
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(":usuario_id", $usuario_id);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":hora_inicio", $hora_inicio);
            $stmt->bindParam(":hora_fim", $hora_fim);
            $stmt->bindParam(":descricao", $descricao);

            return $stmt->execute();
        }

        return false;
    }

    public function verificarDisponibilidade(
        $data,
        $hora_inicio,
        $hora_fim,
        $reserva_id = null,
    ) {
        $sql = "SELECT COUNT(*) FROM reservas
                WHERE data = :data
                AND status != 'cancelada'
                AND (
                    (hora_inicio < :hora_fim AND hora_fim > :hora_inicio)
                )";

        if ($reserva_id) {
            $sql .= " AND id != :reserva_id";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":data", $data);
        $stmt->bindParam(":hora_inicio", $hora_inicio);
        $stmt->bindParam(":hora_fim", $hora_fim);

        if ($reserva_id) {
            $stmt->bindParam(":reserva_id", $reserva_id);
        }

        $stmt->execute();

        return $stmt->fetchColumn() == 0;
    }

    /**
     * Verifica conflito de reserva com detalhes
     */
    public function verificarConflito(
        $data,
        $hora_inicio,
        $hora_fim,
        $auditorio = "principal",
        $reserva_id = null,
    ) {
        $sql = "SELECT r.*, u.nome as usuario_nome
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.data = :data
                AND r.status IN ('aprovada', 'pendente')
                AND (
                    (r.hora_inicio < :hora_fim AND r.hora_fim > :hora_inicio)
                )";

        if ($reserva_id) {
            $sql .= " AND r.id != :reserva_id";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":data", $data);
        $stmt->bindParam(":hora_inicio", $hora_inicio);
        $stmt->bindParam(":hora_fim", $hora_fim);

        if ($reserva_id) {
            $stmt->bindParam(":reserva_id", $reserva_id);
        }

        $stmt->execute();
        $conflitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return !empty($conflitos) ? $conflitos[0] : false;
    }

    /**
     * Verifica disponibilidade de um período específico
     */
    public function verificarDisponibilidadePeriodo(
        $data_inicio,
        $data_fim,
        $auditorio = "principal",
    ) {
        $sql = "SELECT r.data, r.hora_inicio, r.hora_fim, r.descricao, r.status, u.nome as usuario_nome
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.data BETWEEN :data_inicio AND :data_fim
                AND r.status IN ('aprovada', 'pendente')
                ORDER BY r.data ASC, r.hora_inicio ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":data_inicio", $data_inicio);
        $stmt->bindParam(":data_fim", $data_fim);
        $stmt->execute();

        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Organizar por data
        $disponibilidade = [];
        $current = strtotime($data_inicio);
        $end = strtotime($data_fim);

        while ($current <= $end) {
            $dataAtual = date("Y-m-d", $current);
            $disponibilidade[$dataAtual] = [
                "data" => $dataAtual,
                "dia_semana" => date("w", $current),
                "reservas" => [],
                "disponivel" => true,
            ];
            $current = strtotime("+1 day", $current);
        }

        // Adicionar reservas às datas
        foreach ($reservas as $reserva) {
            $data = $reserva["data"];
            if (isset($disponibilidade[$data])) {
                $disponibilidade[$data]["reservas"][] = $reserva;
                $disponibilidade[$data]["disponivel"] = false;
            }
        }

        return array_values($disponibilidade);
    }

    /**
     * Busca horários livres em uma data específica
     */
    public function buscarHorariosLivres(
        $data,
        $duracao_minutos = 60,
        $horario_inicio = "08:00",
        $horario_fim = "22:00",
    ) {
        $sql = "SELECT hora_inicio, hora_fim
                FROM reservas
                WHERE data = :data
                AND status IN ('aprovada', 'pendente')
                ORDER BY hora_inicio ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":data", $data);
        $stmt->execute();

        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $horariosLivres = [];

        $inicioTrabalho = strtotime($data . " " . $horario_inicio);
        $fimTrabalho = strtotime($data . " " . $horario_fim);

        $horaAtual = $inicioTrabalho;

        foreach ($reservas as $reserva) {
            $inicioReserva = strtotime($data . " " . $reserva["hora_inicio"]);
            $fimReserva = strtotime($data . " " . $reserva["hora_fim"]);

            // Verificar espaço antes da reserva
            if ($inicioReserva - $horaAtual >= $duracao_minutos * 60) {
                $horariosLivres[] = [
                    "inicio" => date("H:i", $horaAtual),
                    "fim" => date("H:i", $inicioReserva),
                    "duracao_minutos" => ($inicioReserva - $horaAtual) / 60,
                ];
            }

            $horaAtual = max($horaAtual, $fimReserva);
        }

        // Verificar espaço após a última reserva
        if ($fimTrabalho - $horaAtual >= $duracao_minutos * 60) {
            $horariosLivres[] = [
                "inicio" => date("H:i", $horaAtual),
                "fim" => date("H:i", $fimTrabalho),
                "duracao_minutos" => ($fimTrabalho - $horaAtual) / 60,
            ];
        }

        return $horariosLivres;
    }

    public function listarPorUsuario($usuario_id)
    {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.usuario_id = :usuario_id
                ORDER BY r.data DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas()
    {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                ORDER BY r.data DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatus($id, $status)
    {
        $sql = "UPDATE reservas SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function deletar($id)
    {
        $sql = "DELETE FROM reservas WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obterEstatisticas()
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                    SUM(CASE WHEN status = 'aprovada' THEN 1 ELSE 0 END) as aprovadas,
                    SUM(CASE WHEN status = 'rejeitada' THEN 1 ELSE 0 END) as rejeitadas,
                    SUM(CASE WHEN status = 'cancelada' THEN 1 ELSE 0 END) as canceladas
                FROM reservas";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obterReservasPorMes()
    {
        $sql = "SELECT
                    DATE_FORMAT(data, '%Y-%m') as mes,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'aprovada' THEN 1 ELSE 0 END) as aprovadas
                FROM reservas
                WHERE data >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes
                ORDER BY mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarReservasPublicas()
    {
        $sql = "SELECT r.*, u.nome as usuario_nome
                FROM reservas r
                JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.data >= CURDATE()
                AND r.status IN ('aprovada', 'pendente')
                ORDER BY r.data ASC, r.hora_inicio ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
