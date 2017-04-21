<?php
/**
 * Project: SystemRegPHP
 * Author: MiSCapu
 * Date: 20/04/2017
 * Time: 19:42
 */
	/**
     * SystemRegPHP\Usuario
     * Área de Logueo
     */
	session_start();
	require_once('controladores/Usuario.class.php');

	/* Usando mi namespace SystemRegPHP y heredo la classe Usuario */
	use SystemRegPHP\Usuario as Usuario;

	/* Acceso a MySQL */
	$sql_driver = 'mysql';
	$sql_host = 'localhost';
	$sql_name = 'conexionpdo';
	$sql_user = 'root';
	$sql_pass = '****';
	Usuario::init($sql_driver, $sql_host, $sql_name, $sql_user, $sql_pass);

	/* Chequeando usuario actual */
	$user = false;
	if(Usuario::check()) {
        /* redirecciona para cuenta de usuario(área restrita para usuarios cadastrados) */
        header('Location: vistas/micuenta.php');
        exit();
    }

	/* Valores por defecto */
	$login = '';

	/* ------------------------------Rutina de login------------------------------------- */
	$login_error = array();
	/**
     * $_POST['enter']: es lo que manda nuestro formulario abajo codificado via post (línea 124)
     * si existe $_POST['enter'] puede pasar lo siguiente:
     * la @var string login no esta vacía? entonces asígnele el valor de $_POST['enter']; lo mismo para $password
	*/
	if(isset($_POST['enter'])) {
        $login = !empty($_POST['login']) ? $_POST['login'] : '';
        $password = !empty($_POST['password']) ? $_POST['password'] : '';

        $error_flag = false;

        if(empty($login)) {
            /* login es requerido */
            $login_error['login'] = 'Login es requerido';
            $error_flag = true;
        }

        if(empty($password)) {
            /* password es requerido */
            $login_error['password'] = 'Contraseña es requerida';
            $error_flag = true;
        }

        /* todos los chequeos aprovados */
        if(!$error_flag) {
            if(Usuario::login($login, $password)) {
                /* redirecciona para el área de la cuenta del usuario (area restricta) */
                header('Location: vistas/micuenta.php');
                exit();
            }
            else {
                $login_error['general'] = 'Su login o su contraseña no estan correctos!';
            }
        }
    }

?>

<html>
<head>
    <title>SystemRegPHP: Área de Logueo de Usuarios</title>
    <link rel="stylesheet" href="src/assets/css/bootstrap.min.css"/>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">SystemRegPHP</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Login <span class="sr-only">(current)</span></a></li>
                <li><a href="vistas/registrese.php">Registrese</a></li>
                <li><a href="vistas/recuperacuenta.php">Recupere su Cuenta</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Login</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
            <form action="" method="post">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" name="login" id="login" placeholder="Login" value="<?php echo $login; ?>"/>
                    <?php if(!empty($login_error['login'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $login_error['login']; ?></div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="contraseña" value=""/>
                    <?php if(!empty($login_error['password'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $login_error['password']; ?></div>
                    <?php } ?>
                </div>
                <button type="submit" name="enter" class="btn btn-primary">Login</button>
                <?php if(!empty($login_error['general'])) { ?>
                    <br/><br/>
                    <div class="alert alert-danger" role="alert"><?php echo $login_error['general']; ?></div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
</body>
</html>