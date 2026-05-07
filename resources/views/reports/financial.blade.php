<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        h1 {
            font-size: 16px;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 16px;
        }

        .summary {
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
        }

        .summary-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            flex: 1;
        }

        .summary-box .label {
            font-size: 9px;
            color: #999;
            text-transform: uppercase;
        }

        .summary-box .value {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a2e;
        }

        .value.green {
            color: #2e7d32;
        }

        .value.red {
            color: #c62828;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #1a1a2e;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            padding: 5px 8px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .badge-paid {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .badge-pendent {
            background: #fff8e1;
            color: #f57f17;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .badge-faild {
            background: #ffebee;
            color: #c62828;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #999;
            text-align: right;
        }
    </style>
</head>

<body>
    <h1>Portador Diário — Relatório Financeiro</h1>
    <div class="subtitle">
        Período: {{ $filters['date_from'] ?? 'Início' }} a {{ $filters['date_to'] ?? 'Hoje' }}
        &nbsp;|&nbsp; Gerado em: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-box">
                <div class="label">Total a Cobrar</div>
                <div class="value">{{ number_format($summary['total_to_pay'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Total Cobrado</div>
                <div class="value green">{{ number_format($summary['total_paid'], 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Total em Dívida</div>
                <div class="value red">{{ number_format($summary['total_debt'], 2) }}</div>
            </div>
        </div>
        <div class="summary-row">
            <div class="summary-box">
                <div class="label">Encomendas Pagas</div>
                <div class="value green">{{ $summary['total_paid_orders'] }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Encomendas Pendentes</div>
                <div class="value">{{ $summary['total_pendent_orders'] }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Pagamentos Falhados</div>
                <div class="value red">{{ $summary['total_failed_orders'] }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tracking</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Destino</th>
                <th>A Pagar</th>
                <th>Pago</th>
                <th>Saldo</th>
                <th>Estado</th>
                <th>Método</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->tracking }}</td>
                <td> {{ ($order['client']['name'] ?? '') . ' ' . ($order['client']['lastname'] ?? '') }}</td>
                <td>{{ \Carbon\Carbon::parse($order['reception_date'])->format('d/m/Y') }}</td>
                <td>{{ $order->destination }}</td>
                <td>{{ number_format($order->invoice?->amountTo_pay ?? 0, 2) }}</td>
                <td>{{ number_format($order->invoice?->amount_paid ?? 0, 2) }}</td>
                <td>{{ number_format(($order->invoice?->amountTo_pay ?? 0) - ($order->invoice?->amount_paid ?? 0), 2) }}</td>
                <td>
                    <span class="badge-{{ $order->invoice?->payment_status ?? 'pendent' }}">
                        {{ $order->invoice?->payment_status ?? 'N/A' }}
                    </span>
                </td>
                <td>{{ $order->invoice?->payment_method ?? 'N/A' }}</td>
                <td>{{ $order->responsible?->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Portador Diário, Lda — Maputo, Moçambique</div>
</body>

</html>