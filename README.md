# Laravel Categories

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robotsinside/laravel-categories.svg?style=flat-square)](https://packagist.org/packages/robotsinside/laravel-categories)
[![Total Downloads](https://img.shields.io/packagist/dt/robotsinside/laravel-categories.svg?style=flat-square)](https://packagist.org/packages/robotsinside/laravel-categories)
![CI](https://github.com/robotsinside/laravel-categories/actions/workflows/laravel.yml/badge.svg?style=flat-square)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

A simple package for categorising Eloquent models in Laravel. This package is a sibling of [Laravel Tags](https://github.com/robotsinside/laravel-tags), which can be used to tag Eloquent models. The API is the same as this one.

## Table of contents

- [Installation](#installation)
- [Usage](#usage)
- [Scopes](#scopes)
- [Security](#security)
- [Credits](#credits)
- [Coffee Time](#coffee-time)
- [License](#license)

## Installation

1. Install using Composer

```sh
composer require robotsinside/laravel-categories
```

2. Optionally register the service provider in `config/app.php`

```php
/*
* Package Service Providers...
*/
\RobotsInside\Categories\CategoriesServiceProvider::class,
```

Auto-discovery is enabled, so this step can be skipped.

3. Publish the migrations

```sh
php artisan vendor:publish --provider="RobotsInside\Categories\CategoriesServiceProvider" --tag="migrations"
```

4. Migrate the database. This will create two new tables; `categories` and `categorisables`

```sh
php artisan migrate
```

## Usage

Use the `RobotsInside\Categories\Categorisable` trait in your models.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use RobotsInside\Categories\Categorisable;

class Post extends Model
{
    use Categorisable;
}
```

You are now ready to categorise your models. Models can be categorised by passing an integer, array of integers, a model instance or a collection of models.

```php
<?php

use App\Post;
use Illuminate\Support\Facades\Route;
use RobotsInside\Categories\Models\Category;

Route::get('/', function () {

    // Retrieve a new or existing category
    $category1 = (new Category())->resolve('Category 1');
    $category2 = (new Category())->resolve('Category 2');

    // Or, retrieve a collection of new or existing categories
    $categories = (new Category())->resolveAll(['Category 1', 'Category 2', 'Category 3'])

    $post = new Post();
    $post->title = 'My blog';
    $post->save();

    $post->categorise($category1);
    // Or
    $post->categorise(['category-1']);
    // Or
    $post->categorise([1, 2]);
    // Or
    $post->categorise(Category::get());
});
```

Uncategorising models is just as simple.

```php
<?php

use App\Post;
use Illuminate\Support\Facades\Route;
use RobotsInside\Categories\Models\Category;

Route::get('/', function () {

    $category1 = Category::find(1);

    $post = Post::where('title', 'My blog')->first();

    $post->uncategorise($category1);
    // Or
    $post->uncategorise(['category-1']);
    // Or
    $post->uncategorise([1, 2]);
    // Or
    $post->uncategorise(Category::get());
    // Or
    $post->uncategorise(); // remove all categories
});
```

## Scopes

Each time a `RobotsInside\Categories\Models\Category` is used, the `count` column in the `categories` table is incremented. When a category is removed, the count is decremented until it is zero.

This packages comes with a number of pre-defined scopes to make queries against the `count` column easier, namely `>=`, `>`, `<=` and `<` contstrains, for example:

-   `Category::usedGte(1);`
-   `Category::usedGt(2);`
-   `Category::usedLte(3);`
-   `Category::usedLt(4);`

The `RobotsInside\Categories\Models\Categorisable` model contains a scope to constrain records created within a given time frame. This scope supports human readable values including `days`, `months` and `years` in both singular and plural formats, for example:

-   `Categorisable::categorisedWithin('7 days');`
-   `Categorisable::categorisedWithin('1 month');`
-   `Categorisable::categorisedWithin('2 years');`

## Security

If you discover any security related issues, please email robertfrancken@gmail.com instead of using the issue tracker.

## Credits

- [Rob Francken](https://github.com/robotsinside)
- [All Contributors](../../contributors)

## Coffee Time

Will work for :coffee::coffee::coffee:

<a href="https://www.buymeacoffee.com/robfrancken" target="_blank" width="50"><img src="https://cdn.buymeacoffee.com/buttons/v2/arial-yellow.png" width="200" alt="Buy Me A Coffee"></a>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
