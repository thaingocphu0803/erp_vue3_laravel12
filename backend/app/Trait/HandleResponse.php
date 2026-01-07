<?php

namespace App\Trait;

use Illuminate\Http\Exceptions\HttpResponseException;

trait HandleResponse
{
    public function exceptionResponse($messageCode, $errors, $status_code)
    {
        $dataResponse = $this->initValidationResponseData($messageCode, $errors);

        throw new HttpResponseException(
            response()->json($dataResponse, (int) $status_code)
        );
    }

    public function jsonResponse($messageCode, $status_code = 200, $data = [])
    {
        $dataResponse = $this->initResponseData($messageCode, $data);

        return response()->json($dataResponse, (int) $status_code);
    }

    private function initResponseData($messageCode = '', $dataResponse = [])
    {
        $data = [
            'messageCode' => (string) $messageCode,
            'data' => (array) $dataResponse
        ];

        return $data;
    }

    private function initValidationResponseData($messageCode = '', $errors = [])
    {
        $data = [
            'messageCode' => (string) $messageCode,
            'errors' => (array) $errors
        ];

        return $data;
    }

}
