<?php

use Illuminate\Support\Facades\Storage;
use Markgersalia\LaravelEasyFiles\Tests\Models\DummyModel;

it('can generate a PDF file and save record', function () {
    Storage::fake('public');

    $model = DummyModel::create(['name' => 'Test']);

    $file = $model->generateFile(
        'test.pdf',
        'contracts',
        'pdf.template', // <- create a dummy blade view for testing
        'contract',
        ['foo' => 'bar'],
        [
            'paper' => ['size' => 'letter', 'orientation' => 'landscape'],
            'dompdf' => ['dpi' => 120],
        ]
    );

    expect($file->file_name)->toBe('test.pdf');
    expect($file->document_type)->toBe('contract');
    expect($file->origin)->toBe('generated');
    expect(Storage::disk('public')->exists('contracts/'.auth()->id().'/test.pdf'))->toBeTrue();
});

it('can upload a file and save record', function () {
    Storage::fake('public');

    $model = DummyModel::create(['name' => 'Test']);

    $uploadedFile = \Illuminate\Http\UploadedFile::fake()->create('upload.pdf', 100);

    $file = $model->processUploading('upload.pdf', 'docs', $uploadedFile, 'contract', $model);

    expect($file->file_name)->toBe('upload.pdf');
    expect($file->origin)->toBe('uploaded');
    expect(Storage::disk('public')->exists('docs/uploads/'.auth()->id().'/upload.pdf'))->toBeTrue();
});
