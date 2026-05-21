<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrderService;
use App\Services\InvoiceService;
use App\Services\StatusService;
use App\Traits\ApiResponse;
use App\Http\Requests\Order\StoreOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderRequest;

class OrderController extends Controller
{

    use ApiResponse;
    protected $orderService;
    protected $invoiceService;

    public function __construct(OrderService $orderService, InvoiceService $invoiceService, StatusService $statusService)
    {
        $this->orderService = $orderService;
        $this->invoiceService = $invoiceService;
        $this->statusService = $statusService;
    }


    public function index(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrders($request);
            return $this->success($orders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreOrder $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            $order->load(['category.prices']);

            $weight = $order->weight;
            $price = $order->category->prices->first()?->amount ?? 0;
            $amountToPay = $weight * $price;

            $reference = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceData = [
                "amountTo_pay" => $amountToPay,
                "amount_paid" => 0,
                "referencie" => 'REF-' . $reference,
                "payment_status" => "pendent",
                "payment_method" => "undefined"
            ];
            $invoice = $this->invoiceService->createInvoice($invoiceData);

            $responsible_id = $order->responsible->id;
            $order_id = $order->id;
            $statusData = [
                "descryption" => "recebido_lisboa",
                "responsible_id" => $responsible_id,
                "order_id" => $order_id
            ];
            $status = $this->statusService->createStatus($statusData);


            $order->update([
                'invoice_id' => $invoice->id
            ]);

            return $this->created([
                'order' => $order,
                'invoice' => $invoice,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function tracking(string $tracking): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderByTracking($tracking);

            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            $updatedOrder = $this->orderService->updateOrder($order, $request->validated());
            return $this->success($updatedOrder);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return $this->notFound();
            }
            $this->orderService->deleteOrder($order);
            return $this->success(null, 'Order deleted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
