<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//user & address
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');



Route::group(['middleware' => 'auth:api'], function(){

  Route::group(['middleware' => 'admin:api'], function(){
      Route::post('officer', 'API\UserController@officer');
      Route::post('HistoryPaid', 'API\UserController@history_paid');
      Route::post('PrintReceipt', 'API\UserController@PrintReceipt');
      Route::post('RegisterOfficer', 'API\UserController@RegisterOfficer');
      Route::post('AddRatePrice', 'API\UserController@add_rate_price');
      Route::post('updateThisOfficer', 'API\UserController@updateThisOfficer');
      Route::post('DeleteOfficer', 'API\UserController@delete_officer');
  });
  Route::group(['middleware' => 'officer:api'], function(){
      Route::post('AddOneAddress', 'API\UserController@add_one_address');
      Route::post('DeleteOneAddress', 'API\UserController@delete_one_address');
      Route::post('DeleteMember', 'API\UserController@delete_member');
      Route::post('UpdateMember', 'API\UserController@UpdateMember');
      Route::post('showTransferOfficer', 'API\UserController@showTransferOfficer');
      Route::post('NotPayData', 'API\UserController@not_pay_data');
      Route::post('bill', 'API\UserController@bill');
      Route::post('SelectPayAll', 'API\UserController@select_pay_all');
      Route::post('PaymentConfirm', 'API\UserController@payment_confirm');
      Route::post('CancelPayment', 'API\UserController@cancel_payment');
      Route::post('ThisOfficer', 'API\UserController@ThisOfficer');
      Route::post('UpdateMemberOne', 'API\UserController@update_member');
      Route::post('UpdateOfficerAddress', 'API\UserController@update_officer_address');
      Route::post('RegisterMember', 'API\UserController@register_member');
      Route::post('member', 'API\UserController@member');
      Route::post('AddTransfer', 'API\UserController@Transfer');
      Route::post('TransferAddress', 'API\UserController@Transfer_Address');

  });

  Route::post('ThisMemberAddress', 'API\UserController@this_member_address');
  Route::post('details', 'API\UserController@details');
  Route::post('ThisMember', 'API\UserController@this_member');

  Route::post('repassword', 'API\UserController@repassword');
  Route::post('RepasswordMember', 'API\UserController@repassword_member');

  Route::post('test', 'API\UserController@test');
  Route::post('ImageSlip', 'API\UserController@image_slip');
  Route::post('UploadSlip', 'API\UserController@upload_slip');
  Route::post('UpdateSlip', 'API\UserController@UpdateSlip');

  Route::post('NotiFication', 'API\UserController@NotiFication');
  Route::post('UpdatePayType', 'API\UserController@update_pay_type');

//Payment
  Route::post('TransferAgain', 'API\UserController@TransferAgain');
  Route::post('AddRatePrice', 'API\UserController@add_rate_price');
  Route::post('RatePriceAll', 'API\UserController@rate_price_all');
  Route::post('SelectPay', 'API\UserController@select_pay');

  Route::post('notifications_reset', 'API\UserController@notifications_reset');
  Route::post('history', 'API\UserController@history');


});



Route::get('PaymentHistoryAdmin', 'API\UserController@payment_history_admin');
Route::get('AddPayment', 'API\UserController@add_Payment');
Route::get('RatePrice', 'API\UserController@rate_price');
