<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\MapGenerationAlgorithm;

class StoreGameRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'max_players' => 'required|integer|min:2|max:8',
            'turn_time_limit' => 'nullable|integer|min:30',
            'action_points_per_turn' => 'required|integer|min:1|max:10',
            'map_size' => 'required|integer|min:10|max:100',
            'map_generation_algorithm' => ['required', Rule::enum(MapGenerationAlgorithm::class)],
            'terrain_parameters' => 'nullable|array',
        ];
    }
}
