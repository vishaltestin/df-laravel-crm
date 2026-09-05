<?php
namespace App\Http\Middleware;

use Closure;
use Session;

class CheckOrganizationAccess
{
    public function handle($request, Closure $next)
    {
        $orgId = Session::get('organization');
        $orgIds = Session::get('organizations', []);

        if (!in_array($orgId, $orgIds)) {
            abort(403, 'Unauthorized Org Access');
        }

        return $next($request);
    }
}