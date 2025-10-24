/**
 * Sistema de Reserva de Auditório SENAC
 * JavaScript Principal - Notificações, Validações e Utilitários
 */

// Sistema de Notificações Toast
class ToastManager {
    constructor() {
        this.container = this.createContainer();
        this.toasts = [];
    }

    createContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'info', title = null, duration = 5000) {
        const toast = this.createToast(message, type, title);
        this.container.appendChild(toast);
        this.toasts.push(toast);

        // Animação de entrada
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                this.remove(toast);
            }, duration);
        }

        return toast;
    }

    createToast(message, type, title) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease-out';

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const titles = {
            success: title || 'Sucesso',
            error: title || 'Erro',
            warning: title || 'Aviso',
            info: title || 'Informação'
        };

        toast.innerHTML = `
            <div class="toast-header">
                <span class="toast-title">
                    ${icons[type]} ${titles[type]}
                </span>
                <span class="toast-close" onclick="toast.remove(this.closest('.toast'))">&times;</span>
            </div>
            <div class="toast-message">${message}</div>
        `;

        return toast;
    }

    remove(toast) {
        if (!toast || !toast.parentNode) return;

        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            this.toasts = this.toasts.filter(t => t !== toast);
        }, 300);
    }

    success(message, title = null, duration = 5000) {
        return this.show(message, 'success', title, duration);
    }

    error(message, title = null, duration = 7000) {
        return this.show(message, 'error', title, duration);
    }

    warning(message, title = null, duration = 6000) {
        return this.show(message, 'warning', title, duration);
    }

    info(message, title = null, duration = 5000) {
        return this.show(message, 'info', title, duration);
    }

    clear() {
        this.toasts.forEach(toast => this.remove(toast));
    }
}

// Instância global do Toast
const toast = new ToastManager();

// Sistema de Loading
class LoadingManager {
    constructor() {
        this.overlay = null;
        this.isVisible = false;
    }

    show(text = 'Carregando...') {
        if (this.isVisible) return;

        this.overlay = document.createElement('div');
        this.overlay.className = 'loading-overlay';
        this.overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner"></div>
                <div class="loading-text">${text}</div>
            </div>
        `;

        document.body.appendChild(this.overlay);
        this.isVisible = true;

        // Previne scroll do body
        document.body.style.overflow = 'hidden';
    }

    hide() {
        if (!this.isVisible || !this.overlay) return;

        this.overlay.style.opacity = '0';
        setTimeout(() => {
            if (this.overlay && this.overlay.parentNode) {
                this.overlay.parentNode.removeChild(this.overlay);
            }
            this.overlay = null;
            this.isVisible = false;
            document.body.style.overflow = '';
        }, 200);
    }
}

const loading = new LoadingManager();

// Validação de Formulários
class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = {};
        this.rules = {};
    }

    addRule(field, rules) {
        this.rules[field] = rules;
        return this;
    }

    validate() {
        this.errors = {};
        let isValid = true;

        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (!field) return;

            const value = field.value.trim();
            const rules = this.rules[fieldName];

            rules.forEach(rule => {
                if (rule.required && !value) {
                    this.addError(fieldName, rule.message || `${fieldName} é obrigatório`);
                    isValid = false;
                }

                if (rule.min && value.length < rule.min) {
                    this.addError(fieldName, rule.message || `${fieldName} deve ter pelo menos ${rule.min} caracteres`);
                    isValid = false;
                }

                if (rule.email && value && !this.isValidEmail(value)) {
                    this.addError(fieldName, rule.message || 'Email inválido');
                    isValid = false;
                }

                if (rule.custom && typeof rule.custom === 'function') {
                    const customResult = rule.custom(value, field);
                    if (customResult !== true) {
                        this.addError(fieldName, customResult || 'Valor inválido');
                        isValid = false;
                    }
                }
            });
        });

        this.showErrors();
        return isValid;
    }

    addError(field, message) {
        if (!this.errors[field]) {
            this.errors[field] = [];
        }
        this.errors[field].push(message);
    }

    showErrors() {
        // Remove erros anteriores
        this.form.querySelectorAll('.error-message').forEach(el => el.remove());
        this.form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

        Object.keys(this.errors).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('error');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                errorDiv.textContent = this.errors[fieldName][0];

                field.parentNode.appendChild(errorDiv);
            }
        });
    }

    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
}

// Utilitários AJAX
class AjaxHelper {
    static async request(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        };

        const config = { ...defaults, ...options };

        try {
            const response = await fetch(url, config);

            // Verificar se a resposta é JSON válida
            let data;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                throw new Error('Resposta não é JSON válido: ' + text);
            }

            if (!response.ok) {
                throw new Error(data.error?.message || data.message || 'Erro na requisição');
            }

            return data;
        } catch (error) {
            console.error('Erro AJAX:', error);
            throw error;
        }
    }

    static async get(url) {
        return this.request(url);
    }

    static async post(url, data) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    static async put(url, data) {
        return this.request(url, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    static async delete(url) {
        return this.request(url, {
            method: 'DELETE'
        });
    }
}

// Validação de Conflitos de Reserva
class ReservaValidator {
    static async verificarConflito(data, horaInicio, horaFim, auditorio, reservaId = null) {
        async function verificarConflitoAPI(data, horaInicio, horaFim) {
            try {
                const basePath = window.location.pathname.includes('/view/') ? '../' : '';
                const response = await AjaxHelper.post(basePath + 'api/reserva.php', {
                    action: 'verificar_conflito',
                    data: data,
                    hora_inicio: horaInicio,
                    hora_fim: horaFim,
                    auditorio: 'principal',
                    reserva_id: null
                });

                return response;
            } catch (error) {
                console.error('Erro ao verificar conflito:', error);
                return { conflito: false, message: 'Erro ao verificar conflito' };
            }
        }

    static async validarFormularioReserva(form) {
        const formData = new FormData(form);
        const data = formData.get('data');
        const horaInicio = formData.get('hora_inicio');
        const horaFim = formData.get('hora_fim');
        const auditorio = formData.get('auditorio') || 'principal';
        const descricao = formData.get('descricao') || 'Validação';

        // Validações básicas
        if (!data || !horaInicio || !horaFim) {
            toast.error('Preencha todos os campos obrigatórios');
            return false;
        }

        // Validar se hora fim é maior que hora início
        if (horaFim <= horaInicio) {
            toast.error('A hora de fim deve ser maior que a hora de início');
            return false;
        }

        try {
            // Verificar conflito no servidor usando API
            const basePath = window.location.pathname.includes('/view/') ? '../' : '';
            const response = await AjaxHelper.post(basePath + 'api/reserva.php', {
                action: 'validar_reserva',
                data: data,
                hora_inicio: horaInicio,
                hora_fim: horaFim,
                auditorio: auditorio,
                descricao: descricao
            });

            if (response.success && !response.data.valida) {
                const erros = response.data.erros || ['Erro de validação'];
                toast.error('Problemas encontrados: ' + erros.join(', '));
                return false;
            }

            return true;
        } catch (error) {
            console.error('Erro na validação:', error);
            toast.error('Erro ao validar reserva. Tente novamente.');
            return false;
        }
    }
}

// Formatação de Data/Hora
class DateHelper {
    static formatDate(date, format = 'DD/MM/YYYY') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        switch (format) {
            case 'DD/MM/YYYY':
                return `${day}/${month}/${year}`;
            case 'YYYY-MM-DD':
                return `${year}-${month}-${day}`;
            case 'DD/MM/YYYY HH:mm':
                const hours = String(d.getHours()).padStart(2, '0');
                const minutes = String(d.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            default:
                return date;
        }
    }

    static isDateInPast(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const checkDate = new Date(date);
        checkDate.setHours(0, 0, 0, 0);
        return checkDate < today;
    }

    static isWeekend(date) {
        const d = new Date(date);
        const day = d.getDay();
        return day === 0 || day === 6; // Domingo ou Sábado
    }
}

// Inicialização quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Auto-inicialização de componentes
    initializeComponents();

    // Configurar formulários
    setupForms();

    // Configurar navegação
    setupNavigation();
});

function initializeComponents() {
    // Inicializar tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });

    // Inicializar confirmações
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', handleConfirm);
    });

    // Detectar ambiente e ajustar configurações
    window.APP_ENV = {
        isProduction: window.location.hostname.includes('infinityfree'),
        basePath: window.location.pathname.includes('/view/') ? '../' : '',
        apiUrl: function(endpoint) {
            return this.basePath + 'api/' + endpoint;
        }
    };
}

function setupForms() {
    // Formulário de Reserva
    const reservaForm = document.getElementById('form-reserva');
    if (reservaForm) {
        reservaForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            loading.show('Validando reserva...');

            const isValid = await ReservaValidator.validarFormularioReserva(this);

            loading.hide();

            if (isValid) {
                loading.show('Salvando reserva...');
                this.submit();
            }
        });
    }

    // Formulários de Login e Cadastro
    const loginForm = document.getElementById('form-login');
    if (loginForm) {
        const validator = new FormValidator(loginForm);
        validator
            .addRule('email', [
                { required: true, message: 'Email é obrigatório' },
                { email: true, message: 'Email inválido' }
            ])
            .addRule('senha', [
                { required: true, message: 'Senha é obrigatória' }
            ]);

        loginForm.addEventListener('submit', function(e) {
            if (!validator.validate()) {
                e.preventDefault();
            }
        });
    }
}

function setupNavigation() {
    // Logout com confirmação
    const logoutLinks = document.querySelectorAll('.logout-link');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Deseja realmente sair do sistema?')) {
                window.location.href = this.href;
            }
        });
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip bg-gray-800 text-white px-2 py-1 rounded text-sm absolute z-50';
    tooltip.textContent = e.target.dataset.tooltip;

    document.body.appendChild(tooltip);

    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + 'px';
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';

    e.target._tooltip = tooltip;
}

function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        delete e.target._tooltip;
    }
}

function handleConfirm(e) {
    const message = e.target.dataset.confirm || 'Tem certeza?';
    if (!confirm(message)) {
        e.preventDefault();
    }
}

// Exportar para uso global
window.toast = toast;
window.loading = loading;
window.FormValidator = FormValidator;
window.AjaxHelper = AjaxHelper;
window.ReservaValidator = ReservaValidator;
window.DateHelper = DateHelper;
