<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ConvertCamelCaseToSnakeCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = collect($request->all())
                ->mapWithKeys(function ($value, $key) {
                    return [Str::snake($key) => $value];
                })->all();
        $request->replace($input);
        return $next($request);
    }
}
