<?php

namespace App\Supports\Eloquent;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootSluggable()
    {
        static::saving(function ($entity) {
            $entity->updateSlug();
        });
    }

    /**
     * Create or update the slug.
     *
     * @return void
     */
    public function updateSlug()
    {
        $columnSlug = $this->columnSlug ?? 'name';

        // Check if the slug should be updated
        if ($this->isDirty($columnSlug) || !$this->slug) {
            $slug = Str::slug($this->$columnSlug);
            $allSlugs = $this->getRelatedSlugs($slug, $this->id);

            // Ensure unique slug
            if (!$allSlugs->contains('slug', $slug)) {
                $this->slug = $slug;
            } else {
                $this->slug = $this->generateUniqueSlug($slug, $allSlugs);
            }
        }
    }

    /**
     * Generate a unique slug by appending a counter.
     *
     * @param string $baseSlug
     * @param \Illuminate\Support\Collection $allSlugs
     * @return string
     */
    protected function generateUniqueSlug($baseSlug, $allSlugs)
    {
        $i = 1;
        while (true) {
            $newSlug = $baseSlug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
            $i++;
        }
    }

    /**
     * Get related slugs.
     *
     * @param string $slug
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    protected function getRelatedSlugs($slug, $id = 0)
    {
        return self::select('slug')
            ->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();
    }
}
