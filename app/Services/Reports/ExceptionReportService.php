<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\Observation;
use App\Models\TransitTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExceptionReportService
{
    // Weight per level for quality scoring
    private const LEVEL_WEIGHTS = [
        'good' => 4,
        'medium' => 3,
        'bad' => 2,
        'critical' => 1,
    ];

    // === Entry point ========

    public function generate(array $filters = []): array
    {
        $ordersWithoutClient = $this->getOrdersWithoutClient($filters);
        $ordersWithoutInvoice = $this->getOrdersWithoutInvoice($filters);
        $ordersWithoutDeclaredWeight = $this->getOrdersWithoutDeclaredWeight($filters);
        $ordersWithoutStatus = $this->getOrdersWithoutStatus($filters);
        $invoicesWithNullStatus = $this->getInvoicesWithNullStatus($filters);
        $delays = $this->getDelays($filters);
        $quality = $this->getQuality($filters);

        return [
            'orders_without_client' => $ordersWithoutClient,
            'orders_without_invoice' => $ordersWithoutInvoice,
            'orders_without_declared_weight' => $ordersWithoutDeclaredWeight,
            'orders_without_status' => $ordersWithoutStatus,
            'invoices_with_null_status' => $invoicesWithNullStatus,
            'delays' => $delays,
            'quality' => $quality,
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'summary' => [
                'total_without_client' => $ordersWithoutClient->count(),
                'total_without_invoice' => $ordersWithoutInvoice->count(),
                'total_without_declared_weight' => $ordersWithoutDeclaredWeight->count(),
                'total_without_status' => $ordersWithoutStatus->count(),
                'total_invoices_null_status' => $invoicesWithNullStatus->count(),
                'total_delayed' => $delays['summary']['total_delayed'],
                'quality_score' => $quality['score']['score'],
                'quality_label' => $quality['score']['label'],
            ],
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'filters_applied' => $filters,
        ];
    }

    // == Existing exception sections =============

    private function buildBaseQuery(array $filters)
    {
        return Order::query()
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('reception_date', '>=', $filters['date_from'])
<<<<<<< HEAD
            )->when(
=======
            )
            ->when(
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                isset($filters['date_to']),
                fn($q) => $q->whereDate('reception_date', '<=', $filters['date_to'])
            );
    }

    private function getOrdersWithoutClient(array $filters)
    {
        return $this->buildBaseQuery($filters)
<<<<<<< HEAD
            ->whereNull('client_id')->with([
                'responsible:id,name,user_code',
                'category:id,category',
                'store:id,name',
            ])->get(['id', 'tracking', 'origin', 'destination', 'reception_date',
                   'service_type', 'weight', 'responsible_id', 'category_id', 'store_id']);
=======
            ->whereNull('client_id')
            ->with([
                'responsible:id,name,lastname,user_code',
                'category:id,category',
                'store:id,name',
            ])
            ->get([
                'id',
                'tracking',
                'origin',
                'destination',
                'reception_date',
                'service_type',
                'weight',
                'responsible_id',
                'category_id',
                'store_id',
            ]);
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function getOrdersWithoutInvoice(array $filters)
    {
        return $this->buildBaseQuery($filters)
<<<<<<< HEAD
            ->whereNull('invoice_id')->with([
                'client:id,name,lastname',
                'responsible:id,name,user_code',
            ])->get(['id', 'tracking', 'origin', 'destination', 'reception_date',
                   'weight', 'client_id', 'responsible_id']);
=======
            ->whereNull('invoice_id')
            ->with([
                'client:id,name,lastname',
                'responsible:id,name,lastname,user_code',
            ])
            ->get([
                'id',
                'tracking',
                'origin',
                'destination',
                'reception_date',
                'weight',
                'client_id',
                'responsible_id',
            ]);
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function getOrdersWithoutDeclaredWeight(array $filters)
    {
        return $this->buildBaseQuery($filters)
<<<<<<< HEAD
            ->whereNull('declared_weight')->with([
                'client:id,name,lastname',
                'responsible:id,name,user_code',
            ])->get(['id', 'tracking', 'origin', 'destination', 'reception_date',
                   'weight', 'client_id', 'responsible_id']);
=======
            ->whereNull('declared_weight')
            ->with([
                'client:id,name,lastname',
                'responsible:id,name,lastname,user_code',
            ])
            ->get([
                'id',
                'tracking',
                'origin',
                'destination',
                'reception_date',
                'weight',
                'client_id',
                'responsible_id',
            ]);
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function getOrdersWithoutStatus(array $filters)
    {
        return $this->buildBaseQuery($filters)
<<<<<<< HEAD
            ->whereDoesntHave('statuses')->with([
                'client:id,name,lastname',
                'responsible:id,name,user_code',
            ])->get(['id', 'tracking', 'origin', 'destination', 'reception_date',
                   'weight', 'client_id', 'responsible_id']);
=======
            ->whereDoesntHave('statuses')
            ->with([
                'client:id,name,lastname',
                'responsible:id,name,lastname,user_code',
            ])
            ->get([
                'id',
                'tracking',
                'origin',
                'destination',
                'reception_date',
                'weight',
                'client_id',
                'responsible_id',
            ]);
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function getInvoicesWithNullStatus(array $filters)
    {
        return $this->buildBaseQuery($filters)
<<<<<<< HEAD
        ->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
=======
            ->join('invoices', 'orders.invoice_id', '=', 'invoices.id')
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            ->whereNull('invoices.payment_status')
            ->with([
                'client:id,name,lastname',
                'invoice',
            ])
<<<<<<< HEAD
            ->select('orders.id', 'orders.tracking', 'orders.reception_date',
                     'orders.destination', 'orders.client_id', 'orders.invoice_id')
            ->get();
    }

    // == Delays ===== 
=======
            ->select(
                'orders.id',
                'orders.tracking',
                'orders.reception_date',
                'orders.destination',
                'orders.client_id',
                'orders.invoice_id'
            )
            ->get();
    }

    // == Delays =====
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e

    private function getDelays(array $filters): array
    {
        $orders = Order::query()
            ->with([
                'client:id,name,lastname',
<<<<<<< HEAD
                'responsible:id,name,user_code',
                'store:id,name',
                'statuses:id,order_id,descryption,responsible_id,created_at',
                'statuses.responsible:id,name,user_code',
            ])
            ->whereHas('statuses', fn($q) =>
                $q->where('descryption', 'em_transito')
=======
                'responsible:id,name,lastname,user_code',
                'store:id,name',

                'statuses:id,order_id,descryption,responsible_id,created_at',

                'statuses.responsible:id,name,lastname,user_code',
            ])
            ->whereHas(
                'statuses',
                fn($q) => $q->where('descryption', 'em_transito')
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            )
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('reception_date', '>=', $filters['date_from'])
            )
            ->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('reception_date', '<=', $filters['date_to'])
            )
            ->get();

        $delayed = [];
        $onTime = [];
<<<<<<< HEAD
        $noConfig  = [];

        foreach ($orders as $order) {
=======
        $noConfig = [];

        foreach ($orders as $order) {

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            $analysis = $this->analyseDelay($order);

            if ($analysis === null) {
                $noConfig[] = $this->buildDelayRow($order, null);
                continue;
            }

            if ($analysis['is_delayed']) {
                $delayed[] = $this->buildDelayRow($order, $analysis);
            } else {
                $onTime[] = $this->buildDelayRow($order, $analysis);
            }
        }

        return [
            'summary' => [
                'total_analysed' => count($orders),
                'total_delayed' => count($delayed),
                'total_on_time' => count($onTime),
                'total_no_config' => count($noConfig),
            ],
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'delayed' => $delayed,
            'on_time' => $onTime,
            'no_config' => $noConfig,
        ];
    }

    private function analyseDelay(Order $order): ?array
    {
        $transitStatus = $order->statuses
            ->where('descryption', 'em_transito')
            ->sortBy('created_at')
            ->first();

<<<<<<< HEAD
        if (! $transitStatus) {
=======
        if (!$transitStatus) {
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            return null;
        }

        $transitConfig = $this->findTransitConfig($order);

<<<<<<< HEAD
        if (! $transitConfig) {
=======
        if (!$transitConfig) {
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            return null;
        }

        $transitStartedAt = Carbon::parse($transitStatus->created_at);
<<<<<<< HEAD
        $actualDeparture = $this->getNextDeparture($transitStartedAt, $transitConfig->departure_days);
        $deadline = $actualDeparture->copy()->addHours($transitConfig->expected_hours);
=======

        $actualDeparture = $this->getNextDeparture(
            $transitStartedAt,
            $transitConfig->departure_days
        );

        $deadline = $actualDeparture
            ->copy()
            ->addHours($transitConfig->expected_hours);
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e

        $deliveredStatus = $order->statuses
            ->where('descryption', 'entregue')
            ->sortByDesc('created_at')
            ->first();

        $referenceTime = $deliveredStatus
            ? Carbon::parse($deliveredStatus->created_at)
            : Carbon::now();

        $isDelayed = $referenceTime->isAfter($deadline);
<<<<<<< HEAD
        $delayHours = $isDelayed ? $deadline->diffInHours($referenceTime) : 0;
=======

        $delayHours = $isDelayed
            ? $deadline->diffInHours($referenceTime)
            : 0;

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        $elapsedHours = $actualDeparture->diffInHours($referenceTime);

        return [
            'transit_started_at' => $transitStartedAt->toDateTimeString(),
            'actual_departure_at' => $actualDeparture->toDateTimeString(),
            'deadline_at' => $deadline->toDateTimeString(),
            'delivered_at' => $deliveredStatus?->created_at,
<<<<<<< HEAD
            'elapsed_hours'=> $elapsedHours,
            'delay_hours'  => $delayHours,
            'expected_hours' => $transitConfig->expected_hours,
            'is_delayed' => $isDelayed,
            'is_delivered' => (bool) $deliveredStatus,
            'delivered_by' => $deliveredStatus?->responsible?->name,
=======
            'elapsed_hours' => $elapsedHours,
            'delay_hours' => $delayHours,
            'expected_hours' => $transitConfig->expected_hours,
            'is_delayed' => $isDelayed,
            'is_delivered' => (bool) $deliveredStatus,

            'delivered_by' => $deliveredStatus?->responsible?->full_name ?? '-',
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
        ];
    }

    private function findTransitConfig(Order $order): ?TransitTime
    {
<<<<<<< HEAD
        $serviceType = str_contains(strtolower($order->service_type), 'expresso')
=======
        $serviceType = str_contains(
            strtolower($order->service_type),
            'expresso'
        )
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            ? 'expresso'
            : 'normal';

        return TransitTime::query()
<<<<<<< HEAD
            ->whereHas('originCountry', fn($q) =>
                $q->where('name', 'like', "%{$order->origin}%")
                  ->orWhere('coin', $order->origin)
            )
            ->whereHas('destinationCountry', fn($q) =>
                $q->where('name', 'like', "%{$order->destination}%")
                  ->orWhere('coin', $order->destination)
=======
            ->whereHas(
                'originCountry',
                fn($q) =>
                $q->where('name', 'like', "%{$order->origin}%")
                    ->orWhere('coin', $order->origin)
            )
            ->whereHas(
                'destinationCountry',
                fn($q) =>
                $q->where('name', 'like', "%{$order->destination}%")
                    ->orWhere('coin', $order->destination)
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            )
            ->where('service_type', $serviceType)
            ->first();
    }

    private function getNextDeparture(Carbon $from, array $days): Carbon
    {
        $current = $from->copy();

        for ($i = 0; $i <= 7; $i++) {
<<<<<<< HEAD
            if (in_array((int) $current->format('N'), $days)) {
                return $current->startOfDay();
            }
=======

            if (in_array((int) $current->format('N'), $days)) {
                return $current->startOfDay();
            }

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            $current->addDay();
        }

        return $from->copy();
    }

    private function buildDelayRow(Order $order, ?array $analysis): array
    {
        return [
            'id' => $order->id,
            'tracking' => $order->tracking,
<<<<<<< HEAD
            'client' => $order->client?->full_name ?? '—',
            'origin' => $order->origin,
            'destination' => $order->destination,
            'service_type' => $order->service_type,
            'store' => $order->store?->name ?? '—',
            'responsible' => $order->responsible?->name ?? '—',
            'reception_date' => $order->reception_date?->format('d/m/Y'),
=======

            'client' => $order->client?->full_name ?? '-',

            'origin' => $order->origin,
            'destination' => $order->destination,
            'service_type' => $order->service_type,

            'store' => $order->store?->name ?? '-',

            'responsible' => $order->responsible?->full_name ?? '-',

            'reception_date' => $order->reception_date?->format('d/m/Y'),

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'analysis' => $analysis,
        ];
    }

    // === Quality ===========

    private function getQuality(array $filters): array
    {
        $observations = Observation::query()
            ->with([
<<<<<<< HEAD
                'creator:id,name,user_code',
                'order:id,tracking,destination,service_type,client_id',
=======
                'creator:id,name,lastname,user_code',

                'order:id,tracking,destination,service_type,client_id',

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                'order.client:id,name,lastname',
            ])
            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('created_at', '>=', $filters['date_from'])
            )
            ->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('created_at', '<=', $filters['date_to'])
            )
            ->get();

        if ($observations->isEmpty()) {
            return $this->emptyQuality();
        }

        return [
            'summary' => $this->buildQualitySummary($observations),
<<<<<<< HEAD
            'score' => $this->calculateScore($observations),
            'by_level' => $this->groupByLevel($observations),
            'by_responsible' => $this->groupByResponsible($observations),
            'critical_and_bad_orders' => $this->getCriticalOrders($filters),
=======

            'score' => $this->calculateScore($observations),

            'by_level' => $this->groupByLevel($observations),

            'by_responsible' => $this->groupByResponsible($observations),

            'critical_and_bad_orders' => $this->getCriticalOrders($filters),

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'trend' => $this->getQualityTrend($filters),
        ];
    }

    private function buildQualitySummary(Collection $observations): array
    {
        $counts = $observations
            ->groupBy(fn($obs) => $this->levelValue($obs))
            ->map->count();

        return [
            'total_observations' => $observations->count(),
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'total_good' => $counts->get('good', 0),
            'total_medium' => $counts->get('medium', 0),
            'total_bad' => $counts->get('bad', 0),
            'total_critical' => $counts->get('critical', 0),
        ];
    }

    private function calculateScore(Collection $observations): array
    {
<<<<<<< HEAD
        $totalWeight = $observations->sum(fn($obs) =>
=======
        $totalWeight = $observations->sum(
            fn($obs) =>
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            self::LEVEL_WEIGHTS[$this->levelValue($obs)] ?? 3
        );

        $maxWeight = $observations->count() * 4;
<<<<<<< HEAD
        $score     = $maxWeight > 0 ? round($totalWeight / $maxWeight * 4, 2) : 0;

        return [
            'score' => $score,
            'max_score' => 4.00,
            'percentage' => round($score / 4 * 100, 1),
=======

        $score = $maxWeight > 0
            ? round($totalWeight / $maxWeight * 4, 2)
            : 0;

        return [
            'score' => $score,

            'max_score' => 4.00,

            'percentage' => round($score / 4 * 100, 1),

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'label' => $this->scoreLabel($score),
        ];
    }

    private function groupByLevel(Collection $observations): array
    {
        return $observations
            ->groupBy(fn($obs) => $this->levelValue($obs))
<<<<<<< HEAD
            ->map(fn($group, $level) => [
                'level' => $level,
                'total' => $group->count(),
                'percentage' => round($group->count() / $observations->count() * 100, 1),
            ])->values()->toArray();
=======
            ->map(
                fn($group, $level) => [
                    'level' => $level,

                    'total' => $group->count(),

                    'percentage' => round(
                        $group->count() / $observations->count() * 100,
                        1
                    ),
                ]
            )
            ->values()
            ->toArray();
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function groupByResponsible(Collection $observations): array
    {
        return $observations
            ->groupBy(fn($obs) => $obs->creator?->id)
            ->map(function ($group) {
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                $levelCounts = $group
                    ->groupBy(fn($obs) => $this->levelValue($obs))
                    ->map->count();

                $score = $this->calculateScore($group);

                return [
<<<<<<< HEAD
                    'responsible' => $group->first()->creator?->name ?? '—',
                    'user_code' => $group->first()->creator?->user_code ?? '—',
                    'total' => $group->count(),
=======

                    'responsible' => $group->first()?->creator?->full_name ?? '-',

                    'user_code' => $group->first()?->creator?->user_code ?? '-',

                    'total' => $group->count(),

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                    'good' => $levelCounts->get('good', 0),
                    'medium' => $levelCounts->get('medium', 0),
                    'bad' => $levelCounts->get('bad', 0),
                    'critical' => $levelCounts->get('critical', 0),
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
                    'score' => $score['score'],
                    'score_label' => $score['label'],
                ];
            })
            ->sortBy('score')
            ->values()
            ->toArray();
    }

    private function getCriticalOrders(array $filters): array
    {
        return Order::query()
            ->with([
<<<<<<< HEAD
                'client:id,name,lastname',
                'responsible:id,name,user_code',
                'observations' => fn($q) => $q
                    ->whereIn('level', ['bad', 'critical'])
                    ->when(isset($filters['date_from']), fn($q) =>
                        $q->whereDate('created_at', '>=', $filters['date_from'])
                    )
                    ->when(isset($filters['date_to']), fn($q) =>
                        $q->whereDate('created_at', '<=', $filters['date_to'])
                    )
                    ->with('creator:id,name,user_code'),
            ])
            ->whereHas('observations', fn($q) =>
                $q->whereIn('level', ['bad', 'critical'])
                ->when(isset($filters['date_from']), fn($q) =>
                    $q->whereDate('created_at', '>=', $filters['date_from'])
                )
                ->when(isset($filters['date_to']), fn($q) =>
                    $q->whereDate('created_at', '<=', $filters['date_to'])
                )
            )
            ->get()
            ->map(fn($order) => [
                'tracking' => $order->tracking,
                'client' => $order->client?->full_name ?? '—',
                'destination' => $order->destination,
                'service_type' => $order->service_type,
                'responsible' => $order->responsible?->name ?? '—',
                'observations' => $order->observations->map(fn($obs) => [
                    'level' => $this->levelValue($obs),
                    'description' => $obs->description,
                    'created_by' => $obs->creator?->name ?? '—',
                    'created_at' => Carbon::parse($obs->created_at)->format('d/m/Y H:i'),
                ])->toArray(),
            ])->toArray();
=======

                'client:id,name,lastname',

                'responsible:id,name,lastname,user_code',

                'observations' => fn($q) => $q
                    ->whereIn('level', ['bad', 'critical'])

                    ->when(
                        isset($filters['date_from']),
                        fn($q) =>
                        $q->whereDate('created_at', '>=', $filters['date_from'])
                    )

                    ->when(
                        isset($filters['date_to']),
                        fn($q) =>
                        $q->whereDate('created_at', '<=', $filters['date_to'])
                    )

                    ->with('creator:id,name,lastname,user_code'),
            ])
            ->whereHas(
                'observations',
                fn($q) => $q
                    ->whereIn('level', ['bad', 'critical'])

                    ->when(
                        isset($filters['date_from']),
                        fn($q) =>
                        $q->whereDate('created_at', '>=', $filters['date_from'])
                    )

                    ->when(
                        isset($filters['date_to']),
                        fn($q) =>
                        $q->whereDate('created_at', '<=', $filters['date_to'])
                    )
            )
            ->get()

            ->map(fn($order) => [

                'tracking' => $order->tracking,

                'client' => $order->client?->full_name ?? '-',

                'destination' => $order->destination,

                'service_type' => $order->service_type,

                'responsible' => $order->responsible?->full_name ?? '-',

                'observations' => $order->observations
                    ->map(fn($obs) => [

                        'level' => $this->levelValue($obs),

                        'description' => $obs->description,

                        'created_by' => $obs->creator?->full_name ?? '-',

                        'created_at' => Carbon::parse(
                            $obs->created_at
                        )->format('d/m/Y H:i'),

                    ])
                    ->toArray(),

            ])
            ->toArray();
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    private function getQualityTrend(array $filters): array
    {
        return Observation::query()
            ->selectRaw('DATE(created_at) as date, level, COUNT(*) as total')
<<<<<<< HEAD
            ->when(isset($filters['date_from']),
                fn($q) => $q->whereDate('created_at', '>=', $filters['date_from'])
            )
            ->when(isset($filters['date_to']),
                fn($q) => $q->whereDate('created_at', '<=', $filters['date_to'])
            )
            ->groupBy('date', 'level')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(fn($group, $date) => [
                'date' => $date,
                'good' => $group->where('level', 'good')->sum('total'),
                'medium' => $group->where('level', 'medium')->sum('total'),
                'bad' => $group->where('level', 'bad')->sum('total'),
                'critical' => $group->where('level', 'critical')->sum('total'),
            ]) ->values()->toArray();
=======

            ->when(
                isset($filters['date_from']),
                fn($q) => $q->whereDate('created_at', '>=', $filters['date_from'])
            )

            ->when(
                isset($filters['date_to']),
                fn($q) => $q->whereDate('created_at', '<=', $filters['date_to'])
            )

            ->groupBy('date', 'level')

            ->orderBy('date')

            ->get()

            ->groupBy('date')

            ->map(fn($group, $date) => [

                'date' => $date,

                'good' => $group->where('level', 'good')->sum('total'),

                'medium' => $group->where('level', 'medium')->sum('total'),

                'bad' => $group->where('level', 'bad')->sum('total'),

                'critical' => $group->where('level', 'critical')->sum('total'),

            ])
            ->values()
            ->toArray();
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
    }

    // == Helpers ===============

    private function levelValue(Observation $obs): string
    {
        return $obs->level instanceof \App\Enums\ObservationLevelEnum
            ? $obs->level->value
            : (string) $obs->level;
    }

    private function scoreLabel(float $score): string
    {
<<<<<<< HEAD
        return match(true) {
            $score >= 3.5 => 'Excelente',
            $score >= 2.5 => 'Bom',
            $score >= 1.5 => 'Regular',
=======
        return match (true) {

            $score >= 3.5 => 'Excelente',

            $score >= 2.5 => 'Bom',

            $score >= 1.5 => 'Regular',

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            default => 'Crítico',
        };
    }

    private function emptyQuality(): array
    {
        return [
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'summary' => [
                'total_observations' => 0,
                'total_good' => 0,
                'total_medium' => 0,
                'total_bad' => 0,
                'total_critical' => 0,
            ],
<<<<<<< HEAD
=======

>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
            'score' => [
                'score' => 0,
                'max_score' => 4.00,
                'percentage' => 0,
                'label' => 'Sem dados',
            ],
<<<<<<< HEAD
            'by_level' => [],
            'by_responsible' => [],
            'critical_and_bad_orders' => [],
            'trend' => [],
        ];
    }
}
=======

            'by_level' => [],

            'by_responsible' => [],

            'critical_and_bad_orders' => [],

            'trend' => [],
        ];
    }
}
>>>>>>> 88c6fdd8e6eff657ef79fa59c15942cbc1402a9e
