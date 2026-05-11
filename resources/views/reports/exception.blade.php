@extends('reports.layout')

@section('content')

{{-- REPORT TITLE --}}
<div class="report-title">Relatório de Excepções - Portador Diário</div>
<div class="report-meta">
    Período:
    <strong>{{ isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'Início' }}</strong>
    a
    <strong>{{ isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
    &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
</div>

{{-- SUMMARY BOXES --}}
<div class="summary-wrap">
    <div class="summary-box">
        <span class="s-label">Sem Cliente</span>
        <span class="s-value {{ $summary['total_without_client'] > 0 ? 'red' : 'green' }}">{{ $summary['total_without_client'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Sem Factura</span>
        <span class="s-value {{ $summary['total_without_invoice'] > 0 ? 'red' : 'green' }}">{{ $summary['total_without_invoice'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Sem Peso Declarado</span>
        <span class="s-value {{ $summary['total_without_declared_weight'] > 0 ? 'red' : 'green' }}">{{ $summary['total_without_declared_weight'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Sem Estado</span>
        <span class="s-value {{ $summary['total_without_status'] > 0 ? 'red' : 'green' }}">{{ $summary['total_without_status'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Facturas s/ Estado</span>
        <span class="s-value {{ $summary['total_invoices_null_status'] > 0 ? 'red' : 'green' }}">{{ $summary['total_invoices_null_status'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Em Atraso</span>
        <span class="s-value {{ $summary['total_delayed'] > 0 ? 'red' : 'green' }}">{{ $summary['total_delayed'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Qualidade</span>
        <span class="s-value" style="color:#962479">{{ $summary['quality_label'] }}</span>
    </div>
</div>

{{-- ── SECTION 1: WITHOUT CLIENT ──────────────────────────────── --}}
<div class="exc-header">
    <div class="exc-header-title">1. Encomendas sem Cliente</div>
    <div class="exc-count">
        <span class="{{ $summary['total_without_client'] === 0 ? 'zero' : '' }}">
            {{ $summary['total_without_client'] }} ocorrência(s)
        </span>
    </div>
</div>
@if($orders_without_client->isEmpty())
<div class="empty-state">Nenhuma ocorrência encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Destino</th>
            <th>Data Recepção</th>
            <th>Peso (kg)</th>
            <th>Loja</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders_without_client as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ $order->reception_date?->format('d/m/Y') }}</td>
            <td>{{ $order->weight }}</td>
            <td>{{ $order->store?->name ?? '-' }}</td>
            <td>{{ $order->responsible?->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── SECTION 2: WITHOUT INVOICE ─────────────────────────────── --}}
<div class="exc-header">
    <div class="exc-header-title">2. Encomendas sem Factura</div>
    <div class="exc-count">
        <span class="{{ $summary['total_without_invoice'] === 0 ? 'zero' : '' }}">
            {{ $summary['total_without_invoice'] }} ocorrência(s)
        </span>
    </div>
</div>
@if($orders_without_invoice->isEmpty())
<div class="empty-state">Nenhuma ocorrência encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Destino</th>
            <th>Data Recepção</th>
            <th>Peso (kg)</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders_without_invoice as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td>{{ $order->client?->full_name ?? '-' }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ $order->reception_date?->format('d/m/Y') }}</td>
            <td>{{ $order->weight }}</td>
            <td>{{ $order->responsible?->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── SECTION 3: WITHOUT DECLARED WEIGHT ─────────────────────── --}}
<div class="exc-header">
    <div class="exc-header-title">3. Encomendas sem Peso Declarado</div>
    <div class="exc-count">
        <span class="{{ $summary['total_without_declared_weight'] === 0 ? 'zero' : '' }}">
            {{ $summary['total_without_declared_weight'] }} ocorrência(s)
        </span>
    </div>
</div>
@if($orders_without_declared_weight->isEmpty())
<div class="empty-state">Nenhuma ocorrência encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Destino</th>
            <th>Data Recepção</th>
            <th>Peso Real (kg)</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders_without_declared_weight as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td>{{ $order->client?->full_name ?? '-' }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ $order->reception_date?->format('d/m/Y') }}</td>
            <td>{{ $order->weight }}</td>
            <td>{{ $order->responsible?->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── SECTION 4: WITHOUT STATUS ───────────────────────────────── --}}
<div class="exc-header">
    <div class="exc-header-title">4. Encomendas sem Estado Registado</div>
    <div class="exc-count">
        <span class="{{ $summary['total_without_status'] === 0 ? 'zero' : '' }}">
            {{ $summary['total_without_status'] }} ocorrência(s)
        </span>
    </div>
</div>
@if($orders_without_status->isEmpty())
<div class="empty-state">Nenhuma ocorrência encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Destino</th>
            <th>Data Recepção</th>
            <th>Peso (kg)</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders_without_status as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td>{{ $order->client?->full_name ?? '-' }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ $order->reception_date?->format('d/m/Y') }}</td>
            <td>{{ $order->weight }}</td>
            <td>{{ $order->responsible?->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── SECTION 5: INVOICES WITH NULL STATUS ───────────────────── --}}
<div class="exc-header">
    <div class="exc-header-title">5. Facturas com Estado de Pagamento em Falta</div>
    <div class="exc-count">
        <span class="{{ $summary['total_invoices_null_status'] === 0 ? 'zero' : '' }}">
            {{ $summary['total_invoices_null_status'] }} ocorrência(s)
        </span>
    </div>
</div>
@if($invoices_with_null_status->isEmpty())
<div class="empty-state">Nenhuma ocorrência encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Destino</th>
            <th>Data Recepção</th>
            <th>Valor a Pagar</th>
            <th>Referência</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices_with_null_status as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td>{{ $order->client?->full_name ?? '-' }}</td>
            <td>{{ $order->destination }}</td>
            <td>{{ $order->reception_date?->format('d/m/Y') }}</td>
            <td>{{ number_format($order->invoice?->amountTo_pay ?? 0, 2) }}</td>
            <td>{{ $order->invoice?->referencie ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECTION 6: DELAYS --}}
<div class="exc-header">
    <div class="exc-header-title">6. Encomendas em Atraso</div>
    <div class="exc-count">
        <span class="{{ $delays['summary']['total_delayed'] === 0 ? 'zero' : '' }}">
            {{ $delays['summary']['total_delayed'] }} em atraso
            / {{ $delays['summary']['total_analysed'] }} analisadas
        </span>
    </div>
</div>

@if($delays['summary']['total_no_config'] > 0)
<div class="empty-state" style="margin-bottom:6px">
    ⚠ {{ $delays['summary']['total_no_config'] }} encomenda(s) sem configuração de rota definida - não foram analisadas.
</div>
@endif

@if(empty($delays['delayed']))
<div class="empty-state">Nenhuma encomenda em atraso encontrada.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Origem</th>
            <th>Destino</th>
            <th>Serviço</th>
            <th>Saída Prevista</th>
            <th>Prazo Limite</th>
            <th>Horas Atraso</th>
            <th>Entregue</th>
            <th>Entregue por</th>
        </tr>
    </thead>
    <tbody>
        @foreach($delays['delayed'] as $row)
        <tr>
            <td>{{ $row['tracking'] }}</td>
            <td>{{ $row['client'] }}</td>
            <td>{{ $row['origin'] }}</td>
            <td>{{ $row['destination'] }}</td>
            <td>{{ $row['service_type'] }}</td>
            <td>
                {{ $row['analysis']['actual_departure_at']
              ? \Carbon\Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i')
              : '-' }}
            </td>
            <td>
                {{ $row['analysis']['deadline_at']
              ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i')
              : '-' }}
            </td>
            <td style="color:#b52222;font-weight:bold">
                +{{ $row['analysis']['delay_hours'] }}h
            </td>
            <td>{{ $row['analysis']['is_delivered'] ? 'Sim' : 'Não' }}</td>
            <td>{{ $row['analysis']['delivered_by'] ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ── SECTION 7: QUALITY ──────────────────────────────────────── --}}
<div class="exc-header" style="margin-top:8px">
    <div class="exc-header-title">7. Índice de Qualidade Operacional</div>
    <div class="exc-count">
        @php
        $scoreVal = $quality['score']['score'];
        $scoreClass = $scoreVal >= 2.5 ? 'zero' : '';
        $scoreStyle = $scoreVal < 1.5 ? 'background:#3d0000;color:#fff' : '' ;
            @endphp
            <span class="{{ $scoreClass }}" style="{{ $scoreStyle }}">
            {{ $scoreVal }}/4.00 - {{ $quality['score']['label'] }}
            </span>
    </div>
</div>

{{-- Quality summary boxes --}}
<div class="summary-wrap" style="margin-top:8px;margin-bottom:12px">
    <div class="summary-box">
        <span class="s-label">Total Obs.</span>
        <span class="s-value">{{ $quality['summary']['total_observations'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Good</span>
        <span class="s-value green">{{ $quality['summary']['total_good'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Medium</span>
        <span class="s-value" style="color:#8a6200">{{ $quality['summary']['total_medium'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Bad</span>
        <span class="s-value red">{{ $quality['summary']['total_bad'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Critical</span>
        <span class="s-value" style="color:#3d0000">{{ $quality['summary']['total_critical'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Score</span>
        <span class="s-value" style="color:#962479">{{ $quality['score']['percentage'] }}%</span>
    </div>
</div>

@if($quality['summary']['total_observations'] === 0)
<div class="empty-state">Nenhuma observação registada no período seleccionado.</div>
@else

{{-- Quality by responsible --}}
@if(count($quality['by_responsible']))
<div class="section-title">Qualidade por Colaborador</div>
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Código</th>
            <th>Total Obs.</th>
            <th>Good</th>
            <th>Medium</th>
            <th>Bad</th>
            <th>Critical</th>
            <th>Score</th>
            <th>Classificação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quality['by_responsible'] as $row)
        <tr>
            <td>{{ $row['responsible'] }}</td>
            <td>{{ $row['user_code'] }}</td>
            <td>{{ $row['total'] }}</td>
            <td style="color:#2a6e2a">{{ $row['good'] }}</td>
            <td style="color:#8a6200">{{ $row['medium'] }}</td>
            <td style="color:#b52222">{{ $row['bad'] }}</td>
            <td style="color:#3d0000;font-weight:bold">{{ $row['critical'] }}</td>
            <td style="font-weight:bold">{{ $row['score'] }}/4</td>
            <td>
                @php
                $badgeClass = $row['score'] >= 3.5
                ? 'badge-good'
                : ($row['score'] >= 2.5 ? 'badge-medium' : 'badge-bad');
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $row['score_label'] }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Critical and bad orders --}}
@if(count($quality['critical_and_bad_orders']))
<div class="section-title">Encomendas com Observações Críticas ou Más</div>
@foreach($quality['critical_and_bad_orders'] as $order)
<table class="dt" style="margin-bottom:8px">
    <thead>
        <tr>
            <th colspan="4" style="background:#6b1a50">
                {{ $order['tracking'] }}
                &nbsp;|&nbsp; {{ $order['client'] }}
                &nbsp;|&nbsp; {{ $order['destination'] }}
                &nbsp;|&nbsp; {{ $order['service_type'] }}
                &nbsp;|&nbsp; Resp: {{ $order['responsible'] }}
            </th>
        </tr>
        <tr>
            <th>Nível</th>
            <th>Descrição</th>
            <th>Registado por</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order['observations'] as $obs)
        <tr>
            <td><span class="badge badge-{{ $obs['level'] }}">{{ ucfirst($obs['level']) }}</span></td>
            <td>{{ $obs['description'] }}</td>
            <td>{{ $obs['created_by'] }}</td>
            <td>{{ $obs['created_at'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif

@endif {{-- end quality empty check --}}

@endsection