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
            --accent-3: #1d4ed8;
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
            width: min(1080px, 100%);
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

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
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

        .category-select {
            border: 1px solid #e2e0da;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 600;
            font-family: "Space Grotesk", system-ui, sans-serif;
            background: #fff;
            color: var(--ink);
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

        .match-column-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            margin-bottom: 8px;
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

        .rules,
        .explanation {
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

        .explanation-card {
            margin-top: 16px;
            padding: 14px;
            border-radius: 16px;
            background: #f8f4ee;
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
                    <h1 class="match-title" id="page-title">Kelime-Anlam Ceviri Eslesmesi</h1>
                    <p class="match-subtitle" id="page-subtitle">
                        Kelimeyi dogru cevirisiyle eslestir. English mode is one tap away.
                    </p>
                </div>
                <div class="header-actions">
                    <select class="category-select" id="category-select" aria-label="Kategori">
                        <option value="mixed">Karisik</option>
                        <option value="nature">Doga</option>
                        <option value="tech">Teknoloji</option>
                        <option value="city">Sehir</option>
                        <option value="food">Yemek</option>
                    </select>
                    <div class="lang-toggle" role="tablist" aria-label="Language toggle">
                        <button type="button" class="active" data-lang="tr" role="tab" aria-selected="true">TR</button>
                        <button type="button" data-lang="en" role="tab" aria-selected="false">EN</button>
                    </div>
                </div>
            </header>

            <section class="match-body">
                <div class="match-card">
                    <h2 id="match-title">Ceviri Eslesme</h2>
                    <p class="match-clue" id="match-clue">Sol: kelime, sag: ceviri.</p>
                    <div class="match-grid">
                        <div>
                            <div class="match-column-title" id="left-title">Kelime (TR)</div>
                            <div class="match-list" id="word-list"></div>
                        </div>
                        <div>
                            <div class="match-column-title" id="right-title">Meaning (EN)</div>
                            <div class="match-list" id="meaning-list"></div>
                        </div>
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
                        <div>1. Bir kelime sec, sonra cevirisini sec.</div>
                        <div>2. Yanlis eslesme geri kapanir.</div>
                        <div>3. Dogru eslesmede aciklama gorunur.</div>
                        <div class="tag">EN</div>
                        <div>1. Pick a word, then pick its translation.</div>
                        <div>2. Wrong pairs flip back.</div>
                        <div>3. Dogru eslesmeler aciklamayi gosterir.</div>
                    </div>
                    <div class="explanation-card">
                        <h3 id="explain-title">Aciklama</h3>
                        <div class="explanation" id="explain-text">Bir eslesme yap ve aciklamayi gor.</div>
                    </div>
                </aside>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const categories = {
                mixed: {
                    tr: "Karisik",
                    en: "Mixed",
                },
                nature: {
                    tr: "Doga",
                    en: "Nature",
                },
                tech: {
                    tr: "Teknoloji",
                    en: "Technology",
                },
                city: {
                    tr: "Sehir",
                    en: "City",
                },
                food: {
                    tr: "Yemek",
                    en: "Food",
                },
            };

            const pairs = [
                {
                    id: "nature-forest",
                    category: "nature",
                    word_tr: "orman",
                    word_en: "forest",
                    explain_tr: "Orman, cok sayida agacin bulundugu genis alandir.",
                    explain_en: "A forest is a large area covered with trees.",
                },
                {
                    id: "nature-river",
                    category: "nature",
                    word_tr: "nehir",
                    word_en: "river",
                    explain_tr: "Nehir, uzun bir su yoludur.",
                    explain_en: "A river is a long, flowing body of water.",
                },
                {
                    id: "nature-mountain",
                    category: "nature",
                    word_tr: "dag",
                    word_en: "mountain",
                    explain_tr: "Dag, yuksek ve buyuk kara parcasi.",
                    explain_en: "A mountain is a very high landform.",
                },
                {
                    id: "nature-planet",
                    category: "nature",
                    word_tr: "gezegen",
                    word_en: "planet",
                    explain_tr: "Gezegen, bir yildizin etrafinda dolanan gok cismidir.",
                    explain_en: "A planet orbits a star.",
                },
                {
                    id: "nature-storm",
                    category: "nature",
                    word_tr: "firtina",
                    word_en: "storm",
                    explain_tr: "Firtina, siddetli ruzgarla gelen hava olayidir.",
                    explain_en: "A storm brings strong wind and weather.",
                },
                {
                    id: "tech-robot",
                    category: "tech",
                    word_tr: "robot",
                    word_en: "robot",
                    explain_tr: "Robot, belirli gorevleri yapan otomatik makinedir.",
                    explain_en: "A robot is a machine that performs tasks.",
                },
                {
                    id: "tech-software",
                    category: "tech",
                    word_tr: "yazilim",
                    word_en: "software",
                    explain_tr: "Yazilim, bilgisayara ne yapacagini soyleyen kodlardir.",
                    explain_en: "Software is the code that runs on computers.",
                },
                {
                    id: "tech-network",
                    category: "tech",
                    word_tr: "ag",
                    word_en: "network",
                    explain_tr: "Ag, bagli cihazlarin olusturdugu yapidir.",
                    explain_en: "A network connects multiple devices.",
                },
                {
                    id: "tech-processor",
                    category: "tech",
                    word_tr: "islemci",
                    word_en: "processor",
                    explain_tr: "Islemci, bilgisayarin hesaplama yapan parcasidir.",
                    explain_en: "A processor handles computations.",
                },
                {
                    id: "tech-database",
                    category: "tech",
                    word_tr: "veritabani",
                    word_en: "database",
                    explain_tr: "Veritabani, duzenli veri depolama sistemidir.",
                    explain_en: "A database stores organized data.",
                },
                {
                    id: "city-street",
                    category: "city",
                    word_tr: "sokak",
                    word_en: "street",
                    explain_tr: "Sokak, sehir icindeki yolun kucuk parcasidir.",
                    explain_en: "A street is a road in a city.",
                },
                {
                    id: "city-bridge",
                    category: "city",
                    word_tr: "kopru",
                    word_en: "bridge",
                    explain_tr: "Kopru, iki yeri birlestiren yapidir.",
                    explain_en: "A bridge connects two places.",
                },
                {
                    id: "city-market",
                    category: "city",
                    word_tr: "pazar",
                    word_en: "market",
                    explain_tr: "Pazar, alisverisin yapildigi yerdir.",
                    explain_en: "A market is where people shop.",
                },
                {
                    id: "city-museum",
                    category: "city",
                    word_tr: "muze",
                    word_en: "museum",
                    explain_tr: "Muze, eserlerin sergilendigi yerdir.",
                    explain_en: "A museum displays collections.",
                },
                {
                    id: "city-hospital",
                    category: "city",
                    word_tr: "hastane",
                    word_en: "hospital",
                    explain_tr: "Hastane, saglik hizmeti sunar.",
                    explain_en: "A hospital provides medical care.",
                },
                {
                    id: "food-bread",
                    category: "food",
                    word_tr: "ekmek",
                    word_en: "bread",
                    explain_tr: "Ekmek, un ve sudan yapilan temel gidadir.",
                    explain_en: "Bread is a staple made from flour and water.",
                },
                {
                    id: "food-cheese",
                    category: "food",
                    word_tr: "peynir",
                    word_en: "cheese",
                    explain_tr: "Peynir, sutten elde edilen gidadir.",
                    explain_en: "Cheese is made from milk.",
                },
                {
                    id: "food-apple",
                    category: "food",
                    word_tr: "elma",
                    word_en: "apple",
                    explain_tr: "Elma, tatli bir meyvedir.",
                    explain_en: "An apple is a sweet fruit.",
                },
                {
                    id: "food-soup",
                    category: "food",
                    word_tr: "corba",
                    word_en: "soup",
                    explain_tr: "Corba, sicak ve sulu bir yemektir.",
                    explain_en: "Soup is a warm, liquid dish.",
                },
                {
                    id: "food-coffee",
                    category: "food",
                    word_tr: "kahve",
                    word_en: "coffee",
                    explain_tr: "Kahve, kavrulmus cekirdeklerden elde edilir.",
                    explain_en: "Coffee is made from roasted beans.",
                },
            ];

            const ROUND_SIZE = 8;

            const langButtons = document.querySelectorAll(".lang-toggle button");
            const categorySelect = document.getElementById("category-select");
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
            const leftTitle = document.getElementById("left-title");
            const rightTitle = document.getElementById("right-title");
            const explainTitle = document.getElementById("explain-title");
            const explainText = document.getElementById("explain-text");

            let currentLang = "tr";
            let currentCategory = "mixed";
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
                        title: "Kelime-Anlam Ceviri Eslesmesi",
                        subtitle: "Kelimeyi dogru cevirisiyle eslestir. Dilersen Ingilizceye gec.",
                        match: "Ceviri Eslesme",
                        clue: "Sol: kelime, sag: ceviri.",
                        ready: "Hazir",
                        correct: "Dogru eslesme!",
                        wrong: "Yanlis eslesme.",
                        round: "Yeni Tur",
                        reset: "Sifirla",
                        hint: "Ipu",
                        stats: ["Deneme", "Dogru", "Seri"],
                        explainTitle: "Aciklama",
                        explainText: "Bir eslesme yap ve aciklamayi gor.",
                        leftTitle: "Kelime (TR)",
                        rightTitle: "Anlam (EN)",
                    },
                    en: {
                        title: "Çeviri Eşleştirme",
                        subtitle: "Her kelimeyi doğru çevirisiyle eşleştir.",
                        match: "Çeviri Eşleştirme",
                        clue: "Sol: kelime, sağ: çeviri.",
                        ready: "Hazır",
                        correct: "Doğru eşleşme!",
                        wrong: "Yanlış eşleşme.",
                        round: "Yeni Tur",
                        reset: "Sıfırla",
                        hint: "İpucu",
                        stats: ["Deneme", "Doğru", "Seri"],
                        explainTitle: "Açıklama",
                        explainText: "Açıklamayı görmek için bir eşleşme yap.",
                        leftTitle: "Kelime (EN)",
                        rightTitle: "Anlam (TR)",
                    },
                };
                const text = labels[currentLang];
                pageTitle.textContent = text.title;
                pageSubtitle.textContent = text.subtitle;
                matchTitle.textContent = text.match;
                matchClue.textContent = text.clue;
                newRoundBtn.textContent = text.round;
                resetBtn.textContent = text.reset;
                revealBtn.textContent = text.hint;
                explainTitle.textContent = text.explainTitle;
                explainText.textContent = text.explainText;
                leftTitle.textContent = text.leftTitle;
                rightTitle.textContent = text.rightTitle;
                setStatus(text.ready, true);
                const statLabels = document.querySelectorAll(".stat span:first-child");
                statLabels.forEach((label, index) => {
                    if (text.stats[index]) {
                        label.textContent = text.stats[index];
                    }
                });
                Array.from(categorySelect.options).forEach(option => {
                    const cat = categories[option.value];
                    if (!cat) return;
                    option.textContent = currentLang === "tr" ? cat.tr : cat.en;
                });
            }

            function getPool() {
                if (currentCategory === "mixed") {
                    return pairs;
                }
                return pairs.filter(pair => pair.category === currentCategory);
            }

            function buildRound() {
                const pool = shuffle(getPool());
                const count = Math.min(ROUND_SIZE, pool.length);
                roundPairs = pool.slice(0, count).map((pair, index) => ({
                    ...pair,
                    slotId: `${pair.id}-${index}`,
                }));
                selectedWord = null;
                selectedMeaning = null;
                renderLists();
                setStatus("Hazır", true);
                explainText.textContent =
                    currentLang === "tr"
                        ? "Bir eslesme yap ve aciklamayi gor."
                        : "Açıklamayı görmek için bir eşleşme yap.";
            }

            function renderLists() {
                const words = shuffle(
                    roundPairs.map(pair => ({
                        id: pair.slotId,
                        text: currentLang === "tr" ? pair.word_tr : pair.word_en,
                    }))
                );
                const meanings = shuffle(
                    roundPairs.map(pair => ({
                        id: pair.slotId,
                        text: currentLang === "tr" ? pair.word_en : pair.word_tr,
                    }))
                );
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
                    const matched = roundPairs.find(pair => pair.slotId === id);
                    if (matched) {
                        explainText.textContent = currentLang === "tr" ? matched.explain_tr : matched.explain_en;
                    }
                    setStatus("Dogru eslesme!", true);
                } else {
                    streak = 0;
                    setStatus("Yanlis eslesme.", false);
                    clearWrong([selectedWord, selectedMeaning]);
                }
                selectedWord = null;
                selectedMeaning = null;
                updateCounters();
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
                    const wordBtn = wordList.querySelector(`[data-id="${pair.slotId}"]`);
                    return wordBtn && !wordBtn.classList.contains("matched");
                });
                if (remaining.length === 0) return;
                const hintPair = remaining[Math.floor(Math.random() * remaining.length)];
                markMatched(hintPair.slotId);
                correct += 1;
                streak += 1;
                explainText.textContent = currentLang === "tr" ? hintPair.explain_tr : hintPair.explain_en;
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

            categorySelect.addEventListener("change", () => {
                currentCategory = categorySelect.value;
                resetGame();
            });

            newRoundBtn.addEventListener("click", buildRound);
            resetBtn.addEventListener("click", resetGame);
            revealBtn.addEventListener("click", revealHint);

            updateLabels();
            resetGame();
        });
    </script>
</x-game::layouts.master>
