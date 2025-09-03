<?php

namespace App\Http\Requests\Api\Project;

use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role->isAdmin() || $this->route('task') === null || $this->user()->id === $this->route('task')->assigned_to_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(TaskStatusEnum::values())],
            'assigned_to_id' => ['required', 'exists:users,id'],
        ];
    }
}
