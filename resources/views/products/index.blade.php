@extends('dashboard.base')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('/vendors/DataTables/datatables.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('/vendors/sweetalert2/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/vendors/jquery/jquery-ui.css') }}">
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
                            <th>Barcode</th>
              				<th></th>
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

@endsection

@section('javascript')
  <script src="{{ asset('/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
  <script src="{{ asset('/vendors/jquery/jquery-ui.js') }}"></script>
  <script type="text/javascript" src="{{ asset('/vendors/DataTables/datatables.min.js') }}"></script>
  <script src="{{ asset('/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
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
  
      /*var table = $('#tbl-sims').DataTable({
  		  processing: true,
  		  serverSide: true,
        paging: true,
        info: true,
        "columns": [
          { "data": "cli", "name": "cli", "title": "CLI" },
          { "data": "destination", "name": "destination", "title": "To" },
          { "data": "message", "name": "message", "title": "Message" },
          { "data": "uuid", "name": "uuid", "title": "UUID" },
          { "data": "created_at", "name": "created_at", "title": "Created" }
          //repeat for each of my 20 or so fields
        ],
  		  ajax: '/messages/index',
        orderCellsTop: true,
        fixedHeader: true,
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
                    if(title == 'Created'){
                        $(cell).html('<input id="datepicker-created" type="text" class="datepicker" placeholder="Search ' + title + '" />');
                        $( "#datepicker-created" ).datepicker();
                        $( "#datepicker-created" ).datepicker( "option", "dateFormat", "yy-mm-dd");
                    } else {
                        $(cell).html('<input type="text" placeholder="Search ' + title + '" />');  
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
      });*/
	  
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