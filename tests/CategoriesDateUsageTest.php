<?php

use Illuminate\Support\Str;
use RobotsInside\Categories\Models\Categorisable;

class CategoriesDateUsageTest extends TestCase
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
    public function can_filter_categorisables_by_days()
    {
        $this->lesson->categorise(CategoryStub::whereIn('slug', ['science', 'technology'])->get());

        // Resolve the technology category created_at 7 days 1 hour ago.
        $categorisable = Categorisable::latest()->first();
        $categorisable->created_at = now()->subDays('7')->subHours('1');
        $categorisable->save();

        $newCategories = $categorisable->categorisedWithin('7 days')->get();

        $this->assertCount(1, $newCategories);

        $this->assertNotContains('technology', $newCategories->pluck('slug'));
    }

    /** @test */
    public function can_filter_categorisables_by_months()
    {
        $this->lesson->categorise(CategoryStub::whereIn('slug', ['science', 'technology'])->get());

        // Resolve the technology category created_at 2 months 1 hour ago.
        $categorisable = Categorisable::latest()->first();
        $categorisable->created_at = now()->subMonths(2)->subHours(1);
        $categorisable->save();

        $newCategories = $categorisable->categorisedWithin('2 months')->get();

        $this->assertCount(1, $newCategories);

        $this->assertNotContains('technology', $newCategories->pluck('slug'));
    }

    /** @test */
    public function can_filter_categorisables_by_years()
    {
        $this->lesson->categorise(CategoryStub::whereIn('slug', ['science', 'technology'])->get());

        // Resolve the technology category created_at 1 year 1 hour ago.
        $categorisable = Categorisable::latest()->first();
        $categorisable->created_at = now()->subYears(1)->subHours(1);
        $categorisable->save();

        $newCategories = $categorisable->categorisedWithin('1 year')->get();

        $this->assertCount(1, $newCategories);

        $this->assertNotContains('technology', $newCategories->pluck('slug'));
    }

    /** @test */
    public function filtering_expects_valid_string()
    {
        $this->lesson->categorise(CategoryStub::where('slug', 'science')->first());

        // Resolve the technology category created_at 7 days 1 hour ago.
        $categorisable = Categorisable::latest()->first();
        $categorisable->created_at = now()->subDays(7)->subHours(1);
        $categorisable->save();

        $this->expectException(InvalidArgumentException::class);

        $categorisable->categorisedWithin('1 yearasdf')->get();
    }
}
