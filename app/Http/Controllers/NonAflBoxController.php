<?php

namespace App\Http\Controllers;

use App\NonAflBox;
use App\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;
use DataTables;
use DB;
use Session;

class NonAflBoxController extends Controller
{
    function create (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $packNbr = "PFD/".$rq->packYear."/".$rq->packNo;
            $box = NonAflBox::where('packNbr', $packNbr)->first();

            if($box){
                return ['error' => 'The box is already created.'];
            }else if($rq->packYear == NULL || $rq->packNo == NULL){
                return ['error' => 'Package number must be filled.'];
            }else{
                $submissionId = json_decode($rq->submissionIdArray,true);

                for($i = 0; $i < count($submissionId); $i++){
                    $submission = Submission::join('non_afl_assignments', 'submissions.id', '=', 'non_afl_assignments.submissionId')->where('id', $submissionId[$i])->first();
                    if($submission){
                        return ['error' => 'Submission: '.$submission->formNbr.' is already assigned to another box.'];
                    }
                }

                $number = substr($rq->classOfDate,0,2);
                $month = substr($rq->classOfDate,3,2);
                $year = substr($rq->classOfDate,6,4);
                $date = $year."-".$month."-".$number;
                $classOfDate = Carbon::parse($date);

                $box = new NonAflBox;
                $box->packNbr = $packNbr;
                $box->boxNbr = $rq->boxNo;
                $box->classOfDate = $classOfDate;
                $box->location = config('enums.location')[0];;
                $box->endRetentionDate = Carbon::parse($date)->addMonths(3);
                $box->save();

                for($i = 0; $i < count($submissionId); $i++){
                    $submission = Submission::where('id', $submissionId[$i])->first();
                    $assignment = DB::table('non_afl_assignments')->insert([
                        'nonAflBoxId' => $box->id,
                        'submissionId' => $submission->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                return ['success' => 'Box is created successfully.'];
            }
        }
    }

    function edit ($id) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $box = NonAflBox::leftjoin('non_afl_assignments', 'non_afl_boxes.id', '=', 'non_afl_assignments.nonAflBoxId')
                    ->selectRaw('non_afl_boxes.boxNbr AS boxNbr, non_afl_boxes.id AS id, non_afl_boxes.packNbr AS packNbr, non_afl_boxes.classOfDate AS classOfDate')
                    ->leftjoin('submissions', 'non_afl_assignments.submissionId', '=', 'submissions.id')
                    ->selectRaw('COUNT(non_afl_assignments.submissionId) AS totalSubmission')
                    ->where('non_afl_boxes.id',$id)
                    ->first();
            $submissions = Submission::join('non_afl_assignments', 'submissions.id', '=', 'non_afl_assignments.submissionId')
                            ->where('nonAflBoxId', $id)->get();

            return view('box.entry', compact('box', 'submissions'));
        }
    }

    function readAll (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $dateRegex = "/^(\d{2})-(\d{2})-(\d{4})$/";

            if(preg_match($dateRegex,$rq->packDate)){
                $number = substr($rq->packDate,0,2);
                $month = substr($rq->packDate,3,2);
                $year = substr($rq->packDate,6,4);
                $packDate = $year."-".$month."-".$number;

                $boxes = NonAflBox::leftjoin('non_afl_assignments', 'non_afl_boxes.id', '=', 'non_afl_assignments.nonAflBoxId')
                            ->selectRaw('non_afl_boxes.boxNbr AS boxNbr, non_afl_boxes.id AS id, non_afl_boxes.packNbr AS packNbr, non_afl_boxes.classOfDate AS classOfDate')
                            ->leftjoin('submissions', 'non_afl_assignments.submissionId', '=', 'submissions.id')
                            ->selectRaw('COUNT(non_afl_assignments.submissionId) AS totalSubmission,
                                        SUM(submissions.quantity) AS totalCover')
                            ->where('classofDate','=',$packDate)
                            ->groupBy('packNbr')
                            ->orderBy('packNbr', 'asc')
                            ->get();

                return view('box.indexList', compact('boxes'));
            }
        }        
    }

    function update (Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){               
            if($rq->packYear == NULL || $rq->packNo == NULL){
                return ['error' => 'Package number must be filled.'];
            }else{
                $submissionId = json_decode($rq->submissionIdArray,true);

                for($i = 0; $i < count($submissionId); $i++){
                    $submission = Submission::where('id', $submissionId[$i])->first();
                    if($submission){
                        return ['error' => 'Submission: '.$submission->formNbr.' is already assigned to another box.'];
                    }
                }

                $number = substr($rq->classOfDate,0,2);
                $month = substr($rq->classOfDate,3,2);
                $year = substr($rq->classOfDate,6,4);
                $date = $year."-".$month."-".$number;
                $classOfDate = Carbon::parse($date);

                $box = NonAflBox::where('id', $rq->id)->first();
                $packNbr = "AFL/".$rq->packYear."/".$rq->packNo;
                if ($box->packNbr != $packNbr) {
                    $boxEx = NonAflBox::where('packNbr', $packNbr)->first();                           
                    if ($boxEx) {
                        return ['error' => 'The box is already created.'];
                    } else {
                        $box->packNbr = $packNbr;
                    }
                }
                $box->boxNbr = $rq->boxNo;
                $box->classOfDate = $classOfDate;
                $box->save();

                $assignments = DB::table('non_afl_assignments')->where('nonAflBoxId', $box->id)->delete();

                for($i = 0; $i < count($id); $i++){
                    $submission = Submission::where('id', $submissionId[$i])->first();
                    $assignment = DB::table('non_afl_assignments')->insert([
                        'nonAflBoxId' => $box->id,
                        'submissionId' => $submission->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                return ['success' => 'Box is updated successfully.'];
            }
        }
    }

    function delete ($id) {
        if(Auth::user()->group == 'ADMIN'){
            $movement = DB::table('non_afl_box_movements')->where('id',$id)->delete();
            $assignments = DB::table('non_afl_assignments')->where('nonAflBoxId',$id)->delete();
            $box = NonAflBox::find($id)->delete();
            return ['success' => 'This box has been deleted.'];
        }
    }
}
