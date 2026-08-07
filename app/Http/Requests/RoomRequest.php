<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomId = $this->route('room') ? $this->route('room')->id : null;

        return [
            'room_code' => 'required|string|max:50|unique:rooms,room_code,' . $roomId,
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'room_code.required' => 'The room code is required.',
            'room_code.unique' => 'This room code is already in use.',
            'name.required' => 'The room name is required.',
            'capacity.required' => 'The room capacity is required.',
            'capacity.min' => 'Capacity must be at least 1 person.',
            'status.required' => 'The room status is required.',
        ];
    }
}
