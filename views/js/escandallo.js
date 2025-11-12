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
            const cartUrl = this.getAttribute('data-cart-url');
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

            // Crear FormData para enviar
            const formData = new FormData();
            formData.append('id_product', idProduct);
            formData.append('qty', '1');
            formData.append('add', '1');
            formData.append('action', 'update');
            formData.append('ajax', '1');
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
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.log('Respuesta del servidor:', text);
                        return { success: true };
                    });
                }
            })
            .then(data => {
                // Mostrar mensaje de éxito
                self.innerHTML = '<i class="material-icons">check_circle</i> ¡Añadido!';
                self.classList.remove('btn-primary');
                self.classList.add('btn-success');

                // Disparar eventos de PrestaShop para abrir el modal
                if (typeof prestashop !== 'undefined') {
                    // Disparar evento updateCart
                    if (prestashop.emit) {
                        prestashop.emit('updateCart', {
                            reason: {
                                idProduct: idProduct,
                                idProductAttribute: 0,
                                linkAction: 'add-to-cart',
                                cart: data
                            }
                        });
                    }

                    // Disparar evento personalizado para el modal
                    const cartEvent = new CustomEvent('updateCart', {
                        detail: data
                    });
                    document.body.dispatchEvent(cartEvent);
                }

                // También intentar abrir el modal directamente si existe
                setTimeout(() => {
                    const blockcartModal = document.querySelector('#blockcart-modal');
                    if (blockcartModal) {
                        // Si existe el modal de PrestaShop 1.7, abrirlo
                        if (typeof $ !== 'undefined' && $.fn.modal) {
                            $(blockcartModal).modal('show');
                        }
                    }
                }, 100);

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

    // ===================================
    // MODAL DE IMAGEN DEL PRODUCTO
    // ===================================

    const btnVerImagen = document.querySelectorAll('.escandallo-btn-ver-imagen');

    btnVerImagen.forEach(function(button) {
        button.addEventListener('click', function() {
            const imagenUrl = this.getAttribute('data-imagen');
            const nombre = this.getAttribute('data-nombre');
            const referencia = this.getAttribute('data-referencia');

            // Crear overlay para el modal
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.95);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                cursor: pointer;
                animation: fadeIn 0.3s ease;
            `;

            // Crear contenedor del contenido
            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                display: flex;
                flex-direction: column;
                align-items: center;
                cursor: default;
            `;

            // Crear título del producto
            const titulo = document.createElement('div');
            titulo.style.cssText = `
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 30px;
                border-radius: 10px 10px 0 0;
                text-align: center;
                width: 100%;
                max-width: 700px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            `;
            titulo.innerHTML = `
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 5px;">${nombre}</div>
                <div style="font-size: 14px; opacity: 0.9;">Ref: ${referencia}</div>
            `;

            // Crear imagen
            const imagen = document.createElement('img');
            imagen.src = imagenUrl;
            imagen.alt = nombre;
            imagen.style.cssText = `
                max-width: 700px;
                max-height: 500px;
                width: auto;
                height: auto;
                object-fit: contain;
                background: white;
                padding: 20px;
                border-radius: 0 0 10px 10px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
                cursor: zoom-in;
            `;

            // Botón cerrar
            const btnCerrar = document.createElement('button');
            btnCerrar.innerHTML = '<i class="material-icons">close</i>';
            btnCerrar.style.cssText = `
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
                z-index: 10000;
            `;

            btnCerrar.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 1)';
                this.style.transform = 'scale(1.1)';
            });

            btnCerrar.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.9)';
                this.style.transform = 'scale(1)';
            });

            // Ensamblar modal
            modalContent.appendChild(titulo);
            modalContent.appendChild(imagen);
            overlay.appendChild(btnCerrar);
            overlay.appendChild(modalContent);

            // Prevenir que el clic en el contenido cierre el modal
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Cerrar al hacer clic en el overlay
            overlay.addEventListener('click', function() {
                document.body.removeChild(overlay);
            });

            // Cerrar al hacer clic en el botón
            btnCerrar.addEventListener('click', function() {
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

            // Añadir al body
            document.body.appendChild(overlay);

            // Añadir animación CSS si no existe
            if (!document.getElementById('escandallo-modal-styles')) {
                const style = document.createElement('style');
                style.id = 'escandallo-modal-styles';
                style.innerHTML = `
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }
                `;
                document.head.appendChild(style);
            }
        });
    });

});