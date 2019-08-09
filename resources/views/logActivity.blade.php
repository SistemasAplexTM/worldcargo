@extends('layouts.app')
@section('title', 'Logs')
@section('breadcrumb')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>
         @lang('general.users_logs')
        </h2>
    </div>
</div>
@endsection
@section('content')
<style type="text/css">
    .btn-inicio{
        font-size: 45px!important;
        margin-right: 0px;
    }
    .feed-element, .feed-element .media{
        padding-bottom: 0px;
    }
</style>
<div class="row" id="homeIndex">
    <div class="col-lg-12">
    	<h1>@lang('general.list_of_activities_list')</h1>
        <table id="tbl-log" class="table table-striped table-hover table-bordered" style="width: 100%;">
		<thead>
			<tr>
				<th>@lang('general.action')</th>
				<th>URL</th>
				<th>@lang('general.method')</th>
				<th>Ip</th>
				<th width="300px">@lang('general.user_agent')</th>
				<th>@lang('general.user')</th>
				<th>@lang('general.agency')</th>
			</tr>
		</thead>
	</table>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
    	$('#tbl-log').DataTable({
	        ajax: 'logActivity/all',
	        "order": [[0, "desc"]],
	        columns: [
	            {
	            	"render": function (data, type, full, meta) {
	            		return full.subject + '<br><small>'+full.created_at+'</small>';
	            	}
	            },
	            {data: 'url', name: 'url'},
	            {
	            	"render": function (data, type, full, meta) {
	            		var color = 'info';
	            		if(full.method == 'DELETE'){
	            			color = 'danger';
	            		}else{
	            			if(full.method == 'POST'){
								color = 'primary';
	            			}else{
	            				if(full.method == 'PUT'){
									color = 'warning';
		            			}
	            			}
	            		}
	            		return '<label class="label label-'+color+'">'+full.method+'</label>';
	            	}
	            },
	            {data: 'ip', name: 'ip'},
	            {data: 'agent', name: 'agent'},
	            {
	            	"render": function (data, type, full, meta) {
	            		return full.usuario + ' id('+full.user_id+')';
	            	}
	            },
	            {data: 'agencia', name: 'agencia'}
	        ],
	        'columnDefs': [
	        	{ className: "text-success", "targets": [ 1 ] },
	        	{ className: "text-warning", "targets": [ 3 ] },
	        	{ className: "text-danger", "targets": [ 4 ] },
	        ]
	    });
    });
</script>
@endsection

