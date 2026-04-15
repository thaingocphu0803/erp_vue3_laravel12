<?php

namespace App\Trait;

use Illuminate\Http\Exceptions\HttpResponseException;

trait FormatResponse
{
	public function exceptionResponse(string $message, int $status_code, array $errors = [])
	{
		$dataResponse = $this->initValidationResponseData($message, $errors);

		throw new HttpResponseException(
			response()->json($dataResponse, (int) $status_code)
		);
	}

	public function jsonResponse(string $message, int $status_code = 200, array $data = [])
	{
		$dataResponse = $this->initResponseData($message, $data);

		return response()->json($dataResponse, (int) $status_code);
	}

	private function initResponseData(string $message = '', array $dataResponse = [])
	{
		$data = [
			'messageCode' => (string) $message,
			'data' => (array) $dataResponse
		];

		return $data;
	}

	private function initValidationResponseData(string $message = '', array $errors = [])
	{
		$data = [
			'messageCode' => (string) $message,
			'errors' => (array) $errors
		];

		return $data;
	}
}
