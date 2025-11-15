document.addEventListener('DOMContentLoaded', () => {
    const contactModal = document.getElementById('contact-modal');
    const contactModalTitle = document.getElementById('contact-modal-title');
    const modalForm = document.getElementById('contact-modal-form');
    const businessIdField = document.getElementById('contact-business-id');
    const openContactButtons = document.querySelectorAll('.open-contact-modal');
    const contactCloseElements = contactModal ? contactModal.querySelectorAll('.modal-close') : [];
    const contactOverlay = contactModal ? contactModal.querySelector('.modal-overlay') : null;

    const infoModal = document.getElementById('info-modal');
    const infoButtons = document.querySelectorAll('.open-info-modal');
    const shareButtons = document.querySelectorAll('.open-share');
    shareButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            const dataAttribute = button.getAttribute('data-share');
            if (!dataAttribute) {
                return;
            }
            try {
                const details = JSON.parse(dataAttribute);
                const shareData = {
                    title: details.name || 'Aliado',
                    text: [
                        details.name,
                        details.address,
                        `Tel: ${details.whatsapp || 'N/D'}`,
                        `Correo: ${details.email || 'N/D'}`,
                        details.description,
                    ].filter(Boolean).join('\n'),
                };

                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    await navigator.clipboard.writeText(`${shareData.title}\n${shareData.text}`);
                    const toast = document.createElement('div');
                    toast.className = 'toast-message toast-success';
                    toast.textContent = 'Información copiada al portapapeles';
                    document.body.appendChild(toast);
                    requestAnimationFrame(() => {
                        toast.dataset.state = 'visible';
                    });
                    setTimeout(() => {
                        toast.dataset.state = 'hidden';
                        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
                    }, 2500);
                }
            } catch (error) {
                console.error('No se pudo compartir la información', error);
            }
        });
    });

    const searchInput = document.getElementById('business-search');
    const cards = document.querySelectorAll('.business-card');
    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            const query = (event.target.value || '').trim().toUpperCase();
            cards.forEach((card) => {
                const haystack = card.getAttribute('data-search') || '';
                card.style.display = haystack.includes(query) ? '' : 'none';
            });
        });
    }

    const slider = document.getElementById('tools-slider');
    if (slider) {
        const slides = slider.querySelectorAll('.slide');
        let currentIndex = 0;

        const nextSlide = () => {
            slides[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % slides.length;
            slides[currentIndex].classList.add('active');
        };

        setInterval(nextSlide, 5000);
    }
    const infoCloseElements = infoModal ? infoModal.querySelectorAll('.modal-close') : [];
    const infoOverlay = infoModal ? infoModal.querySelector('.modal-overlay') : null;
    const infoTitle = document.getElementById('info-modal-title');
    const infoAddress = document.getElementById('info-modal-address');
    const infoPhone = document.getElementById('info-modal-phone');
    const infoEmail = document.getElementById('info-modal-email');
    const infoCategories = document.getElementById('info-modal-categories');
    const infoSocial = document.getElementById('info-modal-social');
    const infoDescription = document.getElementById('info-modal-description');

    const closeContactModal = () => {
        if (!contactModal) {
            return;
        }
        contactModal.dataset.state = 'closed';
        modalForm?.reset();
    };

    const closeInfoModal = () => {
        if (!infoModal) {
            return;
        }
        infoModal.dataset.state = 'closed';
    };

    openContactButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!contactModal) {
                return;
            }
            const dataAttribute = button.getAttribute('data-business');
            if (!dataAttribute) {
                return;
            }

            try {
                const business = JSON.parse(dataAttribute);
                contactModal.dataset.state = 'open';
                if (contactModalTitle) {
                    contactModalTitle.textContent = business.name || 'Contacto';
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

    contactCloseElements.forEach((element) => {
        element.addEventListener('click', closeContactModal);
    });

    contactOverlay?.addEventListener('click', closeContactModal);

    infoButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (!infoModal) {
                return;
            }
            const dataAttribute = button.getAttribute('data-details');
            if (!dataAttribute) {
                return;
            }

            try {
                const details = JSON.parse(dataAttribute);
                infoModal.dataset.state = 'open';
                if (infoTitle) {
                    infoTitle.textContent = details.name || 'Aliado';
                }
                if (infoAddress) {
                    infoAddress.textContent = details.address || 'Sin información';
                }
                if (infoPhone) {
                    infoPhone.textContent = details.whatsapp || 'Sin información';
                }
                if (infoEmail) {
                    infoEmail.textContent = details.email || 'Sin información';
                }
                if (infoDescription) {
                    infoDescription.textContent = details.description || 'Sin información';
                }
                if (infoCategories) {
                    infoCategories.innerHTML = '';
                    (details.categories || []).forEach((category) => {
                        const item = document.createElement('li');
                        item.className = 'info-pill';
                        item.textContent = category;
                        infoCategories.appendChild(item);
                    });
                    if (infoCategories.children.length === 0) {
                        const item = document.createElement('li');
                        item.className = 'info-pill';
                        item.textContent = 'Sin categorías';
                        infoCategories.appendChild(item);
                    }
                }
                if (infoSocial) {
                    infoSocial.innerHTML = '';
                    (details.socialLinks || []).forEach((link) => {
                        const item = document.createElement('li');
                        if (link?.url) {
                            const anchor = document.createElement('a');
                            anchor.href = link.url;
                            anchor.target = '_blank';
                            anchor.rel = 'noopener';
                            anchor.textContent = link.label || link.url;
                            item.appendChild(anchor);
                        } else if (link?.label) {
                            item.textContent = link.label;
                        }
                        if (item.childNodes.length || item.textContent) {
                            infoSocial.appendChild(item);
                        }
                    });
                    if (infoSocial.children.length === 0) {
                        const item = document.createElement('li');
                        item.textContent = 'Sin enlaces registrados';
                        infoSocial.appendChild(item);
                    }
                }
            } catch (error) {
                console.error('No se pudo abrir el modal de información', error);
            }
        });
    });

    infoCloseElements.forEach((element) => {
        element.addEventListener('click', closeInfoModal);
    });

    infoOverlay?.addEventListener('click', closeInfoModal);

    document.addEventListener('keyup', (event) => {
        if (event.key === 'Escape') {
            closeContactModal();
            closeInfoModal();
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
                    closeContactModal();
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

