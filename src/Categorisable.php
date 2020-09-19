<?php

namespace RobotsInside\Categories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RobotsInside\Categories\Models\Category;
use RobotsInside\Categories\Scopes\CategorisableScopes;

trait Categorisable
{
    use CategorisableScopes;

    /**
     * Get the categorisable models.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorisable')->withTimestamps();
    }

    /**
     * Add the specified categories.
     *
     * @param integer|array|RobotsInside\Categories\Models\Category $categories
     * @return void
     */
    public function categorise($categories)
    {
        $this->addCategories($this->getNormalizedCategories($categories));
    }

    /**
     * Removes all categories if no argument is passed in, otherwise remove the specified categories.
     *
     * @param integer|array|RobotsInside\Categories\Models\Category $categories
     * @return void
     */
    public function uncategorise($categories = null)
    {
        if ($categories === null) {
            $this->removeAllCategories();
        } else {
            $this->removeCategories($this->getNormalizedCategories($categories));
        }
    }

    /**
     * Recategorise a model with the specified categories.
     *
     * @param integer|array|RobotsInside\Categories\Models\Category $categories
     * @return void
     */
    public function recategorise($categories)
    {
        $this->removeAllCategories();

        $this->categorise($categories);
    }

    /**
     * Remove all categories based on the categories relationship.
     *
     * @return void
     */
    private function removeAllCategories()
    {
        $this->removeCategories($this->categories);
    }

    /**
     * Remove categories and decrement the category count accordingly.
     *
     * @param Collection $categories
     * @return void
     */
    private function removeCategories(Collection $categories)
    {
        $this->categories()->detach($categories);

        $categories->each(function ($category) {
            $category->count <= 0 ?: $category->decrement('count');
        });
    }

    /**
     * Add categories and increment the category count.
     *
     * @param Collection $categories
     * @return void
     */
    private function addCategories(Collection $categories)
    {
        $sync = $this->categories()->syncWithoutDetaching($categories->pluck('id'));

        foreach (Arr::get($sync, 'attached') as $categoryId) {
            $category = $categories->where('id', $categoryId)->first()->increment('count');
        }
    }

    /**
     * Get the normalised categories.
     *
     * @param mixed $categories
     * @return Collection
     */
    private function getNormalizedCategories($categories)
    {
        if (is_array($categories)) {
            return $this->getCategoryModels($categories);
        }

        if ($categories instanceof Model) {
            return $this->getCategoryModels([$categories->slug]);
        }

        return $this->filterCategoriesCollection($categories);
    }

    /**
     * A fallback to resolve only instances of Category.
     *
     * @param Collection $categories
     * @return Collection
     */
    private function filterCategoriesCollection(Collection $categories)
    {
        return $categories->filter(function ($category) {
            return $category instanceof Model;
        });
    }

    /**
     * Perform the DB query.
     *
     * @param array $categories
     * @return Collection
     */
    private function getCategoryModels(array $categories)
    {
        return Category::whereIn('slug', $this->normalizeCategoryNames($categories))->get();
    }

    /**
     * Normalise values to slugified strings.
     *
     * @param array $categories
     * @return array
     */
    private function normalizeCategoryNames($categories)
    {
        return array_map(function ($category) {
            return Str::slug($category);
        }, $categories);
    }
}
