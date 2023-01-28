@extends('dashboard.base')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('/vendors/DataTables/datatables.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('/vendors/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/vendors/jquery/jquery-ui.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css">
<style>
	#tbl-sims {
	    margin: 0 auto;
	    width: 100%;
	    clear: both;
	    border-collapse: collapse;
	    table-layout: fixed; // ***********add this
	    word-wrap:break-word; // ***********and this
	}
	
	.col-modal-body {
		max-height: 400px;
		overflow-y: scroll;
	}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="fade-in">
	  <div class="row">
	    <div class="col-md-12">
	      <div class="card">
	        <div class="card-header d-flex justify-content-between">Products</div>
	        <div class="card-body">
	          <table class="table table-responsive-sm table-hover table-outline mb-0" id="tbl-sims" width="100%">
	            <thead class="thead-light">
	              <tr>
	                <th>Source</th>
					<th>Title</th>
					<th>SKU</th>
	                <th>Barcode</th>
					<th>Status</th>
	  				<th>Actions</th>
	              </tr>
	            </thead>
	          </table>
	        </div>
	      </div>
	    </div>
	    <!-- /.col-->
	  </div>
	  <!-- /.row-->
	</div>
</div>

<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->
<div id="mappingModal" class="modal fade" data-backdrop="static" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Map Product Fields (id:<span id="edit_id"></span>)</h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		<div class="row">
		    <div class="col col-modal-body">
				<form id="shopifyProductFields">
				  <input type="hidden" name="id" id="record_id" value="">
				  <!--<div class="form-group">
				    <label>Title</label>
				    <input type="text" class="form-control" id="title" name="title">
				  </div>-->
				  <?php foreach($sproduct_fields as $field){ ?>
					<?php
						$title = str_replace("_"," ",$field);
						$title = ucwords($title);
					?>
					<div class="form-group">
						<label><?php echo $title; ?></label>
						<input type="text" class="form-control sproduct_field" id="<?php echo $field; ?>" name="<?php echo $field; ?>">
						<small id="<?php echo $field; ?>Help" class="form-text text-muted"></small>
					</div>
				  <?php } ?>
				  <?php foreach($fixed_fields as $field){ ?>
					<?php
						$title = str_replace("_"," ",$field);
						$title = ucwords($title);
					?>
					<div class="form-group">
						<label><?php echo $title; ?></label>
						<input type="text" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" readonly>
						<small id="<?php echo $field; ?>Help" class="form-text text-muted"></small>
					</div>
				  <?php } ?>
				</form>
		    </div>
		    <div class="col col-modal-body" id="matches">
				<!--<div id="product-1">
					<p>
					  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse-1">
					    Sample Record id:1
					  </button>
					</p>
					<div class="collapse in show" id="collapse-1" style="" aria-expanded="true">
					  <div class="card card-body">
					    <p>[STATUS] - active</p>  
						<p>[CODE] - ABS0008</p> 
					  </div>
					</div>
				</div>-->
		    </div>
		</div>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="save-btn">Save changes</button>
      </div>
    </div>

  </div>
</div>

<div id="postingModal" class="modal fade" data-backdrop="static" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send to Shopify (id:<span id="edit_id"></span>)</h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		<div class="row">
		    <div class="col col-modal-body">
				<form id="shopifyProductFields">
				  <input type="hidden" name="id" id="record_id" value="">
				  <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
				  <!--<div class="form-group">
				    <label>Title</label>
				    <input type="text" class="form-control" id="title" name="title">
				  </div>-->
				  <?php foreach($sproduct_fields as $field){ ?>
					<?php
						$title = str_replace("_"," ",$field);
						$title = ucwords($title);
					?>
					<div class="form-group">
						<label><?php echo $title; ?></label>
						<input type="text" class="form-control sproduct_field" id="<?php echo $field; ?>" name="<?php echo $field; ?>" readonly>
						<small id="<?php echo $field; ?>Help" class="form-text text-muted"></small>
					</div>
				  <?php } ?>
				  <?php foreach($fixed_fields as $field){ ?>
					<?php
						$title = str_replace("_"," ",$field);
						$title = ucwords($title);
					?>
					<div class="form-group">
						<label><?php echo $title; ?></label>
						<input type="text" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" readonly>
						<small id="<?php echo $field; ?>Help" class="form-text text-muted"></small>
					</div>
				  <?php } ?>
				</form>
		    </div>
		    <div class="col col-modal-body" id="matches">
		    </div>
		</div>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary" id="new-product-btn">New Product</button>
		<button type="button" class="btn btn-primary" id="new-variant-btn">New Variant</button>
      </div>
    </div>

  </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('/vendors/jquery/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/vendors/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
		$.ajaxSetup({
			headers: {
			  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$('#tbl-sims thead tr')
			.clone(true)
			.addClass('filters')
			.appendTo('#tbl-sims thead');
			
		function addTokenAction(){
			$('#mappingModal .fieldtoken').click(function(){
				var token = $(this).attr('data-token');
				var value = $(this).attr('data-value');
				console.log('token: ' + token);
				console.log('value: ' + value);
				console.log('active field id: ' + last_active_id);
				console.log('cursor position: ' + last_cursor_position);
				if(last_active_id != ''){
					var v = $('#' + last_active_id).val();
					var textBefore = v.substring(0,  last_cursor_position);
					var textAfter  = v.substring(last_cursor_position, v.length);

					$('#' + last_active_id).val(textBefore + token + textAfter);
					$('#' + last_active_id).trigger('change');
					$('#' + last_active_id).prop('selectionStart', textBefore.length + token.length);
					$('#' + last_active_id).prop('selectionEnd', textBefore.length + token.length);
					$('#' + last_active_id).focus();
				}
				return(false);
			});	
		}
		
		function getMappingRecords(table,id){
			$('#mappingModal #record_id').val(id);
			$('#mappingModal #edit_id').html(id);
			$('#mappingModal .sproduct_field').val('');
			$('#mappingModal .form-text').html('');
            $.ajax({
              type: "GET",
              url: '/api/' + table + '/getMappingRecords',
			  data: {id: id},
              success: function(data){
                //console.log(data);
				console.log('status: ' + data.status);
				var values = data.values;
				for(var name in values){
					var val = values[name];
					$('#mappingModal #' + name).val(val);
				}
				var cvalues = data.cvalues;
				for(var name in cvalues){
					var val = cvalues[name];
					/*if(val.includes("=")){
						val = val.replace("=","");
						val = eval(val);
					}*/
					if(val != '') val = 'ex. ' + val;
					$('#mappingModal #' + name + 'Help').html(val);
				}
				var ivalues = data.ivalues;
				for(var name in ivalues){
					var val = ivalues[name];
					$('#mappingModal #' + name).attr('placeholder',val);
				}
				rvalues = data.rvalues;
				var html = '';
				var records = data.records;
				for(var i in records){
					var record = records[i];
					console.log('record id: ' + record.id);
					var fieldtokens = '';
					var rdata = record.data;
					for(var name in rdata){
						var value = rdata[name];
						fieldtokens += '<p><a data-token="[' + record.id + ':' + name + ']" data-value="' + value + '" class="fieldtoken" href="">' + name + '</a> - ' + value + '</p>'; 
					}
					html += '<div id="product-' + record.id + '">' +
								'<p>' +
								  '<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse-' + record.id + '">' +
								    'Sample Record id:' + record.id +
								  '</button>' +
								'</p>' +
								(records.length == 1 ? '<div class="collapse in show" id="collapse-' + record.id + '" style="" aria-expanded="true">' : '<div class="collapse" id="collapse-' + record.id + '">') + 
								  '<div class="card card-body">' +
								    fieldtokens +  
								  '</div>' +
								'</div>' +
							'</div>';
				}
				$('#mappingModal #matches').html(html);
				addTokenAction();
				addSaveAction();
				$('#mappingModal').modal("show");
              }
            });
		}	
		
		var last_active_id = '';
		var last_cursor_position = 0;
		var rvalues = [];
	
		function setValues(id){
			//console.log('active field id: ' + id);
			var cursorPos = $('#' +id).prop('selectionStart');
			//console.log('cursor position: ' + cursorPos);
			last_active_id = id;
			last_cursor_position = cursorPos;
		}
	
		function addSProductFieldAction(){
			$('.sproduct_field').unbind('focus');
			$('.sproduct_field').focus(function(){
				var id = $(this).attr('id');
				setValues(id);
				return(false);
			});	
			
			$('.sproduct_field').unbind('click');
			$('.sproduct_field').click(function(){
				var id = $(this).attr('id');
				setValues(id);
				return(false);
			});	
			
			$('.sproduct_field').unbind('keyup');
			$('.sproduct_field').keyup(function(){
				var id = $(this).attr('id');
				setValues(id);
				return(false);
			});	
			
			$('.sproduct_field').unbind('change');
			$('.sproduct_field').change(function(){
				// get field value.
				var fvalue = $(this).val();
				var ivalue = $(this).attr('placeholder');
				if(fvalue == '' && typeof ivalue != 'undefined') fvalue = ivalue;
				var cvalue = '';
				if(fvalue){
					var cvalue = fvalue;
					for(var name in rvalues){
						var val = rvalues[name];
						cvalue = cvalue.replace(name,val);
					}
					/*if(cvalue.includes("=")){
						cvalue = cvalue.replace("=","");
						cvalue = eval(cvalue);
					}*/
				}
				var fid = $(this).attr('id');
				if(cvalue) cvalue = 'ex. ' + cvalue;
				$('#' + fid + 'Help').html(cvalue);
			});
		}
	
		function addSaveAction(){
			$('#save-btn').unbind('click');
			$('#save-btn').click(function(){
	            $.ajax({
	              type: "POST",
	              url: '/api/products/MappingValues',
				  data: $('#mappingModal #shopifyProductFields').serialize(),
	              success: function(data){
	                console.log(data);
					$('#mappingModal').modal("hide");
					Swal.fire(
						'Saved!',
						data.message,
						data.status
					);
					table.ajax.reload();
	              }
	            });
				return(false);
			});	
		}
	
		function addMapAction(){
			$('.map-btn').unbind('click');
			$('.map-btn').click(function(){
				var id = $(this).attr('data-id');
				console.log('id: ' + id);
				addSProductFieldAction();
				getMappingRecords('products',id);
				return(false);
			});	
		}
		
		function addNewProductAction(){
			$('#new-product-btn').unbind('click');
			$('#new-product-btn').click(function(){
	            $.ajax({
	              type: "POST",
	              url: '/api/products/newShopifyProduct',
				  data: $('#postingModal #shopifyProductFields').serialize(),
	              success: function(data){
	                console.log(data);
					$('#postingModal').modal("hide");
					Swal.fire(
						data.title,
						data.message,
						data.status
					);
					table.ajax.reload();
	              }
	            });
				return(false);
			});	
		}
		
		function addNewVariantAction(){
			$('#new-variant-btn').unbind('click');
			$('#new-variant-btn').click(function(){
	            $.ajax({
	              type: "POST",
	              url: '/api/products/newShopifyVariant',
				  data: $('#postingModal #shopifyProductFields').serialize(),
	              success: function(data){
	                console.log(data);
					$('#postingModal').modal("hide");
					Swal.fire(
						data.title,
						data.message,
						data.status
					);
					table.ajax.reload();
	              }
	            });
				return(false);
			});	
		}
		
		function getPostingRecords(table,id){
			$('#postingModal #record_id').val(id);
			$('#postingModal #edit_id').html(id);
			$('#postingModal .sproduct_field').val('');
			$('#postingModal .form-text').html('');
            $.ajax({
              type: "GET",
              url: '/api/' + table + '/getMappingRecords',
			  data: {id: id},
              success: function(data){
                //console.log(data);
				console.log('status: ' + data.status);
				var values = data.values;
				for(var name in values){
					var val = values[name];
					$('#postingModal #' + name).val(val);
				}
				var cvalues = data.cvalues;
				for(var name in cvalues){
					var val = cvalues[name];
					/*if(val.includes("=")){
						val = val.replace("=","");
						val = eval(val);
					}*/
					if(val != '') val = 'ex. ' + val;
					$('#postingModal #' + name + 'Help').html(val);
				}
				var ivalues = data.ivalues;
				for(var name in ivalues){
					var val = ivalues[name];
					$('#postingModal #' + name).attr('placeholder',val);
				}
				rvalues = data.rvalues;
				var html = '';
				var records = data.records;
				for(var i in records){
					var record = records[i];
					console.log('record id: ' + record.id);
					var fieldtokens = '';
					var rdata = record.data;
					for(var name in rdata){
						var value = rdata[name];
						fieldtokens += '<p><a data-token="[' + record.id + ':' + name + ']" data-value="' + value + '" class="fieldtoken" href="">' + name + '</a> - ' + value + '</p>'; 
					}
					html += '<div id="product-' + record.id + '">' +
								'<p>' +
								  '<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse-' + record.id + '">' +
								    'Sample Record id:' + record.id +
								  '</button>' +
								'</p>' +
								(records.length == 1 ? '<div class="collapse in show" id="collapse-' + record.id + '" style="" aria-expanded="true">' : '<div class="collapse" id="collapse-' + record.id + '">') + 
								  '<div class="card card-body">' +
								    fieldtokens +  
								  '</div>' +
								'</div>' +
							'</div>';
				}
				$('#postingModal #matches').html(html);
				$('#postingModal .fieldtoken').click(function(){
					return(false);
				});	
				
				// enable or disable actions.
				var handle = values['handle'];
				console.log('handle: ' + handle);
				if(handle != ''){
					$('#postingModal #new-product-btn').attr('disabled','true');
					$('#postingModal #new-variant-btn').attr('disabled','true');
				} else {
					$('#postingModal #new-product-btn').removeAttr('disabled');
					$('#postingModal #new-variant-btn').removeAttr('disabled');
				}
				addNewProductAction();
				addNewVariantAction();
				$('#postingModal').modal("show");
              }
            });
		}	
		
		function addPostAction(){
			$('.post-btn').unbind('click');
			$('.post-btn').click(function(){
				var id = $(this).attr('data-id');
				console.log('id: ' + id);
				//addSProductFieldAction();
				getPostingRecords('products',id);
				return(false);
			});	
		}
  
	  	var table = $('#tbl-sims').DataTable({
			processing: true,
		  	serverSide: true,
	        paging: true,
	        info: true,
	        "columns": [
				{ "data": "source", "name": "source", "title": "Source" },
				{ "data": "title", "name": "title", "title": "Title" },
				{ "data": "sku", "name": "sku", "title": "SKU" },
				{ "data": "barcode", "name": "barcode", "title": "Barcode" },
				{ "data": "status", "name": "status", "title": "Status" },
				{ "data": "actions", "name": "actions", "title": "Actions" },
				//repeat for each of my 20 or so fields
	        ],
	  		ajax: '/api/products/search',
	        orderCellsTop: true,
			columnDefs: [
		        { "width": "15%", "targets": 0 },
		        { "width": "20%", "targets": 1 },
		        { "width": "15%", "targets": 2 },
		        { "width": "15%", "targets": 3 },
		        { "width": "15%", "targets": 4 },
		        { "width": "20%", "targets": 5 }
			],
		    "drawCallback": function( settings ) {
		        addMapAction();
				addPostAction();
		    },
	        initComplete: function () {
	            var api = this.api();

	            // For each column
	            api
	                .columns()
	                .eq(0)
	                .each(function (colIdx) {
	                    // Set the header cell to contain the input element
	                    var cell = $('.filters th').eq(
	                        $(api.column(colIdx).header()).index()
	                    );
	                    var title = $(cell).text();
						if(title != 'Actions'){
							$(cell).html('<input type="text" placeholder="Search ' + title + '" />');  
						} else {
							$(cell).html('');
						}

	                    // On every keypress in this input
	                    $(
	                        'input',
	                        $('.filters th').eq($(api.column(colIdx).header()).index())
	                    )
	                        .off('keyup change')
	                        .on('change', function (e) {
	                            // Get the search value
	                            $(this).attr('title', $(this).val());
	                            var regexr = '{search}'; //$(this).parents('th').find('select').val();

	                            var cursorPosition = this.selectionStart;
	                            // Search the column for that value
	                            api
	                                .column(colIdx)
	                                .search(
	                                    this.value != ''
	                                        ? regexr.replace('{search}', this.value)
	                                        : '',
	                                    this.value != '',
	                                    this.value == ''
	                                )
	                                .draw();
	                        })
	                        .on('keyup', function (e) {
	                            e.stopPropagation();

	                            $(this).trigger('change');
	                        });
	                });
	        },
	    });
	  
	    $('.btn-del-sn').click(function(e){
	        e.preventDefault();
	        var id = $(this).data('id');
	        Swal.fire({
	          title: 'Are you sure?',
	          text: "You won't be able to revert this!",
	          icon: 'warning',
	          showCancelButton: true,
	          confirmButtonColor: '#3085d6',
	          cancelButtonColor: '#d33',
	          confirmButtonText: 'Yes, delete it!'
	        }).then((result) => {
	          if (result.isConfirmed) {
	            $.ajax({
	              type: "POST",
	              url: '/api/serial-numbers/'+id+'/delete',
	              success: function(data){
	                console.log(data);
	                var msg = JSON.parse(data);
	                if (msg.status == 'OK') {
	                  Swal.fire(
	                    'Deleted!',
	                    'Serial Number has been deleted.',
	                    'success'
	                  ).then(function() {
	                    location.reload();
	                  });
	                }
	              }
	            });
	          }
	        });
	    });
    });
</script>
@endsection