<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('asset') ? $this->route('asset')->id : null;

        return [
            'asset_code' => 'required|string|max:50|unique:assets,asset_code,' . $assetId,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'condition' => 'required|string|max:100',
            'location' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'asset_code.required' => 'The asset code is required.',
            'asset_code.unique' => 'This asset code is already in use.',
            'name.required' => 'The asset name is required.',
            'category.required' => 'The category field is required.',
            'condition.required' => 'The condition field is required.',
            'location.required' => 'The location field is required.',
        ];
    }
}
