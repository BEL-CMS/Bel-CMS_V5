'use strict';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.news-card').forEach((card) => {
        card.addEventListener('mouseenter', () => card.classList.add('news-hover'));
        card.addEventListener('mouseleave', () => card.classList.remove('news-hover'));
    });
});
