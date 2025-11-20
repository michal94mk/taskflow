<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the project belongs to the authenticated user
        if ($this->has('project_id')) {
            $project = Project::find($this->input('project_id'));
            return $project && $project->user_id === $this->user()->id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'project_id' => 'required|exists:projects,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'due_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'task_status_id' => 'status',
            'task_priority_id' => 'priority',
            'due_date' => 'due date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
        ];
    }
}

