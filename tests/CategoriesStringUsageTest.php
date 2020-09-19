<?php

use Illuminate\Support\Str;

class CategoriesStringUsageTest extends TestCase
{
    protected $lesson;

    public function setUp() :void
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
        $this->lesson->categorise(['science', 'technology']);

        $this->assertCount(2, $this->lesson->categories);

        foreach (['Science', 'Technology'] as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('name'));
        }
    }

    /** @test */
    public function can_uncategorise_a_lesson()
    {
        $this->lesson->categorise(['science', 'technology', 'engineering']);
        $this->lesson->uncategorise(['science']);

        $this->assertCount(2, $this->lesson->categories);

        foreach (['Technology', 'Engineering'] as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('name'));
        }
    }

    /** @test */
    public function can_uncategorise_all_lesson_categories()
    {
        $this->lesson->categorise(['science', 'technology', 'engineering']);
        $this->lesson->uncategorise();

        $this->lesson->load('categories');

        $this->assertCount(0, $this->lesson->categories);
        $this->assertEquals(0, $this->lesson->categories->count());
    }

    /** @test */
    public function can_recategorise_lesson_categories()
    {
        $this->lesson->categorise(['science', 'technology', 'engineering']);

        $categories = ['science', 'mathematics', 'engineering'];
        $this->lesson->recategorise($categories);

        $this->lesson->load('categories');

        $this->assertCount(3, $this->lesson->categories);

        foreach ($categories as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }
    }

    /** @test */
    public function non_existing_categories_are_ignored_on_categorising()
    {
        $this->lesson->categorise(['science', 'technology', 'thermodynamics']);

        $this->assertCount(2, $this->lesson->categories);

        foreach (['science', 'technology'] as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }
    }

    /** @test */
    public function inconstistent_category_cases_are_normalized()
    {
        $this->lesson->categorise(['SciEnce', 'TecHnoLogy', 'enginEEring']);

        $this->assertCount(3, $this->lesson->categories);

        foreach (['science', 'technology', 'engineering'] as $category) {
            $this->assertContains($category, $this->lesson->categories->pluck('slug'));
        }
    }
}
