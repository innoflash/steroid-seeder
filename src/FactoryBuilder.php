<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Database\Eloquent\FactoryBuilder as LaravelFactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;

class FactoryBuilder extends LaravelFactoryBuilder
{
    /**
     * @var int
     */
    protected $chunkSize = 1000;

    /**
     * Whether or not to call the callbacks after creating models.
     *
     * @var bool
     */
    protected $callAfterCreating = true;

    /**
     * Whether or not to force calling afterCreate callbacks.
     *
     * @var bool
     */
    protected $forceAfterCreating = false;

    /**
     * The extra relationships to build from the given model.
     *
     * @var array
     */
    protected $with = [];

    private $tempClass = null;

    /**
     * Sets the chunks size to be set when creating entries.
     *
     * @param  int  $chunkSize
     *
     * @return $this
     */
    public function chunk(int $chunkSize)
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * Sets the seeder to skip calling after creating model callbacks.
     *
     * @return $this
     */
    public function skipAfterCreatingCallbacks()
    {
        $this->callAfterCreating = false;

        return $this;
    }

    /**
     * This forces the app to run model created callbacks.
     *
     * Please note this increases execution time of the seeding.
     *
     * @return $this
     */
    public function forceAfterCreatingCallbacks()
    {
        $this->forceAfterCreating = true;

        return $this;
    }

    /**
     * Creates the other related models for the given parent model.
     *
     * @param  string  $class
     * @param  int  $size
     * @param  array  $attributes
     * @param  string|null  $foreignKey
     *
     * @return $this
     */
    public function with(string $class, int $size = 1, array $attributes = [], string $foreignKey = null)
    {
        array_push($this->with, [$class, $size, $attributes, $foreignKey]);

        return $this;
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function create(array $attributes = [])
    {
        $results = $this->make($attributes);

        if ($results instanceof Model) {
            $this->store(collect([$results]));

            if ($this->callAfterCreating) {
                $this->callAfterCreating(collect([$results]));
            }

            $this->amount = 1;
        } else {
            $results->map(function ($model) {
                return $this->mapModel($model);
            })->chunk($this->chunkSize)
                ->each(function ($modelsChunk) {
                    ($this->class)::insert($modelsChunk->toArray());
                });

        }

        return $this->getCreateResults($this->amount);
    }

    /**
     * Maps model for database saving.
     *
     * @param $model
     *
     * @return array
     */
    private function mapModel($model)
    {
        if (! $model->usesTimestamps()) {
            return $model->toArray();
        }

        return $this->withTimestamps($model->toArray());
    }

    /**
     * Adds timestamps to a model.
     *
     * @param  array  $attributes
     *
     * @return array
     */
    private function withTimestamps(array $attributes)
    {
        if (! in_array('created_at', $attributes)) {
            $attributes['created_at'] = now()->toDateTimeString();
        }

        if (! in_array('updated_at', $attributes)) {
            $attributes['updated_at'] = now()->toDateTimeString();
        }

        return $attributes;
    }

    /**
     * Return the last created models.
     *
     * @param  int  $amount
     *
     * @return mixed
     */
    private function getCreateResults(int $amount)
    {
        $results = ($this->class)::cursor()
            ->reverse()
            ->take($amount)
            ->reverse();

        if ($this->callAfterCreating || $this->forceAfterCreating) {
            $this->callAfterCreating($results);
        }

        if (count($this->with)) {
            $this->makeRelationships($results);
        }

        return $results;
    }

    private function makeRelationships(LazyCollection $records)
    {
        $parent = $records->first();
        $parentForeignKey = $parent->getForeignKey();

        $this->chunk($this->chunkSize * count($this->with));

        $relationship = $this->with[0];

        [$class, $size, $attributes, $foreignKey] = $relationship;

        $newRecords = (clone $records)->map(function ($record) use (
            $class,
            $size,
            $attributes,
            $foreignKey,
            $parentForeignKey
        ) {
            $attributes = array_merge($attributes, [
                $foreignKey ?? $parentForeignKey => $record->getKey(),
            ]);

            return SteroidSeeder::factory($class, $size)->make($attributes);
        });

        //dump($newRecords->flatten()->count());
        //temporarily change the current class to the relationship class.
        $this->tempClass = $class;

        //remove item to prevent getCreateResults from re-running.
        array_shift($this->with);

        $this->createMany($newRecords->flatten()->toArray());
    }

    /**
     * Create a collection of models and persist them to the database.
     *
     * @param  iterable  $records
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function createMany(iterable $records)
    {
        (new LazyCollection($records))
            ->map(function ($model) {
                if ($model instanceof Model) {
                    return $this->mapModel($model);
                }

                return $this->withTimestamps($model);
            })
            ->chunk($this->chunkSize)
            ->each(function ($modelsChunk) {
                $this->tempClass ? ($this->tempClass)::insert($modelsChunk->toArray())
                    : ($this->class)::insert($modelsChunk->toArray());
            });

        $this->tempClass = null;

        if ($this->amount || $this->with) {
            return $this->getCreateResults($this->amount);
        }

        return $this->getCreateResults(count($records));
    }
}
