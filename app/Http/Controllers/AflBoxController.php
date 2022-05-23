<?php

namespace App\Http\Controllers;

use App\AflBox;
use App\AflCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;
use DataTables;
use DB;
use Session;

class AflBoxController extends Controller
{
    function create (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $packNbr = "AFL/".$rq->packYear."/".$rq->packNo;
            $box = AflBox::where('packNbr', $packNbr)->first();

            if($box){
                return ['error' => 'The box is already created.'];
            }else if($rq->packYear == NULL || $rq->packNo == NULL){
                return ['error' => 'Package number must be filled.'];
            }else{
                $aflCopyId = json_decode($rq->aflCopyIdArray, true);

                $box = new AflBox;
                $box->packNbr = $packNbr;
                $box->location = config('enums.location')[0];
                $box->notes = $rq->notes;
                $box->endRetentionDate = Carbon::now();
                $box->save();

                $time_start = microtime(true);

                for($i = 0; $i < count($aflCopyId); $i++) {                    
                    $userData = [];

                    $afl = AflCopy::where('id', $aflCopyId[$i])->first();
                    $aflCollectionId = AflCopy::where('aflnbr', 'LIKE', substr($afl->aflnbr, 0, 4).'%')
                                    ->whereMonth('arrdate', date('m', strtotime($afl->arrdate)))
                                    ->get()
                                    ->pluck('id')->toArray();

                    for($j = 0; $j < count($aflCollectionId); $j++) {
                        $userData[] = [
                            'aflBoxId' => $box->id,
                            'aflCopyId' => $aflCollectionId[$j],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }

                    $chunks = array_chunk($userData, 100);

                    foreach ($chunks as $chunk) {
                        DB::table('afl_assignments')->insert($chunk);
                    }
                }

                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);

                return ['success' => 'Box is created successfully.', 'success2' => 'Execction Time: '.$execution_time.' seconds.'];
            }
        }
    }

    function edit ($id) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $box = AflBox::where('id', $id)->first();

            $aflCopiesExstRaw = AflCopy::join('afl_assignments', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
                        ->selectRaw('MAX(id) AS id, MONTHNAME(arrdate) AS month, YEAR(arrdate) AS year, CONCAT("PK-G", substr(aflnbr, 3, 2)) AS acReg, COUNT(substr(aflnbr, 3, 2)) AS totalCopy, MIN(substr(aflnbr, 5)) AS startNum, MAX(substr(aflnbr, 5)) AS endNum')
                        ->where('afl_assignments.aflBoxId', '=', $id);
                        
            $aflCopiesExst = $aflCopiesExstRaw
                        ->groupBy('acReg', 'month', 'year')
                        ->get();

            $aflCopies = AflCopy::leftjoin('afl_assignments', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
                        ->selectRaw('MAX(id) AS id, MONTHNAME(arrdate) AS month, YEAR(arrdate) AS year, CONCAT("PK-G", substr(aflnbr, 3, 2)) AS acReg, COUNT(substr(aflnbr, 3, 2)) AS totalCopy, MIN(substr(aflnbr, 5)) AS startNum, MAX(substr(aflnbr, 5)) AS endNum')
                        ->where('afl_assignments.aflBoxId', '=', NULL)
                        ->whereDate('arrdate', '<', TODAY()->subMonths(6))
                        ->union($aflCopiesExstRaw)
                        ->groupBy('acReg', 'month', 'year')
                        ->get();

            return view('box.entryAFL', compact('box', 'aflCopiesExst', 'aflCopies'));
        }
    }

    function entry () {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $aflCopies = AflCopy::leftjoin('afl_assignments', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
                        ->selectRaw('MAX(id) AS id, MONTHNAME(arrdate) AS month, YEAR(arrdate) AS year, CONCAT("PK-G", substr(aflnbr, 3, 2)) AS acReg, COUNT(substr(aflnbr, 3, 2)) AS totalCopy, MIN(substr(aflnbr, 5)) AS startNum, MAX(substr(aflnbr, 5)) AS endNum')
                        ->where('afl_assignments.aflCopyId', '=', NULL)
                        ->whereDate('arrdate', '<', TODAY()->subMonths(6))
                        ->groupBy('acReg', 'month', 'year')
                        ->get();
            

            return view('box.entryAFL', compact('aflCopies'));
        }   
    }

    function readAll (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $boxes = AflBox::leftjoin('afl_assignments', 'afl_boxes.id', '=', 'afl_assignments.aflBoxId')
                        ->join('afl_copies', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
                        ->selectRaw('afl_boxes.id AS id, packNbr, MIN(afl_copies.arrdate) AS startDate, MAX(afl_copies.arrdate) AS endDate, location, COUNT(afl_assignments.aflBoxId) AS totalDoc')
                        ->groupBy('afl_boxes.id');

            return Datatables::of($boxes)
                    ->setRowId('id')
                    ->editColumn('startDate', function ($box) {
                        return date('M Y',strtotime($box->startDate));
                    })
                    ->editColumn('endDate', function ($box) {
                        return date('M Y',strtotime($box->endDate));  
                    })
                    ->addColumn('action', function ($box) {
                        return '<a href="../edit/afl/'.$box->id.'" style="font-weight: bold;" class="btn btn-warning">&#x1f589; Edit</a>'.' '.'<a href="../delete/afl/'.$box->id.'" method="post" style="font-weight: bold;" class="btn btn-danger btn-delete" onclick="return false;">X Delete</a>';
                    })
                    ->rawColumns(['startDate', 'endDate', 'action'])
                    ->make(true);
        }
    }

    function update (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){               
            if($rq->packYear == NULL || $rq->packNo == NULL){
                return ['error' => 'Package number must be filled.'];
            }else{
                $aflCopyId = json_decode($rq->aflCopyIdArray, true);

                $box = AflBox::where('id', $rq->id)->first();
                $packNbr = "AFL/".$rq->packYear."/".$rq->packNo;
                if ($box->packNbr != $packNbr) {
                    $boxEx = AflBox::where('packNbr', $packNbr)->first();                           
                    if ($boxEx) {
                        return ['error' => 'The box is already created.'];
                    } else {
                        $box->packNbr = $packNbr;
                    }
                }
                $box->notes = $rq->notes;
                $box->save();

                $time_start = microtime(true);
                $assignments = DB::table('afl_assignments')->where('aflBoxId', $box->id)->delete();

                for($i = 0; $i < count($aflCopyId); $i++) {                    
                    $userData = [];

                    $afl = AflCopy::where('id', $aflCopyId[$i])->first();
                    $aflCollectionId = AflCopy::where('aflnbr', 'LIKE', substr($afl->aflnbr, 0, 4).'%')
                                    ->whereMonth('arrdate', date('m', strtotime($afl->arrdate)))
                                    ->get()
                                    ->pluck('id')->toArray();

                    for($j = 0; $j < count($aflCollectionId); $j++) {
                        $userData[] = [
                            'aflBoxId' => $box->id,
                            'aflCopyId' => $aflCollectionId[$j],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }

                    $chunks = array_chunk($userData, 100);

                    foreach ($chunks as $chunk) {
                        DB::table('afl_assignments')->insert($chunk);
                    }
                }

                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);

                return ['success' => $rq->notes, 'success2' => 'Execction Time: '.$execution_time.' seconds.'];
            }
        }
    }

    function delete ($id) {
        if(Auth::user()->group == 'ADMIN'){
            $movement = DB::table('afl_box_movements')->where('id',$id)->delete();
            $assignments = DB::table('afl_assignments')->where('aflBoxId',$id)->delete();
            $box = AflBox::find($id)->delete();
            return ['success' => 'This box has been deleted.'];
        }
    }
}
