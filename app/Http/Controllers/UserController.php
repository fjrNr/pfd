<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use Hash;
use Session;
use Validator;
use DB;

class UserController extends Controller
{
	function doChangeMyPassword(Request $rq){
        $Validator = Validator::make($rq->all(), [
            'newPassword' => 'required|string|min:6',
            'newPasswordConfirmation' => 'required_with:newPassword|string|min:6'
        ]);

        if (!(Hash::check($rq->currentPassword, Auth::user()->password))) {
            // The passwords matches
            Session::flash('message', 'Your current password does not matches with the password you provided. Please try again.');
            return redirect()->back();
        }
 
        if(strcmp($rq->currentPassword, $rq->newPassword) == 0){
            //Current password and new password are same
            Session::flash('message', 'New Password cannot be same as your current password. Please choose a different password.');
            return redirect()->back();
        }

        if(strcmp($rq->newPassword, $rq->newPasswordConfirmation) != 0){
        	Session::flash('message', 'New Password confirmation does not matches with the new password. Please try again');
            return redirect()->back();
        }
 
        // $user = User::where('id', Auth::user()->id)->first();
        // $user->password = bcrypt($rq->newPassword);
        // $user->save();

        if($Validator->passes()){
            DB::table('auth_users')->where('id',Auth::user()->id)->update([
                'password' => bcrypt($rq->newPassword),
                'updatedt' => Carbon::now()
            ]);

            Session::flash('message', 'Change Password successfull.');
            return redirect('user');    
        }else{
            return redirect()->back()->withErrors($Validator);
        }
        
    }

    function doChangeTheirPassword(Request $rq, $id){
        if(Auth::user()->group == 'ADMIN'){
            $Validator = Validator::make($rq->all(), [
                'newPassword' => 'required|string|min:6',
                'newPasswordConfirmation' => 'required_with:newPassword|string|min:6'
            ]);

            if(strcmp($rq->newPassword, $rq->newPasswordConfirmation) != 0){
                Session::flash('message', 'New Password confirmation does not matches with the new password. Please try again');
                return redirect()->back();
            }
     
            if($Validator->passes()){
                DB::table('auth_users')->where('id', $id)->update([
                    'password' => bcrypt($rq->password),
                    'updatedt' => Carbon::now()
                ]);    

                Session::flash('message', 'Change Password successfull.');
                return redirect('accounts/edit/'.$id);
            }else{
                return redirect()->back()->withErrors($Validator);
            }
            
        }
    }

    function doCreate(Request $rq){
        if(Auth::user()->group == 'ADMIN'){
            $Validator = Validator::make($rq->all(), [
                'id' => 'required|unique:auth_users|min:3',
                'name' => 'required',
                'role' => 'required',
                'department' => 'required',
                'homebase' => 'required',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($Validator->passes()){
                DB::table('auth_users')->insert([
                    'id' => $rq->id,
                    'name' => $rq->name,
                    'password' => bcrypt($rq->password),
                    'group' => $rq->role,
                    'department' => $rq->department,
                    'homebase' => $rq->homebase,
                    'updatedt' => Carbon::now()
                ]);

                Session::flash('message', 'Add new successful.');
                return redirect('accounts/index');
            }
            return redirect()->back()->withErrors($Validator)->withInput();
        }
    }

    function doLoadAllData(){
        if(Auth::user()->group == 'ADMIN'){
            $users = User::select('id', 'name', 'group', 'department', 'homebase')->whereNotIn('id',[Auth::user()->id]);

            return Datatables::of($users)
                    ->addColumn('action', function ($user) {
                        return '<a href="edit/'.$user->id.'" style="font-weight: bold;" class="btn btn-warning">&#x1f589; Edit</a>'.' '.'<a href="delete/'.$user->id.'" style="font-weight: bold;" class="btn btn-danger">X Delete</a>';
                    })
                    ->make(true);
        }else{
            return view('errors.401');
        }
    }

    function doGoToEditMyDataPage(){
        $user = User::where('id',Auth::user()->id)->first();
        return view('account.edit',compact('user'));
    }

    function doGoToEditPage($id){
        if(Auth::user()->group == 'ADMIN'){
            $user = User::where('id',$id)->first();
            return view('account.edit',compact('user'));
        }else{
            return view('errors.401');
        }
    }

    function doUpdate(Request $rq, $id){
        if(Auth::user()->group == 'ADMIN'){
            $Validator = Validator::make($rq->all(), [
                'id' => 'required|min:3|unique:auth_users,id,'.$id,
                'name' => 'required',
                'role' => 'required',
                'department' => 'required',
                'homebase' => 'required',
            ]);

            if($Validator->passes()){
                DB::table('auth_users')->where('id',$id)->update([
                    'id' => $rq->id,
                    'name' => $rq->name,
                    'group' => $rq->role,
                    'department' => $rq->department,
                    'homebase' => $rq->homebase,
                    'updatedt' => Carbon::now()
                ]);

                $submissions = DB::table('submissions')
                                ->where('inputBy',$id)
                                ->update(['inputBy' => $rq->id]);
                Session::flash('message', 'Update successful.');
                return redirect('accounts/edit/'.$rq->id);
            }
            return redirect()->back()->withErrors($Validator)->withInput();
        }
    }

    function doUpdateMyData(Request $rq){
        $Validator = Validator::make($rq->all(), [
            'name' => 'required',
            'department' => 'required',
            'homebase' => 'required',
        ]);

        if($Validator->passes()){
            DB::table('auth_users')->where('id',Auth::user()->id)->update([
                'name' => $rq->name,
                'department' => $rq->department,
                'homebase' => $rq->homebase,
                'updatedt' => Carbon::now()
            ]);

            Session::flash('message', 'Update successful.');
            return redirect()->back();
        }
        return redirect()->back()->withErrors($Validator)->withInput();
    }
}