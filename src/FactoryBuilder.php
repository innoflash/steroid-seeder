<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Database\Eloquent\FactoryBuilder as LaravelFactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class FactoryBuilder extends LaravelFactoryBuilder
{
    protected $chunkSize = 1000;

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
     * Create a collection of models and persist them to the database.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function create(array $attributes = [])
    {
        DB::listen(function (QueryExecuted $query) {
            dump($query->sql);
        });

        $results = $this->make($attributes);

        if ($results instanceof Model) {
            $this->store(collect([$results]));

            $this->callAfterCreating(collect([$results]));

            return $results;
        }

        $results->map(function ($model) {
            if (! $model->usesTimestamps()) {
                return $model->toArray();
            }

            $modelAttributes = $model->toArray();

            if (! in_array('created_at', $modelAttributes)) {
                $model['created_at'] = now()->toDateTimeString();
            }

            if (! in_array('updated_at', $modelAttributes)) {
                $model['updated_at'] = now()->toDateTimeString();
            }

            return $modelAttributes;
        })->chunk($this->chunkSize)
            ->each(function ($modelsChunk) {
                ($this->class)::insert($modelsChunk->toArray());
            });

        $results = ($this->class)::cursor()
            ->reverse()
            ->take($this->amount)
            ->reverse();

        $this->callAfterCreating($results);

        return $results;
    }
}
