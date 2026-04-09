<?php

namespace App\Services\System;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
	protected $disk;
	public function __construct()
	{
		$this->disk = Storage::disk('gcs');
	}
	public function upload(UploadedFile $file, string $path, ?string $filename)
	{
		try {
			if ($filename) {
				return $this->disk->putFileAs($path, $file, $filename);
			}
			return $this->disk->putFile($path, $file);
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getUrl(?string $path)
	{
		if (!$path) {
			return null;
		}

		try {
			return $this->disk->url($path);
		} catch (\Exception $e) {
			return null;
		}
	}

	public function delete(?string $path)
	{
		return $this->disk->delete($path);
	}
}
