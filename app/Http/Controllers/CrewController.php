<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Crew;
use Auth;
use DB;

class CrewController extends Controller
{
    function readAutocomplete(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC'){
            $query = $rq->get('term', '');
            $crews = Crew::where('empNbr', 'LIKE', '%'.$query.'%')->paginate(5);
            $data = [];

            foreach ($crews as $key => $crew){
                $data[] = ['value'=> $crew->empNbr,
                    'name' => $crew->firstName.' '.$crew->middleName.' '.$crew->lastName,
                    'rank' => $crew->empRank,
                ];
            }

            if(count($data)){
                return response($data);
            }else{
                return ['value' => 'No result found'];
            }
        }
    }
}