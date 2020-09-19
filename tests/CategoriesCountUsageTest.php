<?php

class CategoriesCountUsageTest extends TestCase
{
    protected $lesson;

    public function setUp(): void
    {
        parent::setUp();

        $this->lesson = LessonStub::create([
            'title' => 'Lesson count'
        ]);
    }

    /** @test */
    public function category_count_is_incremented_when_categorised()
    {
        $category = CategoryStub::create([
            'name' => 'News',
            'slug' => 'slug',
            'count' => 0,
        ]);

        $this->lesson->categorise($category);

        $category = $category->fresh();

        $this->assertEquals(1, $category->count);
    }

    /** @test */
    public function category_count_is_decremented_when_uncategorised()
    {
        $category = CategoryStub::create([
            'name' => 'Science',
            'slug' => 'slug',
            'count' => 20
        ]);

        $this->lesson->categorise($category);
        $this->lesson->uncategorise($category);

        $category = $category->fresh();

        $this->assertEquals(20, $category->count);
    }

    /** @test */
    public function category_count_does_not_go_below_zero()
    {
        $category = CategoryStub::create([
            'name' => 'Science',
            'slug' => 'slug',
            'count' => 0
        ]);

        $this->lesson->uncategorise($category);

        $category = $category->fresh();

        $this->assertEquals(0, $category->count);
    }

    /** @test */
    public function category_count_is_not_incremented_if_already_exists()
    {
        $category = CategoryStub::create([
            'name' => 'Science',
            'slug' => 'slug',
            'count' => 0
        ]);

        $this->lesson->categorise($category);
        $this->lesson->categorise($category);
        $this->lesson->categorise($category);

        $category = $category->fresh();

        $this->assertEquals(1, $category->count);
    }
}
