/**
 * JavaScript para el módulo Escandallo
 * Maneja la funcionalidad de añadir al carrito y otras interacciones
 */

/**
 * Función para mostrar notificaciones toast
 */
function showToast(title, message, type) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.escandallo-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'escandallo-toast-container';
        document.body.appendChild(toastContainer);
    }

    // Crear toast
    const toast = document.createElement('div');
    toast.className = 'escandallo-toast escandallo-toast-' + type;
    toast.innerHTML = `
        <div class="escandallo-toast-title">${title}</div>
        <div class="escandallo-toast-message">${message}</div>
    `;

    // Añadir al contenedor
    toastContainer.appendChild(toast);

    // Animar entrada
    setTimeout(() => {
        toast.classList.add('escandallo-toast-show');
    }, 10);

    // Remover después de 4 segundos
    setTimeout(() => {
        toast.classList.remove('escandallo-toast-show');
        setTimeout(() => {
            toastContainer.removeChild(toast);
        }, 300);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function() {

    // ===================================
    // AÑADIR AL CARRITO - USANDO SISTEMA NATIVO DE PRESTASHOP
    // ===================================

    const addToCartButtons = document.querySelectorAll('.escandallo-btn-add-cart');

    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const idProduct = this.getAttribute('data-id-product');
            const productName = this.getAttribute('data-product-name');
            const cartUrl = this.getAttribute('data-cart-url');
            const maxStock = parseInt(this.getAttribute('data-max-stock')) || 999;
            const originalText = this.innerHTML;
            const self = this;

            // Si ya está deshabilitado, no hacer nada
            if (this.disabled) {
                return;
            }

            // Deshabilitar botón mientras se procesa
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

            // Crear FormData para enviar
            const formData = new FormData();
            formData.append('id_product', idProduct);
            formData.append('qty', '1');
            formData.append('add', '1');
            formData.append('action', 'update');

            // Obtener token si está disponible
            if (typeof prestashop !== 'undefined' && prestashop.static_token) {
                formData.append('token', prestashop.static_token);
            }

            // Enviar petición usando el método de PrestaShop
            fetch(cartUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(text => {
                // Intentar parsear como JSON, si falla asumir éxito
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    data = { success: true };
                }

                // Mostrar mensaje de éxito en el botón
                self.innerHTML = '<i class="fa fa-check"></i>';
                self.classList.remove('btn-primary');
                self.classList.add('btn-success');

                // Actualizar el contador del carrito usando PrestaShop
                if (typeof prestashop !== 'undefined' && prestashop.emit) {
                    // Disparar evento updateCart para que PrestaShop actualice el carrito
                    prestashop.emit('updateCart', {
                        reason: {
                            idProduct: idProduct,
                            idProductAttribute: 0,
                            linkAction: 'add-to-cart'
                        }
                    });
                }

                // Refrescar el blockcart para actualizar el contador
                const cartRefreshUrl = prestashop && prestashop.urls && prestashop.urls.pages ?
                                       prestashop.urls.pages.cart :
                                       null;

                if (cartRefreshUrl) {
                    // Recargar el componente del carrito
                    fetch(cartRefreshUrl + '?ajax=1&action=refresh')
                        .then(r => r.json())
                        .then(refreshData => {
                            if (prestashop && prestashop.emit) {
                                prestashop.emit('updateCart', {
                                    reason: refreshData
                                });
                            }
                        })
                        .catch(err => console.log('No se pudo refrescar el carrito:', err));
                }

                // Rastrear cuántos productos se han añadido
                let addedCount = parseInt(self.getAttribute('data-added-count') || '0') + 1;
                self.setAttribute('data-added-count', addedCount);

                // Si se alcanzó el stock máximo, deshabilitar permanentemente
                if (addedCount >= maxStock) {
                    setTimeout(() => {
                        self.innerHTML = '<i class="fa fa-ban"></i> Sin stock';
                        self.style.opacity = '0.5';
                        self.style.cursor = 'not-allowed';
                        self.disabled = true;
                        self.classList.remove('btn-success');
                        self.classList.add('btn-secondary');
                    }, 1500);
                } else {
                    // Restaurar botón después de 1.5 segundos
                    setTimeout(() => {
                        self.innerHTML = originalText;
                        self.classList.remove('btn-success');
                        self.classList.add('btn-primary');
                        self.disabled = false;
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error al añadir al carrito:', error);
                self.innerHTML = '<i class="fa fa-exclamation-triangle"></i> Error';
                self.classList.remove('btn-primary');
                self.classList.add('btn-danger');

                setTimeout(() => {
                    self.innerHTML = originalText;
                    self.classList.remove('btn-danger');
                    self.classList.add('btn-primary');
                    self.disabled = false;
                }, 2000);
            });
        });
    });
    
    // ===================================
    // BÚSQUEDA CON AUTOCOMPLETADO (OPCIONAL)
    // ===================================
    
    const searchInput = document.querySelector('.escandallo-search-input');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(function() {
                    // Aquí podrías implementar autocompletado si lo deseas
                    console.log('Búsqueda:', query);
                }, 500);
            }
        });
    }
    
    // ===================================
    // ZOOM EN IMAGEN DE DIAGRAMA (OPCIONAL)
    // ===================================
    
    const diagramImage = document.querySelector('.escandallo-diagram-image');
    
    if (diagramImage) {
        diagramImage.style.cursor = 'zoom-in';
        
        diagramImage.addEventListener('click', function() {
            // Crear overlay para zoom
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: zoom-out;
            `;
            
            const zoomedImage = document.createElement('img');
            zoomedImage.src = this.src;
            zoomedImage.style.cssText = `
                max-width: 95%;
                max-height: 95%;
                object-fit: contain;
            `;
            
            overlay.appendChild(zoomedImage);
            document.body.appendChild(overlay);
            
            // Cerrar al hacer clic
            overlay.addEventListener('click', function() {
                document.body.removeChild(overlay);
            });
            
            // Cerrar con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.body.contains(overlay)) {
                    document.body.removeChild(overlay);
                }
            });
        });
    }
    
    // ===================================
    // ANIMACIÓN DE ENTRADA DE CARDS
    // ===================================
    
    const cards = document.querySelectorAll('.escandallo-principal-card, .escandallo-parte-card');
    
    cards.forEach(function(card, index) {
        card.style.animationDelay = (index * 0.1) + 's';
    });
    
    // ===================================
    // SMOOTH SCROLL PARA NAVEGACIÓN
    // ===================================

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===================================
    // MODAL DE IMAGEN DEL PRODUCTO
    // ===================================

    const btnVerImagen = document.querySelectorAll('.escandallo-btn-ver-imagen');

    btnVerImagen.forEach(function(button) {
        button.addEventListener('click', function() {
            const imagenUrl = this.getAttribute('data-imagen');
            const nombre = this.getAttribute('data-nombre');
            const referencia = this.getAttribute('data-referencia');

            // Crear overlay simple
            const overlay = document.createElement('div');
            overlay.className = 'escandallo-image-modal';
            overlay.innerHTML = `
                <div class="escandallo-modal-overlay"></div>
                <div class="escandallo-modal-content">
                    <button class="escandallo-modal-close">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="escandallo-modal-header">
                        <h3>${nombre}</h3>
                        <p>Ref: ${referencia}</p>
                    </div>
                    <div class="escandallo-modal-body">
                        <img src="${imagenUrl}" alt="${nombre}">
                    </div>
                </div>
            `;

            // Añadir al body
            document.body.appendChild(overlay);

            // Cerrar al hacer clic en el botón de cerrar
            const btnCerrar = overlay.querySelector('.escandallo-modal-close');
            btnCerrar.addEventListener('click', function() {
                document.body.removeChild(overlay);
            });

            // Cerrar al hacer clic fuera del contenido
            const modalOverlay = overlay.querySelector('.escandallo-modal-overlay');
            modalOverlay.addEventListener('click', function() {
                document.body.removeChild(overlay);
            });

            // Cerrar con ESC
            const handleEsc = function(e) {
                if (e.key === 'Escape' && document.body.contains(overlay)) {
                    document.body.removeChild(overlay);
                    document.removeEventListener('keydown', handleEsc);
                }
            };
            document.addEventListener('keydown', handleEsc);
        });
    });

});