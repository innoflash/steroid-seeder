<?php

namespace Innoflash\SteroidSeeder\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Innoflash\SteroidSeeder\SteroidSeeder;
use Innoflash\SteroidSeeder\SteroidSeederServiceProvider;
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
    public function it_can_seed_100k_entries_in_less_than_a_minute()
    {
        $startTime = microtime(true);

        steroidFactory(TestModel::class, 100000)
            ->chunk(10000)
            ->skipAfterCreatingCallbacks()
            ->create();

        $this->assertLessThan(60, (microtime(true) - $startTime));
    }
}
