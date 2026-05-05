<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Http\Resources\CounterResource;
use App\Http\Resources\CountryResource;
use App\Services\CountryService;
use App\Models\Country;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{

    use ApiResponse;

    public function __construct(private readonly CountryService $countryService) {}


    public function index(Request $request): JsonResponse
    {

        $countries = $this->countryService->list(
            $request->only(['search', 'per_page'])
        );

        return $this->success(
            CounterResource::collection($countries)
        );
    }

    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = $this->countryService->create($request->validated());

        return $this->created(
            new CountryResource($country),
            'Country created successfully.'
        );
    }

    public function show(Country $country): JsonResponse
    {
        return $this->success(new CountryResource($country));
    }

    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $updated = $this->countryService->update($country, $request->validated());

        return $this->success(new CountryResource($updated),  'Country updated successfully.');
    }
}
