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

{{-- REPORT TITLE --}}
<div class="report-title">Relatório de Excepções - Portador Diário</div>
<div class="report-meta">
    Período:
    <strong>{{ isset($filters['date_from']) ? \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') : 'Início' }}</strong>
    a
    <strong>{{ isset($filters['date_to']) ? \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
    &nbsp;|&nbsp; Gerado em: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
</div>

{{-- SUMMARY --}}
<div class="summary-wrap">
    <div class="summary-box">
        <span class="s-label">Em Atraso</span>
        <span class="s-value {{ $summary['total_delayed'] > 0 ? 'red' : 'green' }}">{{ $summary['total_delayed'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Analisadas</span>
        <span class="s-value">{{ $delays['summary']['total_analysed'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">A Tempo</span>
        <span class="s-value green">{{ $delays['summary']['total_on_time'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Sem Config.</span>
        <span class="s-value">{{ $delays['summary']['total_no_config'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Total Obs.</span>
        <span class="s-value">{{ $quality['summary']['total_observations'] }}</span>
    </div>
    <div class="summary-box">
        <span class="s-label">Obs. Críticas</span>
        <span class="s-value {{ $quality['summary']['total_critical'] > 0 ? 'red' : 'green' }}">{{ $quality['summary']['total_critical'] }}</span>
    </div>
</div>

{{-- SECTION 1: DELAYS --}}
<div class="exc-header">
    <div class="exc-header-title">1. Encomendas em Atraso</div>
    <div class="exc-count">
        <span class="{{ $delays['summary']['total_delayed'] === 0 ? 'zero' : '' }}">
            {{ $delays['summary']['total_delayed'] }} em atraso
            / {{ $delays['summary']['total_analysed'] }} analisadas
        </span>
    </div>
</div>

@if($delays['summary']['total_no_config'] > 0)
<div class="empty-state" style="margin-bottom:6px">
    ⚠ {{ $delays['summary']['total_no_config'] }} encomenda(s) sem configuração de rota definida — não foram analisadas.
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
            <th>Loja</th>
            <th>Responsável</th>
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
            <td>{{ $row['store'] ?? '-' }}</td>
            <td>{{ $row['responsible'] }}</td>
            <td>{{ $row['analysis']['actual_departure_at'] ? \Carbon\Carbon::parse($row['analysis']['actual_departure_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $row['analysis']['deadline_at'] ? \Carbon\Carbon::parse($row['analysis']['deadline_at'])->format('d/m/Y H:i') : '-' }}</td>
            <td style="color:#b52222;font-weight:bold">+{{ $row['analysis']['delay_hours'] }}h</td>
            <td>{{ $row['analysis']['is_delivered'] ? 'Sim' : 'Não' }}</td>
            <td>{{ $row['analysis']['delivered_by'] ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- SECTION 2: OBSERVAÇÕES --}}
<div class="exc-header" style="margin-top:16px">
    <div class="exc-header-title">2. Observações por Colaborador</div>
    <div class="exc-count">
        <span class="{{ $quality['summary']['total_observations'] === 0 ? 'zero' : '' }}">
            {{ $quality['summary']['total_observations'] }} observação(ões)
        </span>
    </div>
</div>

@if($quality['summary']['total_observations'] === 0)
<div class="empty-state">Nenhuma observação registada no período seleccionado.</div>
@else

{{-- Por colaborador --}}
@if(count($quality['by_responsible']))
<table class="dt" style="margin-bottom:16px">
    <thead>
        <tr>
            <th>Colaborador</th>
            <th>Código</th>
            <th>Total</th>
            <th>Bom</th>
            <th>Médio</th>
            <th>Mau</th>
            <th>Crítico</th>
            <th>Pontuação</th>
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
                $badgeClass = $row['score'] >= 3.5 ? 'badge-good' : ($row['score'] >= 2.5 ? 'badge-medium' : 'badge-bad');
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $row['score_label'] }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Encomendas com obs. críticas ou más --}}
@if(count($quality['critical_and_bad_orders']))
<div class="section-title">Encomendas com Observações Críticas ou Más</div>
@foreach($quality['critical_and_bad_orders'] as $order)
<table class="dt" style="margin-bottom:8px">
    <thead>
        <tr>
            <th colspan="4" style="background:#ffff; color:#962479;">
                {{ $order['tracking'] }}
                &nbsp;|&nbsp; {{ $order['client'] }}
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

@endif {{-- end observations check --}}

@endsection