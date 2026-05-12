<?php

namespace App\Http\Controllers\API\v1;

use App\Exports\ExceptionReportExport;
use App\Exports\FinancialReportExport;
use App\Exports\OperationalReportExport;
use App\Exports\Sheets\ExceptionSummarySheet;
use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
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

    public function __construct(
        private readonly OperationalReportService $operationalService,
        private readonly FinancialReportService   $financialService,
        private readonly ExceptionReportService $exceptionService,
    ) {}

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

    public function exportFinancial(Request $request): mixed
    {
        $filters = $this->validateFinancialFilters($request);
        $format  = $request->input('format', 'excel');
        $data    = $this->financialService->generate($filters);

        return match ($format) {
            'pdf'   => $this->exportFinancialPdf($data, $filters),
            default => Excel::download(
                new FinancialReportExport($data),
                'relatorio_financeiro_' . now()->format('Ymd_His') . '.xlsx'
            ),
        };
    }

    private function exportFinancialPdf(array $data, array $filters): Response
    {
        $pdf = Pdf::loadView('reports.financial', array_merge($data, ['filters' => $filters]))
            ->setPaper('a4', 'landscape');

        return $pdf->download('relatorio_financeiro_' . now()->format('Ymd_His') . '.pdf');
    }

    public function operational(Request $request): JsonResponse
    {
        $filters = $this->validateOperationalFilters($request);
        $data = $this->operationalService->generate($filters);

        return $this->success($data);
    }

    public function exportOperational(Request $request): mixed
    {
        $filters = $this->validateOperationalFilters($request);
        $format  = $request->input('format', 'excel');
        $data    = $this->operationalService->generate($filters);

        return match ($format) {
            'pdf'   => $this->exportOperationalPdf($data, $filters),
            default => Excel::download(
                new OperationalReportExport($data),
                'relatorio_operacional_' . now()->format('Ymd_His') . '.xlsx'
            ),
        };
    }

    private function exportOperationalPdf(array $data, array $filters): Response
    {
        $pdf = Pdf::loadView('reports.operational', array_merge($data, ['filters' => $filters]))
            ->setPaper('a4', 'landscape');

        return $pdf->download('relatorio_operacional_' . now()->format('Ymd_His') . '.pdf');
    }

    private function validateOperationalFilters(Request $request): array
    {
        return $this->validateBaseFilters($request);
    }

    public function exception(Request $request): JsonResponse
    {
        $filters = $this->validateBaseFilters($request);
        $data = $this->exceptionService->generate($filters);

        return $this->success($data);
    }

public function exportException(Request $request): mixed
{
    $filters = $this->validateBaseFilters($request);
    $format = $request->input('format', 'excel');
    $data = $this->exceptionService->generate($filters);


    return match($format) {
        'pdf'=> $this->exportExceptionPdf($data, $filters),
        default => Excel::download(
            new ExceptionReportExport($data),  // agora recebe $data completo
            'relatorio_excepcoes_' . now()->format('Ymd_His') . '.xlsx'
        ),
    };
}


    private function exportExceptionPdf(array $data, array $filters): Response
    {
        $pdf = Pdf::loadView('reports.exception', array_merge($data, [
            'filters' => $filters,
            'orders_without_client' => $data['orders_without_client'],
            'orders_without_invoice' => $data['orders_without_invoice'],
            'orders_without_declared_weight' => $data['orders_without_declared_weight'],
            'orders_without_status' => $data['orders_without_status'],
        ]))->setPaper('a4', 'portrait');

        return $pdf->download('relatorio_excepcoes_' . now()->format('Ymd_His') . '.pdf');
    }
}
