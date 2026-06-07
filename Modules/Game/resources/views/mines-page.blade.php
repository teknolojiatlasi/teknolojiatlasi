<x-game::layouts.master>
    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #111827;
            --bg-3: #172554;
            --ink: #ecfeff;
            --muted: #bfd7ff;
            --card: rgba(15, 23, 42, 0.86);
            --accent: #ff8a00;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100dvh;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), transparent 28%),
                radial-gradient(circle at top right, rgba(244, 63, 94, 0.22), transparent 26%),
                linear-gradient(145deg, var(--bg-1), var(--bg-2) 42%, var(--bg-3));
        }

        .mines-app {
            min-height: 100dvh;
            padding: clamp(14px, 3vw, 28px);
            display: grid;
            place-items: center;
        }

        .frame {
            width: min(960px, 100%);
            display: grid;
            gap: 20px;
        }

        .hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
            max-width: 560px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .mines-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 260px;
            gap: 24px;
            align-items: start;
        }

        .mines-card,
        .panel {
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.18);
            padding: 18px;
        }

        .mines-board {
            display: grid;
            gap: 6px;
            justify-content: center;
            --cell: 32px;
        }

        .mines-cell {
            width: var(--cell);
            height: var(--cell);
            border-radius: 8px;
            border: 0;
            background: #f3f0ea;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .mines-cell.revealed {
            background: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
            cursor: default;
        }

        .mines-cell.flagged {
            background: #ffe6d1;
        }

        .mines-cell.mine {
            background: #ef476f;
            color: #ffffff;
        }

        .mines-controls {
            display: grid;
            gap: 12px;
        }

        .panel h3 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .mines-select,
        .btn {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-family: "Space Grotesk", system-ui, sans-serif;
        }

        .btn {
            border: 0;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .btn.secondary {
            background: #ececec;
            color: #0f172a;
        }

        .mines-stats {
            display: grid;
            gap: 8px;
        }

        .mines-row {
            display: flex;
            justify-content: space-between;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        .tips {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            .mines-layout {
                grid-template-columns: 1fr;
            }

            .mines-app {
                align-items: start;
            }
        }

        @media (max-width: 520px) {
            .mines-board {
                gap: 4px;
            }
        }
    </style>

    <div class="mines-app">
        <div class="frame">
            <header class="hero">
                <div>
                    <h1>Mayin Tarlasi</h1>
                    <p>Mayinlari isaretle, guvenli kareleri ac ve tahtayi temizle.</p>
                </div>
                <div class="hero-chip">Sag tik: bayrak</div>
            </header>

            @include('game::mines')
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const minesBoard = document.getElementById("mines-board");
            const minesLevel = document.getElementById("mines-level");
            const minesLeft = document.getElementById("mines-left");
            const minesTime = document.getElementById("mines-time");
            const minesStatus = document.getElementById("mines-status");
            const minesReset = document.getElementById("mines-reset");
            const minesFlagToggle = document.getElementById("mines-flag-toggle");

            const minePresets = {
                easy: { rows: 9, cols: 9, mines: 10 },
                medium: { rows: 12, cols: 12, mines: 20 },
                hard: { rows: 16, cols: 16, mines: 40 },
            };
            const mineNumberColors = [
                "#0f172a", "#2563eb", "#16a34a", "#f97316", "#ef4444",
                "#7c3aed", "#0f172a", "#db2777", "#111827",
            ];

            let mineConfig = minePresets.easy;
            let mineGrid = [];
            let mineEls = [];
            let mineRevealed = 0;
            let mineFlags = 0;
            let mineStarted = false;
            let mineTimer = null;
            let mineSeconds = 0;
            let mineGameOver = false;
            let mineFlagMode = false;

            function resizeMinesBoard() {
                const gap = parseFloat(window.getComputedStyle(minesBoard).gap) || 6;
                const maxWidth = minesBoard.parentElement.clientWidth;
                let cell = Math.floor((maxWidth - gap * (mineConfig.cols - 1)) / mineConfig.cols);
                cell = Math.max(window.innerWidth <= 640 ? 14 : 18, Math.min(36, cell));
                minesBoard.style.setProperty("--cell", `${cell}px`);
                minesBoard.style.gridTemplateColumns = `repeat(${mineConfig.cols}, var(--cell))`;
            }

            function updateMineStats() {
                minesLeft.textContent = mineConfig.mines - mineFlags;
                minesTime.textContent = mineSeconds;
            }

            function stopMineTimer() {
                if (mineTimer) {
                    clearInterval(mineTimer);
                    mineTimer = null;
                }
            }

            function startMineTimer() {
                if (mineStarted) return;
                mineStarted = true;
                mineSeconds = 0;
                updateMineStats();
                mineTimer = setInterval(() => {
                    mineSeconds += 1;
                    updateMineStats();
                }, 1000);
            }

            function setMineStatus(text) {
                minesStatus.textContent = text;
            }

            function placeMines() {
                let placed = 0;
                while (placed < mineConfig.mines) {
                    const r = Math.floor(Math.random() * mineConfig.rows);
                    const c = Math.floor(Math.random() * mineConfig.cols);
                    if (!mineGrid[r][c].mine) {
                        mineGrid[r][c].mine = true;
                        placed += 1;
                    }
                }
            }

            function countAdjacent(row, col) {
                let count = 0;
                for (let dr = -1; dr <= 1; dr += 1) {
                    for (let dc = -1; dc <= 1; dc += 1) {
                        if (dr === 0 && dc === 0) continue;
                        const nr = row + dr;
                        const nc = col + dc;
                        if (nr < 0 || nr >= mineConfig.rows || nc < 0 || nc >= mineConfig.cols) continue;
                        if (mineGrid[nr][nc].mine) count += 1;
                    }
                }
                return count;
            }

            function buildMineGrid() {
                mineGrid = Array.from({ length: mineConfig.rows }, () =>
                    Array.from({ length: mineConfig.cols }, () => ({
                        mine: false,
                        revealed: false,
                        flagged: false,
                        adjacent: 0,
                    }))
                );
                placeMines();
                for (let r = 0; r < mineConfig.rows; r += 1) {
                    for (let c = 0; c < mineConfig.cols; c += 1) {
                        mineGrid[r][c].adjacent = countAdjacent(r, c);
                    }
                }
            }

            function updateMineCell(row, col) {
                const cell = mineGrid[row][col];
                const el = mineEls[row][col];
                el.classList.toggle("revealed", cell.revealed);
                el.classList.toggle("flagged", cell.flagged);
                el.classList.toggle("mine", cell.revealed && cell.mine);
                if (cell.revealed && cell.mine) {
                    el.textContent = "X";
                    el.style.color = "#ffffff";
                    return;
                }
                if (cell.flagged) {
                    el.textContent = "F";
                    el.style.color = "#111827";
                    return;
                }
                if (!cell.revealed) {
                    el.textContent = "";
                    return;
                }
                el.textContent = cell.adjacent > 0 ? cell.adjacent : "";
                el.style.color = mineNumberColors[cell.adjacent] || "#111827";
            }

            function revealAllMines() {
                for (let r = 0; r < mineConfig.rows; r += 1) {
                    for (let c = 0; c < mineConfig.cols; c += 1) {
                        if (mineGrid[r][c].mine) {
                            mineGrid[r][c].revealed = true;
                            updateMineCell(r, c);
                        }
                    }
                }
            }

            function toggleFlag(row, col) {
                const cell = mineGrid[row][col];
                if (cell.revealed) return;
                cell.flagged = !cell.flagged;
                mineFlags += cell.flagged ? 1 : -1;
                updateMineStats();
                updateMineCell(row, col);
            }

            function floodReveal(startRow, startCol) {
                const stack = [[startRow, startCol]];
                while (stack.length > 0) {
                    const [row, col] = stack.pop();
                    const cell = mineGrid[row][col];
                    if (cell.revealed || cell.flagged) continue;
                    cell.revealed = true;
                    mineRevealed += 1;
                    updateMineCell(row, col);
                    if (cell.adjacent !== 0) continue;
                    for (let dr = -1; dr <= 1; dr += 1) {
                        for (let dc = -1; dc <= 1; dc += 1) {
                            if (dr === 0 && dc === 0) continue;
                            const nr = row + dr;
                            const nc = col + dc;
                            if (nr < 0 || nr >= mineConfig.rows || nc < 0 || nc >= mineConfig.cols) continue;
                            const neighbor = mineGrid[nr][nc];
                            if (!neighbor.revealed && !neighbor.flagged && !neighbor.mine) {
                                stack.push([nr, nc]);
                            }
                        }
                    }
                }
            }

            function checkMineWin() {
                const target = mineConfig.rows * mineConfig.cols - mineConfig.mines;
                if (mineRevealed >= target) {
                    mineGameOver = true;
                    stopMineTimer();
                    setMineStatus("Kazandin");
                }
            }

            function revealMineCell(row, col) {
                if (mineGameOver) return;
                const cell = mineGrid[row][col];
                if (cell.revealed || cell.flagged) return;
                startMineTimer();
                if (cell.mine) {
                    cell.revealed = true;
                    updateMineCell(row, col);
                    revealAllMines();
                    mineGameOver = true;
                    stopMineTimer();
                    setMineStatus("Kaybettin");
                    return;
                }
                floodReveal(row, col);
                checkMineWin();
            }

            function renderMineBoard() {
                mineEls = Array.from({ length: mineConfig.rows }, () => Array(mineConfig.cols).fill(null));
                minesBoard.innerHTML = "";
                resizeMinesBoard();
                for (let r = 0; r < mineConfig.rows; r += 1) {
                    for (let c = 0; c < mineConfig.cols; c += 1) {
                        const btn = document.createElement("button");
                        btn.type = "button";
                        btn.className = "mines-cell";
                        btn.addEventListener("click", () => {
                            if (mineFlagMode) {
                                toggleFlag(r, c);
                            } else {
                                revealMineCell(r, c);
                            }
                        });
                        btn.addEventListener("contextmenu", event => {
                            event.preventDefault();
                            toggleFlag(r, c);
                        });
                        minesBoard.appendChild(btn);
                        mineEls[r][c] = btn;
                    }
                }
            }

            function resetMineGame() {
                mineConfig = minePresets[minesLevel.value] || minePresets.easy;
                mineRevealed = 0;
                mineFlags = 0;
                mineStarted = false;
                mineGameOver = false;
                mineFlagMode = false;
                minesFlagToggle.textContent = "Bayrak Modu: Kapali";
                setMineStatus("Hazir");
                stopMineTimer();
                buildMineGrid();
                renderMineBoard();
                updateMineStats();
            }

            minesReset.addEventListener("click", resetMineGame);
            minesLevel.addEventListener("change", resetMineGame);
            minesFlagToggle.addEventListener("click", () => {
                mineFlagMode = !mineFlagMode;
                minesFlagToggle.textContent = mineFlagMode
                    ? "Bayrak Modu: Acik"
                    : "Bayrak Modu: Kapali";
            });
            window.addEventListener("resize", resizeMinesBoard);

            resetMineGame();
        });
    </script>
</x-game::layouts.master>
