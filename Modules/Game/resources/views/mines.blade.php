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
