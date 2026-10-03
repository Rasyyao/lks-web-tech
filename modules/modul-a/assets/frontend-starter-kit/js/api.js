/**
 * PintarMenabung API Client (Axios)
 * LKS Web Technologies 2026 - Server Side Module Phase 2
 */

// Inisialisasi konfigurasi dasar baseURL API
const API_BASE_URL = window.API_BASE_URL || 'http://localhost:8000/api';

const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// Request interceptor: Sisipkan Bearer token otomatis bila tersedia di localStorage
api.interceptors.request.use(config => {
    const token = localStorage.getItem('pm_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Response interceptor: Tangani error 401 (Unauthenticated) otomatis
api.interceptors.response.use(response => {
    return response;
}, error => {
    if (error.response && error.response.status === 401) {
        localStorage.removeItem('pm_token');
        localStorage.removeItem('pm_user');
        if (!window.location.pathname.endsWith('login.html') && !window.location.pathname.endsWith('register.html')) {
            window.location.href = 'login.html';
        }
    }
    return Promise.reject(error);
});

// Service API Functions
const AuthService = {
    register: (fullName, email, password) => api.post('/auth/register', { full_name: fullName, email, password }),
    login: (email, password) => api.post('/auth/login', { email, password }),
    logout: () => api.post('/auth/logout')
};

const WalletService = {
    getAll: () => api.get('/wallets'),
    getDetail: (walletId) => api.get(`/wallets/${walletId}`),
    create: (name, currencyCode) => api.post('/wallets', { name, currency_code: currencyCode }),
    update: (walletId, name) => api.put(`/wallets/${walletId}`, { name }),
    delete: (walletId) => api.delete(`/wallets/${walletId}`)
};

const TransactionService = {
    getAll: (params = {}) => api.get('/transactions', { params }),
    create: (data) => api.post('/transactions', data),
    delete: (transactionId) => api.delete(`/transactions/${transactionId}`)
};

const ReportService = {
    getExpenseSummary: (month, year) => api.get('/reports/summary-by-category/expense', { params: { month, year } }),
    getIncomeSummary: (month, year) => api.get('/reports/summary-by-category/income', { params: { month, year } })
};

const MetaService = {
    getCurrencies: () => api.get('/currencies'),
    getCategories: () => api.get('/categories')
};
