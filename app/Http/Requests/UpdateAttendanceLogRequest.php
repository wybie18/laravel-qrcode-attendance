<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'personnel_id' => ['required', 'integer', 'exists:personnels,id'],
            'log_date' => ['required', 'date'],
            'time_in' => ['nullable', 'date_format:H:i:s'],
            'time_out' => ['nullable', 'date_format:H:i:s', 'after:time_in'],
        ];
    }
}
