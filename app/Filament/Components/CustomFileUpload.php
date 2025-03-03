<?php
namespace App\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class CustomFileUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(function ($file,          // Uploaded file
            $get,           // Get form state
            $set,           // Set form state
            $model,         // Eloquent model
            $path           // Storage path (previously $storagePath)
        ) {
            dd($path);
            // For GIFs, skip processing and save the original file
            if ($file->getMimeType() === 'image/gif') {
                return $file->storeAs(
                    $path,
                    $file->getClientOriginalName(), // Keep original filename
                    ['disk' => $this->getDiskName()]
                );
            }

            // Default behavior for non-GIFs
            return $file->store(
                $path,
                ['disk' => $this->getDiskName()]
            );
        });
    }
}
?>