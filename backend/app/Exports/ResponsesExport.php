<?php

namespace App\Exports;

use App\Models\Response;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResponsesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $formId;

    public function __construct($formId = null)
    {
        $this->formId = $formId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Response::with(['form', 'answers.question']);

        if ($this->formId) {
            $query->where('form_id', $this->formId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Response ID',
            'Form Name',
            'Submitted',
            'Session Token',
            'Created At',
            'Updated At',
            'Answers', // This will contain JSON of all answers
        ];
    }

    public function map($response): array
    {
        $answers = $response->answers->mapWithKeys(function ($answer) {
            return [$answer->question->question_text => $answer->answer];
        })->toArray();

        return [
            $response->id,
            $response->form->name,
            $response->submitted ? 'Yes' : 'No',
            $response->session_token,
            $response->created_at,
            $response->updated_at,
            json_encode($answers),
        ];
    }
}
