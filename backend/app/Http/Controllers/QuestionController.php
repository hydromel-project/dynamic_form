<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Form $form)
    {
        return $form->questions;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Form $form)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string',
            'options' => 'nullable|json',
            'conditional_rules' => 'nullable|json',
            'required' => 'boolean',
            'order' => 'integer'
        ]);

        $question = $form->questions()->create($validated);

        return response()->json($question, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form, Question $question)
    {
        return $question;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'options' => 'nullable|json',
            'conditional_rules' => 'nullable|json',
            'required' => 'boolean',
            'order' => 'integer'
        ]);

        $question->update($validated);

        return response()->json($question);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form, Question $question)
    {
        $question->delete();

        return response()->json(null, 204);
    }
}
