<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;
use JavaScript;
 
class SetLang
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
        if (Session::has("lang")) {
            $lang = Session::get("lang");
        } else {
            // check browser lang - https://learninglaravel.net/detect-and-change-language-on-the-fly-with-laravel
            $lang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
 
            if ($lang != 'es' && $lang != 'en') {
                $lang = 'en';
            }
        }
        JavaScript::put([
            'lang'          => $lang,   
            'app_label'     => env('APP_LABEL'),   
            'app_type'      => env('APP_TYPE'),   
            'app_client'    => env('APP_CLIENT'),   
        ]);
        App::setLocale($lang);
        return $next($request);
    }
}
