<?php
require_once __DIR__ . '/../model/Usuario.php';

class SolicitacaoController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    public function listarSolicitacoes() {
        return $this->usuarioModel->listarSolicitacoesInstrutores();
    }
    
    public function aprovarSolicitacao($id) {
        return $this->usuarioModel->aprovarInstrutor($id);
    }
    
    public function rejeitarSolicitacao($id) {
        return $this->usuarioModel->rejeitarInstrutor($id);
    }
}

// Processa requisições de aprovação/rejeição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    
    // Verifica se é admin
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header('Location: ../view/login.php');
        exit;
    }
    
    $controller = new SolicitacaoController();
    $id = $_POST['id'] ?? 0;
    
    switch ($_POST['action']) {
        case 'aprovar_solicitacao':
            if ($controller->aprovarSolicitacao($id)) {
                header('Location: ../view/painel_admin.php?sucesso=aprovado');
            } else {
                header('Location: ../view/painel_admin.php?erro=aprovacao');
            }
            exit;
            
        case 'rejeitar_solicitacao':
            if ($controller->rejeitarSolicitacao($id)) {
                header('Location: ../view/painel_admin.php?sucesso=rejeitado');
            } else {
                header('Location: ../view/painel_admin.php?erro=rejeicao');
            }
            exit;
    }
}
?>
