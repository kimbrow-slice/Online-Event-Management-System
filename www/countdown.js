// countdown.js
document.addEventListener('DOMContentLoaded', () => {
    const els = document.querySelectorAll('.countdown');

    function update() {
        const now = Date.now();
        els.forEach(el => {
            const target = new Date(el.dataset.target).getTime();
            let diff = target - now;
            if (diff <= 0) {
                el.textContent = 'Started';
                return;
            }
            const days    = Math.floor(diff / 86400000);
            diff %= 86400000;
            const hours   = Math.floor(diff / 3600000);
            diff %= 3600000;
            const minutes = Math.floor(diff / 60000);
            diff %= 60000;
            const seconds = Math.floor(diff / 1000);

            el.textContent =
                (days    ? days    + 'd ' : '') +
                (hours   ? hours   + 'h ' : '') +
                (minutes ? minutes + 'm ' : '') +
                seconds + 's';
        });
    }

    update();
    setInterval(update, 1000);
});