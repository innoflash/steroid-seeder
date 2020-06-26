# About Steroid-Seeder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/innoflash/steroid-seeder.svg?style=flat-square)](https://packagist.org/packages/innoflash/steroid-seeder)
[![Build Status](https://img.shields.io/travis/innoflash/steroid-seeder/master.svg?style=flat-square)](https://travis-ci.org/innoflash/steroid-seeder)
[![Quality Score](https://img.shields.io/scrutinizer/g/innoflash/steroid-seeder.svg?style=flat-square)](https://scrutinizer-ci.com/g/innoflash/steroid-seeder)
[![Total Downloads](https://img.shields.io/packagist/dt/innoflash/steroid-seeder.svg?style=flat-square)](https://packagist.org/packages/innoflash/steroid-seeder)

This package is built to reduce the time taken when seeding a lot data to the database on development.

<p align="center">
    <img title="Steroid Seeder" height="250" src="https://raw.githubusercontent.com/innoflash/steroid-seeder/master/images/carbon.png" />
</p>

## Installation

You can install the package via composer:

```bash
composer require innoflash/steroid-seeder --dev
```

## Usage
Used the same way we do with default Laravel `factory`

> If you wanna use it in your existing seeding files just replace `factory` with `steroidFactory`
> 
> Alternatively you can call the factory from the facade. `SteroidSeeder::factory` 

Default `factory` seeds one model at a time and that elongates the time of execution for huge data-sets.

``` php
// with default factory (approx 37 seconds on my computer)

factory(TestModel::class, 1000)->create();
```
```php
//with steroidFactory (approx 8 seconds on my machine)

steroidFactory(\App\TestModel::class, 1000)->create();
```

##### Steroid seeder optimization.
* By default the `steroidFactory` save 1000 entries at a go, you cant tune this to whatever size that works for you.
```php
// took 8.8 seconds to seed 10k entries

steroidFactory(\App\TestModel::class, 100000)
    ->chunk(1000)
    ->create();
```
* By default the Laravel factory calls some callbacks after creating your models. This is when the model boot and observers are called and its time consuming because the factory iterate over all the created models.
steroidFactory lets you ignore the callbacks.
```php
// took 4.3 seconds to seed 10k entries

steroidFactory(\App\TestModel::class, 100000)
    ->skipAfterCreatingCallbacks()
    ->create();
```
### Seeding relationships
Steroid seeder can be used to create models with their relationships.

It's a continuation of the above except that the you will need to chain your relationships on the factory. See the example below:
```php
    steroidFactory(TestModel::class)
            ->with(Comment::class)
            ->with(Reaction::class, 1, [], 'model_id')
            ->create();
```
Here is the params expected in the `with` function.
|Position|Variable|Type|Required|Default|Description|
|---------|-------|----|--------|-------|-----------|
|`1`|`$class`|`string`|`true`|`null`|The class name of the model to be related with the model in create.|
|`2`|`$size`|`int`|`false`|`1`|The number of models to be seeded with the parent in`create`.|
|`3`|`$attributes`|`array`|`false`|`[]`| The default attributes to set to the models.|
|`4`|`$foreignKeyName`|`string`|`false`|parent key| The name of the parent's foreign key column name in the models to be seeded.|
### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email innocentmazando@gmail.com instead of using the issue tracker.

## Credits

- [Innocent Mazando](https://github.com/innoflash)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
