<?php
/*
try {
    $host = 'localhost';
    $dbname = 'whitehill';
    $username = 'micchael';
    $password = 'P@ssw0rd';

    $conn = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $username, $password);
} catch (Exception $ex) {
    echo $ex->getMessage() . '<br />';
    die('<p style="color: red;">ERROR: Could not connect to the MySQL Database.</p>');
}
*/
/* Using a singleton design pattern for the database class because we only
 * ever need one instance of this. Constructor and clone set to null so it
 * cannot be created outside the class.
 *
 * Access to the database is gained through calling Db::getInstance();
 */
class Db {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $host = 'localhost';
            $dbname = 'whitehill';
            $user = 'michael';
            $pass = 'P@ssw0rd';

            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

            try {
                self::$instance = new PDO('mysql:host='.$host.';dbname='.$dbname,
                        $user, $pass, $pdo_options);
            } catch (Exception $ex) {
                echo $ex->getMessage() . '<br />';
                die('<p style="color: red;">ERROR: Could not connect to the MySQL Database.</p>');
            }

        }
        return self::$instance;
    }
}