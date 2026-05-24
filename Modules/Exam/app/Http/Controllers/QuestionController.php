<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exam\Exam;
use Modules\Exam\Question;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->orderBy('order')->get();
        return view('exam::admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Exam $exam)
    {
        return view('exam::admin.questions.create', compact('exam'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text' => 'required|string',
            'image_path' => 'nullable|string|max:255',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'explanation' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $question = $exam->questions()->create([
            'question_text' => $request->question_text,
            'image_path' => $request->image_path,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => $request->correct_answer,
            'explanation' => $request->explanation,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('exam.exams.questions.index', $exam->id)
            ->with('success', 'Soru başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam, Question $question)
    {
        return view('exam::admin.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'image_path' => 'nullable|string|max:255',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'explanation' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'image_path' => $request->image_path,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'option_e' => $request->option_e,
            'correct_answer' => $request->correct_answer,
            'explanation' => $request->explanation,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('exam.exams.questions.index', $exam->id)
            ->with('success', 'Soru başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam, Question $question)
    {
        $question->delete();

        return redirect()->route('exam.exams.questions.index', $exam->id)
            ->with('success', 'Soru başarıyla silindi.');
    }
}
