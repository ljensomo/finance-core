<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                 => ['required', 'integer', 'in:1,2'],
            'item'                 => ['required', 'string', 'max:255'],
            'wishlist_category_id' => ['nullable', 'integer', 'exists:wishlist_categories,id'],
            'category'             => ['nullable', 'integer'],
            'priority'             => ['required', 'integer', 'in:1,2,3'],
            'status'               => ['required', 'integer', 'in:1,2,3'],
            'estimated_cost'       => ['required', 'numeric', 'min:0'],
            'saved_amount'         => ['nullable', 'numeric', 'min:0'],
            'actual_cost'          => ['nullable', 'required_if:status,3', 'numeric', 'min:0'],
            'target_date'          => ['nullable', 'date'],
            'end_date'             => ['nullable', 'date', 'after_or_equal:target_date'],
            'completed_at'         => ['nullable', 'date'],
            'notes'                => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'               => 'Please choose Purchase or Travel.',
            'item.required'               => 'Please enter an item or destination.',
            'wishlist_category_id.exists' => 'Selected category does not exist.',
            'priority.required'           => 'Please select a priority level.',
            'status.required'             => 'Please select a status.',
            'estimated_cost.required'     => 'Please enter an estimated cost.',
            'actual_cost.required_if'     => 'Actual cost is required when marked as Done.',
            'end_date.after_or_equal'     => 'End date must be on or after the start date.',
        ];
    }
}