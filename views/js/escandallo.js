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
            
            // Deshabilitar botón mientras se procesa
            this.disabled = true;
            this.innerHTML = '<i class="material-icons">hourglass_empty</i> Añadiendo...';
            
            // Crear formulario para enviar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = cartUrl;
            
            const inputProduct = document.createElement('input');
            inputProduct.type = 'hidden';
            inputProduct.name = 'id_product';
            inputProduct.value = idProduct;
            
            const inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = 'qty';
            inputQty.value = '1';
            
            const inputAdd = document.createElement('input');
            inputAdd.type = 'hidden';
            inputAdd.name = 'add';
            inputAdd.value = '1';
            
            const inputToken = document.createElement('input');
            inputToken.type = 'hidden';
            inputToken.name = 'token';
            inputToken.value = prestashop.static_token;
            
            form.appendChild(inputProduct);
            form.appendChild(inputQty);
            form.appendChild(inputAdd);
            form.appendChild(inputToken);
            
            document.body.appendChild(form);
            
            // Enviar formulario
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                // Mostrar mensaje de éxito
                this.innerHTML = '<i class="material-icons">check_circle</i> ¡Añadido!';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                
                // Actualizar contador del carrito si existe
                if (typeof prestashop !== 'undefined' && prestashop.cart) {
                    prestashop.emit('updateCart', {
                        reason: data
                    });
                }
                
                // Restaurar botón después de 2 segundos
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                    this.disabled = false;
                }, 2000);
                
                document.body.removeChild(form);
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = '<i class="material-icons">error</i> Error';
                this.classList.remove('btn-primary');
                this.classList.add('btn-danger');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-danger');
                    this.classList.add('btn-primary');
                    this.disabled = false;
                }, 2000);
                
                document.body.removeChild(form);
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