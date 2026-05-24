<?php

use Illuminate\Support\Facades\Route;
use Modules\Exam\Http\Controllers\ExamController;
use Modules\Exam\Http\Controllers\MenuController;
use Modules\Exam\Http\Controllers\QuestionController;
use Modules\Exam\Http\Controllers\ExamTakingController;

Route::middleware(['auth', 'verified', 'role:superadmin|admin', 'admin_mfa'])->group(function () {
    // Menu routes
    Route::resource('menus', MenuController::class)->names('exam.menus');
    Route::get('menus-tree', [MenuController::class, 'getMenuTree'])->name('exam.menus.tree');
    
    // Exam routes
    Route::resource('exams', ExamController::class)->names('exam.exams');
    
    // Question routes
    Route::resource('exams.questions', QuestionController::class)->names('exam.exams.questions');
    
    // Exam taking routes
    Route::get('exams/{exam}/take', [ExamTakingController::class, 'show'])->name('exam.exam-taking.show');
    Route::post('exams/{exam}/questions/{question}/submit', [ExamTakingController::class, 'submitAnswer'])->name('exam.exam-taking.submit-answer');
    Route::get('exams/{exam}/finish', [ExamTakingController::class, 'finish'])->name('exam.exam-taking.finish');
    Route::get('exams/{exam}/next-question', [ExamTakingController::class, 'getNextQuestion'])->name('exam.exam-taking.next-question');
});
