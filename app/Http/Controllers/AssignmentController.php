<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use App\NonAflBox;
use App\Submission;
use Auth;
use DataTables;
use DB;
use Session;

class AssignmentController extends Controller
{

// -----------------------------------------------------------------------------------------------------------
    function doAssignToLogBox(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            if($rq->packYear != NULL && $rq->packNo) {
                $id = json_decode($rq->idArray,true);

                for($i = 0; $i < count($id); $i++){
                    $log = DB::table('afl_copies')->where('id',$id[$i])->first();
                }

                $box = new Box;
                $box->packNbr = "AFL/".$rq->packYear."/".$rq->packNo;
                $box->isAFLBox = 1;
                $box->endRetentionDate = Carbon::now();
                $box->save();

                for($i = 0; $i < count($id); $i++){
                    $log = DB::table('afl_copies')->where('id',$id[$i])->first();
                    $assign = DB::table('afl_assignments')->insert([
                    'boxId' => $box->id,
                    'aflCopyId' => $log->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    ]);
                }

                return ['success' => 'successfully.'];                
            }else{
                return ['error' => 'failled.'];
            }
        }
    }

    function doAssignToSubmissionBox(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $packageNo = "PFD/".$rq->year."/".$rq->packageNo;
            $box = Box::where('boxNbr',$rq->boxNo)->where('packNbr',$packageNo)->first();
            $id = json_decode($rq->idArray,true);

            for($i = 0; $i < count($id); $i++){
                $submission = DB::table('submission_assignments')->where('submissionId',$id[$i])->first();
                if($submission){
                    return ['error' => 'One or more submission are already assigned.'];
                }
            }

            for($i = 0; $i < count($id); $i++) {
                $submission = Submission::where('id',$id[$i])->first();
                $submission_assignment = DB::table('submission_assignments')->insert([
                    'submissionId' => $submission->id,
                    'boxId' => $box->id,
                ]);
            }
            
            return ['success' => 'The submission successfully assigned.', 'boxId' => $box->id];
        }
    }

    function doCountAssignedSubmissions(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $totalSubmission = DB::table('submission_assignments')->where('boxId',$rq->id)->count();
            return Response::json($totalSubmission);
        }
    }

    function doEntrySubmissionBox(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $number = substr($rq->classOfDate,0,2);
            $month = substr($rq->classOfDate,3,2);
            $year = substr($rq->classOfDate,6,4);
            $date = $year."-".$month."-".$number;
            $classOfDate = Carbon::parse($date);

            $packageNo = "PFD/".$rq->packageYear."/".$rq->packageNo;

            $exist1 = Box::where('packNbr',$packageNo)->first();
            
            if($exist1){
                return ['error' => 'The box has already been taken.'];
            }else{
                $box = new Box;
                $box->boxNbr = $rq->boxNo;
                $box->classOfDate = $classOfDate;
                $box->packNbr = $packageNo;
                $box->endRetentionDate = Carbon::parse($date)->addMonths(3);
                $box->save();
                return ['success' => 'The box has successfully created.'];
            }
        }
    }

    function doGoToEditPage($id){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $box = Box::where('id',$id)->first();
            return view('box.entry', compact('box'));
        }
    }

    function doLoadAllData(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $dateRegex = "/^(\d{2})-(\d{2})-(\d{4})$/";

            if(preg_match($dateRegex,$rq->packDate)){
                $number = substr($rq->packDate,0,2);
                $month = substr($rq->packDate,3,2);
                $year = substr($rq->packDate,6,4);
                $packDate = $year."-".$month."-".$number;

                $boxes = Box::leftjoin('non_afl_assignments', 'boxes.id', '=', 'non_afl_assignments.boxId')
                            ->selectRaw('boxes.boxNbr AS boxNbr, boxes.id AS id, boxes.packNbr AS packNbr, boxes.classOfDate AS classOfDate')
                            ->leftjoin('submissions', 'non_afl_assignments.submissionId', '=', 'submissions.id')
                            ->selectRaw('COUNT(non_afl_assignments.submissionId) AS totalSubmission,
                                        SUM(submissions.quantity) AS totalDocument')
                            ->where('classofDate','=',$packDate)
                            ->groupBy('packNbr')
                            ->orderBy('packNbr', 'asc')
                            ->get();

                return view('box.indexList', compact('boxes'));
            }
        }
    }

    function doLoadAllAflBoxData(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $boxes = Box::leftjoin('afl_assignments', 'boxes.id', '=', 'afl_assignments.boxId')
                            ->selectRaw('boxes.boxNbr AS boxNbr, boxes.id AS id, boxes.packNbr AS packNbr, boxes.classOfDate AS classOfDate')
                            ->leftjoin('afl_copies', 'afl_assignments.submissionId', '=', 'afl_copies.id')
                            ->selectRaw('COUNT(non_afl_assignments.submissionId) AS totalSubmission,
                                        SUM(submissions.quantity) AS totalDocument')
                            ->orderBy('packNbr', 'asc')
                            ->get();

            return Datatables::of($boxes)
                    ->addColumn('action', function ($user) {
                        return '<a href="edit/box/afl/'.$boxes->id.'" style="font-weight: bold;" class="btn-sm btn-warning">&#x1f589; Edit</a>';
                    })
                    ->make(true);
        }   
    }

    function doLoadAllNonAssignedData(){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $submissions = Submission::leftjoin('non_afl_assignments', 'submissions.id', '=', 'non_afl_assignments.submissionId')
                        ->select('id', 'formNbr', 'quantity', 'empNbr', 'signed', 'receivedDate', 'empRank')
                        ->where('non_afl_assignments.submissionId', '=', NULL)->get();

            return view('box.assignList',compact('submissions'));
        }
    }

    function doLoadAllNonAssignedDataLog(){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $logs = DB::table('afl_copies')
                    ->leftjoin('afl_assignments', 'afl_copies.id', '=' , 'afl_assignments.aflCopyId')
                    ->selectRaw('afl_copies.id, aflNbr, arrdate, picnew, fltnbr, arrstn, depstn')
                    ->whereDate('afl_copies.created_at', '<=', Carbon::now()->subMonths(6))
                    ->where('afl_assignments.aflCopyId', '=', NULL)
                    ->paginate(50);

            return view('box.entryAFL',compact('logs'));
        }
    }
}