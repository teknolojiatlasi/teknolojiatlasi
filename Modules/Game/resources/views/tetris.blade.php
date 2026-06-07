<style>
    .tetris-modern {
        --tm-panel: rgba(2, 6, 23, 0.74);
        --tm-line: rgba(148, 163, 184, 0.18);
        --tm-text-soft: #b7c7df;
    }

    body.game-pwa-shell {
        overflow: hidden;
    }

    .tetris-app {
        padding: 10px;
    }

    .frame {
        width: 100%;
        max-width: 1180px;
        height: calc(100dvh - 20px);
        max-height: calc(100dvh - 20px);
        gap: 0;
    }

    .page-topbar {
        display: none;
    }

    .tetris-modern,
    .tetris-modern .game-grid {
        height: 100%;
        min-height: 0;
    }

    .tetris-modern .game-grid {
        grid-template-columns: minmax(0, 1fr) minmax(240px, 300px);
        align-items: stretch;
    }

    .tetris-modern .board-card,
    .tetris-modern .side-card,
    .tetris-modern .panel,
    .tetris-modern .controls-card {
        border-radius: 8px;
    }

    .tetris-modern .board-card {
        position: relative;
        overflow: hidden;
        min-height: 0;
        grid-template-rows: auto minmax(0, 1fr) auto auto;
        background:
            linear-gradient(145deg, rgba(15, 23, 42, 0.96), rgba(8, 13, 28, 0.94)),
            radial-gradient(circle at 20% 10%, rgba(34, 197, 94, 0.18), transparent 34%);
    }

    .tetris-modern .board-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .tetris-modern .board-wrap {
        width: min(100%, calc((100dvh - 152px) / 2), 390px);
        align-self: center;
        border-radius: 8px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        background: #020617;
    }

    .tetris-modern #board {
        border-radius: 8px;
    }

    .tetris-modern .hud {
        max-width: 420px;
    }

    .tetris-modern .stat {
        border: 1px solid var(--tm-line);
        background: rgba(15, 23, 42, 0.72);
    }

    .tetris-modern .status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 30px;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.1);
        color: #d7ffe8;
        font-size: 13px;
    }

    .tetris-modern .status::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.14);
    }

    .tetris-modern .side-card {
        min-height: 0;
        overflow: hidden;
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.9), rgba(2, 6, 23, 0.82)),
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.14), transparent 36%);
    }

    .tetris-modern .panel,
    .tetris-modern .controls-card {
        border: 1px solid var(--tm-line);
        background: var(--tm-panel);
    }

    .tetris-modern .panel h3 {
        color: #e2e8f0;
        letter-spacing: 0.04em;
    }

    .tetris-modern #next {
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 8px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
    }

    .tetris-modern .scoreboard li {
        padding: 8px 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }

    .tetris-modern .tips {
        color: var(--tm-text-soft);
    }

    .tetris-modern .btn {
        border-radius: 8px;
        background: #38bdf8;
        color: #06111f;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(56, 189, 248, 0.18);
    }

    .tetris-modern .btn.secondary {
        background: rgba(226, 232, 240, 0.12);
        color: #e2e8f0;
        box-shadow: none;
        border: 1px solid rgba(226, 232, 240, 0.16);
    }

    .tetris-modern .mobile-play-guide {
        border: 1px solid rgba(148, 163, 184, 0.16);
    }

    .tetris-modern .touch-controls .btn {
        background: linear-gradient(145deg, #38bdf8, #0ea5e9);
        border-radius: 8px;
        border-color: #082f49;
        color: #f8fafc;
        box-shadow:
            inset 4px 4px 0 rgba(255, 255, 255, 0.2),
            inset -4px -4px 0 rgba(8, 47, 73, 0.42),
            0 3px 0 #082f49;
    }

    .tetris-modern .touch-controls .btn-label {
        font-size: 0;
    }

    .tetris-modern .touch-controls .btn-label::before {
        display: block;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .tetris-modern .touch-controls .btn[data-action="rotate"] .btn-label::before {
        content: "\21bb";
    }

    .tetris-modern .touch-controls .btn[data-action="left"] .btn-label::before {
        content: "\2039";
    }

    .tetris-modern .touch-controls .btn[data-action="down"] .btn-label::before {
        content: "\2193";
    }

    .tetris-modern .touch-controls .btn[data-action="right"] .btn-label::before {
        content: "\203a";
    }

    .tetris-modern .overlay-card {
        border-radius: 8px;
        color: #0f172a;
    }

    .tetris-modern .overlay-card h2,
    .tetris-modern .overlay-card p {
        margin: 0;
    }

    .tetris-modern .overlay-card .btn.secondary {
        background: #e2e8f0;
        color: #0f172a;
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
            height: 100dvh;
            min-height: 100dvh;
            padding: 6px 6px calc(8px + env(safe-area-inset-bottom));
            overflow: hidden;
        }

        .frame {
            height: calc(100dvh - 12px - env(safe-area-inset-bottom));
            max-height: calc(100dvh - 12px - env(safe-area-inset-bottom));
            max-width: calc(100vw - 12px);
            overflow: hidden;
        }

        .tetris-modern .game-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            height: 100%;
        }

        .tetris-modern .side-card {
            display: none;
        }

        .tetris-modern .board-card {
            height: 100%;
            padding: 8px;
            gap: 6px;
            grid-template-rows: auto minmax(0, auto) auto auto auto;
        }

        .tetris-modern .hud {
            gap: 5px;
        }

        .tetris-modern .stat {
            padding: 6px 4px;
        }

        .tetris-modern .stat span {
            font-size: 9px;
        }

        .tetris-modern .stat strong {
            font-size: 15px;
        }

        .tetris-modern .board-wrap {
            width: min(calc(100vw - 24px), calc((100dvh - 290px - env(safe-area-inset-bottom)) / 2), 300px);
            max-height: calc(100dvh - 290px - env(safe-area-inset-bottom));
        }

        .tetris-modern .status {
            min-height: 26px;
            font-size: 12px;
        }

        .tetris-modern .mobile-play-guide {
            display: none;
        }

        .tetris-modern .mobile-actions {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .tetris-modern .mobile-actions .btn {
            min-height: 36px;
            padding: 7px 6px;
            font-size: 12px;
        }

        .tetris-modern .touch-controls,
        .touch-enabled .tetris-modern .touch-controls {
            display: grid;
            grid-template-columns: repeat(3, 62px);
            grid-template-rows: repeat(2, 62px);
            grid-template-areas:
                ". rotate ."
                "left down right";
            gap: 6px;
            margin: 2px auto 0;
            width: max-content;
            padding: 0;
            position: static;
        }

        .tetris-modern .touch-controls .btn {
            width: 62px;
            height: 62px;
            min-width: 62px;
        }

        .tetris-modern .touch-controls .btn-label {
            font-size: 0;
        }

        .tetris-modern .touch-controls .btn-label::before {
            font-size: 28px;
        }
    }
</style>

<section id="tetris-panel" class="tab-panel active tetris-modern" aria-label="Tetris oyunu">
    <div class="game-grid">
        <div class="board-card">
            <div class="hud" aria-label="Oyun istatistikleri">
                <div class="stat">
                    <span>Skor</span>
                    <strong id="score">0</strong>
                </div>
                <div class="stat">
                    <span>Satir</span>
                    <strong id="lines">0</strong>
                </div>
                <div class="stat">
                    <span>Seviye</span>
                    <strong id="level">1</strong>
                </div>
            </div>

            <div class="board-wrap">
                <canvas id="board" width="300" height="600" aria-label="Tetris oyun tahtasi"></canvas>
                <div id="touch-surface" class="touch-surface" aria-hidden="true"></div>

                <div id="game-over" class="overlay" role="dialog" aria-modal="true" aria-labelledby="game-over-title">
                    <div class="overlay-card">
                        <h2 id="game-over-title">Oyun bitti</h2>
                        <p>Skor: <strong id="final-score">0</strong></p>
                        <input id="player-name" type="text" maxlength="18" placeholder="Ismin" autocomplete="off">
                        <button id="save-score" class="btn" type="button">Skoru kaydet</button>
                        <button id="play-again" class="btn secondary" type="button">Tekrar oyna</button>
                    </div>
                </div>
            </div>

            <div id="status" class="status" aria-live="polite">Hazir</div>

            <div class="mobile-actions" aria-label="Mobil oyun komutlari">
                <button id="start-btn-mobile" class="btn" type="button">Baslat</button>
                <button id="pause-btn-mobile" class="btn secondary" type="button">Duraklat</button>
                <button id="reset-btn-mobile" class="btn secondary" type="button">Yenile</button>
            </div>

            <div class="mobile-play-guide">
                <strong>Dokunmatik kontrol</strong>
                Dokunarak dondur, saga sola kaydirarak hareket ettir, yukari kaydirarak hizli birak.
            </div>

            <div class="touch-controls" aria-label="Dokunmatik yon tuslari">
                <button class="btn" type="button" data-action="rotate" aria-label="Parcayi dondur">
                    <span class="btn-label">↻</span>
                    <span class="btn-sub">Dondur</span>
                </button>
                <button class="btn" type="button" data-action="left" aria-label="Sola git">
                    <span class="btn-label">‹</span>
                    <span class="btn-sub">Sol</span>
                </button>
                <button class="btn" type="button" data-action="down" aria-label="Asagi indir">
                    <span class="btn-label">↓</span>
                    <span class="btn-sub">Asagi</span>
                </button>
                <button class="btn" type="button" data-action="right" aria-label="Saga git">
                    <span class="btn-label">›</span>
                    <span class="btn-sub">Sag</span>
                </button>
            </div>
        </div>

        <aside class="side-card" aria-label="Tetris paneli">
            <div class="panel">
                <h3>Siradaki</h3>
                <canvas id="next" width="96" height="96" aria-label="Siradaki parca"></canvas>
            </div>

            <div class="panel">
                <h3>En iyiler</h3>
                <ol id="scores-list" class="scoreboard"></ol>
            </div>

            <div class="controls-card" aria-label="Masaustu kontrolleri">
                <button id="start-btn" class="btn" type="button">Baslat</button>
                <button id="pause-btn" class="btn secondary" type="button">Duraklat</button>
                <button id="reset-btn" class="btn secondary" type="button">Yeniden baslat</button>
            </div>

            <div class="panel tips">
                <h3>Kontrol</h3>
                <div>Ok tuslariyla hareket et. Yukari veya X parcayi dondurur. Space hizli birakir. P duraklatir.</div>
            </div>
        </aside>
    </div>
</section>
