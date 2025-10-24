<?php
require_once __DIR__ . '/../model/Usuario.php';

class UsuarioController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    public function cadastrar($nome, $email, $senha, $telefone, $solicitar_instrutor = true) {
        $tipo = 'instrutor';
        // Todos podem fazer login, mas só aprovados podem fazer reservas
        $status_aprovacao = $solicitar_instrutor ? 'pendente' : 'aprovado';
        
        return $this->usuarioModel->criar($nome, $email, $senha, $telefone, $tipo, $status_aprovacao);
    }
    
    public function listarTodos() {
        return $this->usuarioModel->listarTodos();
    }
    
    public function buscarPorId($id) {
        return $this->usuarioModel->buscarPorId($id);
    }
}

// Processa requisições de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
    $controller = new UsuarioController();
    
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $solicitar_instrutor = isset($_POST['solicitar_instrutor']);
    
    if ($controller->cadastrar($nome, $email, $senha, $telefone, $solicitar_instrutor)) {
        header('Location: ../view/login.php?cadastro=sucesso');
        exit;
    } else {
        header('Location: ../view/cadastro.php?erro=1');
        exit;
    }
}
?>
