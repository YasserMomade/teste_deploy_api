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

   public function getAllClients(int $getPaginate = 6)
    {
        return Client::query()
            ->leftJoin('orders', 'clients.id', '=', 'orders.client_id')
            ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
            ->select(
                'clients.*',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(invoices.amountTo_pay), 0) as total_invested')
            )
            ->groupBy(
                'clients.id',
                'clients.name',
                'clients.lastname',
                'clients.phone',
                'clients.email',
                'clients.created_at',
                'clients.updated_at',
                'clients.deleted_at'
            )
            ->paginate($getPaginate);
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

    public function deleteClient(string $id): void
    {
        $client->destroy($id);
    }
}