# 🏛️ Sistema de Reserva de Auditório - SENAC Umuarama

<div align="center">

**Sistema profissional de gerenciamento de reservas de auditório**

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-SENAC-green.svg)](LICENSE)

</div>

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Características](#-características)
- [Tecnologias](#-tecnologias)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Funcionalidades](#-funcionalidades)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Licença](#-licença)

---

## 🎯 Sobre o Projeto

Sistema web completo e profissional desenvolvido para o **SENAC Umuarama - Paraná** para gerenciar reservas do auditório institucional. O projeto utiliza arquitetura MVC em PHP e destaca-se pela integração com WhatsApp Cloud API para notificações automáticas.

### ✨ Diferenciais

- 🎨 **Design Moderno**: Interface profissional com Tailwind CSS
- 🌙 **Dark Mode**: Tema escuro com azul institucional SENAC
- 📱 **100% Responsivo**: Perfeito em qualquer dispositivo
- 💬 **Notificações WhatsApp**: Confirmações automáticas via API
- 📅 **FullCalendar**: Visualização interativa de eventos
- ⚡ **Alta Performance**: Otimizado para carregamento rápido
- 🔒 **Seguro**: Proteção contra XSS, SQL Injection e CSRF

---

## 🚀 Características

### Interface do Usuário
- ✅ Design moderno e profissional
- ✅ Identidade visual SENAC (azul #004A8D, laranja #F26C21)
- ✅ Dark mode com tema azul escuro institucional
- ✅ Animações suaves e transições elegantes
- ✅ Responsivo para mobile, tablet e desktop

### Funcionalidades Core
- ✅ Sistema de autenticação completo
- ✅ CRUD de reservas com validações
- ✅ Calendário interativo (FullCalendar)
- ✅ Aprovação de reservas por administradores
- ✅ Notificações automáticas via WhatsApp
- ✅ Prevenção de conflitos de horário
- ✅ Dashboard com estatísticas

### Experiência do Usuário
- ✅ Feedback visual em tempo real
- ✅ Validação de formulários robusta
- ✅ Indicador de força de senha
- ✅ Máscaras de input (telefone, CPF)
- ✅ Loading states e spinners
- ✅ Mensagens de erro descritivas
- ✅ Atalhos de teclado (Alt+D para dark mode)

---

## 🛠️ Tecnologias

### Backend
- **PHP 8.0+** - Linguagem principal
- **MySQL** - Banco de dados relacional
- **PDO** - Camada de abstração de dados
- **MVC** - Padrão de arquitetura

### Frontend
- **HTML5** - Estrutura semântica
- **Tailwind CSS** - Framework CSS utilitário
- **JavaScript ES6+** - Interatividade
- **Font Awesome 6** - Ícones vetoriais
- **FullCalendar 6** - Calendário interativo

### APIs e Integrações
- **WhatsApp Cloud API** - Notificações automáticas
- **Google Fonts (Inter)** - Tipografia moderna

---

## 📦 Instalação

### Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx (XAMPP recomendado)

### Instalação com XAMPP

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/reserva-auditorio.git
```

2. **Copie para o diretório do XAMPP**
```bash
# Windows
xcopy . C:\xampp\htdocs\reserva-auditorio\ /E /I

# Linux/Mac
cp -r . /opt/lampp/htdocs/reserva-auditorio/
```

3. **Inicie o XAMPP**
- Inicie Apache e MySQL pelo painel de controle

4. **Importe o banco de dados**
- Acesse `http://localhost/phpmyadmin`
- Crie um banco chamado `reserva_auditorio`
- Importe o arquivo `database.sql`

5. **Configure a conexão**
- Edite `model/Conexao.php` se necessário:
```php
private $host = "localhost";
private $dbname = "reserva_auditorio";
private $username = "root";
private $password = "";
```

6. **Acesse o sistema**
```
http://localhost/reserva-auditorio/
```

---

## ⚙️ Configuração

### Credenciais Padrão

Após a instalação, use estas credenciais:

**Administrador:**
- Email: `admin@senac.com`
- Senha: `admin123`

**Instrutor:**
- Email: `joao@senac.com`
- Senha: `instrutor123`

### WhatsApp API (Opcional)

Para habilitar notificações via WhatsApp:

1. Acesse [Meta for Developers](https://developers.facebook.com/)
2. Crie um app Business
3. Configure WhatsApp Cloud API
4. Obtenha o Token e Phone Number ID
5. Edite `services/WhatsAppService.php`:

```php
private $token = 'SEU_TOKEN_AQUI';
private $phoneNumberId = 'SEU_PHONE_ID_AQUI';
```

---

## 🎯 Funcionalidades

### Para Visitantes
- 👀 Visualizar agenda pública de eventos
- 📅 Ver calendário interativo
- 🔍 Buscar eventos por data
- 📱 Acesso responsivo em qualquer dispositivo

### Para Instrutores
- ➕ Criar novas reservas
- ✏️ Editar reservas pendentes
- 🗑️ Cancelar reservas próprias
- 📊 Visualizar histórico de reservas
- 💬 Receber notificações no WhatsApp

### Para Administradores
- ✅ Aprovar/rejeitar reservas
- 👥 Gerenciar usuários
- 📊 Visualizar estatísticas completas
- 📈 Dashboard com gráficos
- 🔧 Configurações do sistema

---

## 📁 Estrutura do Projeto

```
reserva-auditorio/
├── 📂 api/                     # Endpoints REST
│   └── reserva.php            # API de reservas
├── 📂 controller/              # Controladores MVC
│   ├── LoginController.php
│   ├── ReservaController.php
│   ├── UsuarioController.php
│   └── SolicitacaoController.php
├── 📂 model/                   # Modelos de dados
│   ├── Conexao.php            # Conexão com BD
│   ├── Usuario.php            # Model de usuário
│   └── Reserva.php            # Model de reserva
├── 📂 view/                    # Views (interface)
│   ├── login.php              # Página de login
│   ├── cadastro.php           # Página de cadastro
│   ├── painel_admin.php       # Painel administrativo
│   └── painel_instrutor.php   # Painel do instrutor
├── 📂 includes/                # Componentes reutilizáveis
│   ├── header.php             # Cabeçalho
│   └── footer.php             # Rodapé
├── 📂 public/                  # Arquivos públicos
│   ├── 📂 css/                # Estilos
│   │   ├── style.css
│   │   ├── admin.css
│   │   ├── modern-style.css
│   │   └── global-optimized.css
│   ├── 📂 js/                 # JavaScript
│   │   ├── app.js             # JavaScript principal
│   │   ├── calendar.js        # Calendário
│   │   ├── config.js          # Configurações
│   │   ├── animations.js      # Animações
│   │   └── gsap.min.js        # Biblioteca GSAP
│   ├── 📂 images/             # Imagens
│   │   ├── logo-senac.png
│   │   └── placeholder-logo.svg
│   └── 📂 vendor/             # Bibliotecas externas
│       └── fullcalendar/      # FullCalendar
├── 📂 services/               # Serviços externos
│   └── WhatsAppService.php    # Integração WhatsApp
├── 📄 index.php               # Redirecionamento inicial
├── 📄 home.php                # Página inicial
├── 📄 calendario.php          # Calendário de eventos
├── 📄 database.sql            # Estrutura do banco
├── 📄 update_database.sql     # Atualizações do banco
└── 📄 README.md               # Este arquivo
```

---

## 🔧 Solução de Problemas

### Erro de conexão com banco
- Verifique se MySQL está rodando
- Confirme credenciais em `model/Conexao.php`
- Certifique-se que o banco `reserva_auditorio` existe

### WhatsApp não envia mensagens
- Verifique token da API no WhatsAppService
- Confirme Phone Number ID
- Teste a API diretamente

### Página não carrega CSS
- Limpe cache do navegador (Ctrl+Shift+Delete)
- Verifique permissões da pasta `public/`
- Inspecione console (F12) para erros

---

## 📞 Contato

- 🏫 **SENAC Umuarama - Paraná**
- 📧 Email: contato@senacumuarama.com.br
- 🌐 Site: [www.senacumuarama.com.br](https://www.senacumuarama.com.br)

---

## 📄 Licença

Este projeto foi desenvolvido exclusivamente para o **SENAC Umuarama - Paraná** e é de propriedade da instituição.

**© 2024 SENAC Umuarama. Todos os direitos reservados.**

---

## 🙏 Agradecimentos

Desenvolvido com ❤️ para a comunidade educacional do SENAC Umuarama.

### Tecnologias de Terceiros
- [Tailwind CSS](https://tailwindcss.com) - Framework CSS
- [FullCalendar](https://fullcalendar.io) - Biblioteca de calendário
- [Font Awesome](https://fontawesome.com) - Ícones
- [Google Fonts](https://fonts.google.com) - Tipografia

---

<div align="center">

**Desenvolvido para educação de qualidade** 💙

[⬆ Voltar ao topo](#-sistema-de-reserva-de-auditório---senac-umuarama)

</div>
