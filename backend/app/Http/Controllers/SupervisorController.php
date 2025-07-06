<?php

namespace App\Http\Controllers;

use App\Models\Response;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResponsesExport;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $query = Response::query()->with(['form', 'answers']);

        if ($request->has('form_id')) {
            $query->where('form_id', $request->input('form_id'));
        }

        if ($request->has('submitted')) {
            $query->where('submitted', $request->boolean('submitted'));
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        return $query->paginate(15);
    }

    public function show(string $id)
    {
        return Response::with(['form', 'answers'])->findOrFail($id);
    }

    public function export(Request $request)
    {
        $formId = $request->input('form_id');
        return Excel::download(new ResponsesExport($formId), 'responses.xlsx');
    }
}
