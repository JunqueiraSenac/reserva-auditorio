-- Script para atualizar banco de dados existente
-- Execute este script se o banco já existir

USE if0_40245163_reserva_auditorio;

-- Adicionar coluna status_aprovacao se não existir
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS status_aprovacao ENUM('pendente', 'aprovado', 'rejeitado') NOT NULL DEFAULT 'pendente';

-- Adicionar índice se não existir
ALTER TABLE usuarios 
ADD INDEX IF NOT EXISTS idx_status_aprovacao (status_aprovacao);

-- Atualizar usuários existentes para status aprovado
UPDATE usuarios 
SET status_aprovacao = 'aprovado' 
WHERE status_aprovacao = 'pendente' 
AND (tipo = 'admin' OR email IN ('admin@auditorio.com', 'joao@auditorio.com'));

-- NOTA: Agora todos podem fazer login, mas só usuários aprovados podem fazer reservas
-- O status_aprovacao controla apenas a capacidade de criar reservas, não o login
