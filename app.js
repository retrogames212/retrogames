// Database Daftar 100 Game (Skalabel: Cukup daftarkan metadata di sini)
const gamesDatabase = [
    { id: 1, title: "Tebak Angka", desc: "Tebak angka rahasia dari 1 sampai 10.", file: "game-001.js" },
    { id: 2, title: "Batu Gunting Kertas", desc: "Classic rock-paper-scissors vs bot.", file: "game-002.js" },
    { id: 3, title: "Lempar Dadu Cepat", desc: "Uji keberuntungan angka dadu Anda.", file: "game-003.js" },
    // ... Tambahkan hingga game-100.js di sini dengan pola yang sama!
];

const menuSection = document.getElementById('menuSection');
const gameSection = document.getElementById('gameSection');
const gameGrid = document.getElementById('gameGrid');
const gameTitle = document.getElementById('gameTitle');
const gameViewport = document.getElementById('gameViewport');
const backBtn = document.getElementById('backBtn');

// Render Menu Utama Secara Otomatis
function initMenu() {
    gameGrid.innerHTML = "";
    gamesDatabase.forEach(game => {
        const card = document.createElement('div');
        card.className = 'game-card';
        card.innerHTML = `
            <h3>#${game.id} - ${game.title}</h3>
            <p>${game.desc}</p>
        `;
        card.onclick = () => loadGame(game);
        gameGrid.appendChild(card);
    });
}

// Fungsi Memuat Game Dinamis
async function loadGame(game) {
    menuSection.classList.add('hidden');
    gameSection.classList.remove('hidden');
    backBtn.classList.remove('hidden');
    gameTitle.textContent = game.title;
    gameViewport.innerHTML = "<p>Memuat game...</p>";

    try {
        // Dynamic import file game dari folder games/
        const module = await import(`./games/${game.file}`);
        gameViewport.innerHTML = ""; // Bersihkan loading
        
        // Setiap file game wajib mengekspor fungsi render(container)
        if (typeof module.render === 'function') {
            module.render(gameViewport);
        } else {
            gameViewport.innerHTML = "<p style='color: red;'>Struktur file game tidak valid (Fungsi render tidak ditemukan).</p>";
        }
    } catch (error) {
        // Fallback demo jika file fisik game-002.js / game-003.js belum dibuat
        gameViewport.innerHTML = `
            <h3>🚧 Game Sedang Dalam Pengembangan</h3>
            <p>File <code>games/${game.file}</code> belum dibuat atau sedang disiapkan.</p>
            <p style="font-size:0.8rem; color:#64748b;">(Buat file JS tersebut dengan struktur modular untuk memainkannya)</p>
        `;
    }
}

// Kembali ke Menu Utama
function returnToHome() {
    gameSection.classList.add('hidden');
    backBtn.classList.add('hidden');
    menuSection.classList.remove('hidden');
    gameViewport.innerHTML = "";
}

// Inisialisasi saat pertama buka
initMenu();
