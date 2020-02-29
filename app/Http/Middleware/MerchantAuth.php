<?php

namespace App\Http\Middleware;

use App\Traits\Helpers\Helper;
use App\Traits\Responser\ApiResponser;
use Closure;

class MerchantAuth
{
    use ApiResponser, Helper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role->role == 'user') {
            return $this->errorResponse(403, 'Forbidden');
        }
        return $next($request);
    }
}
