<x-game::layouts.master>
    <style>
        :root {
            --bg-1: #f5efe6;
            --bg-2: #e8f4f1;
            --bg-3: #f0e1cf;
            --ink: #16161b;
            --muted: #5f616b;
            --card: #ffffff;
            --accent: #f97316;
            --accent-2: #0f766e;
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
            background: radial-gradient(circle at top, #fff7ed, transparent 55%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2) 45%, var(--bg-3));
        }

        .memory-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: clamp(16px, 3vw, 40px);
        }

        .memory-shell {
            width: min(1100px, 100%);
            display: grid;
            gap: 20px;
            animation: rise-in 0.8s ease both;
        }

        .memory-header {
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
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--accent-2);
            font-weight: 700;
        }

        .memory-title {
            margin: 6px 0 4px;
            font-size: clamp(26px, 4vw, 40px);
        }

        .memory-subtitle {
            margin: 0;
            color: var(--muted);
            max-width: 520px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .stat {
            background: #f6f2ea;
            padding: 10px 12px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 14px;
        }

        .memory-body {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 20px;
        }

        .board-card,
        .memory-side {
            background: var(--card);
            border-radius: 24px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .board-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .board-top h2 {
            margin: 0;
            font-size: 22px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
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

        .btn:active {
            transform: translateY(1px);
        }

        .count-control {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f6f2ea;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .count-control input {
            width: 64px;
            border: 1px solid #e2dfd6;
            border-radius: 999px;
            padding: 6px 10px;
            font-family: "Space Grotesk", system-ui, sans-serif;
            font-size: 13px;
        }

        .memory-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(86px, 1fr));
            margin-top: 16px;
        }

        .memory-card {
            border: 0;
            padding: 0;
            background: transparent;
            perspective: 900px;
            cursor: pointer;
        }

        .memory-card:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 4px;
            border-radius: 16px;
        }

        .card-inner {
            position: relative;
            width: 100%;
            display: block;
            aspect-ratio: 1 / 1.1;
            transform-style: preserve-3d;
            transition: transform 0.45s ease;
        }

        .memory-card.flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            inset: 0;
            border-radius: 16px;
            display: grid;
            place-items: center;
            backface-visibility: hidden;
            font-size: clamp(20px, 3vw, 28px);
            font-weight: 700;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.05);
        }

        .card-back {
            background: linear-gradient(135deg, #0f172a, #1f2937);
            color: #ffffff;
            letter-spacing: 0.2em;
        }

        .card-front {
            background: #ffffff;
            color: var(--ink);
            transform: rotateY(180deg);
            padding: 10px;
            text-align: center;
            font-size: clamp(12px, 2vw, 18px);
            line-height: 1.2;
        }

        .memory-card.matched .card-front {
            background: #e8f6f0;
            color: var(--accent-2);
        }

        .memory-card.matched {
            cursor: default;
        }

        .status {
            margin-top: 16px;
            font-weight: 600;
            color: var(--accent-2);
        }

        .memory-side h3 {
            margin: 0 0 12px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .rules {
            display: grid;
            gap: 10px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .rule-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f6f2ea;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-2);
        }

        @keyframes rise-in {
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
            .memory-body {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

    <div class="memory-page">
        <div class="memory-shell">
            <header class="memory-header">
                <div>
                    <div class="eyebrow">Memory Grid</div>
                    <h1 class="memory-title">Eslesme Kartlari</h1>
                    <p class="memory-subtitle">
                        Kartlari ac, esleri bul, puani yuksek tut. Hedef: tum kartlar acik.
                    </p>
                </div>
                <div class="stats">
                    <div class="stat"><span>Skor</span><span id="score">0</span></div>
                    <div class="stat"><span>Hamle</span><span id="moves">0</span></div>
                    <div class="stat"><span>Sure</span><span id="time">0:00</span></div>
                </div>
            </header>

            <section class="memory-body">
                <div class="board-card">
                    <div class="board-top">
                        <h2>Kart Eslestirme</h2>
                        <div class="actions">
                            <label class="count-control">
                                <span>Cift</span>
                                <input type="number" id="pair-count" min="2" max="18" value="8" />
                            </label>
                            <button class="btn secondary" id="apply-size-btn">Uygula</button>
                            <button class="btn" id="new-game-btn">Yeni Oyun</button>
                            <button class="btn secondary" id="reset-btn">Sifirla</button>
                        </div>
                    </div>
                    <div class="memory-grid" id="memory-grid" role="grid" aria-label="Kart tahtasi"></div>
                    <div class="status" id="status">Hazir</div>
                </div>

                <aside class="memory-side">
                    <h3>Kurallar</h3>
                    <div class="rules">
                        <div class="rule-pill">Oyun Mantigi</div>
                        <div>Kartlar satir-sutun seklinde tabloda yer alir.</div>
                        <div>Her karttan 2 adet bulunur (es cift).</div>
                        <div>Oyuncu iki kart secer, kartlar acilir.</div>
                        <div>Eslesme varsa acik kalir ve +10 puan.</div>
                        <div>Farkliysa 1 saniye sonra kapanir ve -2 puan.</div>
                        <div>Tum kartlar acilinca oyun biter.</div>
                        <div class="rule-pill">Bonus</div>
                        <div>Sure bazli bonus eklenebilir.</div>
                    </div>
                </aside>
            </section>
        </div>
    </div>

    <script>
        window.memoryPairs = @json($pairs ?? []);
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const gridEl = document.getElementById("memory-grid");
            const scoreEl = document.getElementById("score");
            const movesEl = document.getElementById("moves");
            const timeEl = document.getElementById("time");
            const statusEl = document.getElementById("status");
            const newGameBtn = document.getElementById("new-game-btn");
            const resetBtn = document.getElementById("reset-btn");
            const pairCountInput = document.getElementById("pair-count");
            const applySizeBtn = document.getElementById("apply-size-btn");

            const MIN_PAIRS = 2;
            const DEFAULT_PAIRS = 8;
            const FLIP_BACK_DELAY = 0;

            const availablePairs = Array.isArray(window.memoryPairs) ? window.memoryPairs : [];
            const MAX_PAIRS = Math.max(MIN_PAIRS, availablePairs.length);

            let firstCard = null;
            let secondCard = null;
            let lockBoard = false;
            let matchedPairs = 0;
            let currentPairs = DEFAULT_PAIRS;
            let moves = 0;
            let score = 0;
            let seconds = 0;
            let timer = null;
            let gameStarted = false;

            function shuffle(array) {
                const copy = [...array];
                for (let i = copy.length - 1; i > 0; i -= 1) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [copy[i], copy[j]] = [copy[j], copy[i]];
                }
                return copy;
            }

            function formatTime(totalSeconds) {
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                return `${mins}:${secs.toString().padStart(2, "0")}`;
            }

            function updateStats() {
                scoreEl.textContent = score;
                movesEl.textContent = moves;
                timeEl.textContent = formatTime(seconds);
            }

            function ensurePairsAvailable() {
                if (availablePairs.length === 0) {
                    gridEl.innerHTML = "";
                    pairCountInput.disabled = true;
                    applySizeBtn.disabled = true;
                    newGameBtn.disabled = true;
                    resetBtn.disabled = true;
                    setStatus("Veri yok. Lutfen kelime/anlam ekleyin.", false);
                    return false;
                }
                return true;
            }

            function setStatus(message, success = true) {
                statusEl.textContent = message;
                statusEl.style.color = success ? "var(--accent-2)" : "#b91c1c";
            }

            function startTimer() {
                if (timer) return;
                timer = setInterval(() => {
                    seconds += 1;
                    updateStats();
                }, 1000);
            }

            function stopTimer() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            function resetState(resetScore = true) {
                firstCard = null;
                secondCard = null;
                lockBoard = false;
                matchedPairs = 0;
                moves = 0;
                seconds = 0;
                gameStarted = false;
                stopTimer();
                if (resetScore) {
                    score = 0;
                }
                setStatus("Hazir", true);
                updateStats();
            }

            function getPairCount() {
                const requested = Number.parseInt(pairCountInput.value, 10);
                const safeValue = Number.isFinite(requested) ? requested : DEFAULT_PAIRS;
                const clamped = Math.min(MAX_PAIRS, Math.max(MIN_PAIRS, safeValue));
                pairCountInput.value = clamped;
                return clamped;
            }

            function buildBoard() {
                if (!ensurePairsAvailable()) return;
                const pairCount = getPairCount();
                currentPairs = pairCount;
                const picks = shuffle(availablePairs).slice(0, pairCount);
                const deck = shuffle([
                    ...picks.map(pair => ({
                        pairId: pair.id,
                        label: pair.word,
                        type: "word",
                    })),
                    ...picks.map(pair => ({
                        pairId: pair.id,
                        label: pair.meaning,
                        type: "meaning",
                    })),
                ]);
                gridEl.innerHTML = "";
                deck.forEach((card, index) => {
                    const button = document.createElement("button");
                    button.type = "button";
                    button.className = "memory-card";
                    button.dataset.value = card.pairId;
                    button.dataset.index = `${card.pairId}-${card.type}-${index}`;
                    button.innerHTML = `
                        <span class="card-inner">
                            <span class="card-face card-back">?</span>
                            <span class="card-face card-front">${card.label}</span>
                        </span>
                    `;
                    button.addEventListener("click", () => handleCardClick(button));
                    gridEl.appendChild(button);
                });
            }

            function handleCardClick(card) {
                if (lockBoard || card.classList.contains("matched") || card === firstCard) {
                    return;
                }

                if (!gameStarted) {
                    gameStarted = true;
                    startTimer();
                }

                card.classList.add("flipped");

                if (!firstCard) {
                    firstCard = card;
                    return;
                }

                secondCard = card;
                moves += 1;
                checkMatch();
            }

            function checkMatch() {
                if (!firstCard || !secondCard) return;
                const isMatch = firstCard.dataset.value === secondCard.dataset.value;
                if (isMatch) {
                    firstCard.classList.add("matched");
                    secondCard.classList.add("matched");
                    firstCard.disabled = true;
                    secondCard.disabled = true;
                    matchedPairs += 1;
                    score += 10;
                    setStatus("Dogru eslesme! +10", true);
                    resetSelection();
                    if (matchedPairs === currentPairs) {
                        stopTimer();
                        setStatus("Tebrikler! Tum kartlar acildi.", true);
                    }
                } else {
                    score -= 2;
                    setStatus("Yanlis eslesme. -2", false);
                    lockBoard = true;
                    const flipBack = () => {
                        if (!firstCard || !secondCard) return;
                        firstCard.classList.remove("flipped");
                        secondCard.classList.remove("flipped");
                        resetSelection();
                    };
                    const prefersNoHover =
                        window.matchMedia && window.matchMedia("(hover: none)").matches;
                    if (prefersNoHover) {
                        setTimeout(flipBack, FLIP_BACK_DELAY);
                    } else {
                        const handleLeave = () => {
                            secondCard.removeEventListener("mouseleave", handleLeave);
                            flipBack();
                        };
                        secondCard.addEventListener("mouseleave", handleLeave);
                    }
                }
                updateStats();
            }

            function resetSelection() {
                firstCard = null;
                secondCard = null;
                lockBoard = false;
            }

            function startNewGame() {
                resetState(true);
                buildBoard();
            }

            function resetBoard() {
                resetState(false);
                buildBoard();
            }

            newGameBtn.addEventListener("click", startNewGame);
            resetBtn.addEventListener("click", resetBoard);
            applySizeBtn.addEventListener("click", startNewGame);

            pairCountInput.max = MAX_PAIRS;
            if (availablePairs.length > 0) {
                pairCountInput.value = Math.min(DEFAULT_PAIRS, MAX_PAIRS);
            }

            startNewGame();
        });
    </script>
</x-game::layouts.master>
