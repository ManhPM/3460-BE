<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Post\{PostStatus, PostType};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PriorityStatus;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $guarded = [];

    protected $columnSlug = 'title';

    protected static function boot()
    {
        parent::boot();
    }

    protected $casts = [
        'status' => PostStatus::class,
        'priority' => PriorityStatus::class,
    ];


    public function isPublished(): bool
    {
        return $this->status == PostStatus::Published;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'posts_posts_categories', 'post_id', 'category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::Published);
    }

    public function scopeHasCategories($query, array $categoriesId)
    {
        return $query->whereHas('categories', function ($query) use ($categoriesId) {
            $query->whereIn('id', $categoriesId);
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
