<?php

namespace App\Http\Requests;

use App\Enums\CareNeed;
use App\Enums\HealthCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareSeekerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $careNeedValues = array_column(CareNeed::cases(), 'value');
        $healthConditionValues = array_column(HealthCondition::cases(), 'value');

        return [
            'dob' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'preferred_giver_gender' => ['nullable', 'string'],
            'care_needs' => ['nullable', 'array'],
            'care_needs.*' => ['required', 'string', Rule::in($careNeedValues)],
            'health_conditions' => ['nullable', 'array'],
            'health_conditions.*' => ['required', 'string', Rule::in($healthConditionValues)],
        ];
    }
}
