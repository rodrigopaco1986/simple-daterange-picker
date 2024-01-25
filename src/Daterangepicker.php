<?php

namespace Rpj\Daterangepicker;

use Carbon\Carbon;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rpj\Daterangepicker\DateHelper as Helper;

class Daterangepicker extends Filter
{
    private array $periods = [];
    private bool|string $minDate = false;
    private bool|string $maxDate = false;

    public function __construct(
        private string $column,
        private string $default = Helper::TODAY,
    ) {
    }

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'daterangepicker';

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        [$start, $end] = Helper::getParsedDatesGroupedRanges($value);

        if ($start && $end) {
            return $query->whereBetween($this->column, [$start, $end])
                ->orderBy('id', 'desc');
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @return array
     */
    public function options(NovaRequest $request)
    {
        if (empty($this->periods)) {
            $this->setPeriods([
                'Today' => [Carbon::today(), Carbon::today()],
                'Yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
                'This week' => [
                    Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(),
                ],
                'Last 7 days' => [Carbon::now()->subDays(6), Carbon::now()],
                'Last 30 days' => [Carbon::now()->subDays(29), Carbon::now()],
                'This month' => [
                    Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(),
                ],
                'Last month' => [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ],
            ]);
        }

        return [
            'customRanges' => json_encode($this->periods),
            'maxDate' => $this->maxDate ?? false,
            'minDate' => $this->minDate ?? false,
        ];
    }

    /**
     * @param Carbon[] $periods
     */
    public function setPeriods(array $periods): self
    {
        $result = [];
        foreach ($periods as $periodName => $dates) {
            foreach ($dates as $date) {
                $result[$periodName][] = $date->toDateTimeString();
            }
        }
        $this->periods = $result;

        return $this;
    }

    public function setMaxDate(Carbon $maxDate): self
    {
        $this->maxDate = $maxDate->toDateTimeString();

        return $this;
    }

    public function setMinDate(Carbon $minDate): self
    {
        $this->minDate = $minDate->toDateTimeString();

        return $this;
    }

    public function default(): string
    {
        [$start, $end] = Helper::getParsedDatesGroupedRanges($this->default);

        return $start->format('d-m-Y').' to '.$end->format('d-m-Y');
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
