<?php

namespace App\Api\AdminV1\Http\Requests\Post;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Post\PostStatus;
use Illuminate\Validation\Rules\Enum;

class PostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'title' => ['required', 'string'],
            'avatar' => ['required'],
            'is_featured' => ['nullable'],
            'status' => ['required', new Enum(PostStatus::class)],
            'excerpt' => ['nullable'],
            'content' => ['nullable']
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Post,id'],
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'title' => ['required', 'string'],
            'slug' => ['required'],
            'meta_title' => ['required'],
            'avatar' => ['required'],
            'is_featured' => ['nullable'],
            'status' => ['required', new Enum(PostStatus::class)],
            'excerpt' => ['nullable'],
            'content' => ['nullable']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('please_enter_post_id'),
            'id.exists' => __('post_id_not_exists'),
            'categories_id.*.exists' => __('post_categories_invalid'),
            'title.required' => __('please_enter_post_title'),
            'title.string' => __('post_title_string'),
            'slug.required' => __('please_enter_slug'),
            'meta_title.required' => __('please_enter_meta_title'),
            'image.required' => __('please_enter_post_image'),
            'status.required' => __('please_choose_post_status'),
            'is_featured.enum' => __('post.is_featured_invalid'),
        ];
    }
}

