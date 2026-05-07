<?php

namespace App\Http\Controllers\API\v1;

use App\Exports\ExceptionReportExport;
use App\Exports\FinancialReportExport;
use App\Exports\OperationalReportExport;
use App\Http\Controllers\Controller;
use App\Services\Reports\ExceptionReportService;
use App\Services\Reports\FinancialReportService;
use App\Services\Reports\OperationalReportService;
use App\Traits\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    
use ApiResponse;

public function __construct(private readonly FinancialReportService $financialService){}

public function financial(Request $request): JsonResponse
{
    $filters = $this->validateFinancialFilters($request);
    $data = $this->financialService->generate($filters);

    return $this->success($data);
}

public function validateFinancialFilters(Request $request): array
{
    $base = $this->validateBaseFilters($request);

    $financial = $request->validate([
        'payment_status' => ['sometimes', 'string', 'in:paid,pendent,faild'],
        'payment_method' => ['sometimes', 'string', 'in:card,cash,undefined'],
    ]);

    return array_merge($base, $financial);
}

private function validateBaseFilters(Request $request): array 
{
    return $request->validate([
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'destination' => ['sometimes', 'string'],
            'origin' => ['sometimes', 'string'],
            'store_id' => ['sometimes', 'integer', 'exists:stores,id'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'format' => ['sometimes', 'string', 'in:excel,pdf'],
        ]);
}


}
