## Simple Date Range Filter for Laravel Nova 4

A filter for Nova 4 that displays a Date Range Picker instead of a single date picker using [Daterangepicker library](https://www.daterangepicker.com/)

### Install

Run this command in your nova project:
`composer require rpj/daterangepicker`

### How to use

In your Nova resource, just add DateRangeFilter class in the filters function, and include the column that you would like to use as the one to filter the resource.

```php
 use Rpj\Daterangepicker\Daterangepicker;

 public function filters(Request $request)
    {
        return [
            new Daterangepicker('created_at'),
        ];
    }
```

Additionally we added a custom date range picker that allows user to specify the column to order by with its value and as well as in the case of a joined table to prevent ambiguous mysql error, you can specify the actual table name to know the actual column you are referring to.
this takes, the column to check the date range picker and as well as the column to order by with the asc/desc direction

```php
 use Rpj\Daterangepicker\CustomDaterangepicker;

 public function filters(Request $request)
    {
        return [
            new CustomDaterangepicker('users.created_at', 'users.id', 'asc'),
        ];
    }
```

Additionally, you can pass a string with default date range to use in the component. If no value is passed, TODAY value is set as default.

```php
 use Rpj\Daterangepicker\Daterangepicker;
 use Rpj\Daterangepicker\DateHelper;

 public function filters(Request $request)
    {
        return [
            new Daterangepicker('created_at', DateHelper::THIS_WEEK),
        ];
    }
```