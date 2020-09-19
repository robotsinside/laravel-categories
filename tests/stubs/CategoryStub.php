<?php

use Illuminate\Database\Eloquent\Model;
use RobotsInside\Categories\Scopes\CategoriesUsedScopes;

class CategoryStub extends Model
{
    use CategoriesUsedScopes;

    protected $connection = 'testbench';

    public $table = 'categories';
}
