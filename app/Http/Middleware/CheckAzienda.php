<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class CheckAzienda
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!empty(Auth::id()))
        {
            switch(get_azienda())
            {
                case 'we-com':
                    config(['asgard.core.core.skin' => 'skin-blue']);
                    break;

                case 'digit-consulting':
                    config(['asgard.core.core.skin' => 'skin-yellow']);
                    break;
            }
        }

        return $next($request);
    }
}
