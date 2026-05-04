<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    public function createClient(array $data): Client
    {
        return Client::create($data);
    }

    public function getAllClients()
    {
        return Client::all();
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