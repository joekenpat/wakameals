<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class OrderSearch
{
  public static function apply(Request $filters, $per_page = 20)
  {
    $query = static::applyDecoratorsFromRequest($filters, (new Order)->newQuery());
    return static::getResults($query, $per_page);
  }

  private static function applyDecoratorsFromRequest(Request $request, Builder $query)
  {
    $validFilterNames = Order::filterables;
    foreach ($request->all() as $filterName => $value) {
      if (in_array($filterName, $validFilterNames)) {
        $decorator = static::createFilterDecorator($filterName);
        if (static::isValidDecorator($decorator)) {
          $query = $decorator::apply($query, $value);
        }
      }
    }
    return $query;
  }

  private static function createFilterDecorator($name)
  {
    return __NAMESPACE__ . '\\OrderFilters\\' . Str::studly($name);
  }

  private static function isValidDecorator($decorator)
  {
    return class_exists($decorator);
  }

  private static function getResults(Builder $query, $per_page)
  {
    if ($per_page !== null && $per_page >= 20) {
      return $query->simplePaginate($per_page);
    } else {
      return $query->get();
    }
  }
}