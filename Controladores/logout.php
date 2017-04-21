<?php
/**
 * Project: SystemRegPHP
 * Author: MiSCapuguel
 * Date: 20/04/2017
 * Time: 22:48
 */
	/**
     * SystemRegPHP\Usuario --Script para Logout--
     */
	session_start();
	require_once('Usuario.class.php');

	/* Utilizando la Class Usuario y el namespace SystemRegPHP */
	use SystemRegPHP\Usuario as Usuario;

	/* Mysql access */
	$sql_driver = 'mysql';
	$sql_host = 'localhost';
	$sql_name = 'conexionpdo';
	$sql_user = 'root';
	$sql_pass = '*****';
	Usuario::init($sql_driver, $sql_host, $sql_name, $sql_user, $sql_pass);

	Usuario::logout();

	header('Location: ./../index.php');
	exit();
