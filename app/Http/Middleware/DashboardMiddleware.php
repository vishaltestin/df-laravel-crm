<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
class DashboardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        if(Sentinel::check() && ((Sentinel::getUser()->roles()->first()->slug=='root') 
			|| (Sentinel::getUser()->roles()->first()->slug=='sadmin')
		|| (Sentinel::getUser()->roles()->first()->slug=='admin')
		|| (Sentinel::getUser()->roles()->first()->slug=='manager')
		|| (Sentinel::getUser()->roles()->first()->slug=='operater')
		|| (Sentinel::getUser()->roles()->first()->slug=='student')
 		))
		{
		   return $next($request);
		}
		else
        {
            return redirect('./');
        }

        //return $next($request);

    }
}
