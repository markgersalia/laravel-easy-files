<?php

namespace Markgersalia\LaravelEasyFiles\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'fileable',
        'file_name',
        'path',
        'origin',
        'document_type',
        'preview_url',
    ];
}
