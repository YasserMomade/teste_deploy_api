<?php 

namespace App\Traits;

use Iluminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null , string $message = 'Sucess', int $status = 200): JsonResponse
    {
        return response()->json([
            'Sucess' => true, 
            'message' => $message, 
            'data' => $data,
            ], $status);
    }

    protected function created(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

      protected function error(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, 403);
    }

}