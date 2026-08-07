<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'purpose' => 'required|string|max:1000',
        ];

        if ($this->user() && $this->user()->isAdmin()) {
            $rules['status'] = 'sometimes|in:Pending,Approved,Rejected';
            $rules['user_id'] = 'sometimes|exists:users,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Please select a room.',
            'room_id.exists' => 'The selected room is invalid.',
            'reservation_date.required' => 'Reservation date is required.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',
            'purpose.required' => 'Please provide the purpose of reservation.',
        ];
    }
}
