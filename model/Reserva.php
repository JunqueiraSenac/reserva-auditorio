<?php
require_once 'Conexao.php';

class Reserva {
    private $conn;
    
    public function __construct() {
        $this->conn = Conexao::getInstance()->getConnection();
    }
    
    public function criar($usuario_id, $data, $hora_inicio, $hora_fim, $descricao) {
        // Verifica se já existe reserva no horário
        if ($this->verificarDisponibilidade($data, $hora_inicio, $hora_fim)) {
            $sql = "INSERT INTO reservas (usuario_id, data, hora_inicio, hora_fim, descricao, status) 
                    VALUES (:usuario_id, :data, :hora_inicio, :hora_fim, :descricao, 'pendente')";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fim', $hora_fim);
            $stmt->bindParam(':descricao', $descricao);
            
            return $stmt->execute();
        }
        
        return false;
    }
    
    public function verificarDisponibilidade($data, $hora_inicio, $hora_fim, $reserva_id = null) {
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
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fim', $hora_fim);
        
        if ($reserva_id) {
            $stmt->bindParam(':reserva_id', $reserva_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn() == 0;
    }
    
    public function listarPorUsuario($usuario_id) {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone 
                FROM reservas r 
                JOIN usuarios u ON r.usuario_id = u.id 
                WHERE r.usuario_id = :usuario_id 
                ORDER BY r.data DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarTodas() {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone 
                FROM reservas r 
                JOIN usuarios u ON r.usuario_id = u.id 
                ORDER BY r.data DESC, r.hora_inicio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function atualizarStatus($id, $status) {
        $sql = "UPDATE reservas SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function deletar($id) {
        $sql = "DELETE FROM reservas WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT r.*, u.nome as usuario_nome, u.telefone as usuario_telefone 
                FROM reservas r 
                JOIN usuarios u ON r.usuario_id = u.id 
                WHERE r.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obterEstatisticas() {
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
    
    public function obterReservasPorMes() {
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
    
    public function listarReservasPublicas() {
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
