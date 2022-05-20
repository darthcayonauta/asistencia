function ajxNoImg(target,data)
{
  $.ajax({
			  type: "POST",
			  url: "response.php",
			  data: data,
			  success: function( response ) {
				console.log(response);

			  $(target).html(response);

			  },

			  error: function( response ) {
			  console.log(response);
			  window.location.reload(true);
			}
		});
}

function ajx(target,data)
{
  $.ajax({
			  type: "POST",
			  url: "response.php",
			  data: data,
			  beforeSend:loadingImagen,

			  success: function( response ) {
				console.log(response);

			  $(target).html(response);

			  },

			  error: function( response ) {
			  console.log(response);
			  window.location.reload(true);
			}
		});
}

function ajx22(target,data)
{
  $.ajax({
			  type: "POST",
			  url: "response.php",
			  data: data,
			  beforeSend:loadingImagen2,

			  success: function( response ) {
				console.log(response);

			  $(target).html(response);

			  },

			  error: function( response ) {
			  console.log(response);
			  window.location.reload(true);
			}
		});
}



function validaRut(cuerpoRut)
{

   var myArr = cuerpoRut.split("-");
   var ruti  = myArr[0];
   var dvi   = myArr[1];
   var rut = ruti+"-"+dvi;

   if (rut.length<9)
	   return(false)

	i1=rut.indexOf("-");
	dv=rut.substr(i1+1);
	dv=dv.toUpperCase();
	nu=rut.substr(0,i1);

	cnt=0;
	suma=0;

	for (i=nu.length-1; i>=0; i--)
	{
		dig=nu.substr(i,1);
		fc=cnt+2;
		suma += parseInt(dig)*fc;
		cnt=(cnt+1) % 6;

	 }

	dvok = 11-(suma%11);

	if (dvok==11) dvokstr="0";

	if (dvok==10) dvokstr="K";

	if ((dvok!=11) && (dvok!=10)) dvokstr=""+dvok;

	if (dvokstr==dv)
	   return(true);
	else
	   return(false);
}

function loadingImagen(){

    var x=$("#multiverse");
     x.html('<center><div class="container"><div class="row"><div class="col-md-12"><img src="gfx/cargando.gif" class="img-fluid"></div></div></div><center>');

    }

	function loadingImagen2(){

		var x=$("#multiverso");
		 x.html('<center><div class="container"><div class="row"><div class="col-md-12"><img src="gfx/cargando.gif" class="img-fluid"></div></div></div><center>');
	
		}

	function validaEmail(mail)
    {
        return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(mail);
	}

	function validaClave( cadena )
{

	if( cadena.length < 8 )
		return false
	else
		return true

}

function validaNumeroEntero(numero){
    if (isNaN(numero)){
        return false;
    } else {
        if (numero % 1 == 0) {
            return true;
        } else {
            return false;
        }
    }
}


function validaChain( cadena )
{

		var arrCadena = divideChain(cadena,"&");

		var j   = 0;
		var aux = "";

		for (var i = 0; i < arrCadena.length; i++) {
				aux = divideChain (arrCadena[i],"=");

				if(aux[1] == undefined )
						j++;
		};

		if( j > 0)
				return false;
		else
				return true
}

        function divideChain( chain,simbolo )
        {

                var div = chain.split( simbolo );
                return div;

        }
