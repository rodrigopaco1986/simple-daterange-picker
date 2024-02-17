<?php

namespace Rpj\Daterangepicker;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rpj\Daterangepicker\DateHelper as Helper;

class Daterangepicker extends Filter
{
    public function __construct(
        private string $column,
        private string $default = Helper::TODAY,
        private string $orderByColumn = 'id',
        private string $orderByDir = 'asc',
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        [$start, $end] = Helper::getParsedDatesGroupedRanges($value);

        if ($start && $end) {
            return $query->whereBetween($this->column, [$start, $end])
                ->orderBy($this->orderByColumn, $this->orderByDir);
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
        return [];
    }

    /**
     * Set the default options for the filter.
     *
     * @return array|mixed
     */
    public function default()
    {
        [$start, $end] = Helper::getParsedDatesGroupedRanges($this->default);

        return $start->format('Y-m-d').' to '.$end->format('Y-m-d');
    }
}
