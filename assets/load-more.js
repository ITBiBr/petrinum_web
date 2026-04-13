document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('load-more');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const url = this.dataset.url;
        const offset = parseInt(this.dataset.offset);

        this.disabled = true;

        fetch(`${url}/?offset=${offset}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('data-wrapper')
                    .insertAdjacentHTML('beforeend', data.html);

                // aktualizace offsetu
                this.dataset.offset = data.newOffset;

                // pokud už není víc, tlačítko zmizí
                if (!data.hasMore) {
                    this.remove();
                    return;
                }

                this.disabled = false;
            })
            .catch(() => {
                this.disabled = false;
            });
    });
});
