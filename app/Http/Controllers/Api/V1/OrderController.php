<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrderService;
use App\Services\InvoiceService;
use App\Services\StatusService;
use App\Services\ClientService;
use App\Traits\ApiResponse;
use App\Http\Requests\Order\StoreOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderRequest;

class OrderController extends Controller
{

    use ApiResponse;
    protected $orderService;
    protected $invoiceService;

    public function __construct(OrderService $orderService, InvoiceService $invoiceService, StatusService $statusService, ClientService $clientService)
    {
        $this->orderService = $orderService;
        $this->invoiceService = $invoiceService;
        $this->statusService = $statusService;
        $this->clientService = $clientService;
    }


    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->orderService->getAllOrders($request);
            return $this->success([
                'orders'  => $result['orders'],
                'statisc' => $result['statisc'],
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function indexUnSync(): JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrdersUnSync();
            return $this->success($orders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(StoreOrder $request): JsonResponse
    {
        try {
            // cliente
            $data = $request->validated();

            $clientId = null;

            if (!empty($data['name']) && !empty($data['lastname'])) {

                $client = $this->clientService->createClient([
                    "name" => $data['name'],
                    "lastname" => $data['lastname'],
                ]);

                $clientId = $client->id;

            } else {
                $clientId = $data['client_id'] ?? null;
            }

            //Trackign
           $tracking = 'TRK' . strtoupper(bin2hex(random_bytes(5)));

            // order
           $orderData = [
                "client_id" => $clientId,
                "description" => $data['description'],
                "tracking" => $tracking,
                "reception_date" => $data['reception_date'],
                "weight" => $data['weight'],
                "declared_weight" => $data['declared_weight'],
                "category_id" => $data['category_id'],
                "responsible_id" => $data['responsible_id'],
                "invoice_id" => null,
                "store_id" => $data['store_id'],
            ];

            $order = $this->orderService->createOrder($orderData);

            $order->load(['category.prices']);

            //Invoice
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

            //status
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

    public function statisc(): JsonResponse
    {
        try {
            $order = $this->orderService->statisc();

            return $this->success($order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();

           $orderData = [
                "client_id" => $data['client_id'],
                "description" => $data['description'],
                "weight" => $data['weight'],
                "declared_weight" => $data['declared_weight'],
                "category_id" => $data['category_id'],
                "store_id" => $data['store_id'],
                "responsible_id" => $data['responsible_id'],
            ];

            $order = $this->orderService->updateOrder($id, $orderData);

            //Invoice
            $weight = $order->weight;
            $price = $order->category->prices->first()?->amount ?? 0;
            $amountToPay = $weight * $price;

            $invoiceData = [
                "amountTo_pay" => $amountToPay
            ];
            $invoice_id = $order->invoice_id;
            $invoice = $this->invoiceService->updateInvoice($invoice_id, $invoiceData);

            //status
            $responsible_id = $data['responsible_id']; //responsavel pela atualização
            $statusData = [
                "descryption" => $data['status'],
                "responsible_id" => $responsible_id,
                "order_id" => $id
            ];
            $status = $this->statusService->createStatus($statusData);


            return $this->created([
                'order' => $order,
                'invoice' => $invoice,
                // 'status' => $status
            ]);
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
