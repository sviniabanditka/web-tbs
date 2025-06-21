<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\GameActionType;
use App\Enums\BuildingType;
use App\Enums\UnitType;
use App\Models\Game;

class ExecuteGameActionRequest extends FormRequest
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
        /** @var Game $game */
        $game = $this->route('game');

        $rules = [
            'action_type' => ['required', Rule::enum(GameActionType::class)],
            'source_hex_q' => 'sometimes|required|integer',
            'source_hex_r' => 'sometimes|required|integer',
            'target_hex_q' => 'sometimes|required|integer',
            'target_hex_r' => 'sometimes|required|integer',
            'unit_id' => 'sometimes|required|integer|exists:units,id',
            'building_id' => 'sometimes|required|integer|exists:buildings,id',
            'action_data' => 'sometimes|array',
        ];

        $actionType = $this->input('action_type');

        if ($actionType) {
            switch (GameActionType::tryFrom($actionType)) {
                case GameActionType::BUILD:
                    $rules['action_data.building_type'] = ['required', Rule::enum(BuildingType::class)];
                    $rules['target_hex_q'] = 'required';
                    $rules['target_hex_r'] = 'required';
                    break;
                case GameActionType::RECRUIT:
                    $rules['action_data.unit_type'] = ['required', Rule::enum(UnitType::class)];
                    $rules['building_id'] = 'required|exists:buildings,id';
                    break;
                case GameActionType::MOVE:
                    $rules['unit_id'] = 'required|exists:units,id';
                    $rules['target_hex_q'] = 'required';
                    $rules['target_hex_r'] = 'required';
                    break;
                case GameActionType::ATTACK:
                    $rules['unit_id'] = 'required|exists:units,id';
                    $rules['target_hex_q'] = 'required';
                    $rules['target_hex_r'] = 'required';
                    break;
                case GameActionType::UPGRADE:
                    $rules['building_id'] = 'required|exists:buildings,id';
                    break;
            }
        }

        return $rules;
    }
}
