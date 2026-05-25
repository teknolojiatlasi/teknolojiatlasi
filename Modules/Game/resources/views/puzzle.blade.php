<x-game::layouts.master>
    <style>
        :root {
            --bg-1: #f7f1e8;
            --bg-2: #e8f3f1;
            --bg-3: #f3e0d2;
            --ink: #1b1b1f;
            --muted: #60626b;
            --card: #ffffff;
            --accent: #ff7a00;
            --accent-2: #0f766e;
            --accent-3: #00fdf6;
            --shadow: 0 24px 60px rgba(16, 24, 40, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at top, #fff7ec, transparent 55%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2) 45%, var(--bg-3));
        }

        .match-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: clamp(16px, 3vw, 40px);
        }

        .match-shell {
            width: min(1020px, 100%);
            display: grid;
            gap: 20px;
            animation: float-in 0.8s ease both;
        }

        .match-header {
            background: var(--card);
            border-radius: 24px;
            padding: 20px 24px;
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
        }

        .eyebrow {
            font-size: 12px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--accent-2);
            font-weight: 700;
        }

        .match-title {
            margin: 6px 0 4px;
            font-size: clamp(26px, 4vw, 40px);
        }

        .match-subtitle {
            margin: 0;
            color: var(--muted);
            max-width: 520px;
        }

        .lang-toggle {
            display: flex;
            gap: 8px;
            background: #f2f0ea;
            padding: 6px;
            border-radius: 999px;
        }

        .lang-toggle button {
            border: 0;
            background: transparent;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            color: var(--muted);
            transition: all 0.2s ease;
        }

        .lang-toggle button.active {
            background: var(--ink);
            color: #fff;
        }

        .match-body {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
            gap: 20px;
        }

        .match-card,
        .match-side {
            background: var(--card);
            border-radius: 24px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .match-card h2 {
            margin: 0 0 6px;
            font-size: 24px;
        }

        .match-clue {
            color: var(--muted);
            margin: 0 0 16px;
        }

        .match-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .match-list {
            display: grid;
            gap: 10px;
        }

        .match-item {
            border: 0;
            width: 100%;
            text-align: left;
            padding: 12px 14px;
            border-radius: 16px;
            font-weight: 600;
            font-family: "Space Grotesk", system-ui, sans-serif;
            background: #f8f4ee;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease;
        }

        .match-item.active {
            background: #111827;
            color: #fff;
        }

        .match-item.matched {
            background: #e6f6f0;
            color: #0f766e;
            cursor: default;
        }

        .match-item.wrong {
            background: #ffe4e6;
            color: #be123c;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .btn {
            border: 0;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            background: var(--accent);
            color: #fff;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn.secondary {
            background: #f1eee7;
            color: var(--ink);
        }

        .btn.ghost {
            background: transparent;
            color: var(--accent-3);
            box-shadow: inset 0 0 0 1px rgba(29, 78, 216, 0.2);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .status {
            margin-top: 14px;
            font-weight: 600;
            color: var(--accent-2);
        }

        .stats {
            display: grid;
            gap: 10px;
            margin-top: 16px;
        }

        .stat {
            display: flex;
            justify-content: space-between;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
            background: #f8f4ee;
            padding: 10px 12px;
            border-radius: 12px;
        }

        .match-side h3 {
            margin: 0 0 10px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .rules {
            display: grid;
            gap: 10px;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f8f4ee;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-2);
        }

        @keyframes float-in {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 980px) {
            .match-body {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="match-page">
        <div class="match-shell">
            <header class="match-header">
                <div>
                    <div class="eyebrow">Puzzle Lab</div>
                    <h1 class="match-title" id="page-title">Kelime-Anlam Eslesmesi</h1>
                    <p class="match-subtitle" id="page-subtitle">
                        Kelimeyi dogru anlamiyla eslestir. English mode is one tap away.
                    </p>
                </div>
                <div class="lang-toggle" role="tablist" aria-label="Language toggle">
                    <button type="button" class="active" data-lang="tr" role="tab" aria-selected="true">TR</button>
                    <button type="button" data-lang="en" role="tab" aria-selected="false">EN</button>
                </div>
            </header>

            <section class="match-body">
                <div class="match-card">
                    <h2 id="match-title">Eslesme Oyunu</h2>
                    <p class="match-clue" id="match-clue">Sol taraf kelimeler, sag taraf anlamlar.</p>
                    <div class="match-grid">
                        <div class="match-list" id="word-list"></div>
                        <div class="match-list" id="meaning-list"></div>
                    </div>
                    <div class="actions">
                        <button class="btn" id="new-round-btn">Yeni Tur</button>
                        <button class="btn secondary" id="reset-btn">Sifirla</button>
                        <button class="btn ghost" id="reveal-btn">Ipu</button>
                    </div>
                    <div class="status" id="status">Hazir</div>
                    <div class="stats">
                        <div class="stat"><span>Deneme</span><span id="tries-count">0</span></div>
                        <div class="stat"><span>Dogru</span><span id="correct-count">0</span></div>
                        <div class="stat"><span>Seri</span><span id="streak-count">0</span></div>
                    </div>
                </div>

                <aside class="match-side">
                    <h3>Kurallar / Rules</h3>
                    <div class="rules" id="rules-text">
                        <div class="tag">TR</div>
                        <div>1. Bir kelime sec, sonra anlamini sec.</div>
                        <div>2. Dogru eslesme kilitlenir.</div>
                        <div>3. Ipu ilk dogru eslesmeyi gosterir.</div>
                        <div class="tag">EN</div>
                        <div>1. Bir kelime sec, sonra anlamini sec.</div>
                        <div>2. Dogru eslesmeler sabitlenir.</div>
                        <div>3. Ipu bir dogru cifti gosterir.</div>
                    </div>
                </aside>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const data = {
                tr: [
                    { word: "bulmaca", meaning: "zihni calistiran oyun" },
                    { word: "macera", meaning: "kesif dolu yolculuk" },
                    { word: "kilit", meaning: "guvende tutan arac" },
                    { word: "labirent", meaning: "cikisi bulmasi zor yer" },
                    { word: "sir", meaning: "herkesin bilmedigi sey" },
                    { word: "yildiz", meaning: "gece gokyuzunde parlar" },
                    { word: "sifre", meaning: "gizli kod" },
                    { word: "kasif", meaning: "yeni yerleri arastiran kisi" },
                    { word: "kutuphane", meaning: "kitaplarla dolu yer" },
                    { word: "gunes", meaning: "dunyanin isik kaynagi" },
                    { word: "nehir", meaning: "uzun su yolu" },
                    { word: "dag", meaning: "yuksek kara parcasi" },
                    { word: "pusula", meaning: "yon bulma araci" },
                    { word: "harita", meaning: "bir bolgenin cizimi" },
                    { word: "fener", meaning: "isik veren tasinabilir lamba" },
                ],
                en: [
                    { word: "puzzle", meaning: "a brain-teasing game" },
                    { word: "adventure", meaning: "a journey full of discovery" },
                    { word: "lock", meaning: "keeps something secure" },
                    { word: "maze", meaning: "a confusing network of paths" },
                    { word: "secret", meaning: "something not everyone knows" },
                    { word: "star", meaning: "shines in the night sky" },
                    { word: "cipher", meaning: "a secret code" },
                    { word: "explorer", meaning: "one who explores" },
                    { word: "library", meaning: "a place full of books" },
                    { word: "sun", meaning: "the source of daylight" },
                    { word: "river", meaning: "a long flowing stream" },
                    { word: "mountain", meaning: "a very high landform" },
                    { word: "compass", meaning: "a tool for finding direction" },
                    { word: "map", meaning: "a drawing of an area" },
                    { word: "lantern", meaning: "a portable light source" },
                ],
            };

            const langButtons = document.querySelectorAll(".lang-toggle button");
            const wordList = document.getElementById("word-list");
            const meaningList = document.getElementById("meaning-list");
            const statusEl = document.getElementById("status");
            const triesCount = document.getElementById("tries-count");
            const correctCount = document.getElementById("correct-count");
            const streakCount = document.getElementById("streak-count");
            const newRoundBtn = document.getElementById("new-round-btn");
            const resetBtn = document.getElementById("reset-btn");
            const revealBtn = document.getElementById("reveal-btn");
            const pageTitle = document.getElementById("page-title");
            const pageSubtitle = document.getElementById("page-subtitle");
            const matchTitle = document.getElementById("match-title");
            const matchClue = document.getElementById("match-clue");

            const ROUND_SIZE = 15;
            let currentLang = "tr";
            let roundPairs = [];
            let selectedWord = null;
            let selectedMeaning = null;
            let tries = 0;
            let correct = 0;
            let streak = 0;

            function shuffle(array) {
                const copy = [...array];
                for (let i = copy.length - 1; i > 0; i -= 1) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [copy[i], copy[j]] = [copy[j], copy[i]];
                }
                return copy;
            }

            function setStatus(message, success = true) {
                statusEl.textContent = message;
                statusEl.style.color = success ? "var(--accent-2)" : "#b91c1c";
            }

            function updateCounters() {
                triesCount.textContent = tries;
                correctCount.textContent = correct;
                streakCount.textContent = streak;
            }

            function updateLabels() {
                const labels = {
                    tr: {
                        title: "Kelime-Anlam Eslesmesi",
                        subtitle: "Kelimeyi dogru anlamiyla eslestir. Dilersen Ingilizceye gec.",
                        match: "Eslesme Oyunu",
                        clue: "Sol taraf kelimeler, sag taraf anlamlar.",
                        ready: "Hazir",
                        correct: "Dogru eslesme!",
                        wrong: "Yanlis eslesme.",
                        round: "Yeni Tur",
                        reset: "Sifirla",
                        hint: "Ipu",
                        stats: ["Deneme", "Dogru", "Seri"],
                        hintText: "Ipu: bir dogru eslesme gosterildi.",
                        win: "Turu bitirdin!",
                    },
                    en: {
                        title: "Kelime-Anlam Eşleştirme",
                        subtitle: "Her kelimeyi doğru anlamıyla eşleştir.",
                        match: "Eşleştirme Oyunu",
                        clue: "Sol tarafta kelimeler, sağ tarafta anlamlar.",
                        ready: "Hazır",
                        correct: "Doğru eşleşme!",
                        wrong: "Yanlış eşleşme.",
                        round: "Yeni Tur",
                        reset: "Sıfırla",
                        hint: "İpucu",
                        stats: ["Deneme", "Doğru", "Seri"],
                        hintText: "İpucu: bir doğru eşleşme gösterildi.",
                        win: "Tur tamamlandı!",
                    },
                };
                const dataLabels = labels[currentLang];
                pageTitle.textContent = dataLabels.title;
                pageSubtitle.textContent = dataLabels.subtitle;
                matchTitle.textContent = dataLabels.match;
                matchClue.textContent = dataLabels.clue;
                newRoundBtn.textContent = dataLabels.round;
                resetBtn.textContent = dataLabels.reset;
                revealBtn.textContent = dataLabels.hint;
                setStatus(dataLabels.ready, true);
                const statLabels = document.querySelectorAll(".stat span:first-child");
                statLabels.forEach((label, index) => {
                    if (dataLabels.stats[index]) {
                        label.textContent = dataLabels.stats[index];
                    }
                });
            }

            function buildRound() {
                const pool = shuffle(data[currentLang]);
                roundPairs = pool.slice(0, Math.min(ROUND_SIZE, pool.length)).map((pair, index) => ({
                    id: `${currentLang}-${index}`,
                    word: pair.word,
                    meaning: pair.meaning,
                    matched: false,
                }));
                selectedWord = null;
                selectedMeaning = null;
                renderLists();
                setStatus("Hazır", true);
            }

            function renderLists() {
                const words = shuffle(roundPairs.map(pair => ({ id: pair.id, text: pair.word })));
                const meanings = shuffle(roundPairs.map(pair => ({ id: pair.id, text: pair.meaning })));
                wordList.innerHTML = "";
                meaningList.innerHTML = "";
                words.forEach(item => {
                    const button = document.createElement("button");
                    button.type = "button";
                    button.className = "match-item";
                    button.dataset.id = item.id;
                    button.textContent = item.text;
                    button.addEventListener("click", () => selectWord(button));
                    wordList.appendChild(button);
                });
                meanings.forEach(item => {
                    const button = document.createElement("button");
                    button.type = "button";
                    button.className = "match-item";
                    button.dataset.id = item.id;
                    button.textContent = item.text;
                    button.addEventListener("click", () => selectMeaning(button));
                    meaningList.appendChild(button);
                });
            }

            function selectWord(button) {
                if (button.classList.contains("matched")) return;
                document.querySelectorAll("#word-list .match-item").forEach(item => item.classList.remove("active"));
                button.classList.add("active");
                selectedWord = button;
                tryMatch();
            }

            function selectMeaning(button) {
                if (button.classList.contains("matched")) return;
                document.querySelectorAll("#meaning-list .match-item").forEach(item => item.classList.remove("active"));
                button.classList.add("active");
                selectedMeaning = button;
                tryMatch();
            }

            function markMatched(id) {
                const wordBtn = wordList.querySelector(`[data-id="${id}"]`);
                const meaningBtn = meaningList.querySelector(`[data-id="${id}"]`);
                if (wordBtn) {
                    wordBtn.classList.remove("active");
                    wordBtn.classList.add("matched");
                    wordBtn.disabled = true;
                }
                if (meaningBtn) {
                    meaningBtn.classList.remove("active");
                    meaningBtn.classList.add("matched");
                    meaningBtn.disabled = true;
                }
            }

            function clearWrong(buttons) {
                buttons.forEach(button => {
                    if (!button) return;
                    button.classList.remove("active");
                    button.classList.add("wrong");
                    setTimeout(() => button.classList.remove("wrong"), 350);
                });
            }

            function tryMatch() {
                if (!selectedWord || !selectedMeaning) return;
                tries += 1;
                if (selectedWord.dataset.id === selectedMeaning.dataset.id) {
                    const id = selectedWord.dataset.id;
                    markMatched(id);
                    correct += 1;
                    streak += 1;
                    setStatus("Dogru eslesme!", true);
                } else {
                    streak = 0;
                    setStatus("Yanlis eslesme.", false);
                    clearWrong([selectedWord, selectedMeaning]);
                }
                selectedWord = null;
                selectedMeaning = null;
                updateCounters();
                if (correct > 0 && correct % roundPairs.length === 0) {
                    setStatus("Turu bitirdin!", true);
                }
            }

            function resetGame() {
                tries = 0;
                correct = 0;
                streak = 0;
                updateCounters();
                buildRound();
            }

            function revealHint() {
                const remaining = roundPairs.filter(pair => {
                    const wordBtn = wordList.querySelector(`[data-id="${pair.id}"]`);
                    return wordBtn && !wordBtn.classList.contains("matched");
                });
                if (remaining.length === 0) return;
                const hintPair = remaining[Math.floor(Math.random() * remaining.length)];
                markMatched(hintPair.id);
                correct += 1;
                streak += 1;
                setStatus("Ipu: bir dogru eslesme gosterildi.", true);
                updateCounters();
            }

            langButtons.forEach(button => {
                button.addEventListener("click", () => {
                    langButtons.forEach(btn => {
                        btn.classList.remove("active");
                        btn.setAttribute("aria-selected", "false");
                    });
                    button.classList.add("active");
                    button.setAttribute("aria-selected", "true");
                    currentLang = button.dataset.lang;
                    updateLabels();
                    resetGame();
                });
            });

            newRoundBtn.addEventListener("click", buildRound);
            resetBtn.addEventListener("click", resetGame);
            revealBtn.addEventListener("click", revealHint);

            updateLabels();
            resetGame();
        });
    </script>
</x-game::layouts.master>
