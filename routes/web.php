<?php
use Carbon\Carbon;
use Faker\Factory as Faker;
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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function (){
	//account:
	//GET method:
	Route::get('/accounts/index', function(){
		if(Auth::user()->group == 'ADMIN'){
			return view('account.index');
		}else{
			return view('errors.401');
		}
	});
	Route::get('/accounts/create', function(){
		if(Auth::user()->group == 'ADMIN'){
			return view('account.create');
		}else{
			return view('errors.401');
		}
	});
	Route::get('/accounts/edit/{id}', 'UserController@doGoToEditPage');
	Route::get('/accounts/edit/{id}/password', function(){
		return view('account.changePassword');
	});
	Route::get('/accounts/getData', 'UserController@doLoadAllData');
	//POST method:
	Route::post('/accounts/create', 'UserController@doCreate');
	Route::post('/accounts/delete/{id}', 'UserController@delete');
	Route::post('/accounts/edit/{id}', 'UserController@doUpdate');
	Route::post('/accounts/edit/{id}/password', 'UserController@doChangeTheirPassword');


	//AFL
	Route::get('/afl/readAll', 'AflController@readAll');


	//Box
	//GET method:
    Route::get('/box/entry', function(){ 
		return Auth::user()->group == ('ADMIN' || 'BOX OFC')? view('box.entry') : view('errors.401');
	});
	Route::get('/box/entry/afl', 'AflBoxController@entry');
	Route::get('/box/index', function(){
    	return Auth::user()->group == ('ADMIN' || 'BOX OFC')? view('box.index') : view('errors.401');
    });
    Route::get('/box/index/afl', function(){
    	return Auth::user()->group == ('ADMIN' || 'BOX OFC')? view('box.indexAFL') : view('errors.401');
    });
	Route::get('/box/edit/{id}', 'NonAflBoxController@edit');
	Route::get('/box/edit/afl/{id}', 'AflBoxController@edit');
	Route::get('/box/readAll', 'NonAflBoxController@readAll');
	Route::get('/box/readAll/afl', 'AflBoxController@readAll');
	//POST method:
	Route::post('/box/create', 'NonAflBoxController@create');
	Route::post('/box/create/afl', 'AflBoxController@create');
	Route::post('/box/delete/{id}', 'NonAflBoxController@delete');
	Route::post('/box/delete/afl/{id}', 'AflBoxController@delete');
	Route::get('/box/update/{id}', 'NonAflBoxController@update');
	Route::post('/box/update/afl/{id}', 'AflBoxController@update');


	//Crew
	Route::get('/crew/read/autocomplete', 'CrewController@readAutocomplete');


	//Movement	
	//GET method:
	Route::get('/movement/edit/{id}', 'MovementController@edit');
	Route::get('/movement/entry', 'MovementController@entry');
	Route::get('/movement/index', function(){
		return Auth::user()->group == ('ADMIN' || 'ANALYST')? view('movement.index') : view('errors.401');
	});
	Route::get('/movement/readAll', 'MovementController@readAll');
	//POST method:
	Route::post('/movement/cancel/{id}', 'MovementController@cancel');
	Route::post('/movement/create', 'MovementController@create');
	Route::post('/movement/delete/{id}', 'MovementController@delete');
	Route::post('/movement/update/{id}', 'MovementController@update');


	//Submission
	//GET method:
	Route::get('/submission/edit/{id}', 'SubmissionController@edit');
	Route::get('/submission/entry', function(){
		return Auth::user()->group == ('ADMIN' || 'SUBM OFC')? view('submission.entry') : view('errors.401');	
	});
	Route::get('/submission/index', function(){
		return Auth::user()->group == ('ADMIN' || 'SUBM OFC')? view('submission.index') : view('errors.401');
	});
	Route::get('/submission/read/all', 'SubmissionController@readAll');
	Route::get('/submission/read/autocomplete', 'SubmissionController@readAutocomplete');
	Route::get('/submission/read/assignedOnly', 'SubmissionController@readAssignedOnly');
	Route::get('/submission/read/nonAssignedOnly', 'SubmissionController@readNonAssignedOnly');
	//POST method:
	Route::post('/submission/create', 'SubmissionController@create');
	Route::post('/submission/update/{id}', 'SubmissionController@update');
	Route::post('/submission/delete/{id}', 'SubmissionController@delete');	


// ===========================================================================================================    
	//movement
	// Route::get('/movement/entry', function(){
	// 	return view('movement.entry');
	// });
	
	//submission:
	Route::get('/tracking', function(){
		return Auth::user()->group == ('ADMIN' || 'BOX OFC')? view('submission.indexTrack') : view('errors.401');
	});
	Route::get('/tracking/getData', 'SubmissionController@doTracking');


	//user:
	Route::get('/user', 'UserController@doGoToEditMyDataPage');
	Route::get('/user/password', function(){
		return view('account.changePassword');
	});
	Route::post('/user', 'UserController@doUpdateMyData');
	Route::post('/user/password', 'UserController@doChangeMyPassword');
});

Route::get('/test', function () {
	// $str = "Hello world. 3 It's a beautiful day.";
	// $rank = explode(" ", $str)[2];
	// echo ++$rank;
	// echo $rank;

	// $start_date = strtotime('2018-05-15');
	// $end_date = strtotime('2019-02-15');

	// for($i = $start_date; $i <= $end_date; $i+=86400){
	//     echo date('d-m-Y',$i).'<br>';
	// }

	// $box = DB::table('non_afl_boxes')
	// 		->where('packNbr', 'LIKE', '%/18/%')
	// 		->orwhere('packNbr', 'LIKE', '%/19/%')
	// 		->orwhere('packNbr', 'LIKE', '%/20/%')
	// 		->orwhere('packNbr', 'LIKE', '%/22/%')
	// 		->orwhere('packNbr', 'LIKE', '%/22/%')
	// 		->orwhere('packNbr', 'LIKE', '%/23/%')
	// 		->orwhere('packNbr', 'LIKE', '%/24/%')
	// 		->orwhere('packNbr', 'LIKE', '%/25/%')
	// 		->orwhere('packNbr', 'LIKE', '%/26/%')
	// 		->orwhere('packNbr', 'LIKE', '%/27/%')
	// 		->orwhere('packNbr', 'LIKE', '%/28/%')
	// 		->orwhere('packNbr', 'LIKE', '%/29/%')
	// 		->delete();
	// echo 'test';

	// $start_time = microtime(true);
	// $date = date('Y-m-d', strtotime('2018-05-29'));
	
	// $formNbr = DB::table('submissions')->count();

	// $end_time = microtime(true);
	// $execution_time = ($end_time - $start_time);
	// echo $execution_time.' seconds<br>';
	// echo $formNbr;
	// echo $afl_numbers; 
	// $bool = in_array("18ZZ9998", $afl_copies);

	// $strA = "sonata";
	// $strB = "sonata";
	// if(strcmp($strA, $strB) == 0){
	// 	echo 'same words';
	// }else{
	// 	echo 'not same words';
	// }
});
