$(document).ready(function() {


		$('#id_region').on('change', function() {
  			var value = $(this).val();
  
  			var data = {'id':'displayComboCiudad',
  						'id-region':value};
	

  		//	alert('id-region----->:'+value)			
		
		$.ajax({
                type: "POST",
                url: "response.php",
                data: data,
            
                success: function( response ) {
                  console.log(response);

                $("#ciudad").html(response);
                 
                },
              
                error: function( response ) {
                console.log(response);
                window.location.reload(true);
              }
            });	
     });
});