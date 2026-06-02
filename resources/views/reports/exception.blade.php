@extends('reports.layout')

@php
    function translateLevel($level) {
        return match($level) {
            'bad'      => 'Mau',
            'Bad'      => 'Mau',
            'critical' => 'Crítico',
            'medium'   => 'Médio',
            'good'     => 'Bom',
            default    => ucfirst($level ?? 'N/A')
        };
    }
@endphp

@section('content')

<div class="report-title">Relatório de Excepções - Portador Diário</div>
<div class="report-meta">
    Período:
    <strong>{{ isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'Início' }}</strong>
    a
    <strong>{{ isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
    &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
</div>

<div class="summary-wrap">
    <div class="summary-box">
        <span class="s-label">Em Trânsito (Atrasadas)</span>
        <span class="s-value {{ $summary['total_in_transit_delayed'] > 0 ? 'red' : 'green' }}">{{ $summary['total_in_transit_delayed'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Chegaram em Atraso</span>
        <span class="s-value {{ $summary['total_arrived_late'] > 0 ? 'red' : 'green' }}">{{ $summary['total_arrived_late'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Paradas (+7 dias)</span>
        <span class="s-value {{ $summary['total_stalled'] > 0 ? 'red' : 'green' }}">{{ $summary['total_stalled'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Obs. Críticas</span>
        <span class="s-value {{ $summary['total_critical_obs'] > 0 ? 'red' : 'green' }}">{{ $summary['total_critical_obs'] }}</span>
    </div>
</div>

{{-- SECÇÃO 1 --}}
<div class="exc-header">
    <div class="exc-header-title">1. Encomendas em Trânsito e Atrasadas</div>
    <div class="exc-count">
        <span class="{{ $delays['summary']['total_delayed'] === 0 ? 'zero' : '' }}">
            {{ $delays['summary']['total_delayed'] }} atrasadas / {{ $delays['summary']['total_in_transit'] }} em trânsito
        </span>
    </div>
</div>


@if(empty($delays['delayed']))
<div class="empty-state">Nenhuma encomenda em trânsito com atraso.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Loja</th>
            <th>Responsável</th>
            <th>Saída Prevista</th>
            <th>Prazo Limite</th>
            <th>Horas Atraso</th>
        </tr>
    </thead>
    <tbody>
        @foreach($delays['delayed'] as $row)
        <tr>
            <td>{{ $row['tracking'] }}</td>
            <td>{{ $row['client'] }}</td>
            <td>{{ $row['store'] ?? '-' }}</td>
            <td>{{ $row['responsible'] }}</td>
            <td>{{ $row['analysis']['actual_departure_at'] ? \Carbon\Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $row['analysis']['deadline_at'] ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td style="color:#b52222;font-weight:bold">+{{ $row['analysis']['delay_hours'] }}h</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECÇÃO 2 --}}
<div class="exc-header" style="margin-top:16px">
    <div class="exc-header-title">2. Encomendas que Chegaram em Atraso</div>
    <div class="exc-count">
        <span class="{{ $delays['summary']['total_arrived_late'] === 0 ? 'zero' : '' }}">
            {{ $delays['summary']['total_arrived_late'] }} em atraso / {{ $delays['summary']['total_arrived_on_time'] }} a tempo
        </span>
    </div>
</div>

@if(empty($delays['arrived_late']))
<div class="empty-state">Nenhuma encomenda chegou em atraso no período.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Loja</th>
            <th>Responsável</th>
            <th>Prazo Limite</th>
            <th>Chegou em</th>
            <th>Horas Atraso</th>
        </tr>
    </thead>
    <tbody>
        @foreach($delays['arrived_late'] as $row)
        <tr>
            <td>{{ $row['tracking'] }}</td>
            <td>{{ $row['client'] }}</td>
            <td>{{ $row['store'] ?? '-' }}</td>
            <td>{{ $row['responsible'] }}</td>
            <td>{{ $row['analysis']['deadline_at'] ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $row['analysis']['delivered_at'] ? \Carbon\Carbon::parse($row['analysis']['delivered_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td style="color:#b52222;font-weight:bold">+{{ $row['analysis']['delay_hours'] }}h</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECÇÃO 3 --}}
<div class="exc-header" style="margin-top:16px">
    <div class="exc-header-title">3. Encomendas Paradas (sem actualização há +7 dias)</div>
    <div class="exc-count">
        <span class="{{ count($stalled) === 0 ? 'zero' : '' }}">
            {{ count($stalled) }} encomenda(s) parada(s)
        </span>
    </div>
</div>

@if(empty($stalled))
<div class="empty-state">Nenhuma encomenda parada no período.</div>
@else
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Tracking</th>
            <th>Cliente</th>
            <th>Loja</th>
            <th>Responsável</th>
            <th>Último Estado</th>
            <th>Última Actualização</th>
            <th>Dias Parada</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stalled as $row)
        <tr>
            <td>{{ $row['tracking'] }}</td>
            <td>{{ $row['client'] }}</td>
            <td>{{ $row['store'] }}</td>
            <td>{{ $row['responsible'] }}</td>
            <td>{{ $row['last_status'] }}</td>
            <td>{{ $row['last_update'] }}</td>
            <td style="color:#b52222;font-weight:bold">{{ $row['days_stalled'] }} dias</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECÇÃO 4 --}}
<div class="exc-header" style="margin-top:16px">
    <div class="exc-header-title">4. Observações Críticas e Más</div>
    <div class="exc-count">
        <span class="{{ $quality['summary']['total_critical'] + $quality['summary']['total_bad'] === 0 ? 'zero' : '' }}">
            {{ $quality['summary']['total_critical'] }} críticas / {{ $quality['summary']['total_bad'] }} más
        </span>
    </div>
</div>

@if(empty($quality['critical_and_bad_orders']))
<div class="empty-state">Nenhuma observação crítica ou má no período.</div>
@else
@foreach($quality['critical_and_bad_orders'] as $order)
<table class="dt" style="margin-bottom:8px">
    <thead>
        <tr>
            <th colspan="4" style="background:#fff;color:#962479;">
                {{ $order['tracking'] }} &nbsp;|&nbsp; {{ $order['client'] }} &nbsp;|&nbsp; Resp: {{ $order['responsible'] }}
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
            <td><span class="badge badge-{{ $obs['level'] }}">{{ translateLevel($obs['level']) }}</span></td>
            <td>{{ $obs['description'] }}</td>
            <td>{{ $obs['created_by'] }}</td>
            <td>{{ $obs['created_at'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif

@endsection