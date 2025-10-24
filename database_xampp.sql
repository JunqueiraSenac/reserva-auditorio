-- Database SQL para XAMPP - Sistema de Reserva SENAC
-- Execute este arquivo no phpMyAdmin do XAMPP

-- Criar banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS `reserva_auditorio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `reserva_auditorio`;

-- Limpar tabelas se existirem (para evitar conflitos)
DROP TABLE IF EXISTS `reservas`;
DROP TABLE IF EXISTS `usuarios`;

-- Tabela de usuários
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo_usuario` enum('admin','instrutor') DEFAULT 'instrutor',
  `status_aprovacao` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar índice único para email depois da criação
ALTER TABLE `usuarios` ADD UNIQUE KEY `unique_email` (`email`);

-- Tabela de reservas
CREATE TABLE `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `descricao` text NOT NULL,
  `status` enum('pendente','aprovada','rejeitada','cancelada') DEFAULT 'pendente',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_data` (`data`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar chave estrangeira
ALTER TABLE `reservas` ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

-- Inserir dados padrão (senhas criptografadas para: admin123 e instrutor123)
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `tipo_usuario`, `status_aprovacao`) VALUES
(1, 'Administrador SENAC', 'admin@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 99999-9999', 'admin', 'aprovado'),
(2, 'João Silva', 'joao@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 98888-8888', 'instrutor', 'aprovado'),
(3, 'Maria Santos', 'maria@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 97777-7777', 'instrutor', 'aprovado'),
(4, 'Pedro Costa', 'pedro@senac.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(11) 96666-6666', 'instrutor', 'pendente');

-- Inserir reservas de exemplo
INSERT INTO `reservas` (`usuario_id`, `data`, `hora_inicio`, `hora_fim`, `descricao`, `status`) VALUES
(2, CURDATE(), '09:00:00', '11:00:00', 'Palestra sobre Marketing Digital - Introdução às estratégias digitais modernas', 'aprovada'),
(2, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '16:00:00', 'Workshop de Programação Web - HTML, CSS e JavaScript básico', 'aprovada'),
(3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00:00', '12:00:00', 'Curso de Gestão de Projetos - Metodologias ágeis e tradicionais', 'pendente'),
(3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '13:00:00', '17:00:00', 'Seminário de Empreendedorismo - Como iniciar seu próprio negócio', 'aprovada'),
(2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '10:00:00', '12:00:00', 'Treinamento de Vendas - Técnicas avançadas de negociação', 'pendente'),
(3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '15:00:00', '18:00:00', 'Workshop de Design Gráfico - Princípios básicos do design', 'aprovada'),
(2, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '09:30:00', '11:30:00', 'Curso de Excel Avançado - Fórmulas e macros para produtividade', 'pendente');

-- Verificar se tudo foi criado corretamente
SELECT 'Tabelas criadas com sucesso!' as status;
SELECT COUNT(*) as total_usuarios FROM usuarios;
SELECT COUNT(*) as total_reservas FROM reservas;

-- Mostrar credenciais de acesso
SELECT '=== CREDENCIAIS DE ACESSO ===' as info;
SELECT 'Admin: admin@senac.com / admin123' as login;
SELECT 'Instrutor: joao@senac.com / instrutor123' as login;
SELECT 'Instrutor: maria@senac.com / instrutor123' as login;
