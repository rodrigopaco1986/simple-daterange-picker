<?php

namespace Rpj\Daterangepicker;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Rpj\Daterangepicker\DateHelper as Helper;

class CustomDaterangepicker extends Daterangepicker
{
    

    private string $column;
    private string $orderByColumn;
    private string $orderBy;
    private string $default;

    /**
     * Constructor.
     *
     * @param  string  $column
     * @param  string|null  $default
     * @return void
     */
    public function __construct(string $column, string $orderByColumn='id', string $orderBy='desc', string $default = null)
    {
        $this->column = $column;
        $this->orderByColumn = $orderByColumn;
        $this->orderBy = $orderBy;
        $this->default = $default ?? Helper::TODAY;
        

        parent::__construct($this->column, $this->default);
    }

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'customdaterangepicker';

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
                ->orderBy($this->orderByColumn, $this->orderBy);
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
