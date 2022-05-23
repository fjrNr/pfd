<table border="1" class="table table-hover" id="myTable" style="width:100%">
	<thead>
		<th class="text-center">Class Of</th>
		<th class="text-center">Package Number</th>
		<th class="text-center">Box</th>
		<th class="text-center">Total Submission</th>
		<th class="text-center">Total Cover</th>
		<th class="text-center">Action</th>
	</thead>
	<tbody align="center">
		@if(isset ($boxes))
			@foreach($boxes as $box)
				<tr>
					<td>{{$box->boxNbr}}</td>
					<td>{{date('d-m-Y', strtotime($box->classOfDate))}}</td>
					<td>{{$box->packNbr}}</td>
					<td>{{$box->totalSubmission}}</td>
					<td>
						@if(isset ($box->totalCover))
							{{$box->totalCover}}
						@else
							{{"0"}}
						@endif
					</td>
					<td><a href="{{url('/box/edit/')}}/{{$box->id}}" style="font-weight: bold;" class="btn-sm btn-warning"> &#x1f589; Edit</a></td>
				</tr>
			@endforeach
		@else
			{{"Data Not found."}}
		@endif
	</tbody>
</table>