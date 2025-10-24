/**
 * Calendar JavaScript - Sistema de Reserva SENAC
 * FullCalendar Integration com design moderno
 */

let calendar = null;

// Eventos do sistema (será populado pelo PHP)
const eventosData = window.eventosCalendario || [];

// Inicializar calendário quando o DOM estiver pronto
document.addEventListener("DOMContentLoaded", function () {
  initializeCalendar();
  initializeDarkMode();
  initializeMobileMenu();
  initializeEventModal();
});

/**
 * Inicializar FullCalendar
 */
function initializeCalendar() {
  const calendarEl = document.getElementById("fullcalendar");

  if (!calendarEl) {
    console.warn("Elemento #fullcalendar não encontrado");
    return;
  }

  calendar = new FullCalendar.Calendar(calendarEl, {
    // Configurações básicas
    initialView: "dayGridMonth",
    locale: "pt-br",
    height: "auto",
    firstDay: 0, // Domingo

    // Cabeçalho e botões
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
    },

    // Textos dos botões
    buttonText: {
      today: "Hoje",
      month: "Mês",
      week: "Semana",
      day: "Dia",
      list: "Lista",
    },

    // Configurações de visualização
    views: {
      dayGridMonth: {
        titleFormat: { year: "numeric", month: "long" },
      },
      timeGridWeek: {
        titleFormat: { year: "numeric", month: "short", day: "numeric" },
        slotMinTime: "07:00:00",
        slotMaxTime: "23:00:00",
      },
      timeGridDay: {
        titleFormat: { year: "numeric", month: "long", day: "numeric" },
        slotMinTime: "07:00:00",
        slotMaxTime: "23:00:00",
      },
      listWeek: {
        titleFormat: { year: "numeric", month: "long" },
      },
    },

    // Eventos
    events: eventosData,

    // Configurações de eventos
    eventTimeFormat: {
      hour: "2-digit",
      minute: "2-digit",
      meridiem: false,
    },

    // Interações
    eventClick: function (info) {
      info.jsEvent.preventDefault();
      showEventModal(info.event);
    },

    // Tooltip ao passar o mouse
    eventMouseEnter: function (info) {
      const tooltip = document.createElement("div");
      tooltip.className = "calendar-tooltip";
      tooltip.innerHTML = `
                <div class="font-semibold">${info.event.title}</div>
                <div class="text-sm mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    ${formatTime(info.event.start)} - ${formatTime(info.event.end)}
                </div>
            `;
      document.body.appendChild(tooltip);

      const rect = info.el.getBoundingClientRect();
      tooltip.style.position = "fixed";
      tooltip.style.left = rect.left + "px";
      tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + "px";
      tooltip.style.zIndex = "10000";

      info.el._tooltip = tooltip;
    },

    eventMouseLeave: function (info) {
      if (info.el._tooltip) {
        info.el._tooltip.remove();
        delete info.el._tooltip;
      }
    },

    // Estilização de células
    dayCellDidMount: function (info) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      info.date.setHours(0, 0, 0, 0);

      if (info.date.getTime() === today.getTime()) {
        info.el.classList.add("fc-day-today");
      }
    },

    // Quando eventos são renderizados
    eventDidMount: function (info) {
      // Adicionar classes de status
      const status = info.event.extendedProps.status || "pendente";
      info.el.classList.add(`event-${status}`);

      // Adicionar ícone baseado no status
      const icon = getStatusIcon(status);
      const titleEl = info.el.querySelector(".fc-event-title");
      if (titleEl && icon) {
        titleEl.innerHTML = `<i class="fas ${icon} mr-1"></i>${info.event.title}`;
      }
    },

    // Limite de eventos por dia (view mês)
    dayMaxEvents: 3,
    moreLinkText: function (num) {
      return `+${num} mais`;
    },

    // Configurações de carregamento
    loading: function (isLoading) {
      if (isLoading) {
        showLoading();
      } else {
        hideLoading();
      }
    },
  });

  calendar.render();

  // Log de inicialização
  console.log("📅 FullCalendar inicializado com sucesso");
  console.log(`📊 Total de eventos carregados: ${eventosData.length}`);
}

/**
 * Mudar visualização do calendário
 */
function changeCalendarView(view) {
  if (!calendar) return;

  calendar.changeView(view);

  // Atualizar botões ativos
  document.querySelectorAll(".view-btn").forEach((btn) => {
    btn.classList.remove("bg-senac-blue", "text-white");
    btn.classList.add(
      "bg-gray-200",
      "dark:bg-gray-700",
      "text-gray-700",
      "dark:text-gray-300",
    );
  });

  event.target.classList.remove(
    "bg-gray-200",
    "dark:bg-gray-700",
    "text-gray-700",
    "dark:text-gray-300",
  );
  event.target.classList.add("bg-senac-blue", "text-white");
}

/**
 * Mostrar modal de detalhes do evento
 */
function showEventModal(event) {
  const modal = document.getElementById("event-modal");
  if (!modal) return;

  const startDate = new Date(event.start);
  const endDate = new Date(event.end || event.start);

  const statusConfig = {
    aprovada: {
      class:
        "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400",
      icon: "fa-check-circle",
      text: "Aprovada",
    },
    pendente: {
      class:
        "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400",
      icon: "fa-clock",
      text: "Pendente",
    },
    rejeitada: {
      class: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400",
      icon: "fa-times-circle",
      text: "Rejeitada",
    },
  };

  const status =
    statusConfig[event.extendedProps.status] || statusConfig["pendente"];

  const modalContent = `
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Detalhes do Evento</h3>
                <button onclick="closeEventModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times text-xl text-gray-500 dark:text-gray-400"></i>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Título e Status -->
            <div>
                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                    ${event.title}
                </h4>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${status.class}">
                    <i class="fas ${status.icon} mr-2"></i>
                    ${status.text}
                </span>
            </div>

            <!-- Informações do Evento -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-xl">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-calendar-alt text-senac-blue dark:text-blue-400 text-xl mr-3"></i>
                        <span class="font-semibold text-gray-900 dark:text-white">Data</span>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 ml-9">
                        ${formatDate(startDate)}
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-4 rounded-xl">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-senac-orange dark:text-orange-400 text-xl mr-3"></i>
                        <span class="font-semibold text-gray-900 dark:text-white">Horário</span>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 ml-9">
                        ${formatTime(startDate)} - ${formatTime(endDate)}
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-xl">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-purple-600 dark:text-purple-400 text-xl mr-3"></i>
                        <span class="font-semibold text-gray-900 dark:text-white">Instrutor</span>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 ml-9">
                        ${event.extendedProps.instrutor || "Não informado"}
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-4 rounded-xl">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-map-marker-alt text-green-600 dark:text-green-400 text-xl mr-3"></i>
                        <span class="font-semibold text-gray-900 dark:text-white">Local</span>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 ml-9">
                        Auditório Principal
                    </div>
                </div>
            </div>

            <!-- Descrição -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl">
                <div class="flex items-center mb-3">
                    <i class="fas fa-info-circle text-senac-blue dark:text-senac-orange text-xl mr-3"></i>
                    <span class="font-semibold text-gray-900 dark:text-white">Descrição</span>
                </div>
                <div class="text-gray-700 dark:text-gray-300">
                    ${event.extendedProps.descricao || event.title}
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex justify-end">
                <button onclick="closeEventModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Fechar
                </button>
            </div>
        </div>
    `;

  document.getElementById("modal-body").innerHTML = modalContent;
  modal.classList.remove("hidden");
  document.body.style.overflow = "hidden";
}

/**
 * Fechar modal de evento
 */
function closeEventModal() {
  const modal = document.getElementById("event-modal");
  if (modal) {
    modal.classList.add("hidden");
    document.body.style.overflow = "";
  }
}

/**
 * Inicializar modal de evento
 */
function initializeEventModal() {
  const modal = document.getElementById("event-modal");
  if (!modal) return;

  // Fechar ao clicar fora
  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      closeEventModal();
    }
  });

  // Fechar com ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
      closeEventModal();
    }
  });
}

/**
 * Inicializar Dark Mode
 */
function initializeDarkMode() {
  const themeToggle = document.getElementById("theme-toggle");
  const html = document.documentElement;

  if (!themeToggle) return;

  // Verificar tema salvo
  const currentTheme = localStorage.getItem("theme") || "light";
  html.classList.toggle("dark", currentTheme === "dark");

  themeToggle.addEventListener("click", () => {
    html.classList.toggle("dark");
    const theme = html.classList.contains("dark") ? "dark" : "light";
    localStorage.setItem("theme", theme);

    // Recarregar calendário para atualizar cores
    if (calendar) {
      calendar.render();
    }
  });
}

/**
 * Inicializar menu mobile
 */
function initializeMobileMenu() {
  const mobileMenuBtn = document.getElementById("mobile-menu-btn");
  const mobileMenu = document.getElementById("mobile-menu");

  if (!mobileMenuBtn || !mobileMenu) return;

  mobileMenuBtn.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
  });

  // Fechar menu ao clicar em um link
  mobileMenu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      mobileMenu.classList.add("hidden");
    });
  });
}

/**
 * Formatadores
 */
function formatDate(date) {
  return date.toLocaleDateString("pt-BR", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

function formatTime(date) {
  return date.toLocaleTimeString("pt-BR", {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function getStatusIcon(status) {
  const icons = {
    aprovada: "fa-check-circle",
    pendente: "fa-clock",
    rejeitada: "fa-times-circle",
  };
  return icons[status] || icons["pendente"];
}

/**
 * Loading states
 */
function showLoading() {
  const loadingEl = document.createElement("div");
  loadingEl.id = "calendar-loading";
  loadingEl.className =
    "fixed inset-0 bg-black/50 flex items-center justify-center z-50";
  loadingEl.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-senac-blue border-t-transparent"></div>
            <div class="mt-4 text-gray-700 dark:text-gray-300 font-medium">Carregando...</div>
        </div>
    `;
  document.body.appendChild(loadingEl);
}

function hideLoading() {
  const loadingEl = document.getElementById("calendar-loading");
  if (loadingEl) {
    loadingEl.remove();
  }
}

// Exportar funções globais

window.changeCalendarView = changeCalendarView;

window.showEventModal = showEventModal;

window.closeEventModal = closeEventModal;

// Log de carregamento

console.log("📅 Calendar.js carregado com sucesso");

/**
 * Marcar dias com eventos (badge de contagem) no dayGridMonth
 * - Conta eventos por dia visível
 * - Insere/atualiza um badge na área do número do dia
 */
function updateDayBadges() {
  if (!calendar) return;

  // Mapa de contagem por YYYY-MM-DD
  const counts = {};
  const pad2 = (n) => String(n).padStart(2, "0");
  const ymd = (d) =>
    d
      ? `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`
      : "";

  // Contabiliza eventos pelo dia de início
  try {
    const events = calendar.getEvents();
    events.forEach((ev) => {
      const key = ymd(ev.start);
      if (!key) return;
      counts[key] = (counts[key] || 0) + 1;
    });
  } catch (e) {
    // Se por algum motivo calendar.getEvents() não estiver disponível
    if (Array.isArray(window.eventosCalendario)) {
      window.eventosCalendario.forEach((ev) => {
        const d = new Date(ev.start);
        const key = ymd(d);
        if (!key) return;
        counts[key] = (counts[key] || 0) + 1;
      });
    }
  }

  // Atualiza badges nos dias visíveis
  document.querySelectorAll(".fc-daygrid-day[data-date]").forEach((cell) => {
    const date = cell.getAttribute("data-date");
    const count = counts[date] || 0;

    // Tenta posicionar na área do topo do dia
    const top = cell.querySelector(".fc-daygrid-day-top");
    let badge = cell.querySelector(".fc-day-badge-count");

    // Remove badge quando não há eventos
    if (count === 0) {
      if (badge && badge.parentNode) badge.parentNode.removeChild(badge);
      return;
    }

    // Cria o badge se não existir
    if (!badge) {
      badge = document.createElement("span");
      badge.className = "fc-day-badge-count badge badge-info";
      // Ajustes visuais mínimos
      badge.style.marginLeft = "6px";
      badge.style.fontSize = "10px";
      badge.style.lineHeight = "1";
      if (top) {
        top.appendChild(badge);
      } else {
        // Fallback: adiciona no próprio cell
        cell.appendChild(badge);
      }
    }

    // Atualiza contagem
    badge.textContent = String(count);
  });
}

// Observa mudanças no calendário para manter badges atualizados
(function observeCalendarForBadges() {
  const root = document.getElementById("fullcalendar");
  if (!root) return;

  // Primeira atualização após render inicial
  setTimeout(updateDayBadges, 0);

  const observer = new MutationObserver(() => {
    updateDayBadges();
  });
  observer.observe(root, { childList: true, subtree: true });
})();
