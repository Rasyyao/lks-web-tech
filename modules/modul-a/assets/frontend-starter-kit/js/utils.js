/**
 * PintarMenabung Frontend Utilities
 * LKS Web Technologies 2026 - Server Side Module Phase 2
 */

/**
 * Format angka ke format mata uang sesuai spesifikasi
 * Contoh: 50000, 'IDR' => 'IDR 50.000' atau 12, 'USD' => 'USD 12'
 */
function formatCurrency(amount, currencyCode = 'IDR') {
    const num = Math.abs(Number(amount) || 0);
    const formatted = new Intl.NumberFormat('id-ID').format(num);
    return `${currencyCode} ${formatted}`;
}

/**
 * Format tanggal ke human-readable format
 * Contoh: '2025-07-31' => 'Jul 31, 2025'
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

/**
 * Mengelompokkan transaksi per tanggal dan hanya menampilkan header tanggal sekali per grup
 */
function groupTransactionsByDate(transactions) {
    let lastDate = null;
    return transactions.map(tx => {
        const formattedDate = formatDate(tx.date);
        const showHeader = formattedDate !== lastDate;
        lastDate = formattedDate;
        return {
            ...tx,
            formattedDate,
            showHeader
        };
    });
}

/**
 * Setup Keyboard Shortcuts sesuai kisi-kisi:
 * Alt + W : Buka form "Add Wallet"
 * Alt + N : Buka form "Add Transaction"
 * Alt + T : Buka form "Transfer Money"
 * Esc     : Tutup form / modal aktif
 */
function registerAppShortcuts({ onAddWallet, onAddTransaction, onTransferMoney, onCloseModal }) {
    window.addEventListener('keydown', (e) => {
        if (e.altKey && (e.key === 'w' || e.key === 'W')) {
            e.preventDefault();
            if (typeof onAddWallet === 'function') onAddWallet();
        } else if (e.altKey && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            if (typeof onAddTransaction === 'function') onAddTransaction();
        } else if (e.altKey && (e.key === 't' || e.key === 'T')) {
            e.preventDefault();
            if (typeof onTransferMoney === 'function') onTransferMoney();
        } else if (e.key === 'Escape') {
            if (typeof onCloseModal === 'function') onCloseModal();
        }
    });
}
