<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Facades\Image;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ToolController extends BaseManagerController
{

	public function pageDataTransfer()
	{
		return Inertia::render('PageDataTransfer');
	}

	/**
	 * @throws UploadFailedException
	 */
	public function uploadFile(Request $request)
	{
		// check if the upload is success, throw exception or return response you need
		$receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

		// receive the file
		$save = $receiver->receive();

		// check if the upload has finished (in chunk mode it will send smaller files)
		if ($save->isFinished()) {
			$file = $save->getFile();
			$file_names = $this->createFilename($file);
			$name = $file->getClientOriginalName();
			$mime = $file->getMimeType();
			$date_folder = date('Ymd');


			$with_thumb = Str::startsWith($mime, 'image/');
			$type = $request->input('type') === 'private' ? 'private' : 'public';
			$is_private = $type === 'private';

			if ($is_private) {
				$file_path = "{$type}/{$mime}/{$date_folder}";
			} else {
				$file_path = "{$mime}/{$date_folder}";
			}
			// save the file and return any response you need
			if ($is_private) {
				$result = Storage::disk('private')->putFileAs($file_path, $file, $file_names['file']);
			} else {
				$result = Storage::putFileAs($file_path, $file, $file_names['file']);
			}

			if ($with_thumb) {
				$img = Image::make($file->getRealPath());
				//如果没有传入缩略图大小，则默认为120
				$img->resize($request->input('thumb', 120), null, function ($constraint) {
					$constraint->aspectRatio();
				});

				if ($is_private) {
					$img->save(storage_path('app/' . $file_path . '/' . $file_names['thumb']));
					$thumb_url = Storage::temporaryUrl($file_path . '/' . $file_names['thumb'], now()->addMinutes(120));
				} else {
					$img->save(storage_path('app/public/' . $file_path . '/' . $file_names['thumb']));
					$thumb_url = Storage::disk('public')->url($file_path . '/' . $file_names['thumb']);
				}
			}

			if ($result) {
				return $this->json([
					'path' => $result,
					'name' => $name,
					"thumbUrl" => $with_thumb ? $thumb_url : null,
					'url' => $is_private
						? Storage::disk('private')->temporaryUrl($result, now()->addMinutes(120))
						: Storage::url($result),
					'done' => 100,
				]);
			} else {
				return $this->message('上传失败， 请重新上传');
			}
		}

		// we are in chunk mode, lets send the current progress
		$handler = $save->handler();

		return response()->json([
			'done' => $handler->getPercentageDone(),
		]);
	}

	protected function createFilename(UploadedFile $file): array
	{
		$extension = $file->getClientOriginalExtension();
		$filename = str_replace('.' . $extension, '', $file->getClientOriginalName()); // Filename without extension
		$filename = str_replace('-', '_', $filename); // Replace all dashes with underscores

		// Add timestamp hash to name of the file
		$name = md5($filename) . '_' . md5(time());

		return [
			'thumb' => $name . "_thumb." . $extension,
			'file' => $name . "." . $extension,
		];
	}
}
