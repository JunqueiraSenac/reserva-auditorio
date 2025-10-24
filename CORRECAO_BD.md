# Correção de Banco de Dados (BD) — Alinhamento de Colunas e Queries

Atualizado em: 2025-10-24
Status: Concluído
Ambiente: XAMPP (desenvolvimento) e InfinityFree (produção)

============================================================

1) Resumo do problema

- Erros identificados em tempo de execução (PDO/MySQL):
  - Unknown column 'tipo' in 'field list'
  - Unknown column 'criado_em' in 'field list'
- Causa raiz: divergência entre o schema do banco de dados e as consultas no código.
  - Alguns scripts SQL usam as colunas: tipo, criado_em, atualizado_em
  - O banco ativo no XAMPP usa: tipo_usuario, created_at, updated_at

Essas inconsistências fizeram com que as queries em model/Usuario.php acessassem colunas que não existem no banco em uso.

============================================================

2) Diagnóstico do schema

- database_xampp.sql (padrão XAMPP):
  - Colunas: tipo_usuario, status_aprovacao, created_at, updated_at
- database.sql (variante anterior):
  - Colunas: tipo, status_aprovacao, criado_em, atualizado_em
- database_simple.sql (exemplo simplificado):
  - Colunas: tipo_usuario, status_aprovacao

Conclusão: o banco utilizado localmente (XAMPP) segue o padrão tipo_usuario e created_at/updated_at.

============================================================

3) Estratégia de correção

Para manter compatibilidade com o restante do código (controllers/views) sem breaking changes, adotamos aliases em SELECTs:

- Padronização no BD: usar tipo_usuario, created_at, updated_at
- Compatibilidade no código: mapear tipo_usuario AS tipo e created_at AS criado_em

Assim, o restante do sistema continua acessando $usuario['tipo'] e (onde aplicável) campos de data como criado_em, mesmo que as colunas reais no banco sejam tipo_usuario e created_at.

============================================================

4) Alterações aplicadas

Arquivo: model/Usuario.php

- criar()
  - Antes: INSERT INTO usuarios (..., tipo, ...) VALUES (...)
  - Depois: INSERT INTO usuarios (..., tipo_usuario, ...) VALUES (...)
  - Observação: o parâmetro PHP continua chamado :tipo; apenas a coluna no SQL foi trocada por tipo_usuario.

- autenticar()
  - Antes: SELECT * FROM usuarios WHERE email = :email
  - Depois: SELECT id, nome, email, senha, telefone, tipo_usuario AS tipo, status_aprovacao, created_at AS criado_em FROM usuarios WHERE email = :email

- listarTodos()
  - Antes: SELECT id, nome, email, tipo, criado_em FROM usuarios ORDER BY nome
  - Depois: SELECT id, nome, email, tipo_usuario AS tipo, created_at AS criado_em FROM usuarios ORDER BY nome

- buscarPorId($id)
  - Antes: SELECT id, nome, email, telefone, tipo, status_aprovacao FROM usuarios WHERE id = :id
  - Depois: SELECT id, nome, email, telefone, tipo_usuario AS tipo, status_aprovacao FROM usuarios WHERE id = :id

- listarSolicitacoesInstrutores()
  - Antes: SELECT id, nome, email, telefone, criado_em FROM usuarios WHERE tipo = 'instrutor' AND status_aprovacao = 'pendente' ORDER BY criado_em ASC
  - Depois: SELECT id, nome, email, telefone, created_at AS criado_em FROM usuarios WHERE tipo_usuario = 'instrutor' AND status_aprovacao = 'pendente' ORDER BY created_at ASC

- aprovarInstrutor($id) e rejeitarInstrutor($id)
  - Sem mudanças estruturais (apenas atualização de formatação/consistência).

Arquivo: update_database.sql

- Correção de referência no WHERE:
  - Antes: AND (tipo = 'admin' OR email IN (...))
  - Depois: AND (tipo_usuario = 'admin' OR email IN (...))

============================================================

5) Impacto em outros arquivos

- controller/LoginController.php
  - Usa $usuario['tipo']; permanece funcionando devido ao alias tipo_usuario AS tipo no SELECT.
- Demais controllers e views
  - Não exigiram alteração porque os aliases mantêm a interface de dados estável.

============================================================

6) Como verificar o banco de dados

No MySQL, execute:

- Conferir estrutura da tabela usuarios:
    DESCRIBE usuarios;

- Ver colunas relacionadas ao tipo:
    SHOW COLUMNS FROM usuarios LIKE 'tipo%';

- Ver colunas de criação/atualização:
    SHOW COLUMNS FROM usuarios LIKE '%created%';
    SHOW COLUMNS FROM usuarios LIKE '%updated%';
    SHOW COLUMNS FROM usuarios LIKE '%criado%';
    SHOW COLUMNS FROM usuarios LIKE '%atualizado%';

Resultado esperado (XAMPP): existirem tipo_usuario, created_at e updated_at; não existirem tipo, criado_em ou atualizado_em.

============================================================

7) Migração/Padronização (se necessário)

Caso seu banco esteja com colunas “antigas” (tipo, criado_em, atualizado_em), padronize para o formato atual. Faça backup antes.

- Renomear colunas em usuarios:
    ALTER TABLE usuarios CHANGE tipo tipo_usuario ENUM('admin','instrutor') DEFAULT 'instrutor';
    ALTER TABLE usuarios CHANGE criado_em created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
    ALTER TABLE usuarios CHANGE atualizado_em updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

- Índices recomendados:
    CREATE INDEX idx_email ON usuarios(email);
    CREATE INDEX idx_tipo_usuario ON usuarios(tipo_usuario);
    CREATE INDEX idx_status_aprovacao ON usuarios(status_aprovacao);

- Observação:
  - Se a tabela already possui índices equivalentes, ajuste os comandos conforme necessário (ou crie apenas os que faltam).

============================================================

8) Testes recomendados

- Login (Admin)
  - Credenciais padrão (seeds) dos scripts fornecidos:
    - Email: admin@senac.com
    - Senha: admin123
  - Resultado esperado: login bem-sucedido; sessão com $_SESSION['tipo_usuario'] populada a partir de $usuario['tipo'].

- Painel do Instrutor
  - Acessar após login; checar listagem/consulta de dados do usuário sem erros de coluna.

- Listagem de solicitações de instrutores
  - Verificar se a ordenação por created_at (exposta como criado_em via alias) funciona.

- Reservas
  - Criar, listar e validar que o fluxo não é impactado (o modelo de Reserva não depende de criado_em/updated_at diretamente nas queries).

============================================================

9) Boas práticas adotadas

- Aliases no SELECT garantem compatibilidade sem necessidade de alterar controllers/views.
- Padronização do schema para:
  - tipo_usuario (em vez de tipo)
  - created_at / updated_at (em vez de criado_em / atualizado_em)
- Queries mais explícitas (selecionando colunas nomeadas, evitando SELECT * onde relevante).

============================================================

10) Roadmap sugerido

Curto prazo:
- Executar os testes acima em ambientes local e remoto.
- Garantir que o banco remoto (produção) também esteja padronizado (tipo_usuario, created_at/updated_at).

Médio prazo:
- Consolidar um único arquivo de schema base (preferencialmente database_xampp.sql) como fonte de verdade.
- Adicionar um processo de migração controlado (ex.: scripts versionados de migração).

Longo prazo:
- Considerar adoção de um ORM ou biblioteca de migração (Doctrine Migrations, Phinx, etc.).
- Padronizar nomenclaturas para inglês em todo o schema, se for objetivo (ex.: user_type), com migração planejada.

============================================================

11) Troubleshooting

- Persistência do erro “Unknown column 'tipo'”:
  - Verifique se o schema em uso realmente possui tipo_usuario (e não tipo).
  - Confirme que a versão atualizada do model/Usuario.php está sendo carregada.

- Persistência do erro “Unknown column 'criado_em'”:
  - Verifique se a coluna created_at existe.
  - Cheque as queries do model/Usuario.php para confirmar o alias created_at AS criado_em está presente.

- Múltiplos scripts SQL conflitantes:
  - Escolha um padrão único (recomendado: tipo_usuario + created_at/updated_at).
  - Atualize ou descontinue scripts antigos que gerem tabelas divergentes.

============================================================

12) Conclusão

- As falhas reportadas foram causadas por divergências de nome de coluna entre diferentes versões de scripts SQL.
- O código foi ajustado para usar:
  - tipo_usuario no INSERT e nos filtros
  - Aliases: tipo_usuario AS tipo e created_at AS criado_em nos SELECTs
- update_database.sql foi corrigido para referenciar tipo_usuario.
- O sistema volta a operar com compatibilidade retroativa, sem necessidade de alterar controllers/views.

============================================================

13) Changelog

- 2025-10-24
  - model/Usuario.php:
    - Ajuste do INSERT para tipo_usuario
    - SELECTs usando tipo_usuario AS tipo
    - SELECTs usando created_at AS criado_em
    - Filtro de listarSolicitacoesInstrutores com tipo_usuario e ordenação por created_at
  - update_database.sql:
    - Correção do WHERE para usar tipo_usuario
  - Documentação:
    - Este arquivo criado/atualizado com análise, correções e guia de migração/testes

============================================================

14) Referências internas

- Banco (XAMPP): database_xampp.sql — referência principal para nomes de colunas
- Código:
  - model/Usuario.php (versão corrigida)
  - controller/LoginController.php (consome $usuario['tipo'] — compatível via alias)
  - model/Conexao.php (schema padrão utiliza created_at/updated_at e tipo_usuario)
