<?php

use Illuminate\Support\Facades\Route;
use Modules\Sinav\Http\Controllers\Admin\LessonController as AdminLessonController;
use Modules\Sinav\Http\Controllers\Admin\QuestionImportController as AdminQuestionImportController;
use Modules\Sinav\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use Modules\Sinav\Http\Controllers\Admin\TestController as AdminTestController;
use Modules\Sinav\Http\Controllers\Admin\TopicController as AdminTopicController;
use Modules\Sinav\Http\Controllers\Public\AttemptController;
use Modules\Sinav\Http\Controllers\Public\LessonController;
use Modules\Sinav\Http\Controllers\Public\TestController;
use Modules\Sinav\Http\Controllers\QuestionMediaController;

Route::prefix('sinav')->name('sinav.')->group(function () {
    Route::get('/', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/dersler/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('/media/{path}', [QuestionMediaController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');

    Route::get('/testler/{test}', [TestController::class, 'show'])->name('tests.show');
    Route::post('/testler/{test}/submit', [TestController::class, 'submit'])->name('tests.submit');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/cozumlerim', [AttemptController::class, 'index'])->name('attempts.index');
        Route::get('/cozumlerim/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
    });
});

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])
    ->prefix('admin/sinav')
    ->name('sinav.admin.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('sinav.admin.lessons.index'))->name('home');

        Route::resource('lessons', AdminLessonController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('lessons/{lesson}/topics', [AdminTopicController::class, 'index'])->name('lessons.topics.index');
        Route::post('lessons/{lesson}/topics', [AdminTopicController::class, 'store'])->name('lessons.topics.store');
        Route::put('topics/{topic}', [AdminTopicController::class, 'update'])->name('topics.update');
        Route::delete('topics/{topic}', [AdminTopicController::class, 'destroy'])->name('topics.destroy');

        Route::get('topics/{topic}/tests', [AdminTestController::class, 'index'])->name('topics.tests.index');
        Route::post('topics/{topic}/tests', [AdminTestController::class, 'store'])->name('topics.tests.store');
        Route::put('tests/{test}', [AdminTestController::class, 'update'])->name('tests.update');
        Route::delete('tests/{test}', [AdminTestController::class, 'destroy'])->name('tests.destroy');

        Route::get('questions/import', [AdminQuestionImportController::class, 'create'])->name('questions.import.create');
        Route::get('questions/import/template', [AdminQuestionImportController::class, 'template'])->name('questions.import.template');
        Route::get('questions/import/json', [AdminQuestionImportController::class, 'createJson'])->name('questions.import.json.create');
        Route::get('questions/import/json/template', [AdminQuestionImportController::class, 'jsonTemplate'])->name('questions.import.json.template');
        Route::get('questions/import/topics', [AdminQuestionImportController::class, 'topics'])->name('questions.import.topics');
        Route::get('questions/import/tests', [AdminQuestionImportController::class, 'tests'])->name('questions.import.tests');
        Route::post('questions/import', [AdminQuestionImportController::class, 'store'])->name('questions.import.store');
        Route::post('questions/import/json', [AdminQuestionImportController::class, 'storeJson'])->name('questions.import.json.store');

        Route::get('tests/{test}/questions', [AdminQuestionController::class, 'index'])->name('tests.questions.index');
        Route::post('tests/{test}/questions', [AdminQuestionController::class, 'store'])->name('tests.questions.store');
        Route::put('questions/{question}', [AdminQuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');
    });
