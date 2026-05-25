<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ClientService;
use App\Services\CostumerService;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use App\Http\Requests\Client as ClientRequest;
use App\Http\Requests\UpdateClient;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class ClientControler extends Controller
{

    use ApiResponse;

    protected $clientService;

    public function __construct(ClientService $clientService, CostumerService $costumerService, OrderService $orderService)
    {
        $this->clientService = $clientService;
        $this->costumerService = $costumerService;
        $this->orderService = $orderService;
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

    public function update(UpdateClient $request, int $id): JsonResponse
    {
        try {

            $data = $request->validated();

            $client = $this->clientService->getClientById($id);

            if (!$client) {
                return $this->notFound();
            }

            DB::transaction(function () use ($data, $client, &$responseData) {
                $clientData = [
                    "phone" => $data["phone"] ?? null,
                    "email" => $data["email"] ?? null,
                ];

                $clientupt = $this->clientService
                    ->updateClient($client, $clientData);

                $responseData['client'] = $clientupt;

                if (
                    !empty($data['id_costumer']) &&
                    !empty($data['id_selectedOrder'])
                ) {

                    $costumerdelete = $this->costumerService
                        ->deleteCostumer($data['id_costumer']);

                    $orderupt = $this->orderService->updateMany(
                        (array) $data['id_selectedOrder'],
                        ["sync" => true]
                    );

                    $responseData['costumer_deleted'] = $costumerdelete;
                    $responseData['orders_updated'] = $orderupt;
                }
            });

            return $this->success(
                $responseData ?? null,
                'Successfully'
            );

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
