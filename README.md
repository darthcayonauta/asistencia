# asistencia
Sitio desarrollado por CGH/Darthcayonauta

# ¿Qué hace?
es un sistema básico de asistencia, compuesto de 2 módulos, uno de administrador y otro de usuarios.
Programado orientado a MVC con POO

# ¿Qué tiene?
* PHP
* HTML
* Javascript
* CSS
* JQuery
* Frameworks de CSS ( en este caso Bootstrap 4 )

# Configuracion

Edita el archivo de configuracion 'config.php' y modifica los siguientes parámetros, para la conexion a la db

//nombre de Base de Datos
$conf['base']['dbdata']     = 'dababase';<br/>
//nombre Usuario de la db
$conf['base']['dbuser']     = 'username';
//contraseña, clave o password de la db
$conf['base']['dbpass']     = 'password';

# Nota
* No subir el folder 'db' a producción, por razones obvias y menos si se cuenta con datos reales
* el portal corre bien en sitios creados en Linux tales como Debian, Ubuntu, Mint, incluso Centos, no así en plataformas basadas en Wamp o Xampp

