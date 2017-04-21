<?php
/**
 * Created by PhpStorm.
 * User: Miguel
 * Date: 20/04/2017
 * Time: 22:55
 */
	/**
     * SystemRegPHP\Usuario --Área para recuperar Cuenta--
     */
	session_start();
	require_once('./../Controladores/Usuario.class.php');

	/* Utilizando nuestro namespace y nuestra classe Usuario */
	use SystemRegPHP\Usuario as Usuario;

	/* Mysql access */
	$sql_driver = 'mysql';
	$sql_host = 'localhost';
	$sql_name = 'conexionpdo';
	$sql_user = 'root';
	$sql_pass = '*****';
	Usuario::init($sql_driver, $sql_host, $sql_name, $sql_user, $sql_pass);

	/* check current user */
	$user = false;
	if(Usuario::check()) {
        /* redirect to user account */
        header('Location: micuenta.php');
        exit();
    }

	/* default values */
	$login = '';
	$password = '';

	/* login routine */
	$recover_error = array();
	if(isset($_POST['recover'])) {
        $login = !empty($_POST['login']) ? $_POST['login'] : '';

        $error_flag = false;

        if(empty($login)) {
            /* login is required */
            $recover_error['login'] = 'Login is required';
            $error_flag = true;
        }
        /* We shouldn't check existence of login! */

        /* all checks passed */
        if(!$error_flag) {
            $password = Usuario::recover($login);
            if(empty($password)) {
                $recover_error['general'] = implode('<br/>', Usuario::getError());
            }
        }
    }

?>

<html>
<head>
    <title>SystemRegPHP: Recupere su cuenta</title>
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
                <li><a href="./../index.php">Login <span class="sr-only">(current)</span></a></li>
                <li><a href="registrese.php">Registration</a></li>
                <li class="active"><a href="recuperacuenta.php">Recover account</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Recover</h1>

    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
            <form action="" method="post">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" name="login" id="login" placeholder="Login" value="<?php echo $login; ?>"/>
                    <?php if(!empty($recover_error['login'])) { ?>
                        <br/>
                        <div class="alert alert-danger" role="alert"><?php echo $recover_error['login']; ?></div>
                    <?php } ?>
                </div>
                <button type="submit" name="recover" class="btn btn-primary">Recuperar</button>
                <?php if(!empty($recover_error['general'])) { ?>
                    <br/><br/>
                    <div class="alert alert-danger" role="alert"><?php echo $recover_error['general']; ?></div>
                <?php } ?>
                <?php if(!empty($password)) { ?>
                    <br/><br/>
                    <div class="alert alert-success" role="alert">Su nueva Contraseña: <?php echo $password; ?>.<br/>(Envie esto para su email)</div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>
</body>
</html>