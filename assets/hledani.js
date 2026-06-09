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
        li.classList.add(item.type);
        li.innerHTML = `
         <a href="${item.url}">
        ${item.type === 'clanek' ? '<strong>' + item.title + '</strong>' : item.title}
        ${item.date ? `<span><strong> (${item.date})</span></strong>` : ''}
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

    // 🔥 zavření search při otevření dropdownu
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', () => {
            box.classList.add('d-none');
            box.classList.remove('open');
        });
    });
});
