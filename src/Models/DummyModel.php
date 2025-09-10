<?php

namespace Markgersalia\LaravelEasyFiles\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Markgersalia\LaravelEasyFiles\HasFiles;

class DummyModel extends Model
{
    use HasFiles;

    protected $fillable = ['name'];
}
