-- SQL Super Simples - Sistema SENAC
-- Execute linha por linha se der erro

CREATE DATABASE reserva_auditorio;

USE reserva_auditorio;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  telefone VARCHAR(20),
  tipo_usuario VARCHAR(20) DEFAULT 'instrutor',
  status_aprovacao VARCHAR(20) DEFAULT 'pendente'
);

CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  data DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fim TIME NOT NULL,
  descricao TEXT NOT NULL,
  status VARCHAR(20) DEFAULT 'pendente'
);

INSERT INTO usuarios (nome, email, senha, telefone, tipo_usuario, status_aprovacao) VALUES
('Admin SENAC', 'admin@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11999999999', 'admin', 'aprovado'),
('João Silva', 'joao@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11988888888', 'instrutor', 'aprovado');

INSERT INTO reservas (usuario_id, data, hora_inicio, hora_fim, descricao, status) VALUES
(2, CURDATE(), '09:00:00', '11:00:00', 'Palestra Marketing Digital', 'aprovada'),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '16:00:00', 'Workshop Programação', 'aprovada'),
(2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00:00', '12:00:00', 'Curso Gestão Projetos', 'pendente');
