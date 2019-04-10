<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;
use Modules\Event\Entities\Week;
use Modules\Event\Entities\Event;
use Modules\User\Entities\Group;
use Modules\Subscription\Entities\Subscription;
use Modules\Oratorio\Entities\Oratorio;
use Modules\Event\Entities\EventSpecValue;
use Modules\Event\Entities\EventSpec;
use Modules\Oratorio\Entities\TypeSelect;
use Session;
use Entrust;
use Carbon;
use Input;
use Excel;
use Storage;
use PDF;

class ReportController extends Controller
{

	public function __construct(){
    $this->middleware('permission:generate-report');
  }

	public function eventspecreport(){
		if(Session::has('work_event')){
			return view('report::composer_eventspec');
		}else{
			Session::flash('flash_message', 'Per generare il report delle iscrizioni, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}
	}

	public function weekreport(){
		if(Session::has('work_event')){
			return view('report::composer_weekspec');
		}else{
			Session::flash('flash_message', 'Per generare il report delle iscrizioni, devi prima selezionare un evento con cui lavorare!');
			return redirect()->route('events.index');
		}

	}

	public function gen_eventspec(Request $request){
		$input = $request->all();
		switch($input['format']){
			case 'pdf':
			$pdf = PDF::loadView('report::eventspecreport', compact('input'))->setPaper('a4', 'landscape');
	    return $pdf->download(camel_case("Report").".pdf");
			break;
			case 'excel': return Excel::download(new ReportExport($input), 'report.xlsx');
			break;
			case 'html': return view('report::eventspecreport', ['input' => $input]);
			break;
		}
	}

	public function gen_weekspec(Request $request){
		$input = $request->all();
		switch($input['format']){
			case 'pdf':
			$pdf = PDF::loadView('report::weekreport', compact('input'))->setPaper('a4', 'landscape');
	    return $pdf->download(camel_case("Report").".pdf");
			break;
			case 'excel': return Excel::download(new ReportExportWeek($input), 'report.xlsx');
			break;
			case 'html': return view('report::weekreport2', ['input' => $input]);
			break;
		}
	}

	public function gen_user(Request $request){
		$input = $request->all();
		return view('report::users', ['input' => $input]);
	}

	public function user(Request $request){
		$input = $request->all();
		return view('report::composer_user');
	}

	public function report2(Request $request){
		$title = "Titolo del tuo report";
		$meta = ["Meta1" => "cont_meta1", "Meta2" => "cont_meta2"];
		$query = User::select()->leftJoin("user_oratorio", "users.id", "user_oratorio.id_user")->where("user_oratorio.id_oratorio", 1)->orderBy("users.name", "ASC");
		$columns = [
			"Nome" => "name",
			"Cognome" => "cognome",
			"Email" => "email",
			"Data di nascita" => "nato_il"];

		// return PdfReport::of($title, $meta, $query, $columns)->stream();

	}

}
