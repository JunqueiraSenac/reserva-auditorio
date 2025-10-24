/**
 * Simple Calendar JS - SENAC Auditório
 * Calendário mensal leve (sem FullCalendar), com:
 * - Renderização do mês atual e navegação mês a mês
 * - Eventos (aprovada, pendente, rejeitada)
 * - Feriados nacionais (BrasilAPI) com cor roxa
 * - Cards de estatísticas (total, aprovadas, pendentes, rejeitadas)
 * - Legendinha colorida
 * - Modal de detalhes ao clicar num evento (título, data, responsável, status)
 * - Dark mode (persistido em localStorage)
 *
 * Requisitos de CSS:
 *  - Use o arquivo public/css/simple-calendar.css para o visual (classes prefixadas com .sc-)
 *
 * Como usar:
 *  1) Inclua este JS na página (após o HTML): <script src="public/js/simple-calendar.js"></script>
 *  2) Tenha um container com id (ou selector) para o calendário, ex:
 *      <div id="sc-root"></div>
 *  3) Chame:
 *      SimpleCalendar.init({
 *        root: '#sc-root',
 *        events: window.eventosCalendario || [], // opcional, pode vir do backend
 *        showHolidays: true,                     // se inicia com feriados
 *        title: '📅 Calendário de Eventos do Auditório SENAC'
 *      });
 *
 * Observação:
 *  - Se não chamar init manualmente, o script tenta auto-inicializar usando:
 *      root = '#sc-root'
 *      events = window.eventosCalendario || []
 */

(function () {
  "use strict";

  const PT_BR_WEEKDAYS = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];

  function pad2(n) {
    return String(n).padStart(2, "0");
  }

  function toDateKey(date) {
    // date: Date
    return `${date.getFullYear()}-${pad2(date.getMonth() + 1)}-${pad2(
      date.getDate()
    )}`;
  }

  function parseDateKey(key) {
    // 'YYYY-MM-DD' => Date
    const [y, m, d] = key.split("-").map((x) => parseInt(x, 10));
    return new Date(y, m - 1, d);
  }

  function getMonthMatrix(year, monthIndex) {
    // Retorna um array de 42 entradas (6 semanas x 7 dias), com objetos:
    // { date: Date, inMonth: boolean, isToday: boolean }
    const firstOfMonth = new Date(year, monthIndex, 1);
    const startWeekday = firstOfMonth.getDay(); // 0=Dom,6=Sáb
    const grid = [];

    // Primeiro dia visível no grid (pode ser do mês anterior)
    const gridStart = new Date(year, monthIndex, 1 - startWeekday);

    // 6 linhas x 7 colunas = 42 dias
    for (let i = 0; i < 42; i++) {
      const d = new Date(
        gridStart.getFullYear(),
        gridStart.getMonth(),
        gridStart.getDate() + i
      );
      const inMonth = d.getMonth() === monthIndex;
      const now = new Date();
      now.setHours(0, 0, 0, 0);
      const isToday = toDateKey(d) === toDateKey(now);

      grid.push({ date: d, inMonth, isToday });
    }

    return grid;
  }

  function normalizeEvents(rawEvents) {
    // Converte eventos (vindo do backend ou de window.eventosCalendario)
    // para um formato interno mais simples:
    // {
    //   id, title, dateKey 'YYYY-MM-DD', status ('aprovada'|'pendente'|'rejeitada'|'feriado'),
    //   instrutor, descricao
    // }
    const out = [];
    if (!Array.isArray(rawEvents)) return out;

    for (const ev of rawEvents) {
      let start = ev.start || ev.data || ev.date || ev.startDate;
      if (!start) continue;

      // Normaliza para dateKey
      const dateOnly = String(start).split("T")[0]; // 'YYYY-MM-DD'
      const status =
        (ev.extendedProps && ev.extendedProps.status) ||
        ev.status ||
        "pendente";

      out.push({
        id: ev.id || `ev-${dateOnly}-${Math.random().toString(36).slice(2)}`,
        title: ev.title || ev.descricao || "Evento",
        dateKey: dateOnly,
        status: status,
        instrutor:
          (ev.extendedProps && (ev.extendedProps.instrutor || ev.extendedProps.usuario_nome)) ||
          ev.instrutor ||
          ev.usuario_nome ||
          "",
        descricao:
        (ev.extendedProps && ev.extendedProps.descricao) ||
          ev.descricao ||
          "",
      });
    }
    return out;
  }

  function fetchBrasilAPIFeriados(year) {
    const url = `https://brasilapi.com.br/api/feriados/v1/${year}`;
    return fetch(url).then((r) => r.json());
  }

  function normalizeFeriados(data) {
    // data: [{ date:'YYYY-MM-DD', name: '...', type: 'national|state|municipal|optional' }, ...]
    // Retornaremos uma lista de eventos com status 'feriado' e cor roxa (no CSS).
    if (!Array.isArray(data)) return [];
    return data.map((h) => ({
      id: `feriado-${h.date}`,
      title: `Feriado: ${h.name}`,
      dateKey: h.date,
      status: "feriado",
      instrutor: "", // não se aplica
      descricao: (h.type ? `Tipo: ${h.type}` : "").trim(),
    }));
  }

  function createEl(tag, attrs, ...children) {
    const el = document.createElement(tag);
    if (attrs && typeof attrs === "object") {
      for (const [k, v] of Object.entries(attrs)) {
        if (k === "class") el.className = v;
        else if (k === "style") el.style.cssText = v;
        else if (k.startsWith("on") && typeof v === "function") {
          el.addEventListener(k.slice(2), v);
        } else if (v !== false && v != null) {
          el.setAttribute(k, String(v));
        }
      }
    }
    for (const c of children) {
      if (c == null || c === false) continue;
      if (Array.isArray(c)) {
        c.forEach((cc) => el.appendChild(typeof cc === "string" ? document.createTextNode(cc) : cc));
      } else {
        el.appendChild(typeof c === "string" ? document.createTextNode(c) : c);
      }
    }
    return el;
  }

  function formatMonthTitle(year, monthIndex) {
    const monthNames = [
      "janeiro",
      "fevereiro",
      "março",
      "abril",
      "maio",
      "junho",
      "julho",
      "agosto",
      "setembro",
      "outubro",
      "novembro",
      "dezembro",
    ];
    const m = monthNames[monthIndex] || "";
    return `${m[0].toUpperCase()}${m.slice(1)} de ${year}`;
  }

  function formatDateBR(dateKey) {
    // 'YYYY-MM-DD' => 'dd/mm/yyyy (dia da semana)'
    const d = parseDateKey(dateKey);
    const w = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"][d.getDay()];
    return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} (${w})`;
  }

  function computeStats(events) {
    const stats = {
      total: events.length,
      aprovadas: 0,
      pendentes: 0,
      rejeitadas: 0,
    };
    for (const ev of events) {
      if (ev.status === "aprovada") stats.aprovadas++;
      else if (ev.status === "pendente") stats.pendentes++;
      else if (ev.status === "rejeitada") stats.rejeitadas++;
    }
    return stats;
  }

  function makeModal() {
    const overlay = createEl("div", { class: "sc-modal-overlay", id: "sc-modal-overlay" });
    const modal = createEl("div", { class: "sc-modal", role: "dialog", "aria-modal": "true" });

    const header = createEl("div", { class: "sc-modal-header" });
    const title = createEl("h3", { class: "sc-modal-title", id: "sc-modal-title" }, "Detalhes do Evento");
    const closeBtn = createEl(
      "button",
      { class: "sc-modal-close", "aria-label": "Fechar", onclick: () => overlay.classList.remove("open") },
      "×"
    );
    header.append(title, closeBtn);

    const body = createEl("div", { class: "sc-modal-body", id: "sc-modal-body" });

    modal.append(header, body);
    overlay.append(modal);
    document.body.appendChild(overlay);

    return {
      show(contentTitle, contentEl) {
        title.textContent = contentTitle || "Detalhes do Evento";
        body.innerHTML = "";
        body.appendChild(contentEl);
        overlay.classList.add("open");
      },
      hide() {
        overlay.classList.remove("open");
      },
    };
  }

  function iconForStatus(status) {
    switch (status) {
      case "aprovada":
        return "🟢";
      case "pendente":
        return "🟡";
      case "rejeitada":
        return "🔴";
      case "feriado":
        return "🟣";
      default:
        return "🔹";
    }
  }

  function ensureThemeInit() {
    try {
      const saved = localStorage.getItem("theme");
      if (saved === "dark") {
        document.documentElement.classList.add("dark");
      }
    } catch (e) {}
  }

  function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle("dark");
    try {
      localStorage.setItem("theme", isDark ? "dark" : "light");
    } catch (e) {}
  }

  function SimpleCalendarApp(root, options) {
    // State
    const today = new Date();
    let state = {
      year: today.getFullYear(),
      monthIndex: today.getMonth(),
      events: normalizeEvents(options.events || []),
      feriados: [], // normalizados
      showHolidays: options.showHolidays !== false,
      title:
        options.title ||
        "📅 Calendário de Eventos do Auditório SENAC",
    };

    const modal = makeModal();

    // Root structure
    const container = createEl("div", { class: "sc-calendar" });

    const inner = createEl("div", { class: "sc-container" });

    // Header (title + theme toggle)
    const header = createEl("div", { class: "sc-header" }, [
      createEl("div", { class: "sc-title" }, [
        createEl("span", { class: "emoji" }, "📅"),
        createEl("span", null, state.title),
      ]),
      createEl(
        "div",
        { style: "position:absolute;top:14px;right:14px;display:flex;gap:8px" },
        [
          createEl(
            "button",
            {
              class: "sc-btn",
              title: "Alternar tema",
              onclick: toggleTheme,
            },
            "Modo escuro"
          ),
        ]
      ),
    ]);

    // Stats
    const statsWrap = createEl("div", { class: "sc-stats" });
    const statTotal = createEl("div", { class: "sc-card" });
    const statAprov = createEl("div", { class: "sc-card aprovados" });
    const statPend = createEl("div", { class: "sc-card pendentes" });
    const statReje = createEl("div", { class: "sc-card rejeitados" });

    function renderStats() {
      const stats = computeStats(state.events);
      statTotal.innerHTML = "";
      statAprov.innerHTML = "";
      statPend.innerHTML = "";
      statReje.innerHTML = "";

      statTotal.append(
        createEl("div", { class: "sc-card-label" }, "Total de eventos"),
        createEl("div", { class: "sc-card-number" }, String(stats.total))
      );
      statAprov.append(
        createEl("div", { class: "sc-card-label" }, "Aprovados"),
        createEl("div", { class: "sc-card-number" }, String(stats.aprovadas))
      );
      statPend.append(
        createEl("div", { class: "sc-card-label" }, "Pendentes"),
        createEl("div", { class: "sc-card-number" }, String(stats.pendentes))
      );
      statReje.append(
        createEl("div", { class: "sc-card-label" }, "Rejeitados"),
        createEl("div", { class: "sc-card-number" }, String(stats.rejeitadas))
      );
    }
    renderStats();
    statsWrap.append(statTotal, statAprov, statPend, statReje);

    // Controls (month nav + show holidays)
    const controls = createEl("div", { class: "sc-controls" });
    const btnPrev = createEl(
      "button",
      {
        class: "sc-btn",
        onclick: () => {
          if (state.monthIndex === 0) {
            state.monthIndex = 11;
            state.year -= 1;
          } else {
            state.monthIndex -= 1;
          }
          renderCalendar();
        },
      },
      "◀"
    );
    const monthTitle = createEl(
      "div",
      {
        class: "sc-btn active",
        style:
          "cursor:default;pointer-events:none;font-weight:900;letter-spacing:.3px;",
      },
      formatMonthTitle(state.year, state.monthIndex)
    );
    const btnNext = createEl(
      "button",
      {
        class: "sc-btn",
        onclick: () => {
          if (state.monthIndex === 11) {
            state.monthIndex = 0;
            state.year += 1;
          } else {
            state.monthIndex += 1;
          }
          renderCalendar();
        },
      },
      "▶"
    );

    const toggleHolidays = createEl("label", { class: "sc-toggle" }, [
      createEl("input", {
        type: "checkbox",
        checked: state.showHolidays ? "checked" : null,
        onchange: (e) => {
          state.showHolidays = e.currentTarget.checked;
          renderCalendar();
        },
      }),
      createEl("span", null, "Mostrar feriados"),
    ]);

    controls.append(btnPrev, monthTitle, btnNext, toggleHolidays);

    // Legend
    const legend = createEl("div", { class: "sc-legend" }, [
      createEl("div", { class: "sc-legend-item" }, [
        createEl("span", { class: "sc-dot aprovado" }),
        createEl("span", null, "Aprovado"),
      ]),
      createEl("div", { class: "sc-legend-item" }, [
        createEl("span", { class: "sc-dot pendente" }),
        createEl("span", null, "Pendente"),
      ]),
      createEl("div", { class: "sc-legend-item" }, [
        createEl("span", { class: "sc-dot rejeitado" }),
        createEl("span", null, "Rejeitado"),
      ]),
      createEl("div", { class: "sc-legend-item" }, [
        createEl("span", { class: "sc-dot feriado" }),
        createEl("span", null, "Feriado"),
      ]),
    ]);

    // Calendar Grid structure
    const gridWrap = createEl("div", { class: "sc-calendar-grid" });
    const weekHeader = createEl("div", { class: "sc-weekdays" });
    PT_BR_WEEKDAYS.forEach((wd) => weekHeader.append(createEl("div", { class: "sc-weekday" }, wd)));
    const daysGrid = createEl("div", { class: "sc-days" });

    gridWrap.append(weekHeader, daysGrid);

    // Footer
    const footer = createEl("div", { class: "sc-footer" }, [
      createEl("div", null, "SENAC - Sistema de Reserva de Auditório"),
      createEl("div", { class: "sc-muted" }, `© ${new Date().getFullYear()} SENAC`),
    ]);

    inner.append(header, statsWrap, controls, legend, gridWrap, footer);
    container.append(inner);
    root.innerHTML = "";
    root.append(container);

    // Fetch Feriados (lazy)
    let fetchingHolidays = null;
    function ensureHolidaysForYear(year) {
      if (fetchingHolidays && fetchingHolidays.year === year) return fetchingHolidays.promise;

      const p = fetchBrasilAPIFeriados(year)
        .then((data) => {
          state.feriados = normalizeFeriados(data || []);
        })
        .catch(() => {
          state.feriados = [];
        });

      fetchingHolidays = { year, promise: p };
      return p;
    }

    function dayEventsMap(events) {
      const map = {};
      for (const ev of events) {
        if (!map[ev.dateKey]) map[ev.dateKey] = [];
        map[ev.dateKey].push(ev);
      }
      return map;
    }

    function openEventModal(ev) {
      const content = createEl("div", null);

      // título + status-pill
      const pill = createEl("span", { class: `sc-status-pill ${ev.status}` }, [
        iconForStatus(ev.status),
        " ",
        ev.status[0].toUpperCase() + ev.status.slice(1),
      ]);

      const titleLine = createEl("div", { class: "sc-info-title" }, [
        createEl("span", null, ev.title || "Evento"),
        createEl("span", { style: "margin-left:auto" }, pill),
      ]);
      content.append(titleLine);

      // infos
      const grid = createEl("div", { class: "sc-info-grid" });

      const ci1 = createEl("div", { class: "sc-info-card" }, [
        createEl("div", { class: "sc-info-title" }, ["📅", "Data"]),
        createEl("div", { class: "sc-kv" }, formatDateBR(ev.dateKey)),
      ]);
      const ci2 = createEl("div", { class: "sc-info-card" }, [
        createEl("div", { class: "sc-info-title" }, ["👤", "Responsável"]),
        createEl("div", { class: "sc-kv" }, ev.instrutor || "Não informado"),
      ]);
      const ci3 = createEl("div", { class: "sc-info-card" }, [
        createEl("div", { class: "sc-info-title" }, ["📝", "Descrição"]),
        createEl("div", { class: "sc-kv" }, ev.descricao || "—"),
      ]);

      grid.append(ci1, ci2, ci3);
      content.append(grid);

      modal.show("Detalhes do Evento", content);
    }

    function renderCalendar() {
      // Atualizar título do mês
      monthTitle.textContent = formatMonthTitle(state.year, state.monthIndex);

      const baseEvents = state.events.slice();
      const needsHolidays =
        state.showHolidays &&
        (!state.feriados.length ||
          (state.feriados.length && parseDateKey(state.feriados[0].dateKey).getFullYear() !== state.year));

      const afterHolidays = () => {
        // Mescla os feriados (se mostrar)
        let events = baseEvents;
        if (state.showHolidays && state.feriados.length) {
          events = baseEvents.concat(state.feriados);
        }

        const matrix = getMonthMatrix(state.year, state.monthIndex);
        const eventsByDay = dayEventsMap(events);

        // Limpar grid
        daysGrid.innerHTML = "";

        matrix.forEach((cell) => {
          const dateKey = toDateKey(cell.date);
          const dayNum = cell.date.getDate();
          const evs = eventsByDay[dateKey] || [];

          const dayEl = createEl("div", {
            class: `sc-day${cell.inMonth ? "" : " out-month"}${cell.isToday ? " today" : ""}`,
          });

          // Top: número do dia + badge
          const topRow = createEl("div", { class: "sc-date" }, [
            createEl("span", null, String(dayNum)),
            evs.length ? createEl("span", { class: "sc-badge-count" }, String(evs.length)) : "",
          ]);

          // Bottom: lista de eventos (limitado a 3)
          const evWrap = createEl("div", { class: "sc-events" });
          evs.slice(0, 3).forEach((ev) => {
            const el = createEl(
              "div",
              {
                class: `sc-event ${ev.status}`,
                title: `${ev.title}\n${formatDateBR(ev.dateKey)}\n${ev.instrutor || ""}`,
                onclick: () => openEventModal(ev),
              },
              [
                createEl("span", { class: "sc-ico" }, iconForStatus(ev.status)),
                createEl("span", null, ev.title.length > 16 ? ev.title.slice(0, 16) + "…" : ev.title),
              ]
            );
            evWrap.append(el);
          });

          // Se mais eventos, indicar
          if (evs.length > 3) {
            evWrap.append(createEl("div", { class: "sc-muted", style: "font-size:.8rem" }, `+${evs.length - 3} mais`));
          }

          dayEl.append(topRow, evWrap);
          daysGrid.append(dayEl);
        });
      };

      if (needsHolidays) {
        ensureHolidaysForYear(state.year).finally(afterHolidays);
      } else {
        afterHolidays();
      }
    }

    // Init
    ensureThemeInit();
    // Fechar modal clicando fora
    document.addEventListener("click", function (e) {
      const overlay = document.getElementById("sc-modal-overlay");
      if (!overlay) return;
      if (e.target === overlay) overlay.classList.remove("open");
    });

    renderCalendar();

    // Public API (se for necessário no futuro)
    return {
      setEvents(newEvents) {
        state.events = normalizeEvents(newEvents || []);
        renderStats();
        renderCalendar();
      },
      goto(year, monthIndex) {
        state.year = year;
        state.monthIndex = monthIndex;
        renderCalendar();
      },
      toggleHolidays(show) {
        state.showHolidays = !!show;
        renderCalendar();
      },
      getState() {
        return JSON.parse(JSON.stringify(state));
      },
    };
  }

  const SimpleCalendar = {
    init(config) {
      const rootSel = (config && config.root) || "#sc-root";
      const rootEl = typeof rootSel === "string" ? document.querySelector(rootSel) : rootSel;
      if (!rootEl) {
        console.error("[SimpleCalendar] Root container não encontrado:", rootSel);
        return null;
      }
      const app = SimpleCalendarApp(rootEl, {
        events: (config && config.events) || [],
        showHolidays: config && config.showHolidays,
        title: config && config.title,
      });
      return app;
    },
  };

  // Expor globalmente
  window.SimpleCalendar = SimpleCalendar;

  // Auto-init: se existir #sc-root e não houver init manual
  document.addEventListener("DOMContentLoaded", function () {
    const rootEl = document.getElementById("sc-root");
    if (!rootEl) return;

    const autoEvents = Array.isArray(window.eventosCalendario) ? window.eventosCalendario : [];
    if (!rootEl.dataset.scAutoInit || rootEl.dataset.scAutoInit === "1") {
      SimpleCalendar.init({
        root: rootEl,
        events: autoEvents,
        showHolidays: true,
        title: "📅 Calendário de Eventos do Auditório SENAC",
      });
    }
  });
})();
