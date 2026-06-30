<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareGiverStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dob' => ['required', 'date'],
            'phone_number' => ['required', 'string', 'max:255'],
            'year_experience' => ['required', 'integer', 'min:0'],
            'fee' => ['required', 'numeric', 'min:0'],
            'bio' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['required', 'string', 'max:255'],
            'certifications' => ['nullable', 'array'],
            'certifications.*.name' => ['required', 'string', 'max:255'],
            'certifications.*.issuer' => ['nullable', 'string', 'max:255'],
            'certifications.*.issue_date' => ['nullable', 'date'],
            'certifications.*.expiration_date' => ['nullable', 'date', 'after:issue_date'],
            'schedules' => ['nullable', 'array'],
            'schedules.*.days' => ['required', 'array'],
            'schedules.*.days.*' => ['required', 'string'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}