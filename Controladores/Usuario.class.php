<?php
/**
 * Project: SystemRegPHP
 * Author: MiSCapu
 * Date: 19/04/2017
 * Time: 15:16
 */

/**
 * @namespace SystemRegPHP: he creado este namespace que puede tener cualquier otro nombre (Nombre Opcional); es
 * importante crear un namespace para evitar posibles conflictos de nombres de classes, interfaces, constantes ó métodos
 * que se puedan utilizar a medida que el proyecto va creciendo. Este namespace actúa mas o menos como un directorio en
 * cualquier sistema operativo que no admite un archivo con el mismo nombre dentro de un mismo directorio; pero que nada
 * puede impedir el uso de un archivo con el mismo nombre en directorios distintos.
 */
namespace SystemRegPHP;

/**
 * @class Usuario: esta es una class abstract la cual no permite su instanciamiento, osea no será creado ningún objeto
 * instanciando la class Usuario.(no se usará la instrucción PHP "new").
 */
abstract class Usuario {
    /**
     * @var int const vidaUtilSession: es una constante que incluye el tiempo de vida de las sesiones en PHP, este
     * ejemplo puede resultar útil para definir cuando caduca por defecto la sesión de un usuario en un sistema de
     * logueo.
     * Le colocamos 3600 que significa que la vida de la session será de 3600 segundos (1 hora). Esto es opcional,
     * ustedes pueden aumentar o disminuir este valor.
     */
    const VidaUtilSession = 3600;				/* 1 hora /**
     	* @var object $_db : representa al objeto de conexión a la base de datos usando la "classe PDO" y será
	    * estatic ya que sólo la usaremos en ámbito local; lo que quiere decir que sólo servirá para la class
		* Usuario y la declaramos como static para que no desaparezca cuando acabe la función que la está usando.
     	*/
    private static $_db;
    /**
     * @var bool $_init : inicialización de flags o banderas los cuales se refieren a los bits que serán
     * almacenados en la base de datos al iniciar sesión; osea si existen habrá que realizar una previa
     * validación y si pasan esa validación, podrán ser útiles para ingresar al sistema.
     */
    private static $_init;
    /**
     * @var array $_definition: esta variable representará a cada uno de los datos de la base de datos 'systemregphp'
     * como el nombre de la tabla y sus campos.
     */
    private static $_definition = array(
        'table' => 'usuario',
        'id' => 'userID',
        'login' => 'login',
        'pass' => 'password',
        'key' => 'session_key',
        'fields' => array('group', 'name', 'mail')
    );

    /**
     * @var array $_error_list : variable que contiene una lista de posibles errores del sistema
     */
    private static $_error_list = array();

                    //---------------- FIN DE LA DEFINICIÓN DE VARIABLES ----------//

    /**
     * --------------------------INICIANDO MÉTODOS : CONECTANDONOS A LA BASE DE DATOS------------------
     * @method init: el cual inicia la conexión a la base de datos utilizando la extensión PDO
     * este método puede utilizar 3 tipos de servidores de base de datos:
     * mysql
     * mssql : Microsoft SQL Server
     * sybase
     *
     * @param string $controlador_bd : Contiene el nombre del Controlador de la base de datos o 'driver'.
     * @param string $host_bd : nombre del servidor host de la base de datos
     * @param string $nombre_bd : nombre de la base de datos
     * @param string $usuario_bd : nombre del usuario que puede accesar la base de datos.
     * @param string $contrasenia_bd : contraseña de acceso a la base de datos.
     *
     * @return bool : devuelve el valor de flag de inicializacion como true o false (bool)
     */
    static function init($db_drvr, $db_host, $db_name, $db_user, $db_pass) {
        /**
         * utilizamos el bloque try catch en esta parte para abrir una conexión que tenga acceso a nuestra base de
         * datos y si en caso no consiga obtener acceso, esta disparará inmediatamente una de las estrategias de
         * errores provenientes de la biblioteca PDO.
         */
        try {
            /**
             * switch es una sentencia que hace la misma cosa que if; en este caso switch hará una condición con:
             * @var string $controlador_bd : y comunicará al sistema que en caso 'case' el tipo de $controlador_bd sea
             * MySQL, entonces puede utilizar una conexión PDO con los datos de acceso respectivos incluídos en los
             * parámetros ($host_bd, $nombre_bd, etc) antes descritos para acceder a la base de datos.
             * Esto es opcional porque tambien podriamos haber utilizado 'if y elseif' para realizar el mismo proceso.
             */
            switch($db_drvr) {
                case 'mysql': self::$_db = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass); break;
                case 'mssql': self::$_db = new \PDO('mssql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass); break;
                case 'sybase': self::$_db = new \PDO('sybase:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass); break;
                default: self::$_db = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass); break;
            }
            self::$_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(\PDOException $e) {
            die($e->getMessage());
        }
        /**
         * @var bool $session_check: variable que almacena la verificación true o false de la comparación de PHP
         * para el sistema.
         */

        $session_check = false;
        /**
         * Ahora vamos a utilizar if para condicionar las versiones de PHP que se estan usando:
         * decimos entonces: si "if" version_compare ---->(version_compare:function=>compara dos versiones php)
         * phpversion(), version 5.4.0 , operador de comparación >=  (phpversion() es nuestra actual version de PHP)
         * Osea que si nuestra actual version es mayor o igual que 5.4.0; entonces recibimos verdadero 'True', que es
         * la verificación de session que se almacenará en la variable $session_checkb y el script puede proceder.
         */
        if(version_compare(phpversion(), '5.4.0', '>=')) {
            if(session_status() === PHP_SESSION_ACTIVE) {
                $session_check = true;
            }
        }
        /**
         * Sino se cumple que la version de PHP es mayor o igual a 5.4.0 tendria que ocurrir lo siguiente:
         * si la constante session_id (encargada de obtener o definir el id de la session actual) no es vacía
         * @var bool $session_check es 'true' y el script puede proceder
         */
        else {
            if(!empty(session_id())) {
                $session_check = true;
            }
        }
        /**
         * si:
         * @var bool $session_check no existe, muestre un mensaje aquí referenciado
         */
        if(!$session_check) {
            die('La Session no se ha iniciado. Es necesaria la classe SystemRegPHP\Usuario.');
        }
        /**
         * Si no estan vacíos los campos de la 'table'(usuario) como userId, login y contrasenia; entonces puede
         * acceda a la variable $_init (la cual inicializa los flags) y hacerla 'true'; lo cual hace que el script puede
         * proceder
         */
        if(!empty(self::$_definition['table']) && !empty(self::$_definition['id']) && !empty(self::$_definition['login']) && !empty(self::$_definition['pass'])) {
            self::$_init = true;
        }
        /**
         * usamos return para invocar el control total del programa nuevamente en este bloque y accedemos a la
         * variable $_init que inicia el almacenamiento de datos de session;
         */
        return self::$_init;
    }
    /**
     *   -----------------------FIN DE LAS VALIDACIONES DE ACCESO A LA BASE DE DATOS MYSQL -------------------
     *
     */
    /** --------------------------METODOS PARA INTERACTUAR CON LA BASE DE DATOS "conexionpdo" ------------------
     * @function getById : función que va a realizar la obtención de los datos de usuario de "conexionpdo", de
     * acuerdo a su $userID sin considera su contraseña.
     *
     * @param string $UserID: es el id único para el usuario obtenido de MySQL
     */
    static function getByID($userID) {
        /**
         * @var bool usuario le asignamos false pues es una variable que inicialmente esta vacía
         */
        $user = false;
        /**
         * Si la variable $_init existe, se inicia:
         */
        if(self::$_init) {
            $res = self::$_db->prepare('SELECT * FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['id'] . '` = :id;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            $res->execute(array('id' => $userID));
            if($lot = $res->fetch()) {
                /**
                 * campos principales (menos password)
                 **/
                $user = array(
                    'id' => $lot[self::$_definition['id']],
                    'login' => $lot[self::$_definition['login']],
                    'key' =>  $lot[self::$_definition['key']],
                );
                /* otros campos */
                if(!empty(self::$_definition['fields'])) {
                    foreach(self::$_definition['fields'] as $field) {
                        $user[$field] = !empty($lot[$field]) ? $lot[$field] : '';
                    }
                }
            }
        }
        return $user;
    }

    /**
     * @function getByLogin : método que va a realizar la obtención de los datos de acuerdo a su login; sin considerar
     * la contraseña 'password'
     *
     * @param string $UserID: es el id único para el usuario obtenido de pdomysql

     * @return array|false Devuelve matriz de datos de usuario o false si se produjo un error.
     *
     */
    static function getByLogin($login) {
        $user = false;
        if(self::$_init) {
            $res = self::$_db->prepare('SELECT * FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['login'] . '` = :login;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            $res->execute(array('login' => $login));
            if($lot = $res->fetch()) {
                /* main fields, except password */
                $user = array(
                    'id' => $lot[self::$_definition['id']],
                    'login' => $lot[self::$_definition['login']],
                    'key' =>  $lot[self::$_definition['key']],
                );
                /* other fields */
                if(!empty(self::$_definition['fields'])) {
                    foreach(self::$_definition['fields'] as $field) {
                        $user[$field] = !empty($lot[$field]) ? $lot[$field] : '';
                    }
                }
            }
        }
        return $user;
    }

    /**
     * @method getList: Obtiene lista de usuarios.
     *
     * @return array Devuelve la matriz de datos de los usuarios.
     */
    static function getList() {
        $list = array();
        if(self::$_init) {
            $res = self::$_db->query('SELECT * FROM `' . self::$_definition['table'] . '` ORDER BY `' . self::$_definition['id'] . '`;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            while($lot = $res->fetch()) {
                /* main fields, except password */
                $user = array(
                    'id' => $lot[self::$_definition['id']],
                    'login' => $lot[self::$_definition['login']]
                );
                /* other fields */
                if(!empty(self::$_definition['fields'])) {
                    foreach(self::$_definition['fields'] as $field) {
                        $user[$field] = !empty($lot[$field]) ? $lot[$field] : '';
                    }
                }
                $list[] = $user;
            }
        }
        return $list;
    }

    /**
     * @method add: agrega un nuevo usuario.
     *
     * @param array $data			Tabla de datos de usuario con claves según definición.
     *
     * @return int|false Retorna un nuevo ID de usuario o falso si se produce un error.
     */
    static function add($data) {
        $result = false;
        self::$_error_list = array();
        if(self::$_init) {
            $user_data = array(
                'login' => !empty($data['login']) ? $data['login'] : '',
                'pass' => !empty($data['pass']) ? self::passwordHash($data['pass']) : ''
            );
            $sql_set = array(
                '`' . self::$_definition['login'] . '` = :login',
                '`' . self::$_definition['pass'] . '` = :pass'
            );

            if(!empty(self::$_definition['fields'])) {
                foreach(self::$_definition['fields'] as $field) {
                    $user_data[$field] = !empty($data[$field]) ? $data[$field] : null;
                    $sql_set[] = '`' . $field . '` = :' . $field;
                }
            }
            if(!empty($user_data['login']) && !empty($user_data['pass'])) {
                if(!self::loginExists($user_data['login'])) {
                    $res = self::$_db->prepare('INSERT INTO `' . self::$_definition['table'] . '` SET ' . implode(', ', $sql_set) . ';');
                    $res->setFetchMode(\PDO::FETCH_ASSOC);
                    if($res->execute($user_data)) {
                        $result = self::$_db->lastInsertId();
                    }
                    else {
                        self::$_error_list[] = 'Hubo un error en la base de datos!';
                    }
                }
                else {
                    self::$_error_list[] = 'Ese login ya existe, porfavor utilize otro!';
                }
            }
            else {
                self::$_error_list[] = 'El campo Login y el campo Contraseña no pueden quedarse vacíos!';
            }
        }
        return $result;
    }

    /**
     * @method update: Update de datos de usuario (exceptuando la contrasenia y el login).
     *
     * @param int $userID			Id de usuario.
     * @param array $data			Tabla de datos de usuario con claves de acuerdo a la definición. Algunos datos
     * podrían omitirse.
     *
     * @return boolean Retroan resultados de la actualización.
     */
    static function update($userID, $data) {
        $result = false;
        self::$_error_list = array();
        if(self::$_init) {
            $user_data = array('id' => $userID);
            $sql_set = array();
            if(!empty($data)) {
                foreach($data as $key => $val) {
                    if(in_array($key, self::$_definition['fields'])) {
                        $user_data[$key] = !empty($val) ? $val : null;
                        $sql_set[] = '`' . $key . '` = :' . $key;
                    }
                }
                $res = self::$_db->prepare('UPDATE `' . self::$_definition['table'] . '` SET ' . implode(', ', $sql_set) . ' WHERE `' . self::$_definition['id'] . '` = :id;');
                $res->setFetchMode(\PDO::FETCH_ASSOC);
                if($res->execute($user_data)) {
                    $result = true;
                }
                else {
                    self::$_error_list[] = 'DB error';
                }
            }
        }
        return $result;
    }

    /**
     * @method delete utiliza el parametro $UserID para eliminar un usuario.
     *
     * @param int $userID			Id de usuario.
     *
     * @return boolean Retorna el resultado de eliminar un usuario y todos sus datos.
     */
    static function delete($userID) {
        $result = false;
        self::$_error_list = array();
        if(self::$_init) {
            $res = self::$_db->prepare('DELETE FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['id'] . '` = :id;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            if($res->execute(array('id' => $userID))) {
                $result = true;
            }
            else {
                self::$_error_list[] = 'DB error';
            }
        }
        return $result;
    }

    /**
     * @method loginUpdate: actualiza el login del usuario.
     *
     * @param int $userID			Id de usuario.
     * @param string $login		Login del usuario.
     *
     * @return boolean Retorna los resultados de la actualizacion del usuario.
     */
    static function loginUpdate($userID, $login) {
        $result = false;
        self::$_error_list = array();
        if(self::$_init) {
            if(!empty($userID) && !empty($login)) {
                if(!self::loginExists($login, $userID)) {
                    $res = self::$_db->prepare('UPDATE `' . self::$_definition['table'] . '` SET `' . self::$_definition['login'] . '` = :login WHERE `' . self::$_definition['id'] . '` = :id;');
                    $res->setFetchMode(\PDO::FETCH_ASSOC);
                    if($res->execute(array('id' => $userID, 'login' => $login))) {
                        $result = true;
                    }
                    else {
                        self::$_error_list[] = 'Error en la Base de datos';
                    }
                }
                else {
                    self::$_error_list[] = 'Este login ya existe, porfavor utilize otro!';
                }
            }
            else {
                self::$_error_list[] = 'Usuario y Login son requeridos para actualizar sus datos!';
            }
        }
        return $result;
    }

    /**
     * @method loginExists chequea si el login existe. Omite la comprobación del usuario actual.
     *
     * @param string $login		Login del usuario.
     * @param int $userID		Id del usuario actual cuyo valor por defecto es 0.
     *
     * @return boolean Returns result of check.
     */
    static function loginExists($login, $userID = 0) {
        $result = false;
        if(self::$_init) {
            $res = self::$_db->prepare('SELECT `' . self::$_definition['id'] . '` FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['login'] . '` = :login AND `' . self::$_definition['id'] . '` <> :id;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            $res->execute(array('id' => $userID, 'login' => $login));
            if($res->rowCount() > 0) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @method passwordHashCreate hash de la contrasenia de usuario.
     *
     * @param string $pass	contrasenia del Usuario.
     *
     * @return string Retorna hash del password de usuario.
     */
    static function passwordHash($pass) {
        $salt = md5(microtime(true));
        $hash = $salt . md5($pass . $salt);
        return $hash;
    }

    /**
     * @method passwordCheck: chequea la contraseña del usuario.
     *
     * @param string $pass			Contrasenia del usuaro.
     * @param string $hash			Hash de la contrasenia del usuario.
     *
     * @return string Retorna el resultado del chequeo.
     */
    static function passwordCheck($pass, $hash) {
        $result = false;
        $salt = substr($hash, 0, 32);
        if($hash == $salt . md5($pass . $salt)) {
            $result = true;
        }
        return $result;
    }

    /**
     * @method passwordUpdate: actualiza la contraseña del usuario.
     *
     * @param int $userID			id del usuario.
     * @param string $pass			Contrasenia del usuario.
     *
     * @return boolean              Retorna el resultado de la actualizacion de la contraseña del usuario.
     */
    static function passwordUpdate($userID, $pass) {
        $result = false;
        self::$_error_list = array();
        if(self::$_init) {
            if(!empty($userID) && !empty($pass)) {
                $res = self::$_db->prepare('UPDATE `' . self::$_definition['table'] . '` SET `' . self::$_definition['pass'] . '` = :pass WHERE `' . self::$_definition['id'] . '` = :id;');
                $res->setFetchMode(\PDO::FETCH_ASSOC);
                if($res->execute(array('id' => $userID, 'pass' => self::passwordHash($pass)))) {
                    $result = true;
                }
                else {
                    self::$_error_list[] = 'Error en la Base de Datos';
                }
            }
            else {
                self::$_error_list[] = 'Usuario y Contraseña son campos requeridos para actualizar su contraseña!';
            }
        }
        return $result;
    }

    /**
     * Get user password hash.
     *
     * @param int $userID			ID del usuario.
     *
     * @return string|false         Retorna la 'contrasenia' hash o 'false'.
     */
    static function passwordGet($userID) {
        $result = false;
        if(self::$_init) {
            $res = self::$_db->prepare('SELECT `' . self::$_definition['id'] . '`, `' . self::$_definition['pass'] . '` FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['id'] . '` = :id;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            $res->execute(array('id' => $userID));
            if($lot = $res->fetch()) {
                $result = $lot[self::$_definition['pass']];
            }
        }
        return $result;
    }

    /**
     * @method login: loguea al usuario en el sistema. Empieza una session de usuario.
     *
     * @param string $login		Login de usuario.
     * @param string $pass			Contrasenia del usuario.
     *
     * @return boolean Retorna el resultado del logueo del usuario.
     */
    static function login($login, $pass) {
        $result = false;
        if(self::$_init) {
            $res = self::$_db->prepare('SELECT `' . self::$_definition['id'] . '`, `' . self::$_definition['pass'] . '` FROM `' . self::$_definition['table'] . '` WHERE `' . self::$_definition['login'] . '` = :login;');
            $res->setFetchMode(\PDO::FETCH_ASSOC);
            $res->execute(array('login' => $login));
            if($lot = $res->fetch()) {
                if(self::passwordCheck($pass, $lot[self::$_definition['pass']])) {
                    session_regenerate_id();
                    $result = true;
                    $key = md5(microtime(true));
                    $_SESSION['user']['id'] = $lot[self::$_definition['id']];
                    $_SESSION['user']['key'] = $key;
                    $_SESSION['user']['time'] = time() + self::VidaUtilSession;
                    $res = self::$_db->prepare('UPDATE `' . self::$_definition['table'] . '` SET `' . self::$_definition['key'] . '` = :key WHERE `' . self::$_definition['id'] . '` = :id;');
                    $res->setFetchMode(\PDO::FETCH_ASSOC);
                    $res->execute(array('key' => $key, 'id' => $lot[self::$_definition['id']]));
                }
            }
        }
        return $result;
    }

    /**
     * @method logout: realiza el logout del usuario. Detiene la session de usuario.
     *
     * @return : retorna true
     */
    static function logout() {
        $_SESSION['user'] = null;
        session_regenerate_id();
        return true;
    }

    /**
     * @method recover: Establece una contrasenia de usuario de manera aleatoria.
     *
     * @param string $login		Login de usuario.
     *
     * @return string|false Retorna una nueva contrasenia o false si ha ocurrido algun error.
     */
    static function recover($login) {
        $result = false;
        if(self::$_init) {
            $pass = substr(md5(microtime(true)), 0, 6);
            $user = self::getByLogin($login);
            if(!empty($user['id'])) {
                if($result = self::passwordUpdate($user['id'], $pass)) {
                    $result = $pass;
                }
            }
        }
        return $result;
    }

    /**
     * Return list of latest errors.
     *
     * @return array Returns list of latest errors.
     */
    static function getError() {
        return self::$_error_list;
    }

    /**
     * @method check: chequea la session usuario.
     *
     * @return boolean Retorna el resultado del chequeo. Si false expulsa al usuario.
     */
    static function check() {
        $result = false;
        if(self::$_init) {
            $now = time();
            if(!empty($_SESSION['user']['id']) && !empty($_SESSION['user']['key'])) {
                if($now < $_SESSION['user']['time']) {
                    $now = time();
                    $user = self::getByID($_SESSION['user']['id']);
                    if($user['key'] == $_SESSION['user']['key']) {
                        $_SESSION['user']['time'] = time() + self::VidaUtilSession;
                        $result = true;
                    }
                }

                if(!$result) {
                    self::logout();
                }
            }
        }
        return $result;
    }

}