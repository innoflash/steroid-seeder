<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Database\Eloquent\FactoryBuilder as LaravelFactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;

class FactoryBuilder extends LaravelFactoryBuilder
{
    /**
     * @var int $chunkSize
     */
    protected $chunkSize = 1000;

    /**
     * Whether or not to call the callbacks after creating models.
     * @var bool
     */
    protected $callAfterCreating = true;

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

            return $results;
        }

        $results->map(function ($model) {
            return $this->mapModel($model);
        })->chunk($this->chunkSize)
            ->each(function ($modelsChunk) {
                ($this->class)::insert($modelsChunk->toArray());
            });

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

        if ($this->callAfterCreating) {
            $this->callAfterCreating($results);
        }

        return $results;
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
                ($this->class)::insert($modelsChunk->toArray());
            });

        return $this->getCreateResults(count($records));
    }

}
