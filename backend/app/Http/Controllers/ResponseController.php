<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ResponseController extends Controller
{
    public function start(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
        ]);

        $form = Form::findOrFail($validated['form_id']);

        $sessionToken = Str::random(60);

        $response = $form->responses()->create([
            'session_token' => $sessionToken,
            'submitted' => false,
        ]);

        return response()->json($response, 201);
    }

    public function show(string $session_token)
    {
        $response = Response::where('session_token', $session_token)->with('answers')->firstOrFail();

        return response()->json($response);
    }

    public function save(Request $request, string $session_token)
    {
        $response = Response::where('session_token', $session_token)->firstOrFail();

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required_without_all:answers.*.file|array',
            'answers.*.file' => 'nullable|file|max:10240', // Max 10MB
        ]);

        foreach ($validated['answers'] as $answerData) {
            $answer = $answerData['answer'] ?? null;

            if (isset($answerData['file'])) {
                $question = Question::find($answerData['question_id']);
                if ($question && $question->type === 'file') {
                    $path = $answerData['file']->store('responses');
                    $answer = ['file_path' => $path, 'original_name' => $answerData['file']->getClientOriginalName()];
                }
            }

            $response->answers()->updateOrCreate(
                ['question_id' => $answerData['question_id']],
                ['answer' => $answer]
            );
        }

        return response()->json($response->load('answers'));
    }

    public function submit(Request $request, string $session_token)
    {
        $response = Response::where('session_token', $session_token)->with('answers')->firstOrFail();
        $form = $response->form()->with('questions')->firstOrFail();

        $answers = $response->answers->keyBy('question_id');

        $visibleQuestions = collect();

        foreach ($form->questions->sortBy('order') as $question) {
            $conditionalRules = json_decode($question->conditional_rules, true);

            $isVisible = true;
            if ($conditionalRules && isset($conditionalRules['show_if'])) {
                $rule = $conditionalRules['show_if'];
                $targetQuestionId = $rule['question_id'];
                $operator = $rule['operator'];
                $value = $rule['value'];

                $targetAnswer = $answers->get($targetQuestionId);

                if ($targetAnswer) {
                    $targetAnswerValue = $targetAnswer->answer['value'] ?? null;

                    switch ($operator) {
                        case 'equals':
                            $isVisible = ($targetAnswerValue == $value);
                            break;
                        case 'not_equals':
                            $isVisible = ($targetAnswerValue != $value);
                            break;
                        // Add more operators as needed
                    }
                } else {
                    $isVisible = false; // If target question not answered, dependent question is not visible
                }
            }

            if ($isVisible) {
                $visibleQuestions->put($question->id, $question);

                // Validate required questions only if they are visible
                if ($question->required && !$answers->has($question->id)) {
                    return response()->json(['message' => 'Required question ' . $question->id . ' not answered.'], 422);
                }
            } else {
                // If a question is not visible but has an answer, it's an error
                if ($answers->has($question->id)) {
                    return response()->json(['message' => 'Conditional logic violation: Question ' . $question->id . ' should not have been answered.'], 422);
                }
            }
        }

        // Final check for any answers to non-visible questions (redundant with above, but good for clarity)
        foreach ($answers as $answer) {
            if (!$visibleQuestions->has($answer->question_id)) {
                return response()->json(['message' => 'Conditional logic violation: Question ' . $answer->question_id . ' should not have been answered.'], 422);
            }
        }

        $response->update(['submitted' => true]);

        return response()->json($response);
    }
}
