<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['owner', 'teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => ['sometimes', 'string', 'max:33'],
            'path_trailer' => ['sometimes', 'string', 'max:255'],
            'about' => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'integer'],
            'thumbnail' => ['sometimes', 'image', 'mimes:png,jpg,jpeg'],
            'course_keypoints.*' => ['sometimes','string','max:255']
        ];
    }
}
