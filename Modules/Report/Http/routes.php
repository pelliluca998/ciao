<?php

Route::group(['middleware' => ['web', 'role:admin', 'license:report'], 'prefix' => 'admin', 'namespace' => 'Modules\Report\Http\Controllers'], function()
{
	Route::post('report/gen_eventspec',['as' => 'report.gen_eventspec', 'uses' => 'ReportController@gen_eventspec']);
	Route::post('report/gen_weekspec',['as' => 'report.gen_weekspec', 'uses' => 'ReportController@gen_weekspec']);
	Route::get('report/eventspec', ['as' => 'report.eventspec', 'uses' => 'ReportController@eventspecreport']);
	Route::get('report/weekspec', ['as' => 'report.weekspec', 'uses' => 'ReportController@weekreport']);
	Route::post('report/gen_user', ['as' => 'report.gen_user', 'uses' => 'ReportController@gen_user']);
	
});
