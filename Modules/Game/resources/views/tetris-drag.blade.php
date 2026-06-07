<x-game::layouts.master>
    <style>
        :root {
            --drag-bg: #07111f;
            --drag-panel: rgba(15, 23, 42, 0.82);
            --drag-panel-strong: rgba(2, 6, 23, 0.92);
            --drag-line: rgba(148, 163, 184, 0.18);
            --drag-text: #f8fafc;
            --drag-soft: #b6c7db;
            --drag-accent: #38bdf8;
            --drag-good: #22c55e;
            --cell: 42px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100dvh;
            margin: 0;
            overflow: hidden;
            touch-action: none;
            overscroll-behavior: none;
        }

        body {
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: var(--drag-text);
            background:
                radial-gradient(circle at 12% 8%, rgba(56, 189, 248, 0.28), transparent 30%),
                radial-gradient(circle at 88% 12%, rgba(34, 197, 94, 0.18), transparent 28%),
                linear-gradient(145deg, #07111f, #0f172a 48%, #111827);
        }

        .drag-app {
            width: 100%;
            height: 100dvh;
            padding: 12px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            gap: 10px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-width: 0;
        }

        .brand {
            min-width: 0;
        }

        .brand h1 {
            margin: 0;
            font-size: clamp(18px, 2.8vw, 30px);
            line-height: 1.05;
            letter-spacing: 0;
        }

        .brand p {
            margin: 4px 0 0;
            color: var(--drag-soft);
            font-size: 13px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pill,
        .score-box {
            border: 1px solid var(--drag-line);
            background: rgba(15, 23, 42, 0.68);
            border-radius: 8px;
            padding: 8px 10px;
        }

        .score-box {
            min-width: 92px;
            text-align: right;
        }

        .score-box span {
            display: block;
            color: var(--drag-soft);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .score-box strong {
            display: block;
            font-family: "Space Mono", ui-monospace, monospace;
            font-size: 20px;
            line-height: 1.1;
        }

        .btn {
            min-height: 40px;
            border: 0;
            border-radius: 8px;
            padding: 0 14px;
            background: var(--drag-accent);
            color: #07111f;
            font-weight: 900;
            cursor: pointer;
        }

        .btn.secondary {
            border: 1px solid var(--drag-line);
            background: rgba(226, 232, 240, 0.12);
            color: var(--drag-text);
        }

        .game-shell {
            min-height: 0;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(230px, 300px);
            gap: 12px;
        }

        .board-panel,
        .side-panel {
            min-height: 0;
            border: 1px solid var(--drag-line);
            border-radius: 8px;
            background: var(--drag-panel);
            box-shadow: 0 24px 52px rgba(2, 6, 23, 0.32);
        }

        .board-panel {
            display: grid;
            place-items: center;
            padding: 12px;
            overflow: hidden;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(10, var(--cell));
            grid-template-rows: repeat(10, var(--cell));
            gap: 4px;
            padding: 8px;
            border-radius: 8px;
            background: var(--drag-panel-strong);
            border: 1px solid rgba(148, 163, 184, 0.2);
            touch-action: none;
        }

        .cell {
            width: var(--cell);
            height: var(--cell);
            border-radius: 6px;
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }

        .cell.filled {
            background: var(--color);
            border-color: rgba(255, 255, 255, 0.22);
            box-shadow: inset 4px 4px 0 rgba(255, 255, 255, 0.18), inset -4px -4px 0 rgba(2, 6, 23, 0.18);
        }

        .cell.preview {
            background: color-mix(in srgb, var(--color) 72%, white);
        }

        .cell.invalid {
            background: rgba(244, 63, 94, 0.52);
        }

        .side-panel {
            padding: 12px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 12px;
        }

        .panel-title {
            margin: 0;
            color: var(--drag-soft);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pieces {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .piece-slot {
            min-height: 112px;
            display: grid;
            place-items: center;
            border: 1px solid var(--drag-line);
            border-radius: 8px;
            background: rgba(2, 6, 23, 0.32);
        }

        .piece {
            display: grid;
            gap: 4px;
            padding: 6px;
            touch-action: none;
            cursor: grab;
            user-select: none;
            transform-origin: center;
        }

        .piece.is-used {
            opacity: 0.16;
            pointer-events: none;
        }

        .piece.is-dragging {
            position: fixed;
            z-index: 40;
            cursor: grabbing;
            pointer-events: none;
            filter: drop-shadow(0 20px 26px rgba(2, 6, 23, 0.42));
        }

        .mini-cell {
            width: 22px;
            height: 22px;
            border-radius: 5px;
        }

        .mini-cell.on {
            background: var(--color);
            box-shadow: inset 3px 3px 0 rgba(255, 255, 255, 0.18), inset -3px -3px 0 rgba(2, 6, 23, 0.2);
        }

        .mini-cell.off {
            opacity: 0;
        }

        .status {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid rgba(34, 197, 94, 0.22);
            background: rgba(34, 197, 94, 0.1);
            color: #dcfce7;
            text-align: center;
            font-weight: 800;
            font-size: 13px;
        }

        .toast {
            position: fixed;
            left: 50%;
            bottom: 18px;
            z-index: 60;
            transform: translateX(-50%);
            padding: 10px 14px;
            border-radius: 8px;
            background: rgba(2, 6, 23, 0.92);
            border: 1px solid var(--drag-line);
            color: var(--drag-text);
            font-size: 13px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translate(-50%, -8px);
        }

        @media (max-width: 820px) {
            .drag-app {
                padding: 8px;
                gap: 8px;
            }

            .brand p,
            .pill {
                display: none;
            }

            .top-actions {
                gap: 6px;
            }

            .score-box {
                min-width: 76px;
                padding: 6px 8px;
            }

            .score-box strong {
                font-size: 18px;
            }

            .btn {
                min-height: 36px;
                padding: 0 10px;
                font-size: 12px;
            }

            .game-shell {
                grid-template-columns: 1fr;
                grid-template-rows: minmax(0, 1fr) auto;
                gap: 8px;
            }

            .board-panel {
                padding: 8px;
            }

            .side-panel {
                padding: 8px;
                grid-template-rows: auto auto;
                gap: 8px;
            }

            .side-panel .panel-title,
            .side-panel .status {
                display: none;
            }

            .pieces {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }

            .piece-slot {
                min-height: 82px;
            }

            .mini-cell {
                width: 17px;
                height: 17px;
                border-radius: 4px;
            }
        }

        @media (max-width: 420px) {
            .brand h1 {
                font-size: 16px;
            }

            .score-box span {
                font-size: 9px;
            }

            .score-box strong {
                font-size: 16px;
            }

            .mini-cell {
                width: 15px;
                height: 15px;
            }
        }
    </style>

    <main class="drag-app">
        <header class="topbar">
            <div class="brand">
                <h1>Surukle Tetris</h1>
                <p>Parcayi tut, tahtaya birak. Dolu satir ve sutunlar temizlenir.</p>
            </div>
            <div class="top-actions">
                <div class="pill">Mobil uyumlu</div>
                <div class="score-box">
                    <span>Skor</span>
                    <strong id="score">0</strong>
                </div>
                <button id="new-game" class="btn" type="button">Yeni</button>
            </div>
        </header>

        <section class="game-shell" aria-label="Surukle birak Tetris">
            <div class="board-panel">
                <div id="board" class="board" aria-label="Tetris tahtasi"></div>
            </div>

            <aside class="side-panel">
                <h2 class="panel-title">Parcalar</h2>
                <div id="pieces" class="pieces" aria-label="Suruklenebilir parcalar"></div>
                <div id="status" class="status">Parcalari tahtaya surukle.</div>
            </aside>
        </section>
    </main>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const boardEl = document.getElementById("board");
            const piecesEl = document.getElementById("pieces");
            const scoreEl = document.getElementById("score");
            const statusEl = document.getElementById("status");
            const newGameBtn = document.getElementById("new-game");
            const toastEl = document.getElementById("toast");

            const SIZE = 10;
            const COLORS = ["#38bdf8", "#22c55e", "#f97316", "#e879f9", "#f43f5e", "#facc15", "#818cf8"];
            const SHAPES = [
                [[1]],
                [[1, 1]],
                [[1], [1]],
                [[1, 1, 1]],
                [[1], [1], [1]],
                [[1, 1], [1, 1]],
                [[1, 0], [1, 0], [1, 1]],
                [[0, 1], [0, 1], [1, 1]],
                [[1, 1, 1], [1, 0, 0]],
                [[1, 1, 1], [0, 0, 1]],
                [[1, 1, 0], [0, 1, 1]],
                [[0, 1, 1], [1, 1, 0]],
                [[0, 1, 0], [1, 1, 1]],
                [[1, 1, 1, 1]],
                [[1], [1], [1], [1]],
            ];

            let board = [];
            let cells = [];
            let pieces = [];
            let score = 0;
            let dragging = null;
            let toastTimer = null;

            function cloneShape(shape) {
                return shape.map(row => row.slice());
            }

            function randomItem(list) {
                return list[Math.floor(Math.random() * list.length)];
            }

            function createBoard() {
                board = Array.from({ length: SIZE }, () => Array(SIZE).fill(null));
                cells = [];
                boardEl.innerHTML = "";
                for (let row = 0; row < SIZE; row += 1) {
                    cells[row] = [];
                    for (let col = 0; col < SIZE; col += 1) {
                        const cell = document.createElement("div");
                        cell.className = "cell";
                        cell.dataset.row = row;
                        cell.dataset.col = col;
                        boardEl.appendChild(cell);
                        cells[row][col] = cell;
                    }
                }
            }

            function resizeBoard() {
                const appHeight = window.visualViewport?.height || window.innerHeight;
                const boardPanel = boardEl.parentElement.getBoundingClientRect();
                const widthLimit = boardPanel.width - 28;
                const heightLimit = boardPanel.height - 28;
                const mobileReserve = window.innerWidth <= 820 ? 0 : 12;
                const cell = Math.floor((Math.min(widthLimit, heightLimit - mobileReserve) - 8 - 9 * 4) / SIZE);
                document.documentElement.style.setProperty("--cell", `${Math.max(22, Math.min(48, cell))}px`);
            }

            function renderBoard() {
                for (let row = 0; row < SIZE; row += 1) {
                    for (let col = 0; col < SIZE; col += 1) {
                        const value = board[row][col];
                        const cell = cells[row][col];
                        cell.className = "cell";
                        cell.style.removeProperty("--color");
                        if (value) {
                            cell.classList.add("filled");
                            cell.style.setProperty("--color", value);
                        }
                    }
                }
            }

            function pieceCells(shape) {
                const result = [];
                shape.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value) result.push({ x, y });
                    });
                });
                return result;
            }

            function canPlace(piece, row, col) {
                return pieceCells(piece.shape).every(cell => {
                    const nextRow = row + cell.y;
                    const nextCol = col + cell.x;
                    return nextRow >= 0 && nextRow < SIZE && nextCol >= 0 && nextCol < SIZE && !board[nextRow][nextCol];
                });
            }

            function placePiece(piece, row, col) {
                pieceCells(piece.shape).forEach(cell => {
                    board[row + cell.y][col + cell.x] = piece.color;
                });
                score += pieceCells(piece.shape).length * 8;
                clearLines();
                piece.used = true;
                if (pieces.every(item => item.used)) {
                    createPieces();
                } else {
                    renderPieces();
                }
                renderBoard();
                updateScore();
                if (!hasAnyMove()) {
                    showToast("Hamle kalmadi. Yeni oyun basladi.");
                    resetGame();
                }
            }

            function clearLines() {
                const rows = [];
                const cols = [];

                for (let row = 0; row < SIZE; row += 1) {
                    if (board[row].every(Boolean)) rows.push(row);
                }

                for (let col = 0; col < SIZE; col += 1) {
                    if (board.every(row => row[col])) cols.push(col);
                }

                rows.forEach(row => {
                    for (let col = 0; col < SIZE; col += 1) board[row][col] = null;
                });

                cols.forEach(col => {
                    for (let row = 0; row < SIZE; row += 1) board[row][col] = null;
                });

                const cleared = rows.length + cols.length;
                if (cleared > 0) {
                    score += cleared * cleared * 120;
                    showToast(`${cleared} cizgi temizlendi`);
                }
            }

            function createPieces() {
                pieces = Array.from({ length: 3 }, (_, index) => ({
                    id: `${Date.now()}-${index}`,
                    shape: cloneShape(randomItem(SHAPES)),
                    color: COLORS[Math.floor(Math.random() * COLORS.length)],
                    used: false,
                }));
                renderPieces();
            }

            function renderPieces() {
                piecesEl.innerHTML = "";
                pieces.forEach(piece => {
                    const slot = document.createElement("div");
                    slot.className = "piece-slot";

                    const pieceEl = document.createElement("div");
                    pieceEl.className = `piece${piece.used ? " is-used" : ""}`;
                    pieceEl.dataset.id = piece.id;
                    pieceEl.style.gridTemplateColumns = `repeat(${piece.shape[0].length}, 1fr)`;
                    pieceEl.style.setProperty("--color", piece.color);

                    piece.shape.forEach(row => {
                        row.forEach(value => {
                            const mini = document.createElement("div");
                            mini.className = `mini-cell ${value ? "on" : "off"}`;
                            pieceEl.appendChild(mini);
                        });
                    });

                    pieceEl.addEventListener("pointerdown", event => startDrag(event, piece, pieceEl));
                    slot.appendChild(pieceEl);
                    piecesEl.appendChild(slot);
                });
            }

            function getCellFromPoint(x, y) {
                const element = document.elementFromPoint(x, y);
                return element?.classList.contains("cell") ? element : null;
            }

            function clearPreview() {
                cells.flat().forEach(cell => {
                    cell.classList.remove("preview", "invalid");
                    if (!cell.classList.contains("filled")) cell.style.removeProperty("--color");
                });
            }

            function preview(piece, row, col) {
                clearPreview();
                const valid = canPlace(piece, row, col);
                pieceCells(piece.shape).forEach(shapeCell => {
                    const nextRow = row + shapeCell.y;
                    const nextCol = col + shapeCell.x;
                    if (nextRow < 0 || nextRow >= SIZE || nextCol < 0 || nextCol >= SIZE) return;
                    const cell = cells[nextRow][nextCol];
                    cell.classList.add(valid ? "preview" : "invalid");
                    cell.style.setProperty("--color", piece.color);
                });
                return valid;
            }

            function startDrag(event, piece, sourceEl) {
                if (piece.used) return;
                event.preventDefault();

                const ghost = sourceEl.cloneNode(true);
                const rect = sourceEl.getBoundingClientRect();
                ghost.classList.add("is-dragging");
                ghost.style.width = `${rect.width}px`;
                ghost.style.height = `${rect.height}px`;
                document.body.appendChild(ghost);

                dragging = {
                    piece,
                    ghost,
                    offsetX: rect.width / 2,
                    offsetY: rect.height / 2,
                    row: null,
                    col: null,
                    valid: false,
                };

                moveDrag(event);
                window.addEventListener("pointermove", moveDrag, { passive: false });
                window.addEventListener("pointerup", endDrag, { once: true });
                window.addEventListener("pointercancel", endDrag, { once: true });
            }

            function moveDrag(event) {
                if (!dragging) return;
                event.preventDefault();
                dragging.ghost.style.left = `${event.clientX - dragging.offsetX}px`;
                dragging.ghost.style.top = `${event.clientY - dragging.offsetY}px`;

                const target = getCellFromPoint(event.clientX, event.clientY);
                if (!target) {
                    clearPreview();
                    dragging.valid = false;
                    return;
                }

                dragging.row = Number(target.dataset.row);
                dragging.col = Number(target.dataset.col);
                dragging.valid = preview(dragging.piece, dragging.row, dragging.col);
            }

            function endDrag() {
                if (!dragging) return;
                window.removeEventListener("pointermove", moveDrag);
                clearPreview();

                const { piece, ghost, row, col, valid } = dragging;
                ghost.remove();
                dragging = null;

                if (valid) {
                    placePiece(piece, row, col);
                    statusEl.textContent = "Guzel hamle. Devam et.";
                } else {
                    showToast("Bu parca buraya sigmiyor");
                    statusEl.textContent = "Parcayi bos ve uygun alana birak.";
                }
            }

            function hasMove(piece) {
                for (let row = 0; row < SIZE; row += 1) {
                    for (let col = 0; col < SIZE; col += 1) {
                        if (canPlace(piece, row, col)) return true;
                    }
                }
                return false;
            }

            function hasAnyMove() {
                return pieces.some(piece => !piece.used && hasMove(piece));
            }

            function updateScore() {
                scoreEl.textContent = score;
            }

            function showToast(text) {
                toastEl.textContent = text;
                toastEl.classList.add("show");
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toastEl.classList.remove("show"), 1400);
            }

            function resetGame() {
                createBoard();
                resizeBoard();
                score = 0;
                updateScore();
                createPieces();
                renderBoard();
                statusEl.textContent = "Parcalari tahtaya surukle.";
            }

            newGameBtn.addEventListener("click", resetGame);
            window.addEventListener("resize", () => {
                resizeBoard();
                renderBoard();
            });

            resetGame();
        });
    </script>
</x-game::layouts.master>
