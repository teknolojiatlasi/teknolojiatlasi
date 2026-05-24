<?php

namespace Modules\Game\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Game\Models\WordPair;

class PuzzleController extends Controller
{
    public function index()
    {
        return view('game::puzzle');
    }

    public function puzzle2()
    {
        return view('game::puzzle2');
    }

    public function memory()
    {
        $selectedIds = session('game.memory_pairs', []);
        $query = WordPair::query()->select(['id', 'word', 'meaning']);
        if (is_array($selectedIds) && count($selectedIds) > 0) {
            $query->whereIn('id', $selectedIds);
        }
        $pairs = $query->get();

        return view('game::puzzle-memory', [
            'pairs' => $pairs,
        ]);
    }
}
