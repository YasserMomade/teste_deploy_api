<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function createClient(array $data): Client
    {
        return Client::create($data);
    }

    public function getAllClients($request, int $getPaginate = 6)
    {
        $query = Client::query()
            ->leftJoin('orders', 'clients.id', '=', 'orders.client_id')
            ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
            ->select(
                'clients.*',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(invoices.amountTo_pay), 0) as total_invested')
            )
            ->groupBy(
                'clients.id', 'clients.name', 'clients.lastname',
                'clients.phone', 'clients.email',
                'clients.created_at', 'clients.updated_at', 'clients.deleted_at'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('clients.name', 'like', "%{$search}%")
                ->orWhere('clients.lastname', 'like', "%{$search}%")
                ->orWhere('clients.email', 'like', "%{$search}%")
                ->orWhere('clients.phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('start_date'))
            $query->whereDate('clients.created_at', '>=', $request->start_date);

        if ($request->filled('end_date'))
            $query->whereDate('clients.created_at', '<=', $request->end_date);

        $stats = [
            'total_clients' => (clone $query)->get()->count(),
        ];

        $paginated = $query->latest('clients.created_at')->paginate($getPaginate);

        return [
            'clients' => $paginated,
            'statisc' => $stats,
        ];
    }
    public function getClientById(int $id): ?Client
    {
        return Client::findOrFail($id);
    }

    public function updateClient(Client $client, array $data): Client
    {
        $client->update($data);
        return $client;
    }

    public function deleteClient(Client $client): void
    {
        $client->delete();
    }
}