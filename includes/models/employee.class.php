<?php
class Employee {
    public $username;
    public $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public static function find($username) {
        $db = Db::getInstance();

        $query = $db->prepare('SELECT * FROM employee WHERE Username = :username LIMIT 1');
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        $employee = $query->fetch();

        return new Employee($employee['Username'], $employee['Password']);
    }

    public static function insert($username, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db = Db::getInstance();

        $query = $db->prepare('INSERT INTO employee (Username, Password)
            VALUES (:username, :hash)');
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':hash', $hash, PDO::PARAM_STR);

        $query->execute();
    }

    public static function updatePassword($username, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db = Db::getInstance();

        $query = $db->prepare('UPDATE employee SET Password = :hash
            WHERE Username = :username LIMIT 1');
        $query->bindParam(':hash', $hash, PDO::PARAM_STR);
        $query->bindParam(':username', $username, PDO::PARAM_STR);

        $query->execute();
    }

    public static function tryLogin($username, $password) {
        if (!Employee::verifyPassword($username, $password)) return false;

        $employee = Employee::find($username);
        Employee::onLogin($employee);

        return true;
    }

    public static function verifyPassword($username, $password) {
        $employee = Employee::find($username);

        if ($employee->username == '') return false;

        if (!password_verify($password, $employee->password)) return false;

        return true;
    }

    private static function onLogin($employee) {
        // Stuff that needs to be set when the employee logs in
        session_start();
        $_SESSION['login'] = $employee->username;
        setcookie('date', date("F d, Y - H:i:s"), time() + (86400 * 30), "/"); // 1 day
    }

    public static function logout() {
        setcookie('date', '', time() - 3600, "/"); // Destroy cookie
        session_destroy();
        header("Location: index.php?page=login");
    }

    public static function isLoggedIn() {
        if (isset($_SESSION['login']) && $_SESSION['login'] != '') {
            return true;
        }
        return false;
    }

    public static function isManager() {
        if (Employee::isLoggedIn() && $_SESSION['login'] == 'manager') {
            return true;
        }
        return false;
    }
}
