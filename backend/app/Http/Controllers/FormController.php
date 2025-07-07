<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Http\Resources\FormResource; // Import FormResource

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FormResource::collection(Form::with('questions')->get()); // Eager load questions
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'json_schema' => 'required|json'
        ]);

        $form = Form::create($validated);

        return new FormResource($form->load('questions')); // Eager load questions after creation
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        return new FormResource($form->load('questions')); // Eager load questions
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'json_schema' => 'sometimes|required|json'
        ]);

        $form->update($validated);

        return new FormResource($form->load('questions')); // Eager load questions after update
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        $form->delete();

        return response()->json(null, 204);
    }
}
