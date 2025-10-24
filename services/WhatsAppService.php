<?php

class WhatsAppService {
    private $apiUrl;
    private $apiToken;
    
    public function __construct() {
        // Configure your WhatsApp API credentials here
        // You can use services like Twilio, WhatsApp Business API, or Evolution API
        $this->apiUrl = getenv('WHATSAPP_API_URL') ?: 'https://api.whatsapp.com/send';
        $this->apiToken = getenv('WHATSAPP_API_TOKEN') ?: '';
    }
    
    /**
     * Send reservation confirmation message
     */
    public function enviarConfirmacaoReserva($reserva) {
        if (empty($reserva['usuario_telefone'])) {
            return false;
        }
        
        $mensagem = "🎯 *Reserva Solicitada - SENAC*\n\n";
        $mensagem .= "Olá, {$reserva['usuario_nome']}!\n\n";
        $mensagem .= "Sua solicitação de reserva foi recebida:\n\n";
        $mensagem .= "📅 Data: " . date('d/m/Y', strtotime($reserva['data'])) . "\n";
        $mensagem .= "🕐 Horário: " . substr($reserva['hora_inicio'], 0, 5) . " às " . substr($reserva['hora_fim'], 0, 5) . "\n";
        $mensagem .= "📝 Descrição: {$reserva['descricao']}\n\n";
        $mensagem .= "⏳ Status: Aguardando aprovação\n\n";
        $mensagem .= "Você será notificado quando sua reserva for aprovada ou rejeitada.";
        
        return $this->enviarMensagem($reserva['usuario_telefone'], $mensagem);
    }
    
    /**
     * Send reservation status update message
     */
    public function enviarStatusReserva($reserva, $status) {
        if (empty($reserva['usuario_telefone'])) {
            return false;
        }
        
        $emoji = $status === 'aprovada' ? '✅' : '❌';
        $statusTexto = $status === 'aprovada' ? 'APROVADA' : 'REJEITADA';
        
        $mensagem = "{$emoji} *Reserva {$statusTexto} - SENAC*\n\n";
        $mensagem .= "Olá, {$reserva['usuario_nome']}!\n\n";
        $mensagem .= "Sua reserva foi {$statusTexto}:\n\n";
        $mensagem .= "📅 Data: " . date('d/m/Y', strtotime($reserva['data'])) . "\n";
        $mensagem .= "🕐 Horário: " . substr($reserva['hora_inicio'], 0, 5) . " às " . substr($reserva['hora_fim'], 0, 5) . "\n";
        $mensagem .= "📝 Descrição: {$reserva['descricao']}\n\n";
        
        if ($status === 'aprovada') {
            $mensagem .= "O auditório está reservado para você! 🎉";
        } else {
            $mensagem .= "Entre em contato com a administração para mais informações.";
        }
        
        return $this->enviarMensagem($reserva['usuario_telefone'], $mensagem);
    }
    
    /**
     * Send WhatsApp message using API
     */
    private function enviarMensagem($telefone, $mensagem) {
        // Remove non-numeric characters from phone number
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        // For development/testing, just log the message
        error_log("WhatsApp para {$telefone}: {$mensagem}");
        
        // Uncomment and configure for production use with your WhatsApp API
        /*
        $data = [
            'phone' => $telefone,
            'message' => $mensagem
        ];
        
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
        */
        
        return true;
    }
}
?>
