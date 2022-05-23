<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\AflCopy;
use App\AflBox;
use App\Movement;
use App\NonAflBox;
use Auth;
use DataTables;
use DB;
use Excel;
use Validator;

class MovementController extends Controller
{
	function convertDateFormat($date) {
		$number = substr($date,0,2);
		$month = substr($date,3,2);
		$year = substr($date,6,4);
		$date = $year."-".$month."-".$number;
		return Carbon::parse($date);
	}

	function edit($id){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){

			$aflBoxes = AflBox::leftjoin('afl_box_movements', 'afl_boxes.id', '=', 'afl_box_movements.aflBoxId')
						->join('afl_assignments', 'afl_boxes.id', '=', 'afl_assignments.aflBoxId')
						->join('afl_copies', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
						->selectRaw('afl_boxes.id AS id, packNbr, MIN(afl_copies.arrdate) AS startDate, MAX(afl_copies.arrdate) AS endDate, afl_boxes.notes AS notes, COUNT(afl_assignments.aflBoxId) AS totalDoc')
						->where('afl_box_movements.movementId', '=', $id)
						->groupBy('afl_boxes.id')
						->get();

			$nonAflBoxes = NonAflBox::leftjoin('non_afl_box_movements', 'non_afl_boxes.id', '=', 'non_afl_box_movements.nonAflBoxId')
						->join('non_afl_assignments', 'non_afl_boxes.id', '=', 'non_afl_assignments.nonAflBoxId')
						->join('submissions', 'submissions.id', '=', 'non_afl_assignments.submissionId')
						->selectRaw('non_afl_boxes.id AS id, classOfDate, packNbr, boxNbr, COUNT(non_afl_assignments.nonAflBoxId) AS totalDoc, SUM(submissions.quantity) AS totalCover')
						->where('non_afl_box_movements.movementId', $id)
						->groupBy('non_afl_boxes.id')
						->get();

			$movement = Movement::where('id', $id)->first();

			return view('movement.entry', compact('movement', 'aflBoxes', 'nonAflBoxes'));
		}
	}

	function create(Request $rq){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){
			$validator = Validator::make($rq->all(), [
				'requestDate' => 'required|date_format:d-m-Y',
				'shippingDate' => 'required|date_format:d-m-Y',
			]);

			if($validator->passes()) {
				$idAflBox = json_decode($rq->idAflArray,true);
				$idNonAflBox = json_decode($rq->nonIdAflArray,true);

				$movement = new Movement;
				$movement->requestDate = $this->convertDateFormat($rq->requestDate);
				$movement->shippingDate = $this->convertDateFormat($rq->shippingDate);
				$movement->status = config('enums.status')[1];
				$movement->totalBox = count($idAflBox) + count($idNonAflBox);
				$movement->save();

				for($i = 0; $i < count($idAflBox); $i++){
					$box = AflBox::where('id', $idAflBox[$i])->first();
					$box_movement = DB::table('afl_box_movements')->insert([
						'aflBoxId' => $box->id,
						'movementId' => $movement->id,
						'created_at' => Carbon::now(),
	                    'updated_at' => Carbon::now(),
					]);
				}

				for($i = 0; $i < count($idNonAflBox); $i++){
					$box = NonAflBox::where('id', $idNonAflBox[$i])->first();
					$box_movement = DB::table('non_afl_box_movements')->insert([
						'nonAflBoxId' => $box->id,
						'movementId' => $movement->id,
						'created_at' => Carbon::now(),
	                    'updated_at' => Carbon::now(),
					]);
				}	
				return ['success' => 'A movement request has been requested.'];
			}
			return ['errors' => $validator->getMessageBag()->toArray()];
		}
	}

	function entry() {
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){

			$aflBoxes = AflBox::leftjoin('afl_box_movements', 'afl_boxes.id', '=', 'afl_box_movements.aflBoxId')
						->join('afl_assignments', 'afl_boxes.id', '=', 'afl_assignments.aflBoxId')
						->join('afl_copies', 'afl_copies.id', '=', 'afl_assignments.aflCopyId')
						->selectRaw('afl_boxes.id AS id, packNbr, MIN(afl_copies.arrdate) AS startDate, MAX(afl_copies.arrdate) AS endDate, afl_boxes.notes AS notes, COUNT(afl_assignments.aflBoxId) AS totalDoc')
						->where('afl_box_movements.movementId', '=', NULL)
						->groupBy('afl_boxes.id')
						->get();

			$nonAflBoxes = NonAflBox::leftjoin('non_afl_box_movements', 'non_afl_boxes.id', '=', 'non_afl_box_movements.nonAflBoxId')
						->join('non_afl_assignments', 'non_afl_boxes.id', '=', 'non_afl_assignments.nonAflBoxId')
						->join('submissions', 'submissions.id', '=', 'non_afl_assignments.submissionId')
						->selectRaw('non_afl_boxes.id AS id, classOfDate, packNbr, boxNbr, COUNT(non_afl_assignments.nonAflBoxId) AS totalDoc, SUM(submissions.quantity) AS totalCover')
						// ->where('endRetentionDate', '<=', TODAY())
						->where('non_afl_box_movements.movementId', '=', NULL)
						->groupBy('non_afl_boxes.id')
						->get();

			return view('movement.entry', compact('aflBoxes', 'nonAflBoxes')); 
		}
	}

	function readAll(){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){
			$movements = Movement::select('id', 'requestDate', 'shippingDate', 'shippingNo', 'totalBox', 'status');
			
			return Datatables::of($movements)
						->setRowId('id')
                        ->editColumn('requestDate', function($movement){
                        	return date("d-m-Y", strtotime($movement->requestDate)); 
                        })
                        ->editColumn('shippingDate', function($movement){
                        	$date = date("d-m-Y", strtotime($movement->shippingDate));
	                        if($date == '30-11--0001' || $date == '01-01-1970'){
                        		return 'Not yet sent';
	                        } else {
	                        	return $date;
	                        }
                        })
                        ->editColumn('shippingNo', function($movement){
	                        if(empty($movement->shippingNo)){
                        		return 'Not yet sent';
	                        } else {
	                        	return $movement->shippingNo;
	                        }
                        })
                        ->editColumn('status', function($movement){
                            switch ($movement->status) {
                                case config('enums.status')[0]:
                        			return '<label style="font-weight: bold; color: red;">'.$movement->status.'</label>';
                                case config('enums.status')[3]:
                        			return '<label style="font-weight: bold; color: limegreen;">'.$movement->status.'</label>';
                                default: 
                        			return '<label style="font-weight: bold; color: orange;">'.$movement->status.'</label>';
                            }
                        })
                        ->addColumn('action', function($movement){
                    		return '<a href="edit/'.$movement->id.'" style="font-weight: bold;" class="btn btn-warning">&#x1f589; Edit</a>'.' '.'<a href="delete/'.$movement->id.'" method="post" style="font-weight: bold;" class="btn btn-danger btn-delete" onclick="return false;">X Delete</a>';
                        })
                        ->rawColumns(['requestDate', 'shippingDate', 'shippingNo', 'status', 'action'])
                        ->make(true);
		}
	}


	function update(Request $rq, $id){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){
			$statusColor = 'orange';
			$movement = Movement::find($id);
			$validator = Validator::make($rq->all(), [
				'newsNo' => 'required|'.Rule::unique('movements')->ignore($movement->id),
				'shippingDate' => 'required|date_format:d-m-Y',
                'shippingNo' => 'required|digits:6|'.Rule::unique('movements')->ignore($movement->id),
                'shippingNote' => 'required',
                'storageDate' => 'nullable|date_format:d-m-Y|after_or_equal:shippingDate|',
			]);
			
			if($validator->passes()){
				$afl_boxes = AflBox::join('afl_box_movements', 'afl_boxes.id', '=', 'afl_box_movements.aflBoxId')
						->where('afl_box_movements.movementId', $id)->get();
				$non_afl_boxes = NonAflBox::join('non_afl_box_movements', 'non_afl_boxes.id', '=', 'non_afl_box_movements.nonAflBoxId')
						->where('non_afl_box_movements.movementId', $id)->get();

				if($movement->status == config('enums.status')[1]) {
					$movement->status = config('enums.status')[2];
					$movement->newsNo = $rq->newsNo;
					$movement->shippingNo = $rq->shippingNo;
					$movement->shippingNote = $rq->shippingNote;
					$movement->shippingDate = $this->convertDateFormat($rq->shippingDate);

					foreach ($afl_boxes as $box) {
						$box->location = config('enums.location')[1];
						$box->save();
					}
					foreach ($non_afl_boxes as $box) {
						$box->location = config('enums.location')[1];
						$box->save();
					}
					//774.92 miliseconds -> 10 afl, 10 non afl
				} else if($movement->status == config('enums.status')[2] && isset($rq->storageDate)){
					$statusColor = 'limegreen';
					$movement->status = config('enums.status')[3];
					$movement->storageDate = $this->convertDateFormat($rq->storageDate);

					foreach ($afl_boxes as $box) {
						$box->location = config('enums.location')[2];
						$box->save();
					}
					foreach ($non_afl_boxes as $box) {
						$box->location = config('enums.location')[2];
						$box->save();
					}
					//3503.71 miliseconds -> 10 afl, 10 non afl
				}
				$movement->save();
				return ['success' => 'A movement request has been updated.', 'status' => $movement->status, 'statusColor' => $statusColor];
			}
			return ['errors' => $validator->getMessageBag()->toArray()];
		}
	}

	function cancel($id){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){	
			$movement = Movement::find($id);
			$movement->status = config('enums.status')[0];
		        
			$start_time = microtime(true);

			$afl_boxes = AflBox::join('afl_box_movements', 'afl_boxes.id', '=', 'afl_box_movements.aflBoxId')
					->where('afl_box_movements.movementId', $id)->get();
			$non_afl_boxes = NonAflBox::join('non_afl_box_movements', 'non_afl_boxes.id', '=', 'non_afl_box_movements.nonAflBoxId')
					->where('non_afl_box_movements.movementId', $id)->get();

			foreach ($afl_boxes as $box) {
				$box->location = config('enums.location')[0];
				$box->save();
			}
			foreach ($non_afl_boxes as $box) {
				$box->location = config('enums.location')[0];
				$box->save();
			}

			$afl_box_movements = DB::table('afl_box_movements')->where('movementId',$id)->delete();
	        $non_afl_box_movements = DB::table('non_afl_box_movements')->where('movementId',$id)->delete();
	        $movement->save();
	        //ex. time: 256.60 ms -> 3 afl, 197.13 ms -> 2 afl

	        $end_time = microtime(true);
			$execution_time = ($end_time - $start_time) * 1000;

	        return ['success' => 'A movement request has been cancelled. Execution Time: '.$execution_time.' miliseconds.', 'status' => $movement->status, 'statusColor' => 'red'];
		}

		return ['error' => 'Error error'];
	}

	function delete($id){
		if(Auth::user()->group == 'ADMIN' || Auth::user()->group == 'ANALYST'){
        	$movement = Movement::find($id);
	        if($movement->status != config('enums.status')[3]) {
		        $afl_box_movements = DB::table('afl_box_movements')->where('movementId',$id)->delete();
		        $non_afl_box_movements = DB::table('non_afl_box_movements')->where('movementId',$id)->delete();
	        	$movement->delete();
	        	return ['success' => 'This movement request has been deleted.'];
	        } 
        	return ['error' => 'This movement request cannot be deleted'];
		}
	}

// ====================================================================================================================================

	function print(){
		try
        {
			Excel::create('Form Pertelaan PFD', function($excel) {
				$excel->sheet('First sheet', function($sheet) {
					$sheet->setOrientation('landscape');
					$sheet->row(1, array(
					     'Storage Date', 'KLASIFIKASI', '', '', 'Retensi', 'Nilai Guna', 'Tahun', 'Nomor Boks', 'Keterangan'
					));
					$sheet->row(2, array(
					     '', 'Induk Persoalan', 'Pokok Persoalan', 'KODE', '', '(KU, HK, IT, IF)', '', '', ''
					));
					$sheet->setMergeColumn(array(
						'columns' => array('A', 'E', 'G', 'H', 'I'),
						'rows' => array(
							array(1,2),
						)
					));
					$sheet->mergeCells('B1:D1');
					for($i = 0; $i < 180; $i++){
						$sheet->row(3+($i*2), array(
							date("d-M"), 'OPERASI', 'Flight Support', 'OP/165', '2 Thn', 'IF', date("Y"), 'PFD/'.date("y").'/xxxx', 'Take off' 
						));
					}
				});
			})->download('xlsx');
		}
        catch (\Exception $e)
        {

        }
        //kolom storage date, klasifikasi: induk persoalan, pokok persoalan, kode, tahun, 'box number', 'keterangan'
		return back();
	}
}