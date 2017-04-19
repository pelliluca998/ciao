<?php

Route::group(['middleware' => ['web', 'role:admin', 'license:subscription'], 'prefix' => 'admin', 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
	Route::get('subscription', ['as' => 'subscription.index', 'uses' => 'SubscriptionController@indexCurrentEvent']);
	Route::get('subscription/contact', ['as' => 'subscription.contact', 'uses' => 'SubscriptionController@contact']);
	Route::get('subscription/selectevent', ['as' => 'subscription.selectevent', 'uses' => 'SubscriptionController@selectevent']);	
	//Route::post('subscription/storeselect', ['as' => 'subscription.storeselect', 'uses' => 'SubscriptionController@storeselect']);
	Route::post('subscription/contact_send', ['as' => 'subscription.contact_send', 'uses' => 'SubscriptionController@contact_send']);
	Route::get('subscription/event', ['as' => 'subscription.event', 'uses' => 'SubscriptionController@index'] );
	
	Route::get('subscription/edit', ['as' => 'subscription.edit', 'uses' => 'SubscriptionController@edit']);
	Route::get('subscription/print', ['as' => 'subscription.print', 'uses' => 'SubscriptionController@print']);
	Route::put('subscription/update/{id_subscription}',['as' => 'subscription.update', 'uses' => 'SubscriptionController@update']);
	
	//Route::get('specsubscriptions/{id_subscription}', ['as' => 'subscription.show_specsubscriptions', 'uses' => 'SpecSubscriptionController@show_specsubscriptions']);
	
	
	
});

Route::group(['middleware' => ['web', 'role:user|admin', 'license:subscription'], 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
	Route::post('subscribe/create', ['as' => 'subscribe.create', 'uses' => 'SubscriptionController@subscribe_create']); //chiamata dall'utente che vuole iscriversi all'evento
	Route::post('subscribe/savesubscribe',['as' => 'subscribe.savesubscribe', 'uses' => 'SubscriptionController@savesubscribe']);
	Route::post('subscribe/savespec',['as' => 'subscribe.savespec', 'uses' => 'SubscriptionController@savespecsubscribe']);
	Route::get('subscribe/spec/{id_subscription}', ['as' => 'subscribe.spec', 'uses' => 'SubscriptionController@create']);
	Route::post('subscribe/print', ['as' => 'subscribe.print', 'uses' => 'SubscriptionController@print']);
	Route::get('subscription/destroy', ['as' => 'subscription.destroy', 'uses' => 'SubscriptionController@destroy']);
	//Route::post('specsubscription/save', ['as' => 'specsubscription.save', 'uses' => 'SpecSubscriptionController@save']);
});


Route::group(['middleware' => ['web', 'role:user|admin', 'license:subscription'], 'namespace' => 'Modules\Event\Http\Controllers'], function(){
	Route::post('eventspecvalues/save', ['as' => 'eventspecvalues.save', 'uses' => 'EventSpecValueController@save']);
	Route::get('eventspecvalues/{id_eventspecvalue}/destroy', ['as' => 'eventspecvalues.destroy', 'uses' => 'EventSpecValueController@destroy']);
	
});

Route::group(['middleware' => ['web', 'role:user|dmin', 'license:subscription'], 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
	Route::get('subscriptions', ['as' => 'usersubscriptions.show', 'uses' => 'SubscriptionController@usersubscription']);
	Route::get('usereventspecvalues', ['as' => 'usersubscriptions.showeventspecs', 'uses' => 'SubscriptionController@usersub_showeventspecs']);
	//Route::get('userspecsubscriptions', ['as' => 'usersubscriptions.showweekspecs', 'uses' => 'SubscriptionController@usersub_showweekpecs']);
	Route::get('subscription/print', ['as' => 'subscription.print', 'uses' => 'SubscriptionController@print']);
});
