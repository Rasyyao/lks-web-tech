import Swal from 'sweetalert2';

// 1. Customized SweetAlert2 Instance matching SMK Telkom Purwokerto Design Rules
export const TelkomSwal = Swal.mixin({
    customClass: {
        popup: 'telkom-swal-popup',
        title: 'telkom-swal-title',
        htmlContainer: 'telkom-swal-html',
        actions: 'telkom-swal-actions',
        confirmButton: 'telkom-swal-confirm',
        cancelButton: 'telkom-swal-cancel',
    },
    buttonsStyling: false,
});

// 2. Customized Toast Instance for quick feedback
export const TelkomToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    customClass: {
        popup: 'swal2-toast telkom-swal-toast',
    },
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

// Expose globally
window.Swal = TelkomSwal;
window.TelkomSwal = TelkomSwal;
window.TelkomToast = TelkomToast;

window.showAlert = function (icon, title, text = '', options = {}) {
    return TelkomSwal.fire({
        icon: icon || 'info',
        title: title,
        text: text,
        confirmButtonText: options.confirmButtonText || 'OK',
        ...options,
    });
};

window.showToast = function (icon, title, timer = 3500) {
    return TelkomToast.fire({
        icon: icon || 'info',
        title: title,
        timer: timer,
    });
};

window.showConfirm = function (message, title = 'Konfirmasi Tindakan', confirmButtonText = 'Ya, Lanjutkan') {
    return TelkomSwal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Batal',
        reverseButtons: true,
    });
};

// 3. Process Session Flash Alerts from hidden DOM container
export function processFlashAlerts() {
    const flashContainer = document.getElementById('flash-alerts-data');
    if (!flashContainer) return;

    const successMsg = flashContainer.getAttribute('data-success');
    const errorMsg = flashContainer.getAttribute('data-error');
    const warningMsg = flashContainer.getAttribute('data-warning');
    const infoMsg = flashContainer.getAttribute('data-info');
    const statusMsg = flashContainer.getAttribute('data-status');

    if (successMsg) {
        TelkomSwal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: successMsg,
            confirmButtonText: 'OK',
        });
        flashContainer.removeAttribute('data-success');
    } else if (errorMsg) {
        TelkomSwal.fire({
            icon: 'error',
            title: 'Perhatian',
            text: errorMsg,
            confirmButtonText: 'Tutup',
        });
        flashContainer.removeAttribute('data-error');
    } else if (warningMsg) {
        TelkomToast.fire({
            icon: 'warning',
            title: warningMsg,
            timer: 3500,
        });
        flashContainer.removeAttribute('data-warning');
    } else if (infoMsg || statusMsg) {
        TelkomToast.fire({
            icon: 'info',
            title: infoMsg || statusMsg,
            timer: 3500,
        });
        flashContainer.removeAttribute('data-info');
        flashContainer.removeAttribute('data-status');
    }
}

// 4. Intercept Livewire 3 wire:confirm seamlessly with SweetAlert2
function initLivewireConfirmInterceptor() {
    if (HTMLElement.prototype._has_livewire_swal_confirm) return;

    Object.defineProperty(HTMLElement.prototype, '__livewire_confirm', {
        set(fn) {
            this._custom_livewire_confirm_fn = fn;
        },
        get() {
            return (action, instead) => {
                const message = this.getAttribute('wire:confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                TelkomSwal.fire({
                    title: 'Konfirmasi Tindakan',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        action();
                    } else {
                        if (typeof instead === 'function') {
                            instead();
                        }
                    }
                });
            };
        },
        configurable: true,
        enumerable: true,
    });

    HTMLElement.prototype._has_livewire_swal_confirm = true;
}

// 5. Handle Livewire Dispatched Alert Events
function initLivewireEventListeners() {
    const handleAlertEvent = (detail) => {
        if (!detail) return;
        const config = typeof detail === 'string' ? { text: detail, title: 'Pemberitahuan' } : detail;
        
        if (config.toast) {
            TelkomToast.fire({
                icon: config.icon || 'info',
                title: config.title || config.text,
            });
        } else {
            TelkomSwal.fire({
                icon: config.icon || 'info',
                title: config.title || 'Pemberitahuan',
                text: config.text || config.message || '',
                confirmButtonText: config.confirmButtonText || 'OK',
                showCancelButton: !!config.showCancelButton,
                cancelButtonText: config.cancelButtonText || 'Batal',
            });
        }
    };

    window.addEventListener('swal', (event) => handleAlertEvent(event.detail));
    window.addEventListener('alert', (event) => handleAlertEvent(event.detail));
    window.addEventListener('notify', (event) => handleAlertEvent(event.detail));
    window.addEventListener('toast', (event) => {
        const detail = event.detail;
        if (!detail) return;
        const config = typeof detail === 'string' ? { title: detail } : detail;
        TelkomToast.fire({
            icon: config.icon || 'info',
            title: config.title || config.text || config.message,
        });
    });

    document.addEventListener('livewire:init', () => {
        if (window.Livewire) {
            window.Livewire.on('swal', (data) => handleAlertEvent(Array.isArray(data) ? data[0] : data));
            window.Livewire.on('alert', (data) => handleAlertEvent(Array.isArray(data) ? data[0] : data));
            window.Livewire.on('notify', (data) => handleAlertEvent(Array.isArray(data) ? data[0] : data));
        }
    });
}

// Initialize on document ready & Livewire page navigation
initLivewireConfirmInterceptor();
initLivewireEventListeners();

document.addEventListener('DOMContentLoaded', () => {
    processFlashAlerts();
});

document.addEventListener('livewire:navigated', () => {
    processFlashAlerts();
});
