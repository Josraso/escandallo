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
    // El formulario se envía de forma normal, PrestaShop maneja todo
    // Solo añadimos efecto visual al botón al hacer clic
    // ===================================

    const addToCartButtons = document.querySelectorAll('.escandallo-btn-add-cart');

    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Deshabilitar botón y mostrar spinner mientras se procesa
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
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