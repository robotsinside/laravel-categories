<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RobotsInside\Categories\Models\Categorisable;
use RobotsInside\Categories\Models\Category;

class CategoriesModelUsageTest extends TestCase
{
    protected $lesson;

    public function setUp(): void
    {
        parent::setUp();

        foreach (['Science', 'Technology', 'Engineering', 'Mathematics'] as $category) {
            CategoryStub::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'count' => 0
            ]);
        }

        $this->lesson = LessonStub::create([
            'title' => 'Lesson 1'
        ]);
    }

    /** @test */
    public function can_categorise_a_lesson()
    {
        $this->lesson->categorise(CategoryStub::where('slug', 'science')->first());

        $this->assertCount(1, $this->lesson->categories);

        $this->assertContains('science', $this->lesson->categories->pluck('slug'));
    }

    /** @test */
    public function can_category_lesson_with_a_collection_of_categories()
    {
        $categoryArray = ['science', 'technology', 'engineering'];
        $categories = CategoryStub::whereIn('slug', $categoryArray)->get();

        $this->lesson->categorise($categories);

        $this->assertCount(3, $this->lesson->categories);

        foreach ($categoryArray as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }
    }

    /** @test */
    public function can_uncategorise_lesson_categories()
    {
        $categoryArray = ['science', 'technology', 'engineering'];
        $categories = CategoryStub::whereIn('slug', $categoryArray)->get();

        $this->lesson->categorise($categories);

        $this->lesson->uncategorise($categories->first());

        $this->assertCount(2, $this->lesson->categories);

        array_pop($categoryArray);

        foreach ($categoryArray as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }

        $this->assertNotContains('engineering', $this->lesson->categories->pluck('slug'));
    }

    /** @test */
    public function can_uncategorise_all_lesson_categories()
    {
        $categoryArray = ['science', 'technology', 'engineering'];
        $categories = CategoryStub::whereIn('slug', $categoryArray)->get();

        $this->lesson->categorise($categories);

        $this->lesson->uncategorise();

        $this->lesson->load('categories');

        $this->assertCount(0, $this->lesson->categories);
    }

    /** @test */
    public function can_recategorise_lesson_categories()
    {
        $categoryArray = ['science', 'technology', 'engineering'];
        $recategoriseArray = ['science', 'mathematics'];

        $categories = CategoryStub::whereIn('slug', $categoryArray)->get();
        $recategorised = CategoryStub::whereIn('slug', $recategoriseArray)->get();

        $this->lesson->categorise($categories);

        $this->lesson->recategorise($recategorised);

        $this->lesson->load('categories');

        $this->assertCount(2, $this->lesson->categories);

        foreach ($recategoriseArray as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }
    }

    /** @test */
    public function non_models_are_filtered_when_using_collection()
    {
        $categoryArray = ['science', 'technology', 'engineering'];

        $categories = CategoryStub::whereIn('slug', $categoryArray)->get();

        $categories->push('something weird here'); // ¯\_(ツ)_/¯

        $this->lesson->categorise($categories);

        $this->assertCount(3, $this->lesson->categories);
    }

    /** @test */
    public function can_create_a_category_using_the_resolve_function()
    {
        $category = (new Category())->setConnection('testbench')->resolve('My category');

        $this->assertInstanceOf(Category::class, $category);
    }

    /** @test */
    public function cannot_create_duplicate_categories_using_the_resolve_function()
    {
        $category = (new Category())->setConnection('testbench')->resolve('My category');

        $this->assertInstanceOf(Category::class, $category);

        $category = (new Category())->setConnection('testbench')->resolve('My category');

        $this->assertInstanceOf(Category::class, $category);

        $saved = Category::where('slug', 'my-category')->get();

        $this->assertCount(1, $saved);
    }

    /** @test */
    public function can_resolve_a_collection_of_categories()
    {
        $resolvable = collect(['Category 1', 'Category 2']);

        $categories = (new Category())
            ->setConnection('testbench')
            ->resolveAll($resolvable);

        $this->assertCount(2, $categories);

        $this->assertInstanceOf(Collection::class, $categories);
    }

    /** @test */
    public function can_resolve_an_array_of_categories()
    {
        // Passing an array
        $categories = (new Category())
            ->setConnection('testbench')
            ->resolveAll(['Category 1', 'Category 2']);

        $this->assertCount(2, $categories);

        $this->assertInstanceOf(Collection::class, $categories);
    }

    /** @test */
    public function can_resolve_string_of_categories()
    {
        $categories = (new Category())
            ->setConnection('testbench')
            ->resolveAll('Category 3');

        $this->assertCount(1, $categories);

        $this->assertInstanceOf(Collection::class, $categories);
    }

    /** @test */
    public function cannot_duplicate_resolve_all_categories()
    {
        $collectionA = (new Category())
            ->setConnection('testbench')
            ->resolveAll(['Category 1', 'Category 2']);

        $collectionB = (new Category())
            ->setConnection('testbench')
            ->resolveAll(['Category 1', 'Category 2']);

        // Including STEM categories.
        $this->assertCount(6, Category::get());
    }

    /** @test */
    public function can_filter_categorisables()
    {
        $this->lesson->categorise(CategoryStub::where('slug', 'science')->first());

        $categorisables = Categorisable::type('LessonStub')->get();

        $this->assertInstanceOf(Categorisable::class, $categorisables->first());

        $this->assertCount(1, $categorisables);

        $this->assertInstanceOf(LessonStub::class, $categorisables->first()->categorisable);
    }
}
