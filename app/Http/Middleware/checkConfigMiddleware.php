<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class checkConfigMiddleware
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
 // check apakah konfigurasi dan profil perusahaan telah terisi
 $checkCompanies = DB::table('companies')->where('id', 1)->first();
 if (is_null($checkCompanies)) {
     $checkURLConfigurations = Str::contains(url()->current(), '/dataInduk/perusahaan');
     if ($checkURLConfigurations) {
         return $next($request);
     }else{
         return redirect('/dataInduk/perusahaan');
     }
 }
    }
}
