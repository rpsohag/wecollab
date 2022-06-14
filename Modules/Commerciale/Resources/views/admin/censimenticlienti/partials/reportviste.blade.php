<div class="box-body">
	<div class="box box-primary box-shadow">
	    <div class="box-header with-border">
            <h3><strong>Report viste</strong></h3>
            <br>
            <div>
                <div class="row">
                    {{-- {!! Form::open(['route' => ['admin.commerciale.censimentocliente.reportVistastore'],  'method' => 'post']) !!} --}}
          <form action="" class="btn-submit" method="POST">
            @csrf
            <div class="hidden"> 
                <div class="form-group">
                    <label for="date">clienti</label>
                    <input type="hidden" name="cliente_id" value="{{ $censimentocliente->cliente()->first()->id }}">
               </div>                
            </div>
            <div class="col-md-6"> 
                <div class="form-group">
                    {{ Form::weDate('data', 'Data *', $errors, date('d/m/y')) }}
               </div>                
            </div>
            <div class="col-md-6"> 
                <div class="form-group">
                    {{ Form::weText('descrizione', 'Descrizione *', $errors) }}
               </div>                
            </div>
            
            <div class="col-md-6"> 
                <div class="form-group">             
                    <button type="submit" class="btn btn-success btn-flat" id="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Aggiungi
                    </button>
                </div>              
            </div>  
        </form>
                </div>
            </div>
	    </div>
    </div>


    <table class="data-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>Data</th>
          <th>Utente</th>
          <th>Descrizione</th>
          <th>Azioni</th>
          </thead>
          <tbody>
        </tr>
        <tr>
          <td>demo</td>
          <td>demo</td>
          <td>demo</td>
          <td>edit</td>
        </tr>	
          </tbody>		  
  </table>
</div>




  @push('js-stack')
 
  <script type="text/javascript">


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-submit").click(function(e){

        e.preventDefault();

        var cliente_id = $("input[name=dacliente_id]").val();
        var data = $("input[name=data]").val();
        var descrizione = $("input[name=descrizione]").val();
        var url = '{{ route('admin.commerciale.censimentocliente.reportVistastore') }}';

        $.ajax({
           url:url,
           method:'POST',
           data:{
            cliente_id:cliente_id, 
            data:data,
            descrizione:descrizione,
                },
           success:function(response){
              if(response.success){
                  alert(response.message) //Message come from controller
              }else{
                  alert("Error")
              }
           },
           error:function(error){
              console.log(error)
           }
        });
	});

</script>
@endpush






