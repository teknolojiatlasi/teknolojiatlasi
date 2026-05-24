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
            --accent-2: #22c55e;
            --accent-3: #38bdf8;
            --accent-4: #f43f5e;
            --board: #020617;
            --grid: rgba(255, 255, 255, 0.09);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100dvh;
            overscroll-behavior: none;
            overflow-x: clip;
            overflow-y: hidden;
            max-width: 100%;
        }

        body {
            margin: 0;
            font-family: "Space Grotesk", system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), transparent 28%),
                radial-gradient(circle at top right, rgba(244, 63, 94, 0.22), transparent 26%),
                radial-gradient(circle at bottom left, rgba(34, 197, 94, 0.18), transparent 24%),
                linear-gradient(145deg, var(--bg-1), var(--bg-2) 42%, var(--bg-3));
            min-height: 100dvh;
            overflow-x: clip;
            overflow-y: hidden;
            width: 100%;
            max-width: 100vw;
        }

        body.game-pwa-shell {
            -webkit-tap-highlight-color: transparent;
            position: fixed;
            inset: 0;
        }

        .tetris-app {
            position: relative;
            height: 100dvh;
            min-height: 100dvh;
            padding: clamp(12px, 2vw, 24px);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            width: 100%;
            max-width: 100vw;
            padding-bottom: 24px;
        }

        .tetris-app::before,
        .tetris-app::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(0);
            z-index: 0;
            opacity: 0.5;
        }

        .tetris-app::before {
            background: radial-gradient(circle, rgba(255, 122, 0, 0.25), transparent 70%);
            top: -160px;
            right: -120px;
        }

        .tetris-app::after {
            background: radial-gradient(circle, rgba(43, 124, 255, 0.2), transparent 70%);
            bottom: -180px;
            left: -140px;
        }

        .frame {
            position: relative;
            width: min(1100px, 100%);
            max-width: calc(100vw - clamp(24px, 4vw, 48px));
            max-height: calc(100dvh - clamp(24px, 4vw, 48px));
            z-index: 1;
            display: grid;
            gap: 20px;
            animation: lift 0.8s ease both;
            transform: scale(var(--frame-scale, 1));
            transform-origin: top center;
            overflow: hidden;
        }

        .page-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(17, 24, 39, 0.08);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.12);
        }

        .hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero h1 {
            font-size: clamp(28px, 4vw, 40px);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .hero p {
            margin: 6px 0 0;
            color: var(--muted);
            max-width: 520px;
        }

        .hero-copy {
            max-width: 620px;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            color: var(--ink);
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 14px 30px rgba(20, 20, 30, 0.08);
            backdrop-filter: blur(10px);
        }

        .tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tab-btn {
            border: 0;
            padding: 10px 16px;
            border-radius: 999px;
            background: #ffffff;
            color: var(--muted);
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(20, 20, 30, 0.08);
            transition: transform 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .tab-btn.active {
            background: var(--ink);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(20, 20, 30, 0.16);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
            overflow: hidden;
        }

        .game-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 24px;
            align-items: start;
            min-width: 0;
        }

        .board-card,
        .side-card,
        .panel,
        .controls-card {
            background: var(--card);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(2, 6, 23, 0.34);
            border: 1px solid rgba(148, 163, 184, 0.14);
            backdrop-filter: blur(18px);
        }

        .board-card {
            padding: 20px;
            display: grid;
            gap: 16px;
            justify-items: center;
            animation: lift 0.8s ease both;
            min-width: 0;
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(15, 23, 42, 0.84)),
                radial-gradient(circle at top, rgba(56, 189, 248, 0.18), transparent 40%);
        }

        .board-wrap {
            position: relative;
            width: min(340px, 72vw);
            max-width: 100%;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 50px rgba(11, 18, 32, 0.24);
        }

        .board-wrap::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        #board {
            width: 100%;
            height: auto;
            background: var(--board);
            border-radius: 16px;
            display: block;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.06);
            image-rendering: pixelated;
            touch-action: none;
        }

        .touch-surface {
            display: none;
            position: absolute;
            inset: 0;
            z-index: 2;
            touch-action: none;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0));
        }

        .touch-enabled .touch-surface {
            display: block;
        }

        .gesture-hint {
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .gesture-pill {
            padding: 6px 10px;
            border-radius: 999px;
            background: #f6f4f0;
        }

        .mobile-play-guide {
            display: none;
            width: 100%;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
            text-align: center;
        }

        .mobile-play-guide strong {
            display: block;
            margin-bottom: 4px;
            color: var(--ink);
            font-size: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .hud {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            width: 100%;
        }

        .stat {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 10px 12px;
        }

        .stat span {
            display: block;
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat strong {
            font-size: 20px;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        .status {
            width: 100%;
            text-align: center;
            color: var(--muted);
            font-weight: 700;
        }

        .side-card {
            padding: 18px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            animation: lift 0.9s ease both;
            min-width: 0;
            align-content: start;
        }

        .side-card .controls-card,
        .side-card .tips {
            grid-column: 1 / -1;
        }

        .panel {
            padding: 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            display: grid;
            gap: 10px;
            animation: lift 1s ease both;
        }

        .panel h3 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        #next {
            width: 120px;
            height: 120px;
            background: var(--board);
            border-radius: 12px;
            margin: 0 auto;
            display: block;
            image-rendering: pixelated;
            touch-action: none;
        }

        .scoreboard {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 8px;
            font-size: 14px;
            overflow: hidden;
        }

        .scoreboard li {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            font-family: "Space Mono", ui-monospace, SFMono-Regular, Menlo, monospace;
            min-width: 0;
        }

        .controls-card {
            padding: 16px;
            display: grid;
            gap: 10px;
            align-items: stretch;
            animation: lift 1.1s ease both;
            width: 100%;
            min-width: 0;
        }

        .controls-card .btn {
            width: 100%;
        }

        .btn {
            border: 0;
            background: var(--accent);
            color: #fff;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn.secondary {
            background: #ececec;
            color: var(--ink);
        }

        .btn:active {
            transform: translateY(1px) scale(0.98);
        }

        .tips {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
        }

        .touch-controls {
            display: none;
            gap: 10px;
            justify-content: center;
            position: fixed;
            left: 0;
            right: 0;
            bottom: clamp(8px, 2vh, 18px);
            z-index: 30;
            padding: 0 12px;
            pointer-events: none;
        }

        .touch-controls::before {
            content: "";
            position: absolute;
            inset: -8px 8px -10px;
            border-radius: 26px;
            background: rgba(2, 6, 23, 0.62);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 20px 45px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(18px);
            pointer-events: none;
        }

        .touch-controls .btn {
            position: relative;
            z-index: 1;
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.96), rgba(15, 23, 42, 0.96));
            color: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            min-width: 90px;
            touch-action: none;
            pointer-events: auto;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .touch-controls .btn-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }

        .touch-controls .btn-sub {
            display: block;
            margin-top: 4px;
            font-size: 9px;
            opacity: 0.8;
            line-height: 1;
        }

        .touch-controls .btn[data-action="rotate"] {
            background: linear-gradient(135deg, #ff8a00, #f97316);
        }

        .touch-controls .btn[data-action="left"] {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
        }

        .touch-controls .btn[data-action="right"] {
            background: linear-gradient(135deg, #06b6d4, #2563eb);
        }

        .touch-controls .btn[data-action="down"] {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .touch-controls .btn[data-action="drop"] {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
        }

        .mobile-actions {
            display: none;
            gap: 10px;
            width: 100%;
            justify-content: center;
        }

        .touch-enabled .touch-controls,
        .touch-enabled .mobile-actions {
            display: flex;
        }

        .mines-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 260px;
            gap: 24px;
            align-items: start;
        }

        .mines-card {
            background: var(--card);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(20, 20, 30, 0.08);
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
            cursor: pointer;
            transition: transform 0.1s ease, background 0.2s ease;
        }

        .mines-cell.revealed {
            background: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.06);
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

        .mines-select,
        .mines-toggle {
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid #ddd;
            font-family: "Space Grotesk", system-ui, sans-serif;
            background: #ffffff;
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

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(11, 18, 32, 0.72);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            z-index: 4;
        }

        .overlay.active {
            display: flex;
        }

        .overlay-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            width: min(320px, 90%);
            display: grid;
            gap: 10px;
            text-align: center;
        }

        .overlay-card input {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-family: "Space Grotesk", system-ui, sans-serif;
        }

        @keyframes lift {
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
            .game-grid {
                grid-template-columns: 1fr;
            }

            .side-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .panel:last-child {
                grid-column: 1 / -1;
            }

            .touch-controls {
                bottom: clamp(8px, 2vh, 18px);
            }

            .mines-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-height: 900px) and (min-width: 641px) {
            .page-topbar {
                display: none;
            }

            .frame {
                gap: 14px;
            }

            .board-wrap {
                width: min(300px, 62vw);
            }

            .board-card {
                gap: 12px;
                padding: 16px;
            }

            .side-card,
            .panel,
            .controls-card {
                gap: 12px;
                padding: 14px;
            }

            .scoreboard {
                font-size: 12px;
            }

            #next {
                width: 88px;
                height: 88px;
            }
        }

        @media (hover: none) and (pointer: coarse) {
            .touch-controls {
                bottom: clamp(8px, 2vh, 18px);
            }
        }

        @media (max-width: 640px) {
            html,
            body {
                height: 100dvh;
                min-height: 100dvh;
                overflow: hidden;
            }

            body.game-pwa-shell {
                position: fixed;
                inset: 0;
                width: 100%;
            }

            .tetris-app {
                display: block;
                height: 100dvh;
                min-height: 100dvh;
                padding: 8px 8px calc(108px + env(safe-area-inset-bottom));
                overflow: hidden;
            }

            .tetris-app::before,
            .tetris-app::after {
                display: none;
            }

            .frame {
                width: 100%;
                max-width: calc(100vw - 16px);
                max-height: calc(100dvh - 16px);
                gap: 8px;
                transform: none !important;
                height: 100%;
                grid-template-rows: auto auto minmax(0, 1fr);
                overflow: hidden;
            }

            .page-topbar {
                gap: 8px;
            }

            .eyebrow {
                margin-bottom: 6px;
                padding: 5px 10px;
                font-size: 10px;
            }

            .hero-copy {
                max-width: none;
            }

            .hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .hero h1 {
                font-size: 20px;
            }

            .hero p {
                display: none;
                font-size: 11px;
                max-width: none;
            }

            .hero-actions {
                display: none;
                width: 100%;
                justify-content: flex-start;
                gap: 6px;
            }

            .hero-chip {
                font-size: 10px;
                padding: 7px 10px;
            }

            .tabs {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
                gap: 8px;
            }

            .tab-btn {
                width: 100%;
                min-height: 42px;
                padding: 10px 8px;
                border-radius: 12px;
                font-size: 14px;
            }

            .game-grid,
            .mines-layout {
                gap: 10px;
            }

            .game-grid {
                align-content: start;
            }

            .tab-panel.active {
                height: 100%;
                min-height: 0;
                overflow: hidden;
                padding-bottom: 0;
            }

            .board-card,
            .side-card,
            .mines-card,
            .controls-card {
                border-radius: 14px;
                padding: 9px;
            }

            .board-card {
                gap: 8px;
            }

            .board-wrap {
                width: min(100%, 248px);
            }

            .hud {
                gap: 6px;
            }

            .stat {
                min-width: 0;
                padding: 8px 6px;
                text-align: center;
            }

            .stat span {
                font-size: 10px;
                letter-spacing: 0.04em;
            }

            .stat strong {
                font-size: 16px;
            }

            .mobile-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }

            .mobile-play-guide {
                display: block;
            }

            .mobile-actions,
            .touch-enabled .mobile-actions {
                display: grid;
            }

            .mobile-actions .btn,
            .controls-card .btn,
            .mines-controls .btn,
            .mines-select {
                width: 100%;
                min-height: 40px;
                padding: 9px 8px;
                border-radius: 12px;
                font-size: 13px;
            }

            .controls-card {
                display: none;
            }

            .side-card {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .side-card .panel {
                min-height: 100%;
                padding: 9px;
            }

            .side-card .tips {
                display: none;
            }

            .side-card .controls-card {
                display: none;
            }

            #next {
                width: 78px;
                height: 78px;
            }

            .scoreboard {
                max-height: 74px;
                overflow: hidden;
                font-size: 12px;
            }

            .touch-controls,
            .touch-enabled .touch-controls {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 6px;
                align-items: stretch;
                padding: 0 8px env(safe-area-inset-bottom);
            }

            .touch-controls .btn {
                min-width: 0;
                min-height: 64px;
                padding: 8px 2px;
                border-radius: 16px;
                font-size: 10px;
                font-weight: 800;
                line-height: 1;
                letter-spacing: 0.04em;
            }

            .gesture-hint {
                display: none;
            }

            .gesture-pill {
                padding: 5px 8px;
            }

            .controls-card {
                display: none;
            }

            .pwa-install-prompt {
                display: none !important;
            }

            .mines-board {
                gap: 4px;
            }

            .mines-controls {
                gap: 10px;
            }

            .mines-controls .tips {
                display: none;
            }
        }
    </style>

    <div class="tetris-app">
        <div class="frame">
            <div class="page-topbar">
                <div class="hero-copy">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        Arcade PWA
                    </div>
                    <div class="hero">
                        <div>
                            <h1>Modern Tetris, mobil odakli</h1>
                            <p>Kaydirma derdi olmadan tek elde oynanacak sekilde optimize edildi. Dokun, kaydir, ana ekrana ekle ve uygulama gibi kullan.</p>
                        </div>
                    </div>
                </div>
                <div class="hero-actions">
                    <div class="hero-chip">Swipe: sol, sag, asagi</div>
                    <div class="hero-chip">Tap: dondur</div>
                    <div class="hero-chip">PWA hazir</div>
                </div>
            </div>
            <header class="hero">
                <div class="tabs" role="tablist" aria-label="Game tabs">
                    <button class="tab-btn active" data-tab="tetris-panel" role="tab" aria-selected="true">Tetris</button>
                    <button class="tab-btn" data-tab="mines-panel" role="tab" aria-selected="false">Mayin Tarlasi</button>
                </div>
            </header>

            <section class="tab-panel active" id="tetris-panel">
                <section class="game-grid">
                <div class="board-card">
                    <div class="board-wrap">
                        <canvas id="board" width="300" height="600"></canvas>
                        <div class="touch-surface" id="touch-surface" aria-hidden="true"></div>
                        <div class="overlay" id="game-over" aria-hidden="true">
                            <div class="overlay-card">
                                <h3>Game Over</h3>
                                <div>Skor: <strong id="final-score">0</strong></div>
                                <label>
                                    Isim
                                    <input id="player-name" maxlength="18" placeholder="Player" />
                                </label>
                                <button class="btn" id="save-score">Kaydet</button>
                                <button class="btn secondary" id="play-again">Tekrar Oyna</button>
                            </div>
                        </div>
                    </div>
                <div class="hud">
                    <div class="stat">
                        <span>Score</span>
                        <strong id="score">0</strong>
                    </div>
                    <div class="stat">
                        <span>Lines</span>
                        <strong id="lines">0</strong>
                    </div>
                    <div class="stat">
                        <span>Level</span>
                        <strong id="level">1</strong>
                    </div>
                </div>
                <div class="gesture-hint">
                    <span class="gesture-pill">Tap = Rotate</span>
                    <span class="gesture-pill">Swipe up = Hard drop</span>
                    <span class="gesture-pill">Swipe down = Soft drop</span>
                </div>
                <div class="mobile-play-guide">
                    <strong>Mobil Kontrol</strong>
                    SOL ve SAG ile kaydir. DON ile cevir. IN ile yavas indir. AT ile direkt birak.
                    Istersen oyun alaninda saga, sola ve asagi kaydirarak da oynayabilirsin.
                </div>
                <div class="mobile-actions">
                    <button class="btn" id="start-btn-mobile">Basla</button>
                    <button class="btn secondary" id="pause-btn-mobile">Duraklat</button>
                    <button class="btn secondary" id="reset-btn-mobile">Yeniden Baslat</button>
                </div>
                <div class="status" id="status">Hazır</div>
            </div>

                <aside class="side-card">
                    <div class="panel">
                        <h3>Sonraki Parça</h3>
                        <canvas id="next" width="120" height="120"></canvas>
                    </div>
                    <div class="panel">
                        <h3>Skor Tablosu</h3>
                        <ol class="scoreboard" id="scores-list"></ol>
                    </div>
                    <div class="panel controls-card">
                        <button class="btn" id="start-btn">Basla</button>
                        <button class="btn secondary" id="pause-btn">Duraklat</button>
                        <button class="btn secondary" id="reset-btn">Yeniden Baslat</button>
                    </div>
                    <div class="panel tips">
                        <strong>Kontroller</strong><br>
                        Sol / Sağ Ok: hareket<br>
                        Yukarı Ok veya X: döndür<br>
                        Aşağı Ok: hızlı düşür<br>
                        Boşluk: sert düşür<br>
                        P: duraklat
                    </div>
                </aside>
            </section>

            <div class="touch-controls">
                <button class="btn" data-action="left">
                    <span class="btn-label">SOL</span>
                    <span class="btn-sub">Kaydir</span>
                </button>
                <button class="btn" data-action="right">
                    <span class="btn-label">SAG</span>
                    <span class="btn-sub">Kaydir</span>
                </button>
                <button class="btn" data-action="rotate">
                    <span class="btn-label">DON</span>
                    <span class="btn-sub">Cevir</span>
                </button>
                <button class="btn" data-action="down">
                    <span class="btn-label">IN</span>
                    <span class="btn-sub">Yavas</span>
                </button>
                <button class="btn" data-action="drop">
                    <span class="btn-label">AT</span>
                    <span class="btn-sub">Hizli</span>
                </button>
            </div>
            </section>
            <section class="tab-panel" id="mines-panel">
                <div class="mines-layout">
                    <div class="mines-card">
                        <div class="mines-board" id="mines-board" role="grid" aria-label="Mayin tarlasi"></div>
                    </div>
                    <aside class="mines-card mines-controls">
                        <div class="panel">
                            <h3>Mod</h3>
                            <select class="mines-select" id="mines-level">
                                <option value="easy">Kolay 9x9 (10)</option>
                                <option value="medium">Orta 12x12 (20)</option>
                                <option value="hard">Zor 16x16 (40)</option>
                            </select>
                        </div>
                        <div class="panel mines-stats">
                            <div class="mines-row"><span>Mayin</span><span id="mines-left">0</span></div>
                            <div class="mines-row"><span>Sure</span><span id="mines-time">0</span></div>
                            <div class="mines-row"><span>Durum</span><span id="mines-status">Hazir</span></div>
                        </div>
                        <button class="btn" id="mines-reset">Yeni Oyun</button>
                        <button class="btn secondary mines-toggle" id="mines-flag-toggle">Bayrak Modu: Kapali</button>
                        <div class="panel tips">
                            <strong>Kontroller</strong><br>
                            Tikla: kare ac<br>
                            Bayrak modu: mayin isaretle<br>
                            Uzun basma yerine bayrak modu kullan
                        </div>
                    </aside>
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const canvas = document.getElementById("board");
            const ctx = canvas.getContext("2d");
            const nextCanvas = document.getElementById("next");
            const nextCtx = nextCanvas.getContext("2d");

            const scoreEl = document.getElementById("score");
            const linesEl = document.getElementById("lines");
            const levelEl = document.getElementById("level");
            const statusEl = document.getElementById("status");
            const startBtn = document.getElementById("start-btn");
            const pauseBtn = document.getElementById("pause-btn");
            const resetBtn = document.getElementById("reset-btn");
            const startBtnMobile = document.getElementById("start-btn-mobile");
            const pauseBtnMobile = document.getElementById("pause-btn-mobile");
            const resetBtnMobile = document.getElementById("reset-btn-mobile");
            const gameOverEl = document.getElementById("game-over");
            const finalScoreEl = document.getElementById("final-score");
            const playerNameEl = document.getElementById("player-name");
            const saveScoreBtn = document.getElementById("save-score");
            const playAgainBtn = document.getElementById("play-again");
            const scoresList = document.getElementById("scores-list");
            const tabButtons = document.querySelectorAll(".tab-btn");
            const tabPanels = document.querySelectorAll(".tab-panel");
            const touchSurface = document.getElementById("touch-surface");

            const minesBoard = document.getElementById("mines-board");
            const minesLevel = document.getElementById("mines-level");
            const minesLeft = document.getElementById("mines-left");
            const minesTime = document.getElementById("mines-time");
            const minesStatus = document.getElementById("mines-status");
            const minesReset = document.getElementById("mines-reset");
            const minesFlagToggle = document.getElementById("mines-flag-toggle");

            const COLS = 10;
            const ROWS = 20;
            let BLOCK = 30;
            let NEXT_BLOCK = 24;
            const boardWrap = document.querySelector(".board-wrap");
            const frame = document.querySelector(".frame");
            const app = document.querySelector(".tetris-app");

            const SHAPES = {
                I: [
                    [0, 0, 0, 0],
                    [1, 1, 1, 1],
                    [0, 0, 0, 0],
                    [0, 0, 0, 0],
                ],
                J: [
                    [1, 0, 0],
                    [1, 1, 1],
                    [0, 0, 0],
                ],
                L: [
                    [0, 0, 1],
                    [1, 1, 1],
                    [0, 0, 0],
                ],
                O: [
                    [1, 1],
                    [1, 1],
                ],
                S: [
                    [0, 1, 1],
                    [1, 1, 0],
                    [0, 0, 0],
                ],
                T: [
                    [0, 1, 0],
                    [1, 1, 1],
                    [0, 0, 0],
                ],
                Z: [
                    [1, 1, 0],
                    [0, 1, 1],
                    [0, 0, 0],
                ],
            };

            const COLORS = {
                I: "#7bdff2",
                J: "#ffb347",
                L: "#ffd166",
                O: "#ffe066",
                S: "#06d6a0",
                T: "#ef476f",
                Z: "#f94144",
            };

            const SCORE_TABLE = [0, 100, 300, 500, 800];
            const STORAGE_KEY = "tetris_high_scores";

            function resizeCanvas() {
                const availableWidth = boardWrap.clientWidth;
                const viewportHeight = window.visualViewport?.height || window.innerHeight;
                const isCompact = window.innerWidth <= 640;
                const reservedHeight = isCompact ? 300 : viewportHeight * 0.42;
                const maxHeight = Math.max(isCompact ? 220 : 300, viewportHeight - reservedHeight);
                BLOCK = Math.floor(Math.min(availableWidth / COLS, maxHeight / ROWS));
                BLOCK = Math.max(isCompact ? 14 : 16, BLOCK);
                NEXT_BLOCK = Math.max(14, Math.floor(BLOCK * 0.8));
                canvas.width = COLS * BLOCK;
                canvas.height = ROWS * BLOCK;
                nextCanvas.width = 4 * NEXT_BLOCK;
                nextCanvas.height = 4 * NEXT_BLOCK;
            }

            function fitFrame() {
                frame.style.setProperty("--frame-scale", 1);
                const styles = window.getComputedStyle(app);
                const paddingY = parseFloat(styles.paddingTop) + parseFloat(styles.paddingBottom);
                const paddingX = parseFloat(styles.paddingLeft) + parseFloat(styles.paddingRight);
                const maxWidth = window.innerWidth - paddingX;
                const maxHeight = window.innerHeight - paddingY;
                const scale = Math.min(1, maxWidth / frame.offsetWidth, maxHeight / frame.offsetHeight);
                frame.style.setProperty("--frame-scale", scale.toFixed(3));
            }

            function setActiveTab(id) {
                tabButtons.forEach(button => {
                    const isActive = button.dataset.tab === id;
                    button.classList.toggle("active", isActive);
                    button.setAttribute("aria-selected", isActive ? "true" : "false");
                });
                tabPanels.forEach(panel => {
                    panel.classList.toggle("active", panel.id === id);
                });
            }

            function isTouchMode() {
                return (
                    "ontouchstart" in window ||
                    navigator.maxTouchPoints > 0 ||
                    window.innerWidth <= 980
                );
            }

            function runTouchAction(action) {
                if (!running || paused || gameOverEl.classList.contains("active")) {
                    return;
                }

                if (action === "left") move(-1);
                if (action === "right") move(1);
                if (action === "rotate") playerRotate(1);
                if (action === "down") drop();
                if (action === "drop") hardDrop();
            }

            function lockGameScroll(enabled) {
                if (window.innerWidth > 640) {
                    document.body.style.overflow = "";
                    document.documentElement.style.overflow = "";
                    return;
                }

                document.body.style.overflow = enabled ? "hidden" : "";
                document.documentElement.style.overflow = enabled ? "hidden" : "";
            }

            function bindTouchSurface() {
                if (!touchSurface) {
                    return;
                }

                const minimumSwipe = 24;

                touchSurface.addEventListener("pointerdown", event => {
                    if (!isTouchMode()) {
                        return;
                    }

                    event.preventDefault();
                    touchStartPoint = {
                        x: event.clientX,
                        y: event.clientY,
                    };

                    if (!running) {
                        startGame();
                    }
                });

                touchSurface.addEventListener("pointermove", event => {
                    if (!isTouchMode() || !touchStartPoint) {
                        return;
                    }

                    event.preventDefault();
                });

                ["pointerup", "pointercancel", "pointerleave"].forEach(type => {
                    touchSurface.addEventListener(type, event => {
                        if (!touchStartPoint || !isTouchMode()) {
                            touchStartPoint = null;
                            return;
                        }

                        event.preventDefault();
                        const deltaX = event.clientX - touchStartPoint.x;
                        const deltaY = event.clientY - touchStartPoint.y;
                        const absX = Math.abs(deltaX);
                        const absY = Math.abs(deltaY);

                        if (absX < minimumSwipe && absY < minimumSwipe) {
                            runTouchAction("rotate");
                            touchStartPoint = null;
                            return;
                        }

                        if (absX > absY) {
                            runTouchAction(deltaX > 0 ? "right" : "left");
                        } else if (deltaY > minimumSwipe) {
                            runTouchAction("down");
                        } else if (deltaY < -minimumSwipe) {
                            runTouchAction("drop");
                        }

                        touchStartPoint = null;
                    });
                });
            }

            let grid = createMatrix(ROWS, COLS);
            let bag = [];
            let current = null;
            let nextType = null;
            let score = 0;
            let lines = 0;
            let level = 1;
            let dropCounter = 0;
            let dropInterval = 800;
            let lastTime = 0;
            let running = false;
            let paused = false;
            let touchStartPoint = null;

            function createMatrix(rows, cols) {
                return Array.from({ length: rows }, () => Array(cols).fill(0));
            }

            function shuffle(list) {
                for (let i = list.length - 1; i > 0; i -= 1) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [list[i], list[j]] = [list[j], list[i]];
                }
                return list;
            }

            function refillBag() {
                bag = shuffle(Object.keys(SHAPES));
            }

            function takeFromBag() {
                if (bag.length === 0) {
                    refillBag();
                }
                return bag.pop();
            }

            function createPiece(type) {
                return {
                    type,
                    matrix: SHAPES[type].map(row => row.slice()),
                    pos: { x: 0, y: 0 },
                };
            }

            function resetPiece() {
                current = createPiece(nextType || takeFromBag());
                nextType = takeFromBag();
                current.pos.y = 0;
                current.pos.x = Math.floor((COLS - current.matrix[0].length) / 2);
                if (collides(grid, current)) {
                    endGame();
                }
                drawNext();
            }

            function collides(board, piece) {
                for (let y = 0; y < piece.matrix.length; y += 1) {
                    for (let x = 0; x < piece.matrix[y].length; x += 1) {
                        if (piece.matrix[y][x] === 0) continue;
                        const newX = piece.pos.x + x;
                        const newY = piece.pos.y + y;
                        if (newX < 0 || newX >= COLS || newY >= ROWS) {
                            return true;
                        }
                        if (newY >= 0 && board[newY][newX] !== 0) {
                            return true;
                        }
                    }
                }
                return false;
            }

            function merge(board, piece) {
                piece.matrix.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value !== 0) {
                            board[piece.pos.y + y][piece.pos.x + x] = piece.type;
                        }
                    });
                });
            }

            function rotate(matrix, dir) {
                for (let y = 0; y < matrix.length; y += 1) {
                    for (let x = 0; x < y; x += 1) {
                        [matrix[x][y], matrix[y][x]] = [matrix[y][x], matrix[x][y]];
                    }
                }
                if (dir > 0) {
                    matrix.forEach(row => row.reverse());
                } else {
                    matrix.reverse();
                }
            }

            function playerRotate(dir) {
                const pos = current.pos.x;
                let offset = 1;
                rotate(current.matrix, dir);
                while (collides(grid, current)) {
                    current.pos.x += offset;
                    offset = -(offset + (offset > 0 ? 1 : -1));
                    if (offset > current.matrix[0].length) {
                        rotate(current.matrix, -dir);
                        current.pos.x = pos;
                        return;
                    }
                }
            }

            function sweepLines() {
                let rowCount = 0;
                outer: for (let y = grid.length - 1; y >= 0; y -= 1) {
                    for (let x = 0; x < grid[y].length; x += 1) {
                        if (grid[y][x] === 0) {
                            continue outer;
                        }
                    }
                    const row = grid.splice(y, 1)[0].fill(0);
                    grid.unshift(row);
                    rowCount += 1;
                    y += 1;
                }
                if (rowCount > 0) {
                    score += SCORE_TABLE[rowCount] * level;
                    lines += rowCount;
                    const nextLevel = Math.floor(lines / 10) + 1;
                    if (nextLevel !== level) {
                        level = nextLevel;
                        dropInterval = Math.max(120, 800 - (level - 1) * 60);
                    }
                }
            }

            function drawGrid() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                grid.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value !== 0) {
                            drawBlock(x, y, COLORS[value]);
                        } else {
                            ctx.strokeStyle = "rgba(255,255,255,0.03)";
                            ctx.strokeRect(x * BLOCK, y * BLOCK, BLOCK, BLOCK);
                        }
                    });
                });

                drawPiece(current.matrix, current.pos.x, current.pos.y, COLORS[current.type]);
            }

            function drawBlock(x, y, color) {
                ctx.fillStyle = color;
                ctx.fillRect(x * BLOCK + 1, y * BLOCK + 1, BLOCK - 2, BLOCK - 2);
            }

            function drawPiece(matrix, offsetX, offsetY, color) {
                matrix.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value !== 0) {
                            ctx.fillStyle = color;
                            ctx.fillRect(
                                (x + offsetX) * BLOCK + 1,
                                (y + offsetY) * BLOCK + 1,
                                BLOCK - 2,
                                BLOCK - 2
                            );
                        }
                    });
                });
            }

            function drawNext() {
                nextCtx.clearRect(0, 0, nextCanvas.width, nextCanvas.height);
                const matrix = SHAPES[nextType];
                const color = COLORS[nextType];
                const offsetX = Math.floor((4 - matrix[0].length) / 2);
                const offsetY = Math.floor((4 - matrix.length) / 2);
                matrix.forEach((row, y) => {
                    row.forEach((value, x) => {
                        if (value !== 0) {
                            nextCtx.fillStyle = color;
                            nextCtx.fillRect(
                                (x + offsetX) * NEXT_BLOCK + 2,
                                (y + offsetY) * NEXT_BLOCK + 2,
                                NEXT_BLOCK - 4,
                                NEXT_BLOCK - 4
                            );
                        }
                    });
                });
            }

            function updateStats() {
                scoreEl.textContent = score;
                linesEl.textContent = lines;
                levelEl.textContent = level;
            }

            function hardDrop() {
                while (!collides(grid, current)) {
                    current.pos.y += 1;
                }
                current.pos.y -= 1;
                lockPiece();
            }

            function lockPiece() {
                merge(grid, current);
                sweepLines();
                resetPiece();
                updateStats();
            }

            function drop() {
                current.pos.y += 1;
                if (collides(grid, current)) {
                    current.pos.y -= 1;
                    lockPiece();
                }
                dropCounter = 0;
            }

            function move(dir) {
                current.pos.x += dir;
                if (collides(grid, current)) {
                    current.pos.x -= dir;
                }
            }

            function setStatus(text) {
                statusEl.textContent = text;
            }

            function startGame() {
                if (!running) {
                    resetGame();
                    running = true;
                }
                paused = false;
                gameOverEl.classList.remove("active");
                setStatus("Oynanıyor");
            }

            function resetGame() {
                grid = createMatrix(ROWS, COLS);
                score = 0;
                lines = 0;
                level = 1;
                dropInterval = 800;
                refillBag();
                nextType = takeFromBag();
                resetPiece();
                updateStats();
                dropCounter = 0;
            }

            function togglePause() {
                if (!running) return;
                paused = !paused;
                setStatus(paused ? "Duraklatildi" : "Oynaniyor");
            }

            function endGame() {
                running = false;
                paused = false;
                setStatus("Oyun bitti");
                finalScoreEl.textContent = score;
                playerNameEl.value = "";
                gameOverEl.classList.add("active");
            }

            function update(time = 0) {
                const delta = time - lastTime;
                lastTime = time;
                if (running && !paused) {
                    dropCounter += delta;
                    if (dropCounter > dropInterval) {
                        drop();
                    }
                    drawGrid();
                }
                requestAnimationFrame(update);
            }

            function loadScores() {
                const stored = localStorage.getItem(STORAGE_KEY);
                return stored ? JSON.parse(stored) : [];
            }

            function saveScores(entries) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(entries));
            }

            function renderScores() {
                const entries = loadScores();
                scoresList.innerHTML = "";
                if (entries.length === 0) {
                    const empty = document.createElement("li");
                    empty.textContent = "Henüz skor yok";
                    scoresList.appendChild(empty);
                    return;
                }
                entries.forEach(entry => {
                    const li = document.createElement("li");
                    li.innerHTML = `<span>${entry.name}</span><span>${entry.score}</span>`;
                    scoresList.appendChild(li);
                });
            }

            function recordScore() {
                const name = playerNameEl.value.trim() || "Player";
                const entries = loadScores();
                entries.push({
                    name,
                    score,
                    lines,
                    level,
                    date: new Date().toISOString(),
                });
                entries.sort((a, b) => b.score - a.score);
                saveScores(entries.slice(0, 5));
                renderScores();
            }

            const minePresets = {
                easy: { rows: 9, cols: 9, mines: 10 },
                medium: { rows: 12, cols: 12, mines: 20 },
                hard: { rows: 16, cols: 16, mines: 40 },
            };
            const mineNumberColors = [
                "#0f172a",
                "#2563eb",
                "#16a34a",
                "#f97316",
                "#ef4444",
                "#7c3aed",
                "#0f172a",
                "#db2777",
                "#111827",
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
                if (!minesBoard) return;
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
                if (cell.adjacent > 0) {
                    el.textContent = cell.adjacent;
                    el.style.color = mineNumberColors[cell.adjacent] || "#111827";
                } else {
                    el.textContent = "";
                }
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
                        btn.dataset.row = r;
                        btn.dataset.col = c;
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

            document.addEventListener("keydown", event => {
                if (!running || paused) return;
                if (gameOverEl.classList.contains("active")) return;
                switch (event.code) {
                    case "ArrowLeft":
                        move(-1);
                        break;
                    case "ArrowRight":
                        move(1);
                        break;
                    case "ArrowDown":
                        drop();
                        break;
                    case "ArrowUp":
                    case "KeyX":
                        playerRotate(1);
                        break;
                    case "KeyZ":
                        playerRotate(-1);
                        break;
                    case "Space":
                        hardDrop();
                        break;
                    case "KeyP":
                        togglePause();
                        break;
                    default:
                        break;
                }
            });

            startBtn.addEventListener("click", startGame);
            pauseBtn.addEventListener("click", togglePause);
            resetBtn.addEventListener("click", () => {
                resetGame();
                running = true;
                paused = false;
                setStatus("Oynaniyor");
            });
            startBtnMobile.addEventListener("click", startGame);
            pauseBtnMobile.addEventListener("click", togglePause);
            resetBtnMobile.addEventListener("click", () => {
                resetGame();
                running = true;
                paused = false;
                setStatus("Oynaniyor");
            });

            saveScoreBtn.addEventListener("click", () => {
                recordScore();
                gameOverEl.classList.remove("active");
                setStatus("Hazır");
            });

            playAgainBtn.addEventListener("click", () => {
                resetGame();
                running = true;
                paused = false;
                gameOverEl.classList.remove("active");
                setStatus("Oynanıyor");
            });

            document.querySelectorAll(".touch-controls .btn").forEach(button => {
                const action = button.dataset.action;
                let holdInterval = null;
                const runAction = () => {
                    runTouchAction(action);
                };
                button.addEventListener("pointerdown", event => {
                    event.preventDefault();
                    runAction();
                    if (action === "left" || action === "right" || action === "down") {
                        holdInterval = setInterval(runAction, 90);
                    }
                });
                ["pointerup", "pointerleave", "pointercancel"].forEach(type => {
                    button.addEventListener(type, () => {
                        if (holdInterval) {
                            clearInterval(holdInterval);
                            holdInterval = null;
                        }
                    });
                });
            });

            tabButtons.forEach(button => {
                button.addEventListener("click", () => {
                    setActiveTab(button.dataset.tab);
                    fitFrame();
                    resizeMinesBoard();
                });
            });

            minesReset.addEventListener("click", resetMineGame);
            minesLevel.addEventListener("change", resetMineGame);
            minesFlagToggle.addEventListener("click", () => {
                mineFlagMode = !mineFlagMode;
                minesFlagToggle.textContent = mineFlagMode
                    ? "Bayrak Modu: Acik"
                    : "Bayrak Modu: Kapali";
            });

            renderScores();
            setStatus("Hazır");
            resizeCanvas();
            fitFrame();
            resetMineGame();
            setActiveTab("tetris-panel");
            update();

            function syncTouchUi() {
                const isTouch = isTouchMode();
                document.body.classList.toggle("touch-enabled", isTouch);
                lockGameScroll(isTouch);
            }

            syncTouchUi();
            bindTouchSurface();

            canvas.addEventListener("pointerdown", () => {
                if (!running) {
                    startGame();
                }
            });

            [canvas, touchSurface].forEach(element => {
                if (!element) {
                    return;
                }

                ["touchstart", "touchmove"].forEach(type => {
                    element.addEventListener(type, event => {
                        if (isTouchMode()) {
                            event.preventDefault();
                        }
                    }, { passive: false });
                });
            });

            window.addEventListener("resize", () => {
                resizeCanvas();
                fitFrame();
                syncTouchUi();
                resizeMinesBoard();
                drawGrid();
            });
        });
    </script>
</x-game::layouts.master>
