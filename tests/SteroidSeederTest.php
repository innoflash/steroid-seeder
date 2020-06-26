<?php

namespace Innoflash\SteroidSeeder\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Innoflash\SteroidSeeder\SteroidSeeder;
use Innoflash\SteroidSeeder\SteroidSeederServiceProvider;
use Innoflash\SteroidSeeder\Tests\Models\Comment;
use Innoflash\SteroidSeeder\Tests\Models\Reaction;
use Innoflash\SteroidSeeder\Tests\Models\TestModel;
use Orchestra\Testbench\TestCase;

class SteroidSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/migrations');

        $this->withFactories(__DIR__.'/factories');

        $this->artisan('migrate', ['--database' => 'testing']);
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('steroid-seeder.factories-paths', [
            __DIR__.'/factories',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [SteroidSeederServiceProvider::class];
    }

    /**
     * @test
     */
    public function it_can_seed_one_model()
    {
        steroidFactory(TestModel::class)->create();

        $this->assertCount(1, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_seed_1000_entries()
    {
        steroidFactory(TestModel::class, 1000)->create();

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_create_with_chunking()
    {
        steroidFactory(TestModel::class, 1000)
            ->chunk(200)
            ->create();

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_skip_created_callbacks()
    {
        steroidFactory(TestModel::class, 1000)
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_seed_with_facade()
    {
        SteroidSeeder::factory(TestModel::class)->create();

        $this->assertCount(1, TestModel::all());

        SteroidSeeder::factory(TestModel::class, 999)->create();

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_create_many()
    {
        $models = steroidFactory(TestModel::class, 1000)->make();

        steroidFactory(TestModel::class)->createMany($models);

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_create_many_with_array()
    {
        $models = steroidFactory(TestModel::class, 1000)->make();

        steroidFactory(TestModel::class)->createMany($models->toArray());

        $this->assertCount(1000, TestModel::all());
    }

    /**
     * @test
     */
    public function it_can_seed_100k_entries()
    {
        steroidFactory(TestModel::class, 100000)
            ->chunk(10000)
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(100000, TestModel::cursor());
    }

    /**
     * @test
     */
    public function it_can_seed_100k_entries_in_less_than_65_seconds()
    {
        $startTime = microtime(true);

        steroidFactory(TestModel::class, 100000)
            ->chunk(10000)
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertLessThan(65, (microtime(true) - $startTime));
    }

    /**
     * @test
     */
    public function it_can_seed_relationship()
    {
        steroidFactory(TestModel::class)
            ->with(Comment::class)
            ->create();

        $this->assertCount(1, TestModel::all());
        $this->assertCount(1, Comment::all());
    }

    /**
     * @test
     */
    public function it_can_seed_multiple_relationships()
    {
        steroidFactory(TestModel::class)
            ->with(Comment::class)
            ->with(Reaction::class, 1, [], 'model_id')
            ->create();

        $this->assertCount(1, TestModel::all());
        $this->assertCount(1, Comment::all());
        $this->assertCount(1, Reaction::all());
    }

    /**
     * @test
     */
    public function it_can_seed_more_than_one_relationship()
    {
        steroidFactory(TestModel::class)
            ->with(Comment::class, 5)
            ->create();

        $this->assertCount(1, TestModel::all());
        $this->assertCount(5, Comment::all());
    }

    /**
     * @test
     */
    public function it_can_seed_more_than_one_entry_on_one_model()
    {
        steroidFactory(TestModel::class)
            ->with(Comment::class, 5)
            ->with(Reaction::class, 5, [], 'model_id')
            ->create();

        $this->assertCount(1, TestModel::all());
        $this->assertCount(5, Comment::all());
        $this->assertCount(5, Reaction::all());
    }

    /**
     * @test
     */
    public function it_can_seed_multiple_models_with_relationship()
    {
        steroidFactory(TestModel::class, 10)
            ->with(Comment::class)
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(10, TestModel::all());
        $this->assertCount(10, Comment::all());
    }

    /**
     * @test
     */
    public function it_can_seed_multiple_models_with_different_relationship()
    {
        steroidFactory(TestModel::class, 10)
            ->with(Comment::class)
            ->with(Reaction::class, 1, [], 'model_id')
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(10, TestModel::all());
        $this->assertCount(10, Comment::all());
        $this->assertCount(10, Reaction::all());
    }

    /**
     * @test
     */
    public function it_can_seed_multiple_models_with_multiple_relationships()
    {
        steroidFactory(TestModel::class, 10)
            ->with(Comment::class, 5)
            ->with(Reaction::class, 10, [], 'model_id')
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(10, TestModel::all());
        $this->assertCount(50, Comment::all());
        $this->assertCount(100, Reaction::all());
    }

    /**
     * @test
     */
    public function it_can_seed_a_model_with_relationship_defaults()
    {
        steroidFactory(TestModel::class)
            ->with(Comment::class, 1, [
                'user_name' => 'test-name',
            ])
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(1, TestModel::all());
        $this->assertCount(1, Comment::all());
        $this->assertEquals('test-name', Comment::first()->user_name);
    }

    /**
     * @test
     */
    public function it_can_seed_models_with_relationship_defaults()
    {
        steroidFactory(TestModel::class, 2)
            ->with(Comment::class, 1, [
                'user_name' => 'test-name',
            ])
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertCount(2, TestModel::all());
        $this->assertCount(2, Comment::all());
        $this->assertCount('2', Comment::where('user_name', 'test-name')->get());
    }
}
