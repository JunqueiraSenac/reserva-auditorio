<?php
require_once __DIR__ . '/../model/Usuario.php';

class LoginController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    public function login($email, $senha) {
        $usuario = $this->usuarioModel->autenticar($email, $senha);
        
        if ($usuario) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo'];
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ../view/login.php');
        exit;
    }
    
    public function verificarAutenticacao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: login.php');
            exit;
        }
    }
}

// Processa requisições de login/logout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new LoginController();
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if ($controller->login($email, $senha)) {
                header('Location: ../index.php');
                exit;
            } else {
                header('Location: ../view/login.php?erro=1');
                exit;
            }
        } elseif ($_POST['action'] === 'logout') {
            $controller->logout();
        }
    }
}
?>
