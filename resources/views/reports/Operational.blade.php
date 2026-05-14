@extends('reports.layout')

@section('content')

{{-- REPORT TITLE --}}
<div class="report-title">Relatório Operacional - Portador Diário</div>
<div class="report-meta">
    Período:
    <strong>{{ isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'Início' }}</strong>
    a
    <strong>{{ isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
    @if(isset($filters['destination'])) &nbsp;|&nbsp; Destino: <strong>{{ $filters['destination'] }}</strong> @endif
    @if(isset($filters['origin'])) &nbsp;|&nbsp; Origem: <strong>{{ $filters['origin'] }}</strong> @endif
    &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
</div>

{{-- SUMMARY BOXES --}}
<div class="summary-wrap">
    <div class="summary-box">
        <span class="s-label">Total Encomendas</span>
        <span class="s-value">{{ $summary['total_orders'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Total Volumes</span>
        <span class="s-value">{{ $summary['total_volumes'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Peso Real (kg)</span>
        <span class="s-value">{{ number_format($summary['total_weight'], 3) }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Peso Taxado (kg)</span>
        <span class="s-value">{{ number_format($summary['total_declared_weight'], 3) }}</span>
    </div>
</div>

{{-- BY DESTINATION --}}
@if(count($by_destination))
<div class="section-title">Encomendas por Destino</div>
<table class="dt">
    <thead>
        <tr>
            <th>Destino</th>
            <th>Nº Encomendas</th>
            <th>Peso Total (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($by_destination as $row)
        <tr>
            <td>{{ $row['destination'] }}</td>
            <td>{{ $row['total_orders'] }}</td>
            <td>{{ number_format($row['total_weight'], 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- BY STATUS --}}
@if(count($by_status))
<div class="section-title">Encomendas por Estado Actual</div>
<table class="dt">
    <thead>
        <tr>
            <th>Estado</th>
            <th>Nº Encomendas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($by_status as $row)
        <tr>
            <td>{{ str_replace('_', ' ', ucfirst($row['status'])) }}</td>
            <td>{{ $row['total'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- BY Service_type --}}
@if(count($by_service_type))
<div class="section-title">Encomendas por Tipo de Serviço</div>
<table class="dt">
    <thead>
        <tr>
            <th>Serviço</th>
            <th>Nº Encomendas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($by_service_type as $row)
        <tr>
            <td>{{ str_replace('_', ' ', ucfirst($row['service_type'])) }}</td>
            <td>{{ $row['total'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- BY CATEGORY & STORE --}}
<table style="width:100%;border-collapse:separate;border-spacing:8px 0;margin-bottom:8px">
    <tr>
        <td style="vertical-align:top;width:50%;padding:0">
            @if(count($by_category))
            <div class="section-title">Por Categoria</div>
            <table class="dt">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Encomendas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_category as $row)
                    <tr>
                        <td>{{ $row['category'] }}</td>
                        <td>{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </td>
        <td style="vertical-align:top;width:50%;padding:0">
            @if(count($by_store))
            <div class="section-title">Por Loja</div>
            <table class="dt">
                <thead>
                    <tr>
                        <th>Loja</th>
                        <th>Encomendas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_store as $row)
                    <tr>
                        <td>{{ $row['store'] }}</td>
                        <td>{{ $row['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </td>
    </tr>
</table>

{{-- DETAIL TABLE --}}
<div class="section-title">Detalhe das Encomendas</div>
<table class="dt">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Data</th>
            <th>Loja</th>
            <th>Vol.</th>
            <th>Peso (kg)</th>
            <th>P. Taxado</th>
            <th>Estado</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td> {{ ($order['client']['name'] ?? '') . ' ' . ($order['client']['lastname'] ?? '') }}</td>
            <td>{{ $order->origin }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ \Carbon\Carbon::parse($order['reception_date'])->format('d/m/Y') }}</td>
            <td>{{ $order->store?->name ?? '-' }}</td>
            <td>{{ $order->volume_number }}</td>
            <td>{{ $order->weight }}</td>
            <td>{{ $order->declared_weight ?? '-' }}</td>
            <td>{{ $order->latestStatus?->descryption ? str_replace('_', ' ', $order->latestStatus->descryption) : '-' }}</td>
            <td>{{ $order->responsible?->full_name ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="empty-state">Nenhuma encomenda encontrada para os filtros seleccionados.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection