<?php

namespace Markgersalia\LaravelEasyFiles\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Markgersalia\LaravelEasyFiles\LaravelEasyFiles
 */
class LaravelEasyFiles extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Markgersalia\LaravelEasyFiles\LaravelEasyFiles::class;
    }
}
