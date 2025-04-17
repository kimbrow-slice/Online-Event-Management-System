document.addEventListener('DOMContentLoaded', () => {
  const f = document.getElementById('regForm');
  const m = document.getElementById('msg');

  f.addEventListener('submit', async (e) => {
    e.preventDefault();
    m.textContent = '';

    const data = new FormData(f);
    const res  = await fetch('register.php', { method:'POST', body:data });

    if (res.redirected) {        // server sent Location header
      window.location = res.url;
    } else {
      m.textContent = await res.text();
    }
  });
});
