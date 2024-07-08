// JavaScript para animaciones simples
document.addEventListener('DOMContentLoaded', function() {
    // Agregar clase de animación a las notificaciones al cargar la página
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        notification.classList.add('fadeIn');
    });
});