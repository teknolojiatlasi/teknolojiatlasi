<?php

namespace Modules\Game\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameController extends Controller
{
    private function playableGames(): array
    {
        return [
            'tetris' => [
                'title' => 'Tetris',
                'description' => 'Bloklari yerlestir, satirlari temizle.',
                'route' => 'game.index',
                'icon' => 'fa-gamepad',
            ],
            'mayin-tarlasi' => [
                'title' => 'Mayin Tarlasi',
                'description' => 'Mayinlari bul, guvenli kareleri ac.',
                'route' => 'game.mines',
                'icon' => 'fa-bomb',
            ],
            'eslestirme' => [
                'title' => 'Kelime Eslestirme',
                'description' => 'Kelime ve anlamlarini dogru eslestir.',
                'route' => 'game.puzzle',
                'icon' => 'fa-puzzle-piece',
            ],
            'bulmaca' => [
                'title' => 'Hafiza Bulmacasi',
                'description' => 'Kartlari ac, eslesenleri yakala.',
                'route' => 'game.puzzle2',
                'icon' => 'fa-brain',
            ],
            'kelime-hafiza' => [
                'title' => 'Kelime Hafiza',
                'description' => 'Kelime ciftleriyle hafiza oyunu oyna.',
                'route' => 'game.puzzle-memory',
                'icon' => 'fa-layer-group',
            ],
        ];
    }

    public function play(?string $game = null)
    {
        $games = $this->playableGames();
        $selectedKey = array_key_exists((string) $game, $games) ? (string) $game : array_key_first($games);
        $selectedGame = $games[$selectedKey];

        return view('game::play', [
            'games' => $games,
            'selectedKey' => $selectedKey,
            'selectedGame' => $selectedGame,
            'gameUrl' => route($selectedGame['route']),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('game::index');
    }

    public function mines()
    {
        return view('game::mines-page');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('game::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('game::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('game::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
