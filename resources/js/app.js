

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('uiFeedback', () => ({
    confirmation: null,
    confirmationTrigger: null,
    toast: null,
    toastTimer: null,

    requestConfirmation(detail) {
        if (detail instanceof HTMLFormElement) {
            this.confirmationTrigger = document.activeElement;
            this.confirmation = {
                form: detail,
                onConfirm: null,
                title: detail.dataset.confirmTitle || 'Konfirmasi tindakan',
                message: detail.dataset.confirmMessage || 'Tindakan ini tidak dapat dibatalkan.',
                action: detail.dataset.confirmAction || 'Lanjutkan',
            };
        } else if (detail && typeof detail.onConfirm === 'function') {
            this.confirmationTrigger = document.activeElement;
            this.confirmation = {
                form: null,
                onConfirm: detail.onConfirm,
                title: detail.title || 'Konfirmasi tindakan',
                message: detail.message || 'Lanjutkan tindakan ini?',
                action: detail.action || 'Lanjutkan',
            };
        }

        if (this.confirmation) {
            document.body.classList.add('overflow-y-hidden');
            this.$nextTick(() => this.$refs.confirmCancel?.focus());
        }
    },

    cancelConfirmation() {
        this.confirmation = null;
        document.body.classList.remove('overflow-y-hidden');
        this.$nextTick(() => this.confirmationTrigger?.focus());
        this.confirmationTrigger = null;
    },

    confirmAction() {
        const confirmation = this.confirmation;
        this.cancelConfirmation();

        if (confirmation?.form) {
            confirmation.form.dataset.confirmed = 'true';
            confirmation.form.requestSubmit();
            return;
        }

        confirmation?.onConfirm?.();
    },

    cycleConfirmationFocus(event) {
        const focusable = [this.$refs.confirmCancel, this.$refs.confirmAction];
        const currentIndex = focusable.indexOf(document.activeElement);
        const nextIndex = event.shiftKey
            ? (currentIndex <= 0 ? focusable.length - 1 : currentIndex - 1)
            : (currentIndex >= focusable.length - 1 ? 0 : currentIndex + 1);

        focusable[nextIndex]?.focus();
    },

    showToast({ type = 'info', message = '' } = {}) {
        if (! message) {
            return;
        }

        window.clearTimeout(this.toastTimer);
        this.toast = { type, message };
        this.toastTimer = window.setTimeout(() => {
            this.toast = null;
        }, 4500);
    },

    dismissToast() {
        window.clearTimeout(this.toastTimer);
        this.toast = null;
    },

    async copyText({ text, label = 'Teks' } = {}) {
        try {
            if (! navigator.clipboard?.writeText) {
                throw new Error('Clipboard API tidak tersedia.');
            }

            await navigator.clipboard.writeText(text);
            this.showToast({ type: 'success', message: `${label} berhasil disalin.` });
        } catch {
            this.showToast({ type: 'error', message: `${label} tidak dapat disalin. Periksa izin clipboard browser.` });
        }
    },
}));

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (! (form instanceof HTMLFormElement) || ! form.hasAttribute('data-confirm')) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    event.preventDefault();
    window.dispatchEvent(new CustomEvent('app-confirm', { detail: form }));
});

Alpine.start();
