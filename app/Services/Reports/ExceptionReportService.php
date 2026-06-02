<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\Observation;
use App\Models\TransitTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExceptionReportService
{
    private const STALLED_DAYS = 7;

    public function generate(array $filters = []): array
    {
        $delays  = $this->getDelays($filters);
        $stalled = $this->getStalledOrders($filters);
        $quality = $this->getQuality($filters);

        return [
            'delays'   => $delays,
            'stalled'  => $stalled,
            'quality'  => $quality,
            'summary'  => [
                'total_in_transit_delayed' => $delays['summary']['total_delayed'],
                'total_arrived_late'       => $delays['summary']['total_arrived_late'],
                'total_stalled'            => count($stalled),
                'total_critical_obs'       => $quality['summary']['total_critical'],
            ],
            'filters_applied' => $filters,
        ];
    }

    private function buildBaseQuery(array $filters)
    {
        return Order::query()
            ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']));
    }

    private function withRelations()
    {
        return [
            'client:id,name,lastname',
            'responsible:id,name,lastname,user_code,counter_id',
            'responsible.counter:id,country_id',
            'responsible.counter.country:id,name',
            'store:id,name',
            'statuses:id,order_id,descryption,responsible_id,created_at',
            'statuses.responsible:id,name,lastname,user_code',
        ];
    }

    private function getDelays(array $filters): array
    {
        $inTransit = Order::query()
            ->with($this->withRelations())
            ->whereHas('statuses', fn($q) => $q->where('descryption', 'em_transito'))
            ->whereDoesntHave('statuses', fn($q) => $q->whereIn('descryption', ['recebido_mocambique', 'pronto_levantamento', 'entregue']))
            ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->get();

        $arrived = Order::query()
            ->with($this->withRelations())
            ->whereHas('statuses', fn($q) => $q->where('descryption', 'em_transito'))
            ->whereHas('statuses',  fn($q) => $q->whereIn('descryption', ['recebido_mocambique', 'pronto_levantamento', 'entregue']))
            ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->get();

        $delayed      = [];
        $onTime       = [];
        $noConfig     = [];
        $arrivedLate  = [];
        $arrivedOnTime = [];

        foreach ($inTransit as $order) {
            $analysis = $this->analyseDelay($order);
            if ($analysis === null) {
                $noConfig[] = $this->buildDelayRow($order, null);
                continue;
            }
            $analysis['is_delayed'] ? $delayed[] = $this->buildDelayRow($order, $analysis)
                : $onTime[]  = $this->buildDelayRow($order, $analysis);
        }

        foreach ($arrived as $order) {
            $analysis = $this->analyseDelay($order);
            if ($analysis === null) continue;
            $analysis['is_delayed'] ? $arrivedLate[]   = $this->buildDelayRow($order, $analysis)
                : $arrivedOnTime[] = $this->buildDelayRow($order, $analysis);
        }

        return [
            'summary' => [
                'total_in_transit'     => count($inTransit),
                'total_delayed'        => count($delayed),
                'total_on_time'        => count($onTime),
                'total_no_config'      => count($noConfig),
                'total_arrived_late'   => count($arrivedLate),
                'total_arrived_on_time' => count($arrivedOnTime),
            ],
            'delayed'         => $delayed,
            'on_time'         => $onTime,
            'no_config'       => $noConfig,
            'arrived_late'    => $arrivedLate,
            'arrived_on_time' => $arrivedOnTime,
        ];
    }

    private function analyseDelay(Order $order): ?array
    {
        $transitStatus = $order->statuses->where('descryption', 'em_transito')->sortBy('created_at')->first();
        if (!$transitStatus) return null;

        $transitConfig = $this->findTransitConfig($order);
        if (!$transitConfig) return null;

        $transitStartedAt = Carbon::parse($transitStatus->created_at);
        $actualDeparture  = $this->getNextDeparture($transitStartedAt, $transitConfig->departure_days);
        $deadline         = $actualDeparture->copy()->addHours($transitConfig->expected_hours);

        $finalStatuses = ['entregue', 'recebido_mocambique', 'pronto_levantamento'];
        $deliveredStatus = $order->statuses->whereIn('descryption', $finalStatuses)->sortByDesc('created_at')->first();
        $referenceTime   = $deliveredStatus ? Carbon::parse($deliveredStatus->created_at) : Carbon::now();

        $isDelayed    = $referenceTime->isAfter($deadline);
        $delayHours   = $isDelayed ? $deadline->diffInHours($referenceTime) : 0;
        $elapsedHours = $actualDeparture->diffInHours($referenceTime);

        return [
            'transit_started_at'  => $transitStartedAt->toDateTimeString(),
            'actual_departure_at' => $actualDeparture->toDateTimeString(),
            'deadline_at'         => $deadline->toDateTimeString(),
            'delivered_at'        => $deliveredStatus?->created_at,
            'elapsed_hours'       => $elapsedHours,
            'delay_hours'         => $delayHours,
            'expected_hours'      => $transitConfig->expected_hours,
            'is_delayed'          => $isDelayed,
            'is_delivered'        => (bool) $deliveredStatus,
            'delivered_by'        => $deliveredStatus?->responsible?->full_name ?? '-',
        ];
    }

    private function findTransitConfig(Order $order): ?TransitTime
    {
        $originCountryId = $order->responsible?->counter?->country_id;
        if (!$originCountryId) return null;
        return TransitTime::query()->where('origin_country_id', $originCountryId)->first();
    }

    private function getNextDeparture(Carbon $from, array $days): Carbon
    {
        $current = $from->copy();
        for ($i = 0; $i <= 7; $i++) {
            if (in_array((int) $current->format('N'), $days)) return $current->startOfDay();
            $current->addDay();
        }
        return $from->copy();
    }

    private function buildDelayRow(Order $order, ?array $analysis): array
    {
        return [
            'id'          => $order->id,
            'tracking'    => $order->tracking,
            'client'      => $order->client?->full_name ?? '-',
            'store'       => $order->store?->name ?? '-',
            'responsible' => $order->responsible?->full_name ?? '-',
            'created_at'  => $order->created_at?->format('d/m/Y'),
            'analysis'    => $analysis,
        ];
    }

    private function getStalledOrders(array $filters): array
    {
        $cutoff = Carbon::now()->subDays(self::STALLED_DAYS);

        $orders = $this->buildBaseQuery($filters)
            ->with([
                'client:id,name,lastname',
                'responsible:id,name,lastname,user_code',
                'store:id,name',
                'statuses:id,order_id,descryption,created_at',
            ])
            ->whereHas('statuses')
            ->whereDoesntHave('statuses', fn($q) => $q->whereIn('descryption', ['entregue', 'recebido_mocambique', 'pronto_levantamento']))
            ->get()
            ->filter(function ($order) use ($cutoff) {
                $lastStatus = $order->statuses->sortByDesc('created_at')->first();
                return $lastStatus && Carbon::parse($lastStatus->created_at)->isBefore($cutoff);
            });

        return $orders->map(function ($order) {
            $lastStatus = $order->statuses->sortByDesc('created_at')->first();
            return [
                'id'           => $order->id,
                'tracking'     => $order->tracking,
                'client'       => $order->client?->full_name ?? '-',
                'store'        => $order->store?->name ?? '-',
                'responsible'  => $order->responsible?->full_name ?? '-',
                'last_status'  => $lastStatus?->descryption ?? '-',
                'last_update'  => $lastStatus ? Carbon::parse($lastStatus->created_at)->format('d/m/Y') : '-',
                'days_stalled' => $lastStatus ? Carbon::parse($lastStatus->created_at)->diffInDays(Carbon::now()) : '-',
            ];
        })->values()->toArray();
    }

    private function getQuality(array $filters): array
    {
        $observations = Observation::query()
            ->with(['creator:id,name,lastname,user_code', 'order:id,tracking,client_id', 'order.client:id,name,lastname'])
            ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->get();

        if ($observations->isEmpty()) {
            return [
                'summary'              => ['total_observations' => 0, 'total_good' => 0, 'total_medium' => 0, 'total_bad' => 0, 'total_critical' => 0],
                'critical_and_bad_orders' => [],
            ];
        }

        $counts = $observations->groupBy(fn($obs) => $this->levelValue($obs))->map->count();

        return [
            'summary' => [
                'total_observations' => $observations->count(),
                'total_good'         => $counts->get('good', 0),
                'total_medium'       => $counts->get('medium', 0),
                'total_bad'          => $counts->get('bad', 0),
                'total_critical'     => $counts->get('critical', 0),
            ],
            'critical_and_bad_orders' => $this->getCriticalOrders($filters),
        ];
    }

    private function getCriticalOrders(array $filters): array
    {
        return Order::query()
            ->with([
                'client:id,name,lastname',
                'responsible:id,name,lastname,user_code',
                'observations' => fn($q) => $q
                    ->whereIn('level', ['bad', 'critical'])
                    ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
                    ->with('creator:id,name,lastname,user_code'),
            ])
            ->whereHas(
                'observations',
                fn($q) => $q
                    ->whereIn('level', ['bad', 'critical'])
                    ->when(isset($filters['date_from']), fn($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
                    ->when(isset($filters['date_to']),   fn($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            )
            ->get()
            ->map(fn($order) => [
                'tracking'     => $order->tracking,
                'client'       => $order->client?->full_name ?? '-',
                'responsible'  => $order->responsible?->full_name ?? '-',
                'observations' => $order->observations->map(fn($obs) => [
                    'level'       => $this->levelValue($obs),
                    'description' => $obs->description,
                    'created_by'  => $obs->creator?->full_name ?? '-',
                    'created_at'  => Carbon::parse($obs->created_at)->format('d/m/Y H:i'),
                ])->toArray(),
            ])
            ->toArray();
    }

    private function levelValue(Observation $obs): string
    {
        return $obs->level instanceof \App\Enums\ObservationLevelEnum
            ? $obs->level->value
            : (string) $obs->level;
    }
}
