<?php

namespace Biigle\Http\Requests;

use Biigle\Budget;
use Illuminate\Foundation\Http\FormRequest;

class StoreBudget extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Budget::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:2000',
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'currency' => 'nullable|string|size:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'active' => 'nullable|boolean',
        ];
    }
}