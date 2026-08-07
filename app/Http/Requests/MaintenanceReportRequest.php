<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'asset_id' => 'nullable|exists:assets,id',
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'required|string|max:1000',
        ];

        if ($this->user() && ($this->user()->isAdmin() || $this->user()->isStaff())) {
            $rules['status'] = 'sometimes|in:Pending,Completed';
            $rules['user_id'] = 'sometimes|exists:users,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'asset_id.required' => 'Please select an asset.',
            'asset_id.exists' => 'The selected asset is invalid.',
            'description.required' => 'Please provide a description of the issue or maintenance request.',
        ];
    }
}
