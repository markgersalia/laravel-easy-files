<?php

namespace Markgersalia\LaravelEasyFiles\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Markgersalia\LaravelEasyFiles\Traits\InteractsWithFiles;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TempUploadController extends Controller
{
    use InteractsWithFiles;

    public function store(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|file|max:10240', // max 10MB
        // ]);

        if ($request->hasFile('filepond')) {
            $file = $request->file('filepond');
            $uniqueId = Str::uuid()->toString();

            $filename = $uniqueId . '_' . $file->getClientOriginalName();
            $path = $file->storeAs(
                'temp/laravel-easy-files',
                $filename,
                'public'
            );
  

            return response()->json([
                'id'       => $uniqueId, // 👈 this is what you’ll use later
                'filename' => $filename,
                'path'     => $path,
                'url'      => asset("storage/{$path}"),
            ]);
        } 
    }
}
