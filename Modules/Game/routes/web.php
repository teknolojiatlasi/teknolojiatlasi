<?php

use Illuminate\Support\Facades\Route;
use Modules\Game\Http\Controllers\GameController;
use Modules\Game\Http\Controllers\PuzzleController;
use Modules\Game\Http\Controllers\WordPairController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('games', GameController::class)->names('game');
    Route::get('puzzle2', [PuzzleController::class, 'puzzle2'])->name('game.puzzle2');
    Route::get('puzzle', [PuzzleController::class, 'index'])->name('game.puzzle');

    Route::get('word-pairs', [WordPairController::class, 'index'])->name('game.word-pairs.index');
    Route::post('word-pairs', [WordPairController::class, 'store'])->name('game.word-pairs.store');
    Route::delete('word-pairs/{wordPair}', [WordPairController::class, 'destroy'])->name('game.word-pairs.destroy');
    Route::post('puzzle-memory/selection', [WordPairController::class, 'selectForPuzzle'])->name('game.puzzle-memory.select');
    Route::get('puzzle-memory', [PuzzleController::class, 'memory'])->name('game.puzzle-memory');
});
