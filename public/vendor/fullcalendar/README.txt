FullCalendar - Local Vendor Assets (Offline/No-CDN)
===================================================

Este diretório armazena os arquivos locais do FullCalendar para uso sem CDN.
Recomendado quando:
- Sua rede bloqueia CDNs ou altera o MIME-Type (ex.: nosniff em text/plain).
- Você quer previsibilidade em produção (sem depender de terceiros).
- Políticas de segurança (CSP) exigem assets self-hosted.

Versão recomendada
- FullCalendar v6.1.10 (ou superior, desde que testado).
- Baixe sempre os arquivos compatíveis entre si.

Arquivos necessários (mínimo)
- index.global.min.css
- index.global.min.js

Como obter os arquivos
1) Site oficial/Distribuição:
   - unpkg:   https://unpkg.com/fullcalendar@6.1.10/
   - jsDelivr: https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/
   Procure por:
     - index.global.min.css
     - index.global.min.js

2) Salve os arquivos neste caminho:
   reserva-auditorio/public/vendor/fullcalendar/index.global.min.css
   reserva-auditorio/public/vendor/fullcalendar/index.global.min.js

Referência no código (calendario.php)
1) Substitua o uso do CDN pelos arquivos locais:
   <!-- CSS local -->
   <link rel="stylesheet" href="public/vendor/fullcalendar/index.global.min.css">
   <!-- JS local -->
   <script src="public/vendor/fullcalendar/index.global.min.js"></script>

2) (Opcional) Mantenha um fallback para CDN, caso o arquivo local falhe:
   <script>
     (function(){
       if (typeof FullCalendar === 'undefined') {
         var s = document.createElement('script');
         s.src = 'https://unpkg.com/fullcalendar@6.1.10/index.global.min.js';
         s.defer = true;
         s.onload = function(){ console.log('[fallback] FullCalendar carregado do CDN'); };
         document.head.appendChild(s);
       }
     })();
   </script>

Integração com o calendário do sistema
- O script principal do projeto (public/js/calendar.js) espera que o objeto global `FullCalendar` exista após o load de index.global.min.js.
- Eventos são injetados via `window.eventosCalendario = [...]` em calendario.php antes de carregar o calendar.js.
- Feriados podem ser mesclados nesse array antes da inicialização do calendário.

Dicas e Boas Práticas
- Limpe o cache do navegador após trocar de CDN para local (Ctrl+F5).
- Verifique no console que não há 404 para os arquivos locais.
- Teste em HTTP (XAMPP). Evite abrir arquivos via file:// para não incorrer em CORS/MIME estranhos.
- Mantenha a versão fixa (pin) e atualize conscientemente (changelog do FullCalendar).
- Para internacionalização adicional, se necessário, use o build global (index.global) que já inclui suporte a múltiplas views e recursos comuns. Locales específicos também podem ser adicionados (não obrigatório para pt-BR quando se usa locale global).

Solucionando problemas comuns
- “NS_ERROR_CORRUPTED_CONTENT” ou “X-Content-Type-Options: nosniff”:
  - Certifique-se de que os arquivos estão servidos pelo servidor HTTP (XAMPP).
  - Use os assets locais e evite proxies que alteram o MIME-Type.
- “FullCalendar is undefined”:
  - O JS do FullCalendar deve ser carregado antes de qualquer script que o utilize.
  - Revise a ordem: CSS, JS do FullCalendar, injeção de eventos (window.eventosCalendario), por fim public/js/calendar.js.
- “404 Not Found”:
  - Verifique o caminho exato: public/vendor/fullcalendar/index.global.min.(css|js)
  - No PHP, caminhos relativos devem ser calculados a partir do arquivo atual (calendario.php).

Atualizações futuras
- Quando atualizar para uma nova versão (ex.: v6.1.11+):
  1) Baixe os novos arquivos.
  2) Substitua os arquivos neste diretório.
  3) Teste o calendário (mês/semana/dia/lista, eventos, feriados, modal, dark mode).
  4) Atualize comentários de versão neste README se necessário.

Licença
- FullCalendar é distribuído sob licença MIT (confira: https://fullcalendar.io/license).
- Se necessário para auditoria, inclua um arquivo LICENSE nesta pasta com a licença do FullCalendar.

Estrutura recomendada do diretório
public/
  vendor/
    fullcalendar/
      index.global.min.css
      index.global.min.js
      README.txt  (este arquivo)

Contato/Manutenção
- Este projeto integra FullCalendar ao sistema de reservas do auditório SENAC.
- Para empacotar outras dependências localmente (Font Awesome, etc.), crie pastas em public/vendor e ajuste as tags <link> e <script> nos arquivos PHP.
