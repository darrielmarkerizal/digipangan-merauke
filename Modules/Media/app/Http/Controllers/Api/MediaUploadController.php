<?php

namespace Modules\Media\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Media\Services\TemporaryMediaService;

class MediaUploadController extends Controller
{
    public function __construct(
        private TemporaryMediaService $service
    ) {}

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $temporaryFile = $this->service->handleUpload($request->file('file'));

            return response()->json([
                'folder' => $temporaryFile->folder,
                'filename' => $temporaryFile->filename,
            ], 200);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function destroy(Request $request)
    {
        $folder = $request->input('folder') ?? $request->folder;
        
        if (!$folder) {
            return response()->json(['error' => 'Folder not provided'], 400);
        }

        if ($this->service->deleteByFolder($folder)) {
            return response()->json(['message' => 'File deleted']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
