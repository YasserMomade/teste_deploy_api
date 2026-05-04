<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ClientService;
use App\Traits\ApiResponse;
use App\Http\Requests\Client as ClientRequest;
use App\Http\Controllers\Controller;

class ClientControler extends Controller
{

use ApiResponse;

    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index() : JsonResponse
    {
        try {
            $clients = $this->clientService->getAllClients();
            return $this->success($clients);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(ClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->created($client);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            if (!$client) {
                return $this->notFound();
            }
            return $this->success($client);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(ClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            if (!$client) {
                return $this->notFound();
            }
            $updatedClient = $this->clientService->updateClient($client, $request->validated());
            return $this->success($updatedClient);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            if (!$client) {
                return $this->notFound();
            }
            $this->clientService->deleteClient($client);
            return $this->success(null, 'Client deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
