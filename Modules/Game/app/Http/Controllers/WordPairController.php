<?php

namespace Modules\Game\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Game\Models\WordPair;

class WordPairController extends Controller
{
    public function index()
    {
        $pairs = WordPair::query()->orderByDesc('id')->get();
        $selectedIds = session('game.memory_pairs', []);

        return view('game::word-pairs.index', [
            'pairs' => $pairs,
            'selectedIds' => $selectedIds,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:255'],
            'meaning' => ['required', 'string', 'max:255'],
        ]);

        WordPair::query()->create($data);

        return redirect()->route('game.word-pairs.index');
    }

    public function destroy(WordPair $wordPair): RedirectResponse
    {
        $wordPair->delete();

        return redirect()->route('game.word-pairs.index');
    }

    public function selectForPuzzle(Request $request): RedirectResponse
    {
        $mode = $request->input('mode', 'selected');
        $ids = $request->input('pair_ids', []);
        $cleanIds = array_values(array_filter(array_map('intval', (array) $ids)));

        if ($mode === 'random' || count($cleanIds) === 0) {
            $request->session()->forget('game.memory_pairs');
        } else {
            $request->session()->put('game.memory_pairs', $cleanIds);
        }

        return redirect()->route('game.puzzle-memory');
    }
}
