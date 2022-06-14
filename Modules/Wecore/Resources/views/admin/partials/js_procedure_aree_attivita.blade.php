@push('js-stack')
<script>
  $(document).ready(function() {
    
    var aree = $.parseJSON(atob("{{ get_json_aree() }}"));
    var gruppi = $.parseJSON(atob("{{ get_json_gruppi() }}"));
   
    var procedura_select = $('#procedura_select');
    var area_select = $('#area_select');
    var gruppo_select = $('#gruppo_select');
    var clienti_select = $('#cliente_select');
    var destinatario_select= $('#destinatario_select');
   
    //console.log(destinatari);
    //console.log(gruppi); 
    //console.log(destinatari);

    clienti_select.change(function(e) {
      area_select.empty();
      gruppo_select.empty();
      destinatario_select.empty();

      procedura_select.select2('open');
    });

    procedura_select.change(function(e) {
      area_select.empty();
      destinatario_select.empty();

      var procedura_selezionata = procedura_select.val(); 

      
      
      aree.forEach(element => {
          //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata

          if(element.procedura_id == procedura_selezionata){
              var newOption = new Option(element.titolo, element.id, false, false);
              area_select.append(newOption);    
          }
      });
      
      //assegnare nuove_aree_di_intervento alla select delle aree di intervento
      area_select.trigger('change');
      area_select.select2('open');
      gruppo_select.select2('close');  
    });


    area_select.change(function(e) {
      gruppo_select.empty();
      destinatario_select.empty();

      var area_selezionata = $(this).val(); 
      //console.log(area_di_intevento_selezionata);

      gruppi.forEach(element => {
        //scorro tutto l'array e controllo quelli che hanno il procedura id  =  alla procedura selezionata
        if(element.area_id ==area_selezionata){
            
            var newOption = new Option(element.nome, element.id, false, false);
            gruppo_select.append(newOption);   
        }    
      });
      //assegnare nuove_attivita alla select delle attivita
      
      destinatario_select.trigger('change');
      gruppo_select.select2('open');
      gruppo_select.trigger('change');
    });

    
  });
</script>
@endpush