<?php

namespace App\Admin\Http\Requests\Post;

use App\Admin\Http\Requests\BaseRequest;
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
            'id.required' => 'ID bài viết là bắt buộc.',
            'id.exists' => 'Bài viết không tồn tại.',
            'categories_id.*.exists' => 'Danh mục không hợp lệ.',
            'title.required' => 'Tiêu đề bài viết là bắt buộc.',
            'title.string' => 'Tiêu đề bài viết phải là chuỗi.',
            'slug.required' => 'Slug là bắt buộc.',
            'meta_title.required' => 'Meta title là bắt buộc.',
            'image.required' => 'Ảnh bài viết là bắt buộc.',
            'status.required' => 'Trạng thái bài viết là bắt buộc.',
            'is_featured.enum' => 'Giá trị is_featured không hợp lệ.',
        ];
    }
}
