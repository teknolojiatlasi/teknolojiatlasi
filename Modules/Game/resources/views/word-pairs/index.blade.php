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

        .pairs-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: clamp(16px, 3vw, 40px);
        }

        .pairs-shell {
            width: min(1100px, 100%);
            display: grid;
            gap: 20px;
            animation: rise-in 0.8s ease both;
        }

        .pairs-header,
        .card,
        .table-card {
            background: var(--card);
            border-radius: 24px;
            padding: 20px 24px;
            box-shadow: var(--shadow);
        }

        .pairs-header h1 {
            margin: 0 0 6px;
            font-size: clamp(26px, 4vw, 40px);
        }

        .pairs-header p {
            margin: 0;
            color: var(--muted);
            max-width: 540px;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 20px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        .field {
            display: grid;
            gap: 6px;
            margin-bottom: 12px;
        }

        .field label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .field input {
            border: 1px solid #e2dfd6;
            border-radius: 12px;
            padding: 10px 12px;
            font-family: "Space Grotesk", system-ui, sans-serif;
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

        .btn.ghost {
            background: transparent;
            color: var(--accent-2);
            box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.2);
        }

        .btn:active {
            transform: translateY(1px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid #eee8de;
            vertical-align: middle;
        }

        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .table-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .empty {
            color: var(--muted);
            font-size: 14px;
            padding: 12px 0;
        }

        .note {
            font-size: 13px;
            color: var(--muted);
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
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="pairs-page">
        <div class="pairs-shell">
            <header class="pairs-header">
                <h1>Kelime - Anlam Yönetimi</h1>
                <p>Kelimeleri ekle, listeden seç ve memory puzzle için eşleştirme destesi oluştur.</p>
            </header>

            <section class="grid">
                <div class="card">
                    <h2>Yeni Kelime Ekle</h2>
                    <form method="POST" action="{{ route('game.word-pairs.store') }}">
                        @csrf
                        <div class="field">
                            <label for="word">Kelime</label>
                            <input id="word" name="word" type="text" required />
                        </div>
                        <div class="field">
                            <label for="meaning">Anlam</label>
                            <input id="meaning" name="meaning" type="text" required />
                        </div>
                        <div class="actions">
                            <button class="btn" type="submit">Ekle</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Puzzle Oluştur</h2>
                    <p class="note">Seçilen kelimelerle puzzle başlatılır. Seçim yapmazsan rastgele tüm kelimelerden seçilir.</p>
                    <form method="POST" action="{{ route('game.puzzle-memory.select') }}">
                        @csrf
                        <div class="actions">
                            <button class="btn" type="submit" name="mode" value="selected">Seçilenlerle Başlat</button>
                            <button class="btn ghost" type="submit" name="mode" value="random">Rastgele Başlat</button>
                            <a class="btn secondary" href="{{ route('game.puzzle-memory') }}">Puzzle Sayfası</a>
                        </div>
                        <div class="table-card" style="margin-top: 16px; padding: 0;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Kelime</th>
                                        <th>Anlam</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pairs as $pair)
                                        <tr>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    name="pair_ids[]"
                                                    value="{{ $pair->id }}"
                                                    @if (in_array($pair->id, (array) $selectedIds, true)) checked @endif
                                                />
                                            </td>
                                            <td>{{ $pair->word }}</td>
                                            <td>{{ $pair->meaning }}</td>
                                            <td class="table-actions">
                                                <button class="btn secondary" type="submit" form="delete-{{ $pair->id }}">Sil</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="empty">Henüz kelime yok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                        @foreach ($pairs as $pair)
                            <form id="delete-{{ $pair->id }}" method="POST" action="{{ route('game.word-pairs.destroy', $pair) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                </div>
            </section>
        </div>
    </div>
</x-game::layouts.master>
