<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use App\Http\Requests\Shipment\StoreShipment;
use Whoops\Handler\JsonResponseHandler;

class ShipmentController extends Controller
{
    use ApiResponse;

    protected $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function index(): JsonResponse
    {
        try {
            $shipments = $this->shipmentService->getAllShipments();
            return $this->success($shipments);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function availableOrders(): JsonResponse
    {
        try {
            $orders = $this->shipmentService->getAvailableOrders();
            return $this->success($orders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function show(string $id): JsonResponse
    {
        try {
            $shipment = $this->shipmentService->getShipmentById($id);
            return $this->success($shipment);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreShipment $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['responsible_id'] = $request->user()?->id;

            $shipment = $this->shipmentService->createShipment($data);
            return $this->created($shipment, 'Envio criado com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->shipmentService->deleteShipment($id);
            return $this->success(null, 'Envio eliminado com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function uploadDocument(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        try {
            $file = $this->shipmentService->uploadDocument(
                $id,
                $request->file('file'),
                $request->user()?->id
            );
            return $this->created($file, 'Documento anexado com sucesso.');
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deleteDocument(string $id, string $fileId): JsonResponse
    {
        try {
            $this->shipmentService->deleteDocument($id, $fileId);
            return $this->success(null, 'Documento removido com sucesso.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
