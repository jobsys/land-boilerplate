<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ToolController extends BaseManagerController
{
    /**
     * @throws UploadFailedException
     */
    public function uploadFile(Request $request)
    {
        // check if the upload is success, throw exception or return response you need
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            $file = $save->getFile();
            $file_name = $this->createFilename($file);
            $name = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $date_folder = date("Ymd");

            $type = $request->input("type") === 'private' ? 'private' : 'public';
            $is_private = $type === 'private';

            if ($is_private) {
                $file_path = "{$type}/{$mime}/{$date_folder}";
            } else {
                $file_path = "{$mime}/{$date_folder}";
            }
            // save the file and return any response you need
            if ($is_private) {
                $result = Storage::putFileAs($file_path, $file, $file_name);
            } else {
                $result = Storage::disk('public')->putFileAs($file_path, $file, $file_name);
            }

            if ($result) {
                return $this->json([
                    "path" => $result,
                    "name" => $name,
                    "url" => $is_private
                        ? Storage::temporaryUrl($result, now()->addMinutes(120))
                        : Storage::disk('public')->url($result),
                    "done" => 100
                ]);
            } else {
                return $this->message("上传失败， 请重新上传");
            }
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
    }

    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension
        $filename = str_replace('-', '_', $filename); // Replace all dashes with underscores

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }
}
