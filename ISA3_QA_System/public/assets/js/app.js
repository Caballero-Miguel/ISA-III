document.addEventListener('click', (evento) => {
    const elemento = evento.target.closest('[data-confirmar]');

    if (elemento && !window.confirm(elemento.getAttribute('data-confirmar'))) {
        evento.preventDefault();
    }
});

window.setTimeout(() => {
    document.querySelectorAll('.mensaje-flash').forEach((mensaje) => {
        mensaje.hidden = true;
    });
}, 5000);
