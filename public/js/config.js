/**
 * Configurações do Sistema de Reserva SENAC
 * Arquivo centralizado de configurações para JavaScript
 */

// Configuração global da aplicação
window.SENAC_CONFIG = {
    // Informações da aplicação
    app: {
        name: 'Sistema de Reserva SENAC',
        version: '2.0.0',
        environment: null, // será detectado automaticamente
    },

    // Configurações de API
    api: {
        baseUrl: null, // será calculado automaticamente
        timeout: 10000, // 10 segundos
        retries: 3,
        endpoints: {
            reservas: 'api/reserva.php',
            usuarios: 'api/usuario.php',
            auth: 'controller/LoginController.php'
        }
    },

    // Configurações de UI
    ui: {
        toast: {
            duration: {
                success: 5000,
                error: 7000,
                warning: 6000,
                info: 5000
            },
            position: 'top-right',
            showCloseButton: true
        },
        loading: {
            showAfter: 300, // milissegundos
            minShowTime: 500
        },
        animations: {
            enabled: true,
            duration: 300
        }
    },

    // Configurações de validação
    validation: {
        reserva: {
            descricaoMinLength: 10,
            horarioMinimo: '08:00',
            horarioMaximo: '22:00',
            duracaoMinima: 30, // minutos
            duracaoMaxima: 480, // 8 horas
            antecedenciaMinima: 0 // dias
        },
        debounceTime: 500 // milissegundos para validações em tempo real
    },

    // Configurações do calendário
    calendar: {
        locale: 'pt-br',
        firstDay: 0, // Domingo
        timeFormat: 'H:mm',
        defaultView: 'dayGridMonth',
        colors: {
            approved: '#10b981',
            pending: '#f59e0b',
            rejected: '#ef4444',
            cancelled: '#6b7280'
        }
    },

    // Cores do tema SENAC
    colors: {
        primary: '#004A8D',      // SENAC Blue
        secondary: '#F26C21',    // SENAC Orange
        accent: '#C9A961',       // SENAC Gold
        lightBlue: '#E8F2FF',    // SENAC Light Blue
        darkBlue: '#003366'      // SENAC Dark Blue
    },

    // Configurações de cache
    cache: {
        enabled: true,
        duration: 5 * 60 * 1000, // 5 minutos
        keys: {
            reservas: 'senac_reservas',
            usuario: 'senac_usuario',
            config: 'senac_config'
        }
    }
};

// Classe para gerenciar configurações
class ConfigManager {
    constructor() {
        this.config = window.SENAC_CONFIG;
        this.init();
    }

    init() {
        this.detectEnvironment();
        this.setupApiUrls();
        this.loadUserPreferences();
    }

    /**
     * Detecta o ambiente atual
     */
    detectEnvironment() {
        const hostname = window.location.hostname;
        const isProduction = hostname.includes('infinityfree') ||
                           hostname.includes('senachub');

        this.config.app.environment = isProduction ? 'production' : 'development';
    }

    /**
     * Configura URLs da API baseado no ambiente
     */
    setupApiUrls() {
        const basePath = this.getBasePath();
        this.config.api.baseUrl = basePath;

        // Atualizar endpoints com base path
        Object.keys(this.config.api.endpoints).forEach(key => {
            const endpoint = this.config.api.endpoints[key];
            this.config.api.endpoints[key] = basePath + endpoint;
        });
    }

    /**
     * Calcula o caminho base baseado na URL atual
     */
    getBasePath() {
        const path = window.location.pathname;

        // Se estamos em uma subpasta (como /view/), volta um nível
        if (path.includes('/view/')) {
            return '../';
        }

        // Se já estamos na raiz
        return '';
    }

    /**
     * Carrega preferências do usuário do localStorage
     */
    loadUserPreferences() {
        try {
            const preferences = localStorage.getItem('senac_user_preferences');
            if (preferences) {
                const parsed = JSON.parse(preferences);

                // Aplicar preferências de UI
                if (parsed.ui) {
                    Object.assign(this.config.ui, parsed.ui);
                }

                // Aplicar preferências do calendário
                if (parsed.calendar) {
                    Object.assign(this.config.calendar, parsed.calendar);
                }
            }
        } catch (error) {
            console.warn('Erro ao carregar preferências do usuário:', error);
        }
    }

    /**
     * Salva preferências do usuário
     */
    saveUserPreferences(preferences) {
        try {
            localStorage.setItem('senac_user_preferences', JSON.stringify(preferences));
        } catch (error) {
            console.warn('Erro ao salvar preferências do usuário:', error);
        }
    }

    /**
     * Obtém uma configuração específica usando notação de ponto
     * Exemplo: get('ui.toast.duration.success')
     */
    get(path) {
        return path.split('.').reduce((obj, key) => obj && obj[key], this.config);
    }

    /**
     * Define uma configuração específica
     */
    set(path, value) {
        const keys = path.split('.');
        const lastKey = keys.pop();
        const target = keys.reduce((obj, key) => obj[key], this.config);
        target[lastKey] = value;
    }

    /**
     * Obtém URL completa de um endpoint
     */
    getApiUrl(endpoint) {
        return this.config.api.endpoints[endpoint] || this.config.api.baseUrl + endpoint;
    }

    /**
     * Verifica se está em ambiente de produção
     */
    isProduction() {
        return this.config.app.environment === 'production';
    }

    /**
     * Verifica se está em ambiente de desenvolvimento
     */
    isDevelopment() {
        return this.config.app.environment === 'development';
    }

    /**
     * Obtém informações do ambiente
     */
    getEnvironmentInfo() {
        return {
            environment: this.config.app.environment,
            hostname: window.location.hostname,
            basePath: this.getBasePath(),
            apiBaseUrl: this.config.api.baseUrl,
            version: this.config.app.version
        };
    }
}

// Inicializar gerenciador de configurações
window.configManager = new ConfigManager();

// Aliases para facilitar o uso
window.getConfig = (path) => window.configManager.get(path);
window.setConfig = (path, value) => window.configManager.set(path, value);
window.getApiUrl = (endpoint) => window.configManager.getApiUrl(endpoint);

// Log de inicialização (apenas em desenvolvimento)
if (window.configManager.isDevelopment()) {
    console.log('🎯 SENAC Config inicializado:', window.configManager.getEnvironmentInfo());
}

// Exportar para uso em modules (se necessário)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SENAC_CONFIG: window.SENAC_CONFIG, ConfigManager };
}
