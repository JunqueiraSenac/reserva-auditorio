<?php
require_once 'Conexao.php';

class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = Conexao::getInstance()->getConnection();
    }
    
    public function criar($nome, $email, $senha, $telefone, $tipo = 'instrutor', $status_aprovacao = 'pendente') {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo, status_aprovacao) VALUES (:nome, :email, :senha, :telefone, :tipo, :status_aprovacao)";
        $stmt = $this->conn->prepare($sql);
        
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':status_aprovacao', $status_aprovacao);
        
        return $stmt->execute();
    }
    
    public function autenticar($email, $senha) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        
        return false;
    }
    
    public function listarTodos() {
        $sql = "SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY nome";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT id, nome, email, telefone, tipo, status_aprovacao FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function listarSolicitacoesInstrutores() {
        $sql = "SELECT id, nome, email, telefone, criado_em FROM usuarios 
                WHERE tipo = 'instrutor' AND status_aprovacao = 'pendente' 
                ORDER BY criado_em ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function aprovarInstrutor($id) {
        $sql = "UPDATE usuarios SET status_aprovacao = 'aprovado' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function rejeitarInstrutor($id) {
        $sql = "UPDATE usuarios SET status_aprovacao = 'rejeitado' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>
