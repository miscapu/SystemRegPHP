<?php
/**
 * Project: SystemRegPHP
 * Author: MiScapu
 * Date: 20/04/2017
 * Time: 21:22
 */
	/**
     * SystemRegPHP\Usuario micuenta.php
     */
	session_start();
	require_once('./../Controladores/Usuario.class.php');

	/* usando el namespace SystemRegPHP y la classe Usuario */
	use SystemRegPHP\Usuario as Usuario;

	/* Ingresando los datos para accesar MySQL */
	$sql_driver = 'mysql';
	$sql_host = 'localhost';
	$sql_name = 'conexionpdo';
	$sql_user = 'root';
	$sql_pass = '*****';
	Usuario::init($sql_driver, $sql_host, $sql_name, $sql_user, $sql_pass);

	/* chequea al usuario actual */
	$user = false;
	if(Usuario::check()) {
        $user = Usuario::getByID($_SESSION['user']['id']);
    }
    else {
        /* redireccionando a index.php */
        header('Location: ../../index.php');
        exit();
    }

	/* rutina de datos para actualizar (si existen datos para actualizar $data_update es true)  */
	$data_error = array();
	$data_update = false;
	if(isset($_POST['update_data'])) {
        $user['name'] = !empty($_POST['name']) ? $_POST['name'] : '';
        $user['mail'] = !empty($_POST['mail']) ? $_POST['mail'] : '';
        if(Usuario::update($user['id'], $user)) {
            $data_update = true;
        }
        else {
            $data_error['general'] = implode('<br/>', Usuario::getError());
        }
    }

	/* actualizando login */
	$login_error = array();
	$login_update = false;
	if(isset($_POST['update_login'])) {
        $login = !empty($_POST['login']) ? $_POST['login'] : '';
        $password = !empty($_POST['password']) ? $_POST['password'] : '';

        $error_flag = false;

        if(empty($login)) {
            /* login is required */
            $login_error['login'] = 'Un Login es requerido';
            $error_flag = true;
        }

        if(empty($password)) {
            /* contraseña es requerida */
            $login_error['password'] = 'Una Contraseña es requerida';
            $error_flag = true;
        }
        else {
            $hash = Usuario::passwordGet($user['id']);
            if(!Usuario::passwordCheck($password, $hash)) {
                $login_error['password'] = 'Esa Contraseña no es igual a la anterior!';
                $error_flag = true;
            }
        }

        if(!$error_flag) {
            if(Usuario::loginUpdate($user['id'], $login)) {
                $login_update = true;
                $user['login'] = $login;
            }
            else {
                $login_error['general'] = implode('<br/>', Usuario::getError());
            }
        }
    }

	/* Actualiza contraseña */
	$password_error = array();
	$password_update = false;
	if(isset($_POST['update_password'])) {
        $password = !empty($_POST['password']) ? $_POST['password'] : '';
        $password_new = !empty($_POST['password_new']) ? $_POST['password_new'] : '';
        $password_key = !empty($_POST['password_key']) ? $_POST['password_key'] : '';

        $error_flag = false;

        if(empty($password)) {
            /* contraseña es requerida */
            $password_error['password'] = 'Para Actualizar, ingrese su contraseña antigua!';
            $error_flag = true;
        }
        else {
            $hash = Usuario::passwordGet($user['id']);
            if(!Usuario::passwordCheck($password, $hash)) {
                $password_error['password'] = 'Esa Contraseña no existe en nuestra base de datos!';
                $error_flag = true;
            }
        }
        /**
         * Área de contraseña nueva
        */
        if(empty($password_new)) {
            /* contraseña es requerida */
            $password_error['password_new'] = 'Ingrese una nueva contraseña; sólo despues de haber ingresado su antigua contraseña en el campo anterior!';
            $error_flag = true;
        }
        else if($password_new != $password_key) {
            /* chequeando la contraseña para que sea igual a la anterior ¿$password_new === password_key?  */
            $password_error['password_key'] = 'Esta Contraseña debe coincidir con la anterior!';
            $error_flag = true;
        }

        if(!$error_flag) {
            if(Usuario::passwordUpdate($user['id'], $password_new)) {
                $password_update = true;
            }
            else {
                $password_error['general'] = implode('<br/>', Usuario::getError());
            }
        }
    }
?>

<html>
<head>
    <title>SystemRegPHP: Mi Cuenta</title>
    <link rel="stylesheet" href="./../src/assets/css/bootstrap.css"/>
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
                <li><a href="#">Hola, <?php echo $user['name']; ?></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="./../Controladores/logout.php">Salir</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Account</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Datos de usuario</div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Su Nombre" value="<?php echo $user['name']; ?>"/>
                        </div>
                        <div class="form-group">
                            <label for="mail">E-mail</label>
                            <input type="text" class="form-control" name="mail" id="mail" placeholder="E-Mail" value="<?php echo $user['mail']; ?>"/>
                        </div>
                        <button type="submit" name="update_data" class="btn btn-primary">Actualize</button>
                        <?php if(!empty($data_error['general'])) { ?>
                            <br/><br/>
                            <div class="alert alert-danger" role="alert"><?php echo $data_error['general']; ?></div>
                        <?php } ?>
                        <?php if(!empty($data_update)) { ?>
                            <br/><br/>
                            <div class="alert alert-success" role="alert">Datos Actualizados correctamente!</div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Nuevo Login</div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="login">Nuevo Login</label>
                            <input type="text" class="form-control" name="login" id="login" placeholder="Nuevo Login" value="<?php echo $user['login']; ?>"/>
                            <?php if(!empty($login_error['login'])) { ?>
                                <br/>
                                <div class="alert alert-danger" role="alert"><?php echo $login_error['login']; ?></div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" value=""/>
                            <?php if(!empty($login_error['password'])) { ?>
                                <br/>
                                <div class="alert alert-danger" role="alert"><?php echo $login_error['password']; ?></div>
                            <?php } ?>
                        </div>
                        <button type="submit" name="update_login" class="btn btn-primary">Actualizar</button>
                        <?php if(!empty($login_error['general'])) { ?>
                            <br/><br/>
                            <div class="alert alert-danger" role="alert"><?php echo $login_error['general']; ?></div>
                        <?php } ?>
                        <?php if(!empty($login_update)) { ?>
                            <br/><br/>
                            <div class="alert alert-success" role="alert">Login Actualizado con suceso!</div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Nueva Contraseña</div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="password">Contraseña Antigua</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Su Contraseña Antigua" value=""/>
                            <?php if(!empty($password_error['password'])) { ?>
                                <br/>
                                <div class="alert alert-danger" role="alert"><?php echo $password_error['password']; ?></div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="password_new">Nueva Contraseña</label>
                            <input type="password" class="form-control" name="password_new" id="password_new" placeholder="Su nueva Contraseña" value=""/>
                            <?php if(!empty($password_error['password_new'])) { ?>
                                <br/>
                                <div class="alert alert-danger" role="alert"><?php echo $password_error['password_new']; ?></div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="password_key">Confirme su nueva Contraseña</label>
                            <input type="password" class="form-control" name="password_key" id="password_key" placeholder="Confirme su Contraseña" value=""/>
                            <?php if(!empty($password_error['password_key'])) { ?>
                                <br/>
                                <div class="alert alert-danger" role="alert"><?php echo $password_error['password_key']; ?></div>
                            <?php } ?>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-primary">Actualizar</button>
                        <?php if(!empty($password_error['general'])) { ?>
                            <br/><br/>
                            <div class="alert alert-danger" role="alert"><?php echo $password_error['general']; ?></div>
                        <?php } ?>
                        <?php if(!empty($password_update)) { ?>
                            <br/><br/>
                            <div class="alert alert-success" role="alert">Su Contraseña fué actualizada </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>