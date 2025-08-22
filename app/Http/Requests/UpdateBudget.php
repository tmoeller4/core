<?php

namespace Biigle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudget extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:2|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'amount' => 'sometimes|numeric|min:0|max:999999999999.99',
            'spent' => 'sometimes|numeric|min:0|max:999999999999.99',
            'currency' => 'sometimes|string|size:3',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'active' => 'sometimes|boolean',
        ];
    }
}