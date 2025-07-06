<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Form::all();
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

        return response()->json($form, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        return $form;
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

        return response()->json($form);
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
