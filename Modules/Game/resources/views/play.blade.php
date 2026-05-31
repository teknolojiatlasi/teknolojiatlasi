@extends('layouts.app2')

@section('title', 'Oyunlar')

@push('styles')
    <style>
        .games-page {
            padding: 1.5rem 0 2rem;
        }

        .games-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }

        .games-sidebar,
        .games-player {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        }

        .games-sidebar {
            position: sticky;
            top: 92px;
            border-radius: 1rem;
            padding: 1rem;
        }

        .games-title {
            margin: 0 0 0.75rem;
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .games-list {
            display: grid;
            gap: 0.5rem;
        }

        .games-link {
            display: grid;
            grid-template-columns: 2.25rem minmax(0, 1fr);
            gap: 0.65rem;
            align-items: center;
            padding: 0.75rem;
            border-radius: 0.75rem;
            color: #334155;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .games-link:hover,
        .games-link.is-active {
            color: #0f172a;
            background: #eef6ff;
            border-color: rgba(37, 99, 235, 0.2);
            text-decoration: none;
        }

        .games-link-icon {
            display: inline-flex;
            width: 2.25rem;
            height: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.7rem;
            background: #0ea5e9;
            color: #fff;
        }

        .games-link strong,
        .games-link span {
            display: block;
            min-width: 0;
        }

        .games-link strong {
            font-size: 0.92rem;
            line-height: 1.2;
        }

        .games-link span {
            margin-top: 0.15rem;
            color: #64748b;
            font-size: 0.78rem;
            line-height: 1.35;
        }

        .games-player {
            border-radius: 1rem;
            overflow: hidden;
            min-height: calc(100vh - 140px);
        }

        .games-player-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .games-player-header h1 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .games-player-frame {
            display: block;
            width: 100%;
            height: calc(100vh - 210px);
            min-height: 620px;
            border: 0;
            background: #0f172a;
        }

        @media (max-width: 900px) {
            .games-shell {
                grid-template-columns: 1fr;
            }

            .games-sidebar {
                position: static;
            }

            .games-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .games-player-frame {
                height: 720px;
                min-height: 720px;
            }
        }

        @media (max-width: 560px) {
            .games-page {
                padding-top: 1rem;
            }

            .games-list {
                grid-template-columns: 1fr;
            }

            .games-player-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .games-player-frame {
                height: 760px;
                min-height: 760px;
            }
        }
    </style>
@endpush

@section('content')
    <main class="games-page">
        <div class="container">
            <div class="games-shell">
                <aside class="games-sidebar" aria-label="Oyun listesi">
                    <h1 class="games-title">Oyunlar</h1>
                    <nav class="games-list">
                        @foreach ($games as $key => $game)
                            <a class="games-link {{ $selectedKey === $key ? 'is-active' : '' }}" href="{{ route('game.play', $key) }}">
                                <span class="games-link-icon"><i class="fa {{ $game['icon'] }}"></i></span>
                                <span>
                                    <strong>{{ $game['title'] }}</strong>
                                    <span>{{ $game['description'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </nav>
                </aside>

                <section class="games-player" aria-label="{{ $selectedGame['title'] }}">
                    <div class="games-player-header">
                        <h1>{{ $selectedGame['title'] }}</h1>
                        <a class="btn btn-sm btn-outline-primary" href="{{ $gameUrl }}" target="_blank" rel="noopener">
                            Tam ekran ac
                        </a>
                    </div>
                    <iframe class="games-player-frame" src="{{ $gameUrl }}" title="{{ $selectedGame['title'] }}" loading="eager"></iframe>
                </section>
            </div>
        </div>
    </main>
@endsection
