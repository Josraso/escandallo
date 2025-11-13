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
    // AÑADIR AL CARRITO - USANDO SISTEMA 100% NATIVO DE PRESTASHOP
    // Los formularios se envían normalmente sin JavaScript
    // PrestaShop maneja todo: añadir, modal, contador, stock
    // ===================================

    // No interceptamos nada, dejamos que PrestaShop haga su magia

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
    // POPUP EN IMAGEN DE DIAGRAMA
    // ===================================

    const diagramImage = document.querySelector('.escandallo-diagram-image');

    if (diagramImage) {
        diagramImage.addEventListener('click', function() {
            const imagenUrl = this.src;
            const nombre = this.alt;

            // Crear overlay con modal
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
                        <p>Diagrama Técnico</p>
                    </div>
                    <div class="escandallo-modal-body">
                        <img src="${imagenUrl}" alt="${nombre}">
                    </div>
                </div>
            `;

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