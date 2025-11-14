document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('contact-modal');
    const modalTitle = document.getElementById('contact-modal-title');
    const modalForm = document.getElementById('contact-modal-form');
    const businessIdField = document.getElementById('contact-business-id');
    const openButtons = document.querySelectorAll('.open-contact-modal');
    const closeElements = modal ? modal.querySelectorAll('.modal-close') : [];
    const overlay = modal ? modal.querySelector('.modal-overlay') : null;

    const closeModal = () => {
        if (!modal) {
            return;
        }
        modal.dataset.state = 'closed';
        modalForm?.reset();
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!modal) {
                return;
            }
            const dataAttribute = button.getAttribute('data-business');
            if (!dataAttribute) {
                return;
            }

            try {
                const business = JSON.parse(dataAttribute);
                modal.dataset.state = 'open';
                if (modalTitle) {
                    modalTitle.textContent = business.name || 'Contacto';
                }
                if (businessIdField) {
                    businessIdField.value = business.id ?? '';
                }
                if (modalForm) {
                    modalForm.setAttribute('action', business.contactUrl || '/site/contact-business');
                }
            } catch (e) {
                console.error('No se pudo preparar el modal de contacto', e);
            }
        });
    });

    closeElements.forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    overlay?.addEventListener('click', closeModal);

    document.addEventListener('keyup', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    const showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(() => {
            toast.dataset.state = 'visible';
        });
        setTimeout(() => {
            toast.dataset.state = 'hidden';
            toast.addEventListener('transitionend', () => toast.remove(), { once: true });
        }, 4000);
    };

    if (modalForm) {
        modalForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitButton = modalForm.querySelector('[type="submit"]');
            submitButton?.setAttribute('disabled', 'disabled');

            const formData = new FormData(modalForm);

            try {
                const response = await fetch(modalForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                let payload;
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    payload = await response.json();
                }

                if (response.ok && payload?.success) {
                    showToast(payload.message || 'Mensaje enviado exitosamente.');
                    closeModal();
                } else {
                    const errorMessage = payload?.errors ? Object.values(payload.errors)[0] : null;
                    showToast(errorMessage || 'No se pudo enviar el mensaje.', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('No se pudo enviar el mensaje.', 'error');
            } finally {
                submitButton?.removeAttribute('disabled');
            }
        });
    }
});

