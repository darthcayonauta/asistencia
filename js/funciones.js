$("#salir").click(function (event){

    event.preventDefault();

    var data={'id':0};
 
        $.ajax({
              type: "POST",
              url: "index.php",
              data: data,
          
              success: function( response ) {
                console.log(response);

              //$("#espacioClienteRespuesta").html(response);
                window.location.reload(true);

              },
            
              error: function( response ) {
              console.log(response);
              window.location.reload(true);
            }
         });
   });

 $("#region").click(function(event) {
  
   event.preventDefault();

   alert("ola ke ase!!!!!");

   var alt = $(this).attr("alt");

   var data = {'accion':'send',
               'id':'region',
               'alt':alt };
   $.ajax({
              type: "POST",
              url: "response.php",
              data: data,
          
              success: function( response ) {
                console.log(response);

              $("#cuerpoPrincipal").html(response);
               
              },
            
              error: function( response ) {
              console.log(response);
              window.location.reload(true);
            }
         });

 });
