<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use App;
class Rights
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

        /*$user_login = strtolower($_SERVER['REMOTE_USER'])*/;
        $principal = explode("@",$_SERVER['REMOTE_USER']);
        $user_login = $principal[0];
        $results = DB::select('SELECT rights.id as id, rights.access as access FROM rights WHERE rights.name = ?', array($user_login));
        $data = json_decode(json_encode((array) $results), true);
        if (count($data) > 0){
            $access = $data[0]['access'];
        } else {
            /*return json_encode(array("results" => "Ошибка! Такого аккаунта не существует"));*/
            return App::abort(403);
        }

        if ($access == 1){
            /*return json_encode(array("results" => true));*/
            return $next($request);
        } else {
            return App::abort(403);
        }

    }
}
