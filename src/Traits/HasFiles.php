<?php

namespace Markgersalia\LaravelEasyFiles\Traits;

use App\Models\File\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

trait HasFiles
{
    const IS_UPLOADED = 'uploaded';

    const IS_GENERATED = 'generated';

    public function getIsSingle()
    {
        return $this->isSingleFile;
    }

    public function processUploading($file_name, $path, $file, $type, $resource)
    {
        $origin = self::IS_UPLOADED;
        $auth_id = auth()->id();
        // Store file
        $storedPath = Storage::disk('public')->putFileAs(
            "$path/uploads/$auth_id",
            $file,
            $file_name
        );

        $fullPath = storage_path("app/public/{$storedPath}");
        if ($this->getIsSingle()) {
            $file = $resource->files()->first();

            if ($file) {
                $file->update([
                    'file_name' => $file_name,
                    'document_type' => $type,
                    'path' => $fullPath,
                    'origin' => $origin,
                    'preview_url' => "/storage/{$storedPath}", // make sure matches DB column
                ]);

                return $file->fresh(); // return updated model
            }

            return $resource->files()->first()->update([
                'file_name' => $file_name,
                'document_type' => $type,
                'path' => $fullPath,
                'origin' => $origin,
                'preview_url' => "/storage/{$storedPath}",
            ]);
        }

        return $resource->files()->create([
            'file_name' => $file_name,
            'document_type' => $type,
            'path' => $fullPath,
            'origin' => $origin,
            'preview_url' => "/storage/{$storedPath}",
        ]);
    }

    public function generatePdf($file_name, $path, $template, $type, $data = null, $options = [])
    {
        $auth_id = auth()->id();
        $storedDir = sprintf('%s%s', $path, $auth_id);

        $fullPath = storage_path("app/public/{$storedDir}/{$file_name}");

        $createdFile = $this->files()->create([
            'file_name' => $file_name,
            'document_type' => $type,
            'path' => $fullPath,
            'origin' => self::IS_GENERATED,
            'preview_url' => "/storage/{$storedDir}/{$file_name}",
        ]);

        // Ensure dir exists
        if (! file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        $pdf = Pdf::loadView($template, ['data' => $data]);

        if (isset($options['paper'])) {
            $pdf->setPaper(
                $options['paper']['size'] ?? 'A4',
                $options['paper']['orientation'] ?? 'portrait'
            );
        }

        if (! empty($options['dompdf'])) {
            $pdf->setOption($options['dompdf']);
        }

        $pdf->save($fullPath);

        $createdFile->save();

        return $createdFile;
    }

    public function files()
    {
        $fileModel = config('easy-files.file_model');

        return $this->morphMany($fileModel, 'fileable');
    }

    public function getFilesByDocumentType($type)
    {
        return $this->files()->where('document_type', $type);
    }

    public function attachTempFile(string $tempFilename, string $targetPath, string $type)
    {
        $auth_id = auth()->id();
        // Where the temp file lives
        $tempPath = "temp/laravel-easy-files/{$tempFilename}";

        if (!Storage::disk('public')->exists($tempPath)) {
            throw new \Exception("Temp file not found: {$tempFilename}");
        }

        // Final filename
        $fileName = $tempFilename; // or generate new name if you like
 
          // Ensure dir exists
        if (! file_exists(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0777, true);
        }
        // Move the file using Laravel Storage
        $storedPath = Storage::disk('public')->putFileAs(
            "$targetPath/uploads/{$auth_id}",    // 👈 final folder
            Storage::disk('public')->path($tempPath), // source
            $fileName
        );

        // Delete temp file
        Storage::disk('public')->delete($tempPath);

        // Save file record in DB
        return $this->files()->create([
            'file_name'     => $fileName,
            'document_type' => $type,
            'path'          => storage_path($storedPath),              // relative path
            'origin'        => self::IS_UPLOADED,
            'preview_url'  => Storage::url($storedPath) // generates /storage/...
        ]);
    }
}
