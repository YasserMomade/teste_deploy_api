@extends('reports.layout')

@php
function translateStatus($status) {
return match($status) {
'paid' => 'Pago',
'pendent' => 'Pendente',
default => ucfirst($status ?? 'N/A')
};
}

function paymentMethod($method) {
return match($method) {
'cash' => 'Dinheiro',
'card' => 'Cartão',
default => ucfirst($method ?? '-'),
};
}

@endphp

@section('content')


{{-- REPORT TITLE --}}
<div class="report-title">Relatório Financeiro - Portador Diário</div>
<div class="report-meta">
    Período:
    <strong>{{ isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'Início' }}</strong>
    a
    <strong>{{ isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
    @if(isset($filters['payment_status'])) &nbsp;|&nbsp; Estado: <strong>{{ ucfirst($filters['payment_status']) }}</strong> @endif
    @if(isset($filters['payment_method'])) &nbsp;|&nbsp; Método: <strong>{{ ucfirst($filters['payment_method']) }}</strong> @endif
    &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
</div>

{{-- SUMMARY BOXES --}}
<div class="summary-wrap">
    <div class="summary-box">
        <span class="s-label">Total Processado</span>
        <span class="s-value">{{ number_format($summary['total_to_pay'], 2) . ' €' }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Total Cobrado</span>
        <span class="s-value green">{{ number_format($summary['total_paid'], 2) . ' €' }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Total em Dívida</span>
        <span class="s-value red">{{ number_format($summary['total_debt'], 2) . ' €'}}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Encomendas Pagas</span>
        <span class="s-value green">{{ $summary['total_paid_orders'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Pendentes</span>
        <span class="s-value">{{ $summary['total_pendent_orders'] }}</span>
    </div>
</div>

{{-- BY PAYMENT STATUS & METHOD --}}
<table style="width:100%;border-collapse:separate;border-spacing:8px 0;margin-bottom:4px">
    <tr>
        <td style="vertical-align:top;width:55%;padding:0">
            @if(count($by_payment_status))
            <div class="section-title">Por Estado de Pagamento</div>
            <table class="dt">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Nº Encomendas</th>
                        <th>Total Cobrar</th>
                        <th>Total Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_payment_status as $row)
                    <tr>
                        <td><span class="badge badge-{{ $row['status'] ?? 'pendent' }}">{{ translateStatus($row['status'] ?? 'N/A') }}</span></td>
                        <td>{{ $row['total_orders'] }}</td>
                        <td>{{ number_format($row['total_amount'], 2) }}</td>
                        <td>{{ number_format($row['total_paid'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </td>
        <td style="vertical-align:top;width:45%;padding:0">
            @if(count($by_payment_method))
            <div class="section-title">Por Método de Pagamento</div>
            <table class="dt">
                <thead>
                    <tr>
                        <th>Método</th>
                        <th>Nº Encomendas</th>
                        <th>Total Cobrado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_payment_method as $row)

                    @if(!empty($row['method']) && $row['method'] !== 'undefined')

                    <tr>
                        <td>{{ paymentMethod($row['method']) }}</td>
                        <td>{{ $row['total_orders'] }}</td>
                        <td>{{ number_format($row['total_collected'], 2) . ' €' }}</td>
                    </tr>

                    @endif

                    @endforeach
                </tbody>
            </table>
            @endif
        </td>
    </tr>
</table>

{{-- DAILY TOTALS --}}
@if(count($daily_totals))
<div class="section-title">Totais Diários</div>
<table class="dt">
    <thead>
        <tr>
            <th>Data</th>
            <th>Nº Encomendas</th>
            <th>Total a Cobrar</th>
            <th>Total Pago</th>
            <th>Peso Total (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($daily_totals as $row)
        <tr>
            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
            <td>{{ $row['total_orders'] }}</td>
            <td>{{ number_format($row['total_to_pay'], 2) . ' €' }}</td>
            <td>{{ ($order->invoice?->amount_paid ?? 0) > 0 ? number_format($order->invoice->amount_paid, 2) . ' €' : '-'}}</td>
            <td>{{ number_format($row['total_weight'], 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- DETAIL TABLE --}}
<div class="section-title">Detalhe Financeiro das Encomendas</div>
<table class="dt">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Data Encomenda</th>
            <th>Data Pagamento</th>
            <th>A Pagar</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th>Método</th>
            <th>Referência</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->tracking }}</td>
            <td> {{ ($order['client']['name'] ?? '') . ' ' . ($order['client']['lastname'] ?? '') }}</td>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
            <td> {{ $order->invoice?->paid_at ? \Carbon\Carbon::parse($order->invoice->paid_at)->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ number_format($order->invoice?->amountTo_pay ?? 0, 2) . ' €'}}</td>
            <td>{{ ($order->invoice?->amount_paid ?? 0) > 0 ? number_format($order->invoice->amount_paid, 2) . ' €' : '-'}}</td>
            <td>{{ number_format(($order->invoice?->amountTo_pay ?? 0) - ($order->invoice?->amount_paid ?? 0), 2) . ' €' }}</td>
            <td>
                @php $st = $order->invoice?->payment_status ?? 'pendent' @endphp
                <span class="badge badge-{{ $st }}">{{ translateStatus($st) }}</span>
            </td>
            <td>
                {{ (!empty($order->invoice?->payment_method) && $order->invoice?->payment_method !== 'undefined')
        ? paymentMethod($order->invoice->payment_method)
        : '-' }}
            </td>

            <td>{{ $order->invoice?->referencie ?? '-' }}</td>
            <td>{{ ($order->responsible?->name ?? '') . ' ' . ($order->responsible?->lastname ?? '') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="empty-state">Nenhuma encomenda encontrada para os filtros seleccionados.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection