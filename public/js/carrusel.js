document.querySelectorAll('[data-carrusel]').forEach((carrusel) => {
    const pista = carrusel.querySelector('[data-carrusel-pista]');
    const btnPrev = carrusel.querySelector('[data-carrusel-prev]');
    const btnNext = carrusel.querySelector('[data-carrusel-next]');

    if (!pista) return;

    const desplazar = (direccion) => {
        const slide = pista.querySelector('.carrusel__slide');
        const ancho = slide
            ? slide.getBoundingClientRect().width + 24 // 24px = gap (1.5rem)
            : pista.clientWidth * 0.8;

        pista.scrollBy({ left: direccion * ancho, behavior: 'smooth' });
    };

    btnPrev?.addEventListener('click', () => desplazar(-1));
    btnNext?.addEventListener('click', () => desplazar(1));
});