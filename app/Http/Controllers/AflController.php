<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\AflCopy;
use Auth;
use DB;

class AflController extends Controller
{
    function readAll(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC' || Auth::user()->group == 'ANALYST'){
            $query = $rq->get('term', '');
            $logs = AflCopy::where('aflnbr', 'LIKE', '%'.$query.'%')->paginate(5);
            $data = [];
            foreach ($logs as $key => $log){
                $data[] = ['value'=> $log->aflnbr,
                		'pic'=> $log->picnew,
                		'arr'=> $log->arrstn,
                		'dep'=> $log->depstn,
                		'nbr'=> $log->fltnbr,
                		'date'=> $log->arrdate,
                ];
            }

            if(count($data)){
                return response($data);
            }else{
                return ['value' => 'No result found', 'id' => ''];
            }       
        }
    }

}
