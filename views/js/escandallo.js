/**
 * JavaScript para el módulo Escandallo
 * Maneja la funcionalidad de añadir al carrito y otras interacciones
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===================================
    // AÑADIR AL CARRITO
    // ===================================
    
    const addToCartButtons = document.querySelectorAll('.escandallo-btn-add-cart');
    
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const idProduct = this.getAttribute('data-id-product');
            let cartUrl = this.getAttribute('data-cart-url');
            const originalText = this.innerHTML;
            const self = this;

            // Deshabilitar botón mientras se procesa
            this.disabled = true;
            this.innerHTML = '<i class="material-icons">hourglass_empty</i> Añadiendo...';

            // Obtener token estático
            let staticToken = '';
            if (typeof prestashop !== 'undefined' && prestashop.static_token) {
                staticToken = prestashop.static_token;
            } else {
                // Fallback: buscar token en el DOM
                const tokenInput = document.querySelector('input[name="token"]');
                if (tokenInput) {
                    staticToken = tokenInput.value;
                }
            }

            // Añadir ajax=1 para obtener respuesta JSON
            if (cartUrl.indexOf('?') === -1) {
                cartUrl += '?ajax=1&action=update';
            } else {
                cartUrl += '&ajax=1&action=update';
            }

            // Crear FormData para enviar
            const formData = new FormData();
            formData.append('id_product', idProduct);
            formData.append('qty', '1');
            formData.append('add', '1');
            formData.append('action', 'update');
            if (staticToken) {
                formData.append('token', staticToken);
            }

            // Enviar petición AJAX
            fetch(cartUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Si no es JSON, consideramos que se añadió correctamente
                    return { success: true };
                }
            })
            .then(data => {
                // Mostrar mensaje de éxito
                self.innerHTML = '<i class="material-icons">check_circle</i> ¡Añadido!';
                self.classList.remove('btn-primary');
                self.classList.add('btn-success');

                // Actualizar contador del carrito si existe
                if (typeof prestashop !== 'undefined') {
                    if (prestashop.emit) {
                        prestashop.emit('updateCart', {
                            reason: data
                        });
                    } else {
                        // Recargar el bloque del carrito manualmente
                        prestashop.cart = prestashop.cart || {};
                        prestashop.cart.products_count = (prestashop.cart.products_count || 0) + 1;
                    }
                }

                // Restaurar botón después de 2 segundos
                setTimeout(() => {
                    self.innerHTML = originalText;
                    self.classList.remove('btn-success');
                    self.classList.add('btn-primary');
                    self.disabled = false;
                }, 2000);
            })
            .catch(error => {
                console.error('Error al añadir al carrito:', error);
                self.innerHTML = '<i class="material-icons">error</i> Error';
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
    
});