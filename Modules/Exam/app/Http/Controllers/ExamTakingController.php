<?php

namespace Modules\Exam\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exam\Exam;
use Modules\Exam\ExamSession;
use Modules\Exam\Question;
use Modules\Exam\UserAnswer;

class ExamTakingController extends Controller
{
    /**
     * Show the exam taking interface.
     */
    public function show(Exam $exam)
    {
        // Check if user has an active session
        $session = ExamSession::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->where('is_completed', false)
            ->first();

        // If no session, create one
        if (!$session) {
            $session = ExamSession::create([
                'exam_id' => $exam->id,
                'user_id' => auth()->id(),
                'started_at' => now(),
                'time_limit_minutes' => 60, // Default 60 minutes
            ]);
        }

        // Load the first unanswered question
        $answeredQuestionIds = $session->userAnswers()->pluck('question_id')->toArray();
        $currentQuestion = $exam->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->orderBy('order')
            ->first();

        // If no more questions, finish the exam
        if (!$currentQuestion) {
            return $this->finish($exam);
        }

        $totalQuestions = $exam->questions()->count();
        $answeredQuestions = count($answeredQuestionIds);

        return view('exam::exam-taking.show', compact(
            'exam',
            'session',
            'currentQuestion',
            'totalQuestions',
            'answeredQuestions'
        ));
    }

    /**
     * Submit an answer to a question.
     */
    public function submitAnswer(Request $request, Exam $exam, Question $question)
    {
        $request->validate([
            'answer' => 'required|in:A,B,C,D,E',
        ]);

        // Get or create session
        $session = ExamSession::firstOrCreate([
            'exam_id' => $exam->id,
            'user_id' => auth()->id(),
            'is_completed' => false,
        ], [
            'started_at' => now(),
            'time_limit_minutes' => 60,
        ]);

        // Save the answer
        $userAnswer = UserAnswer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id' => $question->id,
            ],
            [
                'selected_answer' => $request->answer,
                'is_correct' => ($request->answer === $question->correct_answer),
            ]
        );

        // Check if there are more questions
        $answeredQuestionIds = $session->userAnswers()->pluck('question_id')->toArray();
        $nextQuestion = $exam->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->orderBy('order')
            ->first();

        if ($nextQuestion) {
            return response()->json([
                'status' => 'success',
                'next_question_url' => route('exam.exam-taking.show', [$exam->id]),
                'message' => 'Cevap başarıyla kaydedildi.'
            ]);
        } else {
            // No more questions, finish the exam
            return response()->json([
                'status' => 'completed',
                'redirect_url' => route('exam.exam-taking.finish', [$exam->id]),
                'message' => 'Sınav tamamlandı!'
            ]);
        }
    }

    /**
     * Finish the exam and show results.
     */
    public function finish(Exam $exam)
    {
        $session = ExamSession::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->where('is_completed', false)
            ->firstOrFail();

        // Mark session as completed
        $session->update([
            'finished_at' => now(),
            'is_completed' => true,
        ]);

        // Calculate results
        $totalQuestions = $exam->questions()->count();
        $answeredQuestions = $session->getAnsweredQuestionsCount();
        $correctAnswers = $session->getCorrectAnswersCount();
        $scorePercentage = $session->getScorePercentage();

        return view('exam::exam-taking.finish', compact(
            'exam',
            'session',
            'totalQuestions',
            'answeredQuestions',
            'correctAnswers',
            'scorePercentage'
        ));
    }

    /**
     * Get the next question via AJAX.
     */
    public function getNextQuestion(Exam $exam)
    {
        $session = ExamSession::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->where('is_completed', false)
            ->firstOrFail();

        $answeredQuestionIds = $session->userAnswers()->pluck('question_id')->toArray();
        $nextQuestion = $exam->questions()
            ->whereNotIn('id', $answeredQuestionIds)
            ->orderBy('order')
            ->first();

        if ($nextQuestion) {
            return response()->json([
                'status' => 'success',
                'question' => [
                    'id' => $nextQuestion->id,
                    'text' => $nextQuestion->question_text,
                    'image_path' => $nextQuestion->image_path,
                    'options' => $nextQuestion->getOptionsArray(),
                    'order' => $nextQuestion->order,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'completed',
                'message' => 'Başka soru kalmadı.'
            ]);
        }
    }
}
