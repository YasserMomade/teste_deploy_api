<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Order;
use App\Services\StatusService;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function __construct(
        private StatusService $statusService
    ) {}

    public function getAllShipments()
    {
        return Shipment::with('responsible:id,name')
            ->withCount('orders')
            ->latest()
            ->get();
    }


    public function getShipmentById(string $id)
    {
        return Shipment::with([
            'responsible:id,name',
            'orders.client',
            'orders.category',
            'orders.invoice',
            'orders.latestStatus',
        ])->findOrFail($id);
    }

 
    public function getAvailableOrders()
    {
        return Order::with(['client', 'category', 'invoice', 'latestStatus'])
            ->whereNull('shipment_id')
            ->whereHas('latestStatus', function ($q) {
                $q->whereIn('descryption', ['pronto_expedicao']);
            })
            ->get();
    }


    public function createShipment(array $data): Shipment
    {
        return DB::transaction(function () use ($data) {

            $shipment = Shipment::create([
                'reference' => $data['reference'],
                'carta_porte' => $data['carta_porte'],
                'airline' => $data['airline'],
                'origin' => $data['origin'] ?? 'Lisboa',
                'destination' => $data['destination'] ?? 'Maputo',
                'shipment_date' => $data['shipment_date'],
                'general_observations' => $data['general_observations'] ?? null,
                'responsible_id' => $data['responsible_id'] ?? null,
            ]);

            $orderIds = $data['order_ids'];

        
            Order::whereIn('id', $orderIds)->update(['shipment_id' => $shipment->id]);

            foreach ($orderIds as $orderId) {
                $this->statusService->createStatus([
                    'descryption' => 'em_transito',
                    'responsible_id' => $data['responsible_id'] ?? null,
                    'order_id' => $orderId,
                ]);
            }

            return $shipment->fresh(['orders.client', 'responsible:id,name']);
        });
    }

    public function deleteShipment(string $id): void
    {
        DB::transaction(function () use ($id) {
            $shipment = Shipment::findOrFail($id);
            Order::where('shipment_id', $shipment->id)->update(['shipment_id' => null]);
            $shipment->delete();
        });
    }
}
