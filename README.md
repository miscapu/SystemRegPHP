# Formulario para Insertar Registros a Una Base de datos MySQL
Log System in PHP and its PDO extension, all interacting with the MySQL database manager; in a safe way!
Sistema de Registros hecho con el lenguaje PHP y su extensión PDO así como el gestor de base de datos MySQL. Código con bloques de comentário donde explico paso a paso las funciones y classe utilizadas. 
## Instalación y Uso:
### Primer Paso: Para usar este formulario necesitas crear una base de datos llamada "conexionpdo"
### Segundo Paso: Debes crear una tabla llamada usuario con 7 campos, aquí la consulta:
```
CREATE TABLE `usuario` (
  `userID` int(11) NOT NULL,
  `login` varchar(30) NOT NULL,
  `password` char(64) NOT NULL,
  `session_key` char(32) DEFAULT NULL,
  `group` tinyint(4) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `mail` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```
### Tercer Paso: Coloca los datos de acceso a MySQL en los archivos:
 * index.php
 * vistas/micuenta.php
 * vistas/registrese.php
 * vistas/recuperacuenta.php
 * Controladores/logout.php

	$sql_driver = 'mysql';   //driver para conexion
	$sql_host = 'localhost';  //nombre del servidor de la base de datos
	$sql_name = 'conexionpdo';  //nombre de la base de datos
	$sql_user = 'root'; //nombre del usuario que puede acceder al gestor de base de datos MySQL
	$sql_pass = '*****';  //contraseña de acceso

## Preguntas y Críticas
Author: [MiSCapu](http://miscapu.blogspot.com).  <a href="https://sourceforge.net/projects/systemregphp/files/latest/download" rel="nofollow"><img alt="Download SystemRegPHP" src="https://a.fsdn.com/con/app/sf-download-button" /></a>
