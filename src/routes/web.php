<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect to mailchimp
Route::get('/', function()
{
    return Redirect::route('mailchimp.list.index');
});

// Lists
Route::get('/mailchimp', ['as' => 'mailchimp.list.index', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@index']);
Route::post('/mailchimp', ['as' => 'mailchimp.list.store', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@store']);
Route::get('/mailchimp/create', ['as' => 'mailchimp.list.create', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@create']);
Route::get('/mailchimp/sync', ['as' => 'mailchimp.list.syncall', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@syncAll']);
Route::delete('/mailchimp/{id}', ['as' => 'mailchimp.list.destroy', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@destroy']);
Route::get('/mailchimp/{id}', ['as' => 'mailchimp.list.show', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@show']);
Route::put('/mailchimp/{id}', ['as' => 'mailchimp.list.update', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@update']);
Route::post('/mailchimp/{id}/bulk', ['as' => 'mailchimp.list.bulk', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@bulk']);
Route::get('/mailchimp/{id}/delete', ['as' => 'mailchimp.list.delete', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@delete']);
Route::get('/mailchimp/{id}/edit', ['as' => 'mailchimp.list.edit', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@edit']);
Route::get('/mailchimp/{id}/sync', ['as' => 'mailchimp.list.sync', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListController@sync']);

// Members
Route::get('/mailchimp/{id}/members', ['as' => 'mailchimp.member.index', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@index']);
Route::post('/mailchimp/{id}/members', ['as' => 'mailchimp.member.store', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@store']);
Route::get('/mailchimp/{id}/members/sync', ['as' => 'mailchimp.member.syncall', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@syncAll']);
Route::delete('/mailchimp/{id}/members/{mid}', ['as' => 'mailchimp.member.destroy', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@destroy']);
Route::get('/mailchimp/{id}/members/{mid}', ['as' => 'mailchimp.member.show', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@show']);
Route::put('/mailchimp/{id}/members/{mid}', ['as' => 'mailchimp.member.update', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@update']);
Route::get('/mailchimp/{id}/members/{mid}/delete', ['as' => 'mailchimp.member.delete', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@delete']);
Route::get('/mailchimp/{id}/members/{mid}/edit', ['as' => 'mailchimp.member.edit', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@edit']);
Route::get('/mailchimp/{id}/members/{mid}/sync', ['as' => 'mailchimp.member.sync', 'middleware' => 'web', 'uses' => 'Sjdaws\LoyaltyCorpTest\Controllers\Mailchimp\ListMemberController@sync']);
