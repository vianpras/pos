<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\Helper;


class AclUserMiddleware
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
       
        // check SSL 
        if ((env('HTTPS_DOMAIN_NOCHECK')) && (env('APP_DEBUG'))) {
            if (!$request->secure()) {
                return abort(404);
            }
        }
        if (!is_null(Auth::id())) {
            Helper::loggingApp($request->ip(), Auth::id(), 'kode transaksi : '.$request->code ?? '' );
            $status = Auth::user()->status;
            $users_acl_id = Auth::user()->users_acls_id;
            $sudo = Auth::user()->sudo;
            Session::put('_status', $status);
            Session::put('_users_acl_id', $users_acl_id);
            Session::put('_sudo', $sudo);
            $users_acl = DB::table('users_acls')->where('id', $users_acl_id)->first();
            if ($sudo == 1) {
                $users_acl = DB::table('users_acls')->where('id', 0)->first();
            }
            Session::put('_users_acl', $users_acl);
            // set last_login
            DB::table('users')->where('id', Auth::id())->update(['last_login' => Carbon::now()]);
            if ($status == 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('dashboard');
            }
        }
        return $next($request);
    }
}
