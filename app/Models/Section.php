<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Post\{PostStatus, PostType};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PriorityStatus;

class Section extends Model
{
    use HasFactory;

    protected $table = 'sections';

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


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'section_categories', 'section_id', 'category_id')->orderBy('position', 'asc');
    }
}
