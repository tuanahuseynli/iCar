<?php
require_once "users.php";
class Auth
{
    private $userRepository;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } 
        $this->userRepository = new UserRepository();
    }
    public function register($user)
    {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        return $this->userRepository->add((object) $user);
    }
    public function user_exists($email)
    {
        $users = $this->userRepository->findMany(function ($user) use ($email) {
            return $user['email'] === $email; 
        });
        return count($users) >= 1;
    }
    public function login($user)
    {
        $_SESSION["user"] = $user['email'];
    }
    public function check_credentials($email, $password)
    {
        $users = $this->userRepository->findMany(function ($user) use ($email) {
            return $user['email']  === $email;
        });
        if (count($users) === 1) {
            $user = (array) array_values($users)[0];
            return password_verify($password,$user['password']) ? $user : false;
        }
        return false;
    }
    public function is_authenticated()
    {
        return isset($_SESSION["user"]);
    }
    public function logout()
    {
        unset($_SESSION["user"]);
    }

    public function isadmin(){
        return $this->userRepository->admin === true;
    }
}