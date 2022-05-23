<br>
<table border="1" id="myTable" style="width:100%">
    <thead>
        <th class="text-center" style="width:15%;">Form Number</th>
        <th class="text-center" style="width:30%;">Package Number</th>
        <th class="text-center">Location</th>
    </thead>
    <tbody align="center">
        @if(isset($submissions))
            @foreach($submissions as $subm)
            <tr>
                <td>{{$subm->formNbr}}</td>
                <td>{{$subm->packNbr}}</td>
                <td>{{"Temporary Storage"}}</td>
            </tr>
            @endforeach
        @else
            {{"No data found."}}
        @endif
    </tbody>
</table>

<script type="text/javascript">
	$('#myTable').DataTable({
		"searching": false
	});
</script>