export function render(container) {
    container.innerHTML = `
        <p>Tebak angka dari 1 sampai 10:</p>
        <input type="number" id="gInput" min="1" max="10" placeholder="Angka..." style="padding:10px; border-radius:5px; border:none; width:150px; text-align:center;">
        <br><br>
        <button id="gBtn">Tebak Sekarang</button>
        <p id="gMsg" style="margin-top:15px; font-weight:bold;"></p>
    `;

    let target = Math.floor(Math.random() * 10) + 1;
    const btn = container.querySelector('#gBtn');
    const input = container.querySelector('#gInput');
    const msg = container.querySelector('#gMsg');

    btn.onclick = () => {
        let val = Number(input.value);
        if (val === target) {
            msg.style.color = "#4ade80";
            msg.textContent = "🎉 Benar! Angka rahasianya adalah " + target;
        } else if (val < target) {
            msg.style.color = "#f87171";
            msg.textContent = "📈 Terlalu kecil!";
        } else {
            msg.style.color = "#f87171";
            msg.textContent = "📉 Terlalu besar!";
        }
    };
}
