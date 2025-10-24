🏛️ Reserva de Auditório - Sistema Web

Sistema web robusto e prático, desenvolvido para o gerenciamento de reservas de auditórios. O projeto utiliza o padrão de arquitetura MVC em PHP e possui como principal diferencial a integração de comunicação via WhatsApp Cloud API para notificação automática.

🔗 Projeto Online

O sistema está hospedado e pode ser acessado em:

👉 https://senachub.infinityfree.me/home.php

✨ Destaques do Projeto

    Comunicação Automatizada: Envio automático de mensagens de confirmação e status de reserva via WhatsApp Cloud API.

    Arquitetura Profissional: Código organizado, desacoplado e escalável, utilizando o padrão Model-View-Controller (MVC).

    Gestão Completa: Permite o controle total sobre o ciclo de vida da reserva (cadastro, edição, listagem e exclusão).

📝 Funcionalidades

Área	Funcionalidade	Descrição
Acesso	Autenticação de Usuários	Login seguro e cadastro de novos instrutores/administradores.
Reservas	CRUD Completo	Cadastro, visualização, edição e exclusão de agendamentos.
Visualização	Calendário de Eventos	Consulta rápida da disponibilidade do auditório por data e hora.
Notificação	Confirmação via WhatsApp	Envio automático de mensagens após a aprovação da reserva.
Relatórios	Listagem de Reservas	Visualização de reservas ativas e históricas.

⚙️ Tecnologias Utilizadas

O projeto é baseado nas seguintes tecnologias Full-Stack:
Categoria	Tecnologia	Detalhes
Backend	PHP 8.x	Linguagem principal para a lógica de negócio e controladores.
Banco de Dados	MySQL (via PDO)	Gerenciamento de dados com uso de Prepared Statements.
Frontend	HTML5, CSS3, JavaScript	Desenvolvimento da interface de usuário e interações.
Comunicação	WhatsApp Cloud API	Serviço essencial para a automação de notificações.
Ambiente Local	XAMPP/WAMP	Utilizado como servidor de desenvolvimento (Apache + MySQL).

🚀 Como Rodar Localmente

Siga os passos para configurar e executar o projeto em seu ambiente de desenvolvimento.

1. Pré-requisitos

    Servidor Web (XAMPP, WAMP ou Docker) com PHP 8.x.

    MySQL Server.

    Credenciais da WhatsApp Cloud API (Token e ID do Telefone).

2. Clonagem e Configuração

    Clone o repositório:
    Bash

git clone https://github.com/JunqueiraSenac/reserva-auditorio.git
cd reserva-auditorio

Configure o banco de dados:

    Crie um banco de dados MySQL (ex: reserva_auditorio).

    Importe o esquema inicial utilizando o script:
    Bash

        # Execute este script no seu gerenciador MySQL (phpMyAdmin, DBeaver, etc.)
        SOURCE database.sql;

    Ajuste as credenciais de conexão com o banco de dados no arquivo em lib/ (ou onde estiver sua configuração de conexão).

3. Configuração da API (WhatsApp)

Insira as credenciais da WhatsApp Cloud API nas variáveis de ambiente ou no arquivo de serviço responsável pela comunicação (services/whatsappService.php ou similar) para habilitar o envio de mensagens.

4. Execução

    Inicie o Apache e o MySQL no seu servidor local (XAMPP/WAMP).

    Acesse o projeto pelo navegador: http://localhost/reserva-auditorio/home.php

🗂️ Estrutura do Código

A organização em MVC permite uma separação clara de responsabilidades:

reserva-auditorio/
├── app/                  # Configurações e lógica de inicialização
├── components/           # Elementos de UI reutilizáveis (Frontend)
├── controller/           # CONTROLADORES: Regras de requisição e orquestração
├── model/                # MODELOS: Lógica de negócio e acesso ao Banco de Dados
├── view/                 # VIEWS: Interface do usuário (HTML/CSS)
├── lib/                  # Funções e classes auxiliares (Conexão DB, utilitários)
├── services/             # Lógica de APIs externas (WhatsApp Cloud API)
├── database.sql          # Script de criação das tabelas
└── index.php             # Ponto de entrada (Front Controller)

🤝 Contribuição

Contribuições são muito bem-vindas! Se você tiver sugestões, melhorias ou correções:

    Faça um Fork do projeto.

    Crie uma nova branch (git checkout -b feature/minha-melhoria).

    Commit suas alterações (git commit -m 'Adiciona funcionalidade X' ou git commit -m 'Corrige bug Y').

    Faça um Push para a branch.

    Abra um Pull Request.

👤 Autor

[Seu Nome ou Nome da Equipe]

📄 Licença

Este projeto é de código aberto e está sob a licença MIT.