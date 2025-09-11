<?php

namespace Markgersalia\LaravelEasyFiles\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Markgersalia\LaravelEasyFiles\Models\File;

trait InteractsWithFiles
{
    protected function getTemplate(): string
    {
        return $this->template ?? '';
    }

    protected function getDocumentType(): string
    {
        return $this->document_type ?? '';
    }

    protected function getFilename(): string
    {
        return $this->file_name ?? '';
    }

    private function generateFilePath($resource): string
    {
        if ($this->getDocumentType()) {
            return "{$resource->id}/{$this->getDocumentType()}/";
        }

        return "{$resource->id}";

    }

    private function generateFilename($resource): string
    {
        if ($this->getFilename()) {
            return sprintf('%s.pdf', $this->getFilename());
        }

        return sprintf('%s_%d.pdf', now()->format('Ymd'), $resource->id);
    }

    public function download($resource, $fileId): void
    {
        $resource = $this->processResource($resource);
        $file = $resource->files()->findOrFail($fileId);

        $file->logDownloadHistory($file->file_name, $file->preview_link, $file->version);
    }

    public function processGeneratePdf($resource)
    {
        return DB::transaction(function () use ($resource) {
            return $resource->generatePdf(
                $this->generateFilename($resource),
                $this->generateFilePath($resource),
                $this->getTemplate(),
                $this->getDocumentType(),
                $resource
            );
        });
    }

    public function processUpload($resource, $requestFile)
    {
        return DB::transaction(function () use ($resource, $requestFile) {
            $file = $requestFile;
            $fileName = $file->getClientOriginalName(); 
            $uploaded = $resource->processUploading(
                $fileName,
                $this->generateFilePath($resource),
                $file,
                $this->getDocumentType(),
                $resource
            );

            return $uploaded;
        });
    }

    


    public function deleteFile($resource)
    {
        return DB::transaction(function () use ($resource) {
            $resource = $this->processResource($resource);
            $file = File::findOrFail(request()->file_id);

            // Strip "/storage/"
            $relativePath = str_replace('/storage/', '', $file->preview_link);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            $file->delete();

            return $this->deleteResponse(['item' => $resource, $this->getDeleteMessage()]);
        });
    }
}
