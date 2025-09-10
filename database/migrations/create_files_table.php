<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable'); // fileable_id, fileable_type
            $table->string('file_name');        // unify filename
            $table->string('path');             // storage path
            $table->enum('origin', ['generated', 'uploaded'])->default('generated');
            $table->string('document_type');
            $table->string('preview_url')->nullable(); // link for preview/download
            $table->timestamps();
        });
    }
};
