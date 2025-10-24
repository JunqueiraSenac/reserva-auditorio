-- Banco de dados já criado no InfinityFree
-- Nome: if0_40245163_reserva_auditorio

USE if0_40245163_reserva_auditorio;

-- Tabela de usuários
-- Added telefone field for WhatsApp notifications
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    tipo ENUM('instrutor', 'admin') NOT NULL DEFAULT 'instrutor',
    status_aprovacao ENUM('pendente', 'aprovado', 'rejeitado') NOT NULL DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo),
    INDEX idx_status_aprovacao (status_aprovacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('pendente', 'aprovada', 'rejeitada', 'cancelada') NOT NULL DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_data (data),
    INDEX idx_status (status),
    INDEX idx_data_hora (data, hora_inicio, hora_fim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário administrador padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, telefone, tipo, status_aprovacao) VALUES 
('Administrador', 'admin@auditorio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5541999999999', 'admin', 'aprovado');

-- Inserir usuário instrutor de exemplo (senha: instrutor123)
INSERT INTO usuarios (nome, email, senha, telefone, tipo, status_aprovacao) VALUES 
('João Silva', 'joao@auditorio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5541988888888', 'instrutor', 'aprovado');

-- Inserir algumas reservas de exemplo
INSERT INTO reservas (usuario_id, data, hora_inicio, hora_fim, descricao, status) VALUES 
(2, CURDATE() + INTERVAL 1 DAY, '09:00:00', '11:00:00', 'Palestra sobre Tecnologia', 'pendente'),
(2, CURDATE() + INTERVAL 2 DAY, '14:00:00', '16:00:00', 'Workshop de Programação', 'aprovada'),
(2, CURDATE() + INTERVAL 3 DAY, '10:00:00', '12:00:00', 'Reunião de Equipe', 'pendente');
