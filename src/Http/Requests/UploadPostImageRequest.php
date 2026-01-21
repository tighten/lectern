<?php

namespace Tightenco\Lectern\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Tightenco\Lectern\Models\Post;

class UploadPostImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $post instanceof Post && $this->user()->can('update', $post);
    }

    public function rules(): array
    {
        $maxSize = config('lectern.images.max_size', 2048);
        $allowedTypes = config('lectern.images.allowed_types', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        return [
            'image' => [
                'required',
                File::types($allowedTypes)->max($maxSize),
            ],
        ];
    }

    public function messages(): array
    {
        $maxSizeMB = config('lectern.images.max_size', 2048) / 1024;

        return [
            'image.max' => "The image must not be larger than {$maxSizeMB}MB.",
        ];
    }
}
