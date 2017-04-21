<?php
/**
 * Project: SystemRegPHP
 * Author: MiSCapu
 * Date: 20/04/2017
 * Time: 22:59
 */
	/**
     * SystemRegPHP\Usuario --Área de Registro--
     */
	session_start();
	require_once('./../Controladores/Usuario.class.php');

	/* usando mi namespace SystemRegPHP y nuestra classe Usuario */
	use SystemRegPHP\Usuario as Usuario;

	/* Introduciendo datos ára tener acceso a MySQL */
	$sql_driver = 'mysql';
	$sql_host = 'localhost';
	$sql_name = 'conexionpdo';
	$sql_user = 'root';
	$sql_pass = '*****';
	Usuario::init($sql_driver, $sql_host, $sql_name, $sql_user, $sql_pass);

	/* Chequeando el usuario Actual */
	$user = false;
	if(Usuario::check()) {
        /* Redirecciono para micuenta.php */
        header('Location: micuenta.php');
        exit();
    }

	/* valores por defecto */
	$login = '';
	$password = '';
	$password_key = '';

	/* --------------------------------------Rutinas de Registro-------------------------------- */
	$registration_error = array();
	if(isset($_POST['registrtion'])) {
        $login = !empty($_POST['login']) ? $_POST['login'] : '';
        $password = !empty($_POST['password']) ? $_POST['password'] : '';
        $password_key = !empty($_POST['password_key']) ? $_POST['password_key'] : '';

        $error_flag = false;

        if(empty($login)) {
            /* Login es requerido */
            $registration_error['login'] = 'Un login es requerido para su acceso';
            $error_flag = true;
        }
        else if(Usuario::loginExists($login)) {
            /* Login ya existente :( */
            $registration_error['login'] = 'Ese Login infelizmente ya existe en nuestro sistema, elija otro!';
            $error_flag = true;
        }

        if(empty($password)) {
            /* Contraseña es requerida */
            $registration_error['password'] = 'Una Contraseña es requerida para su próximo acceso!';
            $error_flag = true;
        }
        else if($password != $password_key) {
            /* chequea la contraseña y dispara un error si no coinciden */
            $registration_error['password_key'] = 'Esa contraseña no coincide con la anterior, debe ser igual';
            $error_flag = true;
        }

        /* Pasó todos los chequeos, podemos darle el permiso de entrar a el archivo "micuenta.php" */
        if(!$error_flag) {
            $user_data = array(
                'login' => $login,
                'pass' => $password
            );
            $userID = Usuario::add($user_data);
            if(!empty($userID)) {
                /* registration done */
                /* login user and redirect to account */
                if(Usuario::login($login, $password)) {
                    /* redirect to user account */
                    header('Location: micuenta.php');
                    exit();
                }
            }
            else {
                $registration_error['general'] = implode('<br/>', Usuario::getError());
            }
        }
    }

?>

<html>
<head>
    <title>SystemRegPHP: Área de Registro</title>
    <link rel="stylesheet" href="./../src/assets/css/bootstrap.min.css"/>
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
                <li><a href="./../index.php">Login</a></li>
                <li class="active"><a href="registrese.php">Registro <span class="sr-only">(Actual)</span></a></li>
                <li><a href="recuperacuenta.php">Recupere Su Cuenta</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Registrese:</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
            <form action="" method="post">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" name="login" id="login" placeholder="Login" value="<?php echo $login; ?>"/>
                    <?php if(!empty($registration_error['login'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $registration_error['login']; ?></div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" value="<?php echo $password; ?>"/>
                    <?php if(!empty($registration_error['password'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $registration_error['password']; ?></div>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="password_key">Confirme su Contraseña</label>
                    <input type="password" class="form-control" name="password_key" id="password_key" placeholder="Confirme su Contraseña" value="<?php echo $password_key; ?>"/>
                    <?php if(!empty($registration_error['password_key'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $registration_error['password_key']; ?></div>
                    <?php } ?>
                </div>
                <button type="submit" name="registrtion" class="btn btn-primary">Registrese</button>
                <?php if(!empty($registration_error['general'])) { ?>
                    <br/><br/>
                    <div class="alert alert-danger" role="alert"><?php echo $registration_error['general']; ?></div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
</body>
</html>