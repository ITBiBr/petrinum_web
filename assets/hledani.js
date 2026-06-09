const input = document.getElementById('search');
const suggestions = document.getElementById('suggestions');

input.addEventListener('input', async () => {
    const q = input.value.trim();

    if (q.length < 2) {
        suggestions.innerHTML = '';
        return;
    }

    const response = await fetch('/hledani?q=' + encodeURIComponent(q));
    const data = await response.json();

    suggestions.innerHTML = '';

    data.forEach(item => {
        const li = document.createElement('li');

        li.innerHTML = `
        <a href="${item.url}">
            <span>${item.title}</span>
        </a>
        `;


        suggestions.appendChild(li);
    });
});
document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById('search-toggle');
    const box = document.getElementById('search-box');
    const input = document.getElementById('search');

    toggle.addEventListener('click', () => {
        box.classList.toggle('d-none');

        if (!box.classList.contains('d-none')) {
            input.focus();
        }
    });
    toggle.addEventListener('click', () => {
        box.classList.toggle('open');
        input.focus();
    });

});
