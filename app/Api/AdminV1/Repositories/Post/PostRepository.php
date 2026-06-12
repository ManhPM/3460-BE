<?php

namespace App\Api\AdminV1\Repositories\Post;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Post;
use Illuminate\Support\Str;

class PostRepository extends EloquentRepository implements PostRepositoryInterface
{
    public function getModel(): string
    {
        return Post::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('categories');

        // Column-specific filters
        if (request()->has('title') && !empty(request('title'))) {
            $query->where('title', 'like', "%" . request('title') . "%");
        }

        if (request()->has('slug') && !empty(request('slug'))) {
            $query->where('slug', 'like', "%" . request('slug') . "%");
        }

        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        if (request()->has('is_featured') && request('is_featured') !== '' && request('is_featured') !== null) {
            $query->where('is_featured', request('is_featured'));
        }

        // Filter by category
        if (request()->has('category_id') && request('category_id') !== '' && request('category_id') !== null) {
            $query->whereHas('categories', function ($q) {
                $q->where('posts_categories.id', request('category_id'));
            });
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data)
    {
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $post = parent::create($data);

        if (!empty($categoryIds)) {
            $post->categories()->sync($categoryIds);
        }

        return $post->load('categories');
    }

    public function update($id, array $data)
    {
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['category_ids']);

        $post = parent::update($id, $data);

        if ($categoryIds !== null) {
            $post->categories()->sync($categoryIds);
        }

        return $post->load('categories');
    }
}
