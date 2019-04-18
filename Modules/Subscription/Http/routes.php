<?php

Route::group(['middleware' => ['web', 'verified'], 'prefix' => 'admin', 'namespace' => 'Modules\Subscription\Http\Controllers'], function(){

	Route::resource('subscription', 'SubscriptionController', ['only' => ['index', 'store']]);

	Route::get('subscription/user', ['as' => 'subscription.user', 'uses' => 'SubscriptionController@usersubscription']);
	Route::get('subscription/getDataUser', ['as' =>'subscription.data_user', 'uses' => 'SubscriptionController@data_user']);

	Route::get('subscription/contact', ['as' => 'subscription.contact', 'uses' => 'SubscriptionController@contact']);
	Route::get('subscription/selectevent', ['as' => 'subscription.selectevent', 'uses' => 'SubscriptionController@selectevent']);
	Route::post('subscription/contact_send', ['as' => 'subscription.contact_send', 'uses' => 'SubscriptionController@contact_send']);
	Route::get('subscription/event', ['as' => 'subscription.event', 'uses' => 'SubscriptionController@event'] );

	Route::get('subscription/approve', ['as' => 'subscription.approve', 'uses' => 'SubscriptionController@approve']);
	Route::get('subscription/batch_delete', ['as' => 'subscription.batch_delete', 'uses' => 'SubscriptionController@batch_delete']);

	Route::get('subscription/edit', ['as' => 'subscription.edit', 'uses' => 'SubscriptionController@edit']);
	//Route::get('subscription/print', ['as' => 'subscription.print', 'uses' => 'SubscriptionController@print']);
	Route::put('subscription/update/{id_subscription}',['as' => 'subscription.update', 'uses' => 'SubscriptionController@update']);
	Route::get('subscription/getData', ['as' =>'subscription.data', 'uses' => 'SubscriptionController@data']);
	Route::post('subscription/action', ['as' =>'subscription.action', 'uses' => 'SubscriptionController@action']);
	Route::get('subscription/{id_sub}', ['as' => 'subscription.show_eventspecvalues', 'uses' => 'SubscriptionController@show_eventspecvalues']);
	Route::post('subscription/user_popover', ['as' => 'subscription.user_popover', 'uses' => 'SubscriptionController@user_popover']);

	//Route::get('specsubscriptions/{id_subscription}', ['as' => 'subscription.show_specsubscriptions', 'uses' => 'SpecSubscriptionController@show_specsubscriptions']);



});

Route::group(['middleware' => ['web', 'verified'], 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
	//Pagina delle iscrizioni utente
	Route::get('iscrizioni', ['as' => 'iscrizioni.index', 'uses' => 'SubscriptionController@index_iscrizioni']);
	Route::post('iscrizioni', ['as' => 'iscrizioni.store', 'uses' => 'SubscriptionController@store_iscrizioni']);
	Route::get('iscrizioni/getData', ['as' => 'iscrizioni.data', 'uses' => 'SubscriptionController@data_iscrizioni']);

	Route::get('iscrizioni/{id_sub}', ['as' => 'iscrizioni.show_iscrizione', 'uses' => 'SubscriptionController@show_iscrizione']);


	Route::post('subscribe/create', ['as' => 'subscribe.create', 'uses' => 'SubscriptionController@subscribe_create']); //chiamata dall'utente che vuole iscriversi all'evento
	Route::get('subscribe/{id_subscription}/grazie',['as' => 'subscribe.grazie', 'uses' => 'SubscriptionController@grazie']);
	Route::post('subscribe/savesubscribe',['as' => 'subscribe.savesubscribe', 'uses' => 'SubscriptionController@savesubscribe']);
	Route::post('subscribe/savespec',['as' => 'subscribe.savespec', 'uses' => 'SubscriptionController@savespecsubscribe']);
	Route::get('subscribe/spec/{id_subscription}', ['as' => 'subscribe.spec', 'uses' => 'SubscriptionController@create']);
	//Route::post('subscribe/print', ['as' => 'subscribe.print', 'uses' => 'SubscriptionController@print']);
	// Route::get('subscription/destroy', ['as' => 'subscription.destroy', 'uses' => 'SubscriptionController@destroy']);
	//Route::post('specsubscription/save', ['as' => 'specsubscription.save', 'uses' => 'SpecSubscriptionController@save']);
});


Route::group(['middleware' => ['web', 'verified'], 'namespace' => 'Modules\Event\Http\Controllers'], function(){
	Route::resource('eventspecvalues', 'EventSpecValueController', ['only' => ['index', 'update', 'store']]);
	//Route::post('eventspecvalues/save', ['as' => 'eventspecvalues.save', 'uses' => 'EventSpecValueController@save']);
	Route::get('eventspecvalues/data', ['as' => 'eventspecvalues.data', 'uses' => 'EventSpecValueController@data']);
	Route::post('eventspecvalues/valore_field', ['as' => 'eventspecvalues.valore_field', 'uses' => 'EventSpecValueController@valore_field']);
	//Route::get('eventspecvalues/{id_eventspecvalue}/destroy', ['as' => 'eventspecvalues.destroy', 'uses' => 'EventSpecValueController@destroy']);

});

Route::group(['middleware' => ['web', 'verified'], 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
	//Route::get('subscriptions', ['as' => 'usersubscriptions.show', 'uses' => 'SubscriptionController@usersubscription']);
	Route::get('subscription/user', ['as' => 'subscription.user', 'uses' => 'SubscriptionController@usersubscription']);
	Route::get('subscription/getDataUser', ['as' =>'subscription.data_user', 'uses' => 'SubscriptionController@data_user']);
	Route::get('usereventspecvalues', ['as' => 'usersubscriptions.showeventspecs', 'uses' => 'SubscriptionController@usersub_showeventspecs']);
	//Route::get('userspecsubscriptions', ['as' => 'usersubscriptions.showweekspecs', 'uses' => 'SubscriptionController@usersub_showweekpecs']);
	Route::get('subscription/{id_subscription}/print', ['as' => 'subscription.print', 'uses' => 'SubscriptionController@print_subscription']);
});
