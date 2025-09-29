$(document).ready(function() {
    // Inicialización del calendario (ya manejada en Blade)
    // Funciones adicionales pueden ir aquí
    window.updateCalendarEvents = function() {
        $('#calendar').fullCalendar('refetchEvents');
    };
});
