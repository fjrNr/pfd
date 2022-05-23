<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AflCopy;
use App\Submission;
use App\SubmissionDetail;
use Auth;
use Validator;
use Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use DB;
use DataTables;

class SubmissionController extends Controller
{

    function create(Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC'){
            $errors2 = [];

            $validator = Validator::make($rq->all(), [
                'empNbr' => 'nullable|digits:6',
                'receivedDate' => 'nullable|date_format:d-m-Y',
                'formNbr' => 'required|unique:submissions|digits_between:4,7',
                'qtyDoc' => 'required|numeric|min:1',
            ]);

            if($rq->input('aflNbr')){
                //for validate unique value
                foreach ($rq->input('aflNbr') as $key => $value) {
                    $aflNbr = [
                        'aflNbr' => $rq->aflNbr[$key],
                    ];
                    $aflValidator = Validator::make($aflNbr, 
                        ['aflNbr' => 'required|unique:submission_details,aflNbr|regex:/(^(\d{2})([A-Z]{2})(\d{4})([A-Z]?)$)/u'], 
                        ['unique' => 'The value of :attribute field has already been taken.'],
                        ['aflNbr' => '(item) AFL number']
                    );   
                    $aflErrors = [];

                    if ($aflValidator->fails()) {
                        $aflErrors = $aflValidator->errors()->all();
                    }

                    foreach($aflErrors as $k => $v) {
                        $rank = ++$key;
                        switch ($rank) {
                            case 1: $rank = $rank.'st'; break;
                            case 2: $rank = $rank.'nd'; break;
                            case 3: $rank = $rank.'rd'; break;
                            default: $rank = $rank.'th'; break;
                        }
                        $aflErrors[$k] = str_replace('(item)', $rank, $v);
                    }
                    $errors2 = array_merge($errors2, $aflErrors);
                }

                //for validate distinct value
                $aflNbrs = [
                    'aflNbr' => $rq->aflNbr,
                ];
                $aflValidator2 = Validator::make(
                    $aflNbrs, 
                    ['aflNbr.*' => 'distinct']
                );
                $aflErrors2 = [];

                if($aflValidator2->fails()) {
                    $aflErrors2 = $aflValidator2->errors()->all();
                }                

                foreach($aflErrors2 as $k => $v) {
                    $aflErrors2[$k] = str_replace('aflNbr.', 'AFL Number ', $v);
                    $rank = explode(" ", $aflErrors2[$k])[3];
                    $aflErrors2[$k] = str_replace($rank, '', $aflErrors2[$k]);
                    switch (++$rank){
                        case 1: $rank = $rank.'st'; break;
                        case 2: $rank = $rank.'nd'; break;
                        case 3: $rank = $rank.'rd'; break;
                        default: $rank = $rank.'th'; break;
                    };
                    $aflErrors2[$k] = str_replace('The AFL Number  ', 'The '.$rank.' AFL Number ', $aflErrors2[$k]);
                }

                $errors2 = array_merge($errors2, $aflErrors2);
            }

            if ($validator->passes() && empty($errors2)) {
                $submission = new Submission;
                $crew = DB::table('crews')->where('empnbr', $rq->empNbr)->first();
                if($rq->empNbr){
                    $submission->empNbr = $crew->empNbr;
                    $submission->empRank = $crew->empRank;
                    $submission->signed = $crew->firstName.' '.$crew->middleName.' '.$crew->lastName;
                }                
                if($rq->receivedDate){
                    $number = substr($rq->receivedDate,0,2);
                    $month = substr($rq->receivedDate,3,2);
                    $year = substr($rq->receivedDate,6,4);
                    $date = $year."-".$month."-".$number;
                    $submission->receivedDate = Carbon::parse($date);
                }

                $submission->inputBy = Auth::user()->id;
                $submission->formNbr = $rq->formNbr;
                $submission->quantity = $rq->qtyDoc;
                $submission->remark = $rq->remark;
                $submission->save();

                if($rq->input('aflNbr')){
                    foreach ($rq->input('aflNbr') as $key => $v) {
                        $log = new SubmissionDetail;
                        $log->aflNbr = $rq->aflNbr[$key];
                        $log->submissionId = $submission->id;
                        $log->flightPlan = filter_var($rq->cbFlightPlan[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->dispatchRelease = filter_var($rq->cbDispatch[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->weatherForecast = filter_var($rq->cbWeather[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->notam = filter_var($rq->cbNotam[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->toLdgDataCard = filter_var($rq->cbLdgData[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->loadSheet = filter_var($rq->cbLoadSheet[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->fuelReceipt = filter_var($rq->cbFuel[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->paxManifest = filter_var($rq->cbPax[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->notoc = filter_var($rq->cbNotoc[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->save();
                    }
                }

                return ['success' => 'Insert successfully!'];
            }
            return ['errors' => $validator->getMessageBag()->toArray(), 'errors2' => $errors2];
        }
    }

    function edit($id) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC'){
            $submission = Submission::where('id', $id)->where('inputBy',Auth::user()->id)->first();
            if($submission){
                $logs = SubmissionDetail::where('submissionId', $id)->get();
                return view('submission.entry', compact('submission', 'logs'));
            }else{
                return view('errors.401');
            }
        }else{
            return view('errors.401');
        }
    }

    function readAll(Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC'){
            $dateRegex = "/^(\d{2})-(\d{2})-(\d{4})$/";
            $submissions = Submission::select('empNbr', 'id', 'receivedDate', 'formNbr', 'quantity', 'inputBy', 'created_at', 'signed', 'empRank')
                        ->where('inputBy', 'LIKE', '%'.$rq->inputBy.'%')
                        ->where('formNbr', 'LIKE', '%'.$rq->formNbr.'%');

            function changeFormatDate($oldDate){
                $number = substr($oldDate,0,2);
                $month = substr($oldDate,3,2);
                $year = substr($oldDate,6,4);
                $newDate = $year."-".$month."-".$number;
                return $newDate;
            }

            if(preg_match($dateRegex, $rq->revDate) && $rq->revDate){
                $revDate = changeFormatDate($rq->revDate);
                $submissions->whereDate('submissions.receivedDate', '=', $revDate);
            }

            return Datatables::of($submissions)
                    ->setRowId('id')
                    ->editColumn('empNbr', '@if(isset ($empNbr)){{$empNbr}} @else {{"Unidentified"}} @endif')
                    ->addColumn('action', function ($submission) {
                        $view = '<a href="edit/'.$submission->id.'" style="font-weight: bold;" class="btn btn-warning">&#x1f589; Edit</a>';
                        if(Auth::user()->group == 'ADMIN'){
                            $view .= '&nbsp;<a href="delete/'.$submission->id.'" method="post" style="font-weight: bold;" class="btn btn-danger btn-delete" onclick="return false;">X Delete</a>';

                        }
                        return $view;
                    })
                    ->make(true);

        }
    }

    function readAutocomplete(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC' || Auth::user()->group == 'ANALYST'){

            $query = $rq->get('term', '');
            $submissions = Submission::rightjoin('non_afl_assignments', 'non_afl_assignments.submissionId', '=', 'submissions.id')
                            ->join('non_afl_boxes', 'non_afl_boxes.id', '=', 'non_afl_assignments.nonAflBoxId')
                            ->where('formNbr', 'LIKE', '%'.$query.'%')->paginate(5);
            $data = [];
            foreach ($submissions as $key => $subm){
                $data[] = ['value'=> $subm->formNbr,
                    'idtyBox' => $subm->packNbr,
                    'classOfDate' => $subm->classOfDate,
                    'boxNo' => $subm->boxNbr,
                ];
            }

            if(count($data)){
                return response($data);
            }else{
                return ['value' => 'No result found', 'id' => ''];
            }
        }
    }

    function readAssignedOnly(Request $rq){
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $dateRegex = "/^(\d{2})-(\d{2})-(\d{4})$/";
            $submissions = Submission::join('non_afl_assignments', 'non_afl_assignments.submissionId', '=', 'submissions.id')
                            ->join('non_afl_boxes', 'non_afl_boxes.id', '=', 'non_afl_assignments.aflBoxId')
                            ->join('submission_details', 'submission_details.submissionId', '=', 'submissions.id')
                            ->selectRaw('submissions.formNbr, non_afl_boxes.packNbr');

            if($rq->fltnbr || $rq->depstn || $rq->arrstn || $rq->fltdate || $rq->pic){
                if(preg_match($dateRegex,$rq->fltdate)){
                    $number = substr($rq->fltdate,0,2);
                    $month = substr($rq->fltdate,3,2);
                    $year = substr($rq->fltdate,6,4);
                    $rq->fltdate = $year."-".$month."-".$number;
                }

                $in_afls = DB::table('afl_copies')->select('aflnbr')
                            ->where('fltnbr','LIKE','%'.$rq->fltnbr.'%')
                            ->where('depstn','LIKE','%'.$rq->depstn.'%')
                            ->where('arrstn','LIKE','%'.$rq->arrstn.'%')
                            ->where('depdate','LIKE','%'.$rq->fltdate.'%');

                if($rq->pic){
                    $in_afls = $in_afls->where('picnew','LIKE','%'.$rq->pic.'%')->get();    
                }else{
                    $in_afls = $in_afls->get();
                }
    
                $aflnbrs = json_encode($in_afls);
                $aflnbrs = str_replace('{"aflnbr":','',$aflnbrs);
                $aflnbrs = str_replace('}','',$aflnbrs);
                $aflnbrs = json_decode($aflnbrs);

                $submissions = $submissions->whereIn('afl_copies.aflNbr', $aflnbrs)->groupBy('formNbr')->get();
            }else{
                $submissions = $submissions->groupBy('formNbr')->get();
            }

            return view('searchList',compact('submissions'));
        }
    }

    function readNonAssignedOnly(Request $rq) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'BOX OFC'){
            $submissions = Submission::leftjoin('non_afl_assignments', 'submissions.id', '=', 'non_afl_assignments.submissionId')->select('empNbr', 'id', 'receivedDate', 'formNbr', 'quantity', 'signed', 'empRank')
                ->where('non_afl_assignments.submissionId', '=', NULL);

            return Datatables::of($submissions)
                    ->setRowId('id')
                    ->editColumn('empNbr', '@if(isset ($empNbr)){{$empNbr}} @else {{"Unidentified"}} @endif')
                    ->make(true);

        }
    }

    function update(Request $rq, $id) {
        if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'SUBM OFC'){
            $errors2 = [];

            $submission = Submission::where('id', $id)->where('inputBy', Auth::user()->id)->first();
            $validator = Validator::make($rq->all(), [
                'empNbr' => 'nullable|digits:6',
                'receivedDate' => 'nullable|date_format:d-m-Y',
                'formNbr' => 'required|digits_between:4,5|'.Rule::unique('submissions')->ignore($submission->id),
                'qtyDoc' => 'required|numeric|min:1',
            ]);

            if($rq->input('aflNbr')){
                //for validate unique value
                foreach ($rq->input('aflNbr') as $key => $value) {
                    $uniqueRule = '';
                    $submission_detail = SubmissionDetail::where('submissionId', $id)->where('aflNbr',$rq->aflNbr[$key])->first();

                    if(empty($submission_detail)){
                        $uniqueRule = 'unique:submission_details,aflNbr';
                    }else{
                        $uniqueRule = Rule::unique('submission_details')->ignore($submission_detail->id);
                    }

                    $aflNbr = [
                        'aflNbr' => $rq->aflNbr[$key],
                    ];
                    $aflValidator = Validator::make($aflNbr, [
                        'aflNbr' => [
                            'required',
                            'regex:/(^(\d{2})([A-Z]{2})(\d{4})([A-Z]?)$)/u',
                            $uniqueRule
                        ]],
                        ['unique' => 'The value of :attribute field has already been taken.'],
                        ['aflNbr' => '(item) AFL number']
                    );   
                    $aflErrors = [];

                    if ($aflValidator->fails()) {
                        $aflErrors = $aflValidator->errors()->all();
                    }

                    foreach($aflErrors as $k => $v) {
                        $rank = ++$key;
                        switch ($rank) {
                            case 1: $rank = $rank.'st'; break;
                            case 2: $rank = $rank.'nd'; break;
                            case 3: $rank = $rank.'rd'; break;
                            default: $rank = $rank.'th'; break;
                        }
                        $aflErrors[$k] = str_replace('(item)', $rank, $v);
                    }
                    $errors2 = array_merge($errors2, $aflErrors);
                }

                //for validate distinct value
                $aflNbrs = [
                    'aflNbr' => $rq->aflNbr,
                ];
                $aflValidator2 = Validator::make(
                    $aflNbrs, 
                    ['aflNbr.*' => 'distinct']
                );
                $aflErrors2 = [];

                if($aflValidator2->fails()) {
                    $aflErrors2 = $aflValidator2->errors()->all();
                }                

                foreach($aflErrors2 as $k => $v) {
                    $aflErrors2[$k] = str_replace('aflNbr.', 'AFL Number ', $v);
                    $rank = explode(" ", $aflErrors2[$k])[3];
                    $aflErrors2[$k] = str_replace($rank, '', $aflErrors2[$k]);
                    switch (++$rank){
                        case 1: $rank = $rank.'st'; break;
                        case 2: $rank = $rank.'nd'; break;
                        case 3: $rank = $rank.'rd'; break;
                        default: $rank = $rank.'th'; break;
                    };
                    $aflErrors2[$k] = str_replace('The AFL Number  ', 'The '.$rank.' AFL Number ', $aflErrors2[$k]);
                }

                $errors2 = array_merge($errors2, $aflErrors2);
            }

            if ($validator->passes() && empty($errors2)) {
                $crew = DB::table('crews')->where('empnbr', $rq->empNbr)->first();
                if($rq->empNbr){
                    $submission->empNbr = $crew->empNbr;
                    $submission->empRank = $crew->empRank;
                    $submission->signed = $crew->firstName.' '.$crew->middleName.' '.$crew->lastName;
                }                
                if($rq->receivedDate){
                    $number = substr($rq->receivedDate,0,2);
                    $month = substr($rq->receivedDate,3,2);
                    $year = substr($rq->receivedDate,6,4);
                    $date = $year."-".$month."-".$number;
                    $submission->receivedDate = Carbon::parse($date);
                }
                
                $submission->formNbr = $rq->formNbr;
                $submission->quantity = $rq->qtyDoc;
                $submission->remark = $rq->remark;

                if($rq->input('aflNbr')){
                    //delete log at outside this submission
                    $logs = SubmissionDetail::where('submissionId',$id)->whereNotIn('aflNbr',$rq->aflNbr)->delete();

                    //insert update
                    foreach ($rq->input('aflNbr') as $key => $v) {
                        $log = SubmissionDetail::where('aflNbr',$rq->aflNbr[$key])->where('submissionId', $id)->first();
                        if(!$log){
                            $log = new SubmissionDetail;
                            $log->submissionId = $id;
                        }
                        
                        $log->aflNbr = $rq->aflNbr[$key];
                        $log->flightPlan = filter_var($rq->cbFlightPlan[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->dispatchRelease = filter_var($rq->cbDispatch[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->weatherForecast = filter_var($rq->cbWeather[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->notam = filter_var($rq->cbNotam[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->toLdgDataCard = filter_var($rq->cbLdgData[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->loadSheet = filter_var($rq->cbLoadSheet[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->fuelReceipt = filter_var($rq->cbFuel[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->paxManifest = filter_var($rq->cbPax[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->notoc = filter_var($rq->cbNotoc[$key], FILTER_VALIDATE_BOOLEAN);
                        $log->save();
                    }

                }
                $submission->save();

                return ['success' => 'Update successfully!'];
            }
            return ['errors' => $validator->getMessageBag()->toArray(), 'errors2' => $errors2];
        }
    }

    function delete($id) {
        if(Auth::user()->group == 'ADMIN'){
            $assignment = DB::table('non_afl_assignments')->where('submissionId', $id)->delete();
            $logs = SubmissionDetail::where('submissionId', $id)->delete();
            $submission = Submission::find($id)->delete();
            return ['success' => 'This submission has been deleted.'];
        }
    }

    function showAflValidated() {

    }
}