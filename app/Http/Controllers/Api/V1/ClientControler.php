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
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->clientService->getAllClients($request);
            return $this->success([
                'clients' => $result['clients'],
                'statisc' => $result['statisc'],
            ]);
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

                if (!empty($data["name"]) && !empty($data["lastname"])) {
                    $clientData = array_merge($clientData, [
                        "name"     => $data["name"],
                        "lastname" => $data["lastname"],
                    ]);
                }

                $clientupt = $this->clientService->updateClient($client, $clientData);
                $responseData['client'] = $clientupt;

                if (!empty($data['id_costumer']) && !empty($data['id_selectedOrder'])) {
                    if (!empty($data['file_order_map'])) {
                        foreach ($data['file_order_map'] as $fileCostumerId => $orderId) {
                            $fileCostumer = \App\Models\FileCostumer::find($fileCostumerId);
                            if (!$fileCostumer) continue;

                            \App\Models\File::create([
                                'document_type' => $fileCostumer->document_type,
                                'url'           => $fileCostumer->url,
                                'order_id'      => $orderId,
                                'responsible_id' => auth()->id(), 
                            ]);

                            $fileCostumer->delete();
                        }
                    }

                    $costumerdelete = $this->costumerService
                        ->deleteCostumer($data['id_costumer']);

                    $orderupt = $this->orderService->updateMany(
                        (array) $data['id_selectedOrder'],
                        ["sync" => true]
                    );

                    $responseData['costumer_deleted'] = $costumerdelete;
                    $responseData['orders_updated']   = $orderupt;
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
