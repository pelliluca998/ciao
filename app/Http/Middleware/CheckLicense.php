<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\License;

class CheckLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public function handle($request, Closure $next, $moduleName){
		//cerco una licenza attiva, compreso il nome del modulo da cui il controllo proviene
		$now = date("Y-m-d");
		$license = License::where([['module_name', $moduleName], ['data_inizio', '<=', $now], ['data_fine', '>=', $now], ['id_oratorio', Session::get('session_oratorio')]])->first();
		//$license = License::leftJoin('license_types', 'licenses.license_type', 'license_types.id')->where([['licenses.id_oratorio', Session::get('session_oratorio')], ["modules", "like", "%".$moduleName."%"]])->orWhere([['licenses.data_fine', '>=', $now], ['licenses.data_fine', 'null']])->get();
		if($license != null){
		  return $next($request);
		}else{
		  return redirect('licenza');
		}

		//
	}
}
