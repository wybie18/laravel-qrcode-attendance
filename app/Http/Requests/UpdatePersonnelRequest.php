<?php

namespace App\Http\Requests;

use App\Models\Personnel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonnelRequest extends FormRequest
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
        $personnel = $this->route('personnel');
        $personnelId = $personnel instanceof Personnel ? $personnel->id : null;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('personnels', 'email')->ignore($personnelId)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'office_id' => ['required', 'integer', Rule::exists('offices', 'id')],
            'position_id' => ['required', 'integer', Rule::exists('positions', 'id')],
        ];
    }
}
