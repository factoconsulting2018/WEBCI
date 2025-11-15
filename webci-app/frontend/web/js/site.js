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
    const mainContactModal = document.getElementById('main-contact-modal');
    const openMainContactButtons = document.querySelectorAll('.open-main-contact');
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
                        `Tel: ${details.whatsapp || 'N/A'}`,
                        `Correo: ${details.email || 'N/A'}`,
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
    const resetCardVisibility = () => {
        cards.forEach((card) => {
            const isFeatured = card.dataset.featured === '1';
            card.style.display = isFeatured ? 'block' : 'none';
        });
    };
    resetCardVisibility();
    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            const query = (event.target.value || '').trim().toUpperCase();
            if (!query) {
                resetCardVisibility();
                return;
            }
            cards.forEach((card) => {
                const haystack = card.getAttribute('data-search') || '';
                card.style.display = haystack.includes(query) ? 'block' : 'none';
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

    const SOCIAL_PROVIDERS = [
        { key: 'facebook', match: /facebook\.com/i, label: 'Facebook', icon: 'FB', className: 'facebook' },
        { key: 'instagram', match: /instagram\.com/i, label: 'Instagram', icon: 'IG', className: 'instagram' },
        { key: 'whatsapp', match: /(?:wa\.me|whatsapp\.com)/i, label: 'WhatsApp', icon: 'WA', className: 'whatsapp' },
        { key: 'linkedin', match: /linkedin\.com/i, label: 'LinkedIn', icon: 'IN', className: 'linkedin' },
    ];

    const extractHandle = (urlObject, providerKey) => {
        const pathname = (urlObject.pathname || '').split('/').map((segment) => segment.trim()).filter(Boolean);
        if (providerKey === 'whatsapp') {
            const phone = urlObject.searchParams.get('phone');
            if (phone) {
                return phone;
            }
            if (pathname.length) {
                return pathname[pathname.length - 1];
            }
            const textParam = urlObject.searchParams.get('text');
            return textParam ? decodeURIComponent(textParam).slice(0, 40) : '';
        }
        if (!pathname.length) {
            return '';
        }
        const ignored = ['pages', 'pg', 'p'];
        while (pathname.length && ignored.includes(pathname[0].toLowerCase())) {
            pathname.shift();
        }
        return pathname.length ? pathname[pathname.length - 1] : '';
    };

    const describeSocialLink = (link) => {
        if (!link?.url) {
            return null;
        }
        let parsedUrl;
        try {
            parsedUrl = new URL(link.url, window.location.origin);
        } catch (error) {
            return null;
        }
        const normalized = link.url.toLowerCase();
        const provider = SOCIAL_PROVIDERS.find((entry) => entry.match.test(normalized));
        const baseLabel = provider ? provider.label : (link.label || parsedUrl.hostname);
        const handle = extractHandle(parsedUrl, provider?.key);
        const displayText = handle ? `${baseLabel} / ${handle}` : baseLabel;

        return {
            url: parsedUrl.href,
            displayText,
            iconText: provider?.icon || '↗',
            className: provider ? `social-icon ${provider.className}` : 'social-icon generic',
        };
    };

    const renderSocialLinks = (links) => {
        if (!infoSocial) {
            return;
        }
        infoSocial.innerHTML = '';
        const rendered = [];

        (links || []).forEach((link) => {
            const meta = describeSocialLink(link);
            if (!meta) {
                return;
            }
            const item = document.createElement('li');
            item.className = 'social-link-item';

            const icon = document.createElement('span');
            icon.className = meta.className;
            icon.textContent = meta.iconText;

            const anchor = document.createElement('a');
            anchor.href = meta.url;
            anchor.target = '_blank';
            anchor.rel = 'noopener';
            anchor.textContent = meta.displayText;

            item.appendChild(icon);
            item.appendChild(anchor);
            infoSocial.appendChild(item);
            rendered.push(item);
        });

        if (!rendered.length) {
            const empty = document.createElement('li');
            empty.textContent = 'Sin enlaces registrados';
            infoSocial.appendChild(empty);
        }
    };

    const openInfoDetails = (details) => {
        if (!infoModal) {
            return;
        }
        infoModal.dataset.state = 'open';
        if (infoTitle) {
            infoTitle.textContent = details.name || 'Aliado';
        }
        if (infoAddress) {
            infoAddress.textContent = details.address || 'N/A';
        }
        if (infoPhone) {
            infoPhone.textContent = details.whatsapp || 'N/A';
        }
        if (infoEmail) {
            infoEmail.textContent = details.email || 'N/A';
        }
        if (infoDescription) {
            infoDescription.textContent = details.description || 'N/A';
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
                item.textContent = 'N/A';
                infoCategories.appendChild(item);
            }
        }
        renderSocialLinks(details.socialLinks);
    };

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
            const dataAttribute = button.getAttribute('data-details');
            if (!dataAttribute) {
                return;
            }

            try {
                const details = JSON.parse(dataAttribute);
                openInfoDetails(details);
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
            if (mainContactModal) {
                mainContactModal.dataset.state = 'closed';
            }
        }
    });

    const tickerTrack = document.getElementById('ally-ticker-track');
    const tickerItems = tickerTrack ? Array.from(tickerTrack.querySelectorAll('.ticker-item')) : [];
    if (tickerTrack && tickerItems.length > 0) {
        const VISIBLE_COUNT = 4;
        let tickerIndex = 0;

        const getStep = () => {
            if (tickerItems.length > 1) {
                return tickerItems[1].offsetLeft - tickerItems[0].offsetLeft;
            }
            return tickerItems[0].offsetWidth + 24;
        };

        const updateTicker = () => {
            const step = getStep();
            tickerItems.forEach((item, idx) => {
                item.dataset.state = (idx >= tickerIndex && idx < tickerIndex + VISIBLE_COUNT) ? 'active' : 'inactive';
            });
            tickerTrack.style.transform = `translateX(-${tickerIndex * step}px)`;
        };

        updateTicker();

        setInterval(() => {
            tickerIndex += 1;
            if (tickerIndex > tickerItems.length - VISIBLE_COUNT) {
                tickerIndex = 0;
            }
            updateTicker();
        }, 2500);

        tickerItems.forEach((item) => {
            item.addEventListener('click', () => {
                const dataAttribute = item.getAttribute('data-details');
                if (!dataAttribute) {
                    return;
                }
                try {
                    const details = JSON.parse(dataAttribute);
                    openInfoDetails(details);
                } catch (error) {
                    console.error('No se pudo abrir el aliado desde el ticker', error);
                }
            });
        });
    }

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

    const registerModal = (selector) => {
        const modal = document.querySelector(selector);
        if (!modal) {
            return null;
        }
        const overlay = modal.querySelector('.modal-overlay');
        const closeButtons = modal.querySelectorAll('.modal-close');
        const close = () => {
            modal.dataset.state = 'closed';
        };
        closeButtons.forEach((button) => button.addEventListener('click', close));
        overlay?.addEventListener('click', close);
        return { open: () => (modal.dataset.state = 'open'), close };
    };

    const mainContactModalInstance = registerModal('#main-contact-modal');
    openMainContactButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            mainContactModalInstance?.open();
        });
    });

    const rankingModal = registerModal('#ranking-modal');
    const distributionModal = registerModal('#distribution-modal');

    document.querySelectorAll('.open-dashboard-modal').forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-target');
            if (target === '#ranking-modal') {
                rankingModal?.open();
            }
            if (target === '#distribution-modal') {
                distributionModal?.open();
            }
        });
    });

    if (mainContactModal && !mainContactModalInstance) {
        const mainContactClose = mainContactModal.querySelectorAll('.modal-close');
        const mainContactOverlay = mainContactModal.querySelector('.modal-overlay');
        const closeMainModal = () => {
            mainContactModal.dataset.state = 'closed';
        };
        openMainContactButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                mainContactModal.dataset.state = 'open';
            });
        });
        mainContactClose.forEach((button) => button.addEventListener('click', closeMainModal));
        mainContactOverlay?.addEventListener('click', closeMainModal);

        const mainForm = document.getElementById('main-contact-form');
        if (mainForm) {
            mainForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                const submitButton = mainForm.querySelector('[type="submit"]');
                submitButton?.setAttribute('disabled', 'disabled');

                const formData = new FormData(mainForm);
                try {
                    const response = await fetch('/site/contact-general', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const payload = await response.json();
                    if (payload.success) {
                        showToast(payload.message || 'Mensaje enviado.');
                        closeMainModal();
                        mainForm.reset();
                    } else {
                        showToast('No se pudo enviar el mensaje.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    showToast('No se pudo enviar el mensaje.', 'error');
                } finally {
                    submitButton?.removeAttribute('disabled');
                }
            });
        }
    }

    const benefitModal = document.getElementById('benefit-modal');
    if (benefitModal) {
        const benefitModalTitle = document.getElementById('benefit-modal-title');
        const benefitTitleInputs = benefitModal.querySelectorAll('.benefit-title-input');
        const benefitOverlay = benefitModal.querySelector('.modal-overlay');
        const benefitCloseButtons = benefitModal.querySelectorAll('.modal-close');
        const benefitTabButtons = benefitModal.querySelectorAll('.modal-tab-button');
        const benefitTabPanels = benefitModal.querySelectorAll('.modal-tab-content');
        const benefitForms = benefitModal.querySelectorAll('.benefit-form');

        const setBenefitTitle = (title) => {
            const safeTitle = title || 'Beneficio';
            if (benefitModalTitle) {
                benefitModalTitle.textContent = safeTitle;
            }
            benefitTitleInputs.forEach((input) => {
                input.value = safeTitle;
            });
        };

        const closeBenefitModal = () => {
            benefitModal.dataset.state = 'closed';
            benefitForms.forEach((form) => form.reset());
        };

        const openBenefitModal = (title) => {
            setBenefitTitle(title);
            benefitModal.dataset.state = 'open';
        };

        benefitCloseButtons.forEach((button) => button.addEventListener('click', closeBenefitModal));
        benefitOverlay?.addEventListener('click', closeBenefitModal);

        document.querySelectorAll('.benefit-row[data-benefit-title]').forEach((row) => {
            const title = row.getAttribute('data-benefit-title') || 'Beneficio';
            const handleOpen = (event) => {
                if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }
                event.preventDefault();
                openBenefitModal(title);
            };
            row.addEventListener('click', handleOpen);
            row.addEventListener('keydown', handleOpen);
        });

        benefitTabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.dataset.tab;
                benefitTabButtons.forEach((btn) => btn.classList.toggle('active', btn === button));
                benefitTabPanels.forEach((panel) => panel.classList.toggle('active', panel.id === target));
            });
        });

        benefitForms.forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const submitButton = form.querySelector('[type="submit"]');
                submitButton?.setAttribute('disabled', 'disabled');

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.getAttribute('action') || '/site/benefit-inquiry', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const payload = await response.json();

                    if (response.ok && payload?.success) {
                        showToast(payload.message || 'Solicitud enviada.');
                        closeBenefitModal();
                    } else {
                        showToast(payload?.message || 'No se pudo enviar la solicitud.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    showToast('No se pudo enviar la solicitud.', 'error');
                } finally {
                    submitButton?.removeAttribute('disabled');
                }
            });
        });

        document.addEventListener('keyup', (event) => {
            if (event.key === 'Escape') {
                closeBenefitModal();
            }
        });
    }

});

