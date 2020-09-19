<?php

use RobotsInside\Categories\Categorisable;
use Illuminate\Database\Eloquent\Model;

class LessonStub extends Model
{
    use Categorisable;

    protected $connection = 'testbench';

    public $table = 'lessons';
}
