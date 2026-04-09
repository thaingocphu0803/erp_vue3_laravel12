<?php

namespace App\Trait;

use Illuminate\Http\Exceptions\HttpResponseException;

trait HasResponse
{
	public function exceptionResponse($message, $status_code, $errors = [])
	{
		$dataResponse = $this->initValidationResponseData($message, $errors);

		throw new HttpResponseException(
			response()->json($dataResponse, (int) $status_code)
		);
	}

	public function jsonResponse($message, $status_code = 200, $data = [])
	{
		$dataResponse = $this->initResponseData($message, $data);

		return response()->json($dataResponse, (int) $status_code);
	}

	private function initResponseData($message = '', $dataResponse = [])
	{
		$data = [
			'messageCode' => (string) $message,
			'data' => (array) $dataResponse
		];

		return $data;
	}

	private function initValidationResponseData($message = '', $errors = [])
	{
		$data = [
			'messageCode' => (string) $message,
			'errors' => (array) $errors
		];

		return $data;
	}
}
