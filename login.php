<?php
require_once "storage/auth.php";
session_start();
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
function validate($input, &$errors, $auth)
{

    if (is_empty($input, "email")) {
        $errors[] = "Email is required";
    }
    if (is_empty($input, "password")) {
        $errors[] = "Password is required";
    }
    if (count($errors) == 0) {
        if (!$auth->check_credentials($input['email'], $input['password'])) {
            $errors[] = "Invalid username or password";
        }
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $auth->login($_POST);
        header('Location: index.php');
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./media/style3.css">
</head>

<body>
<header>
    <div id="top-header">
        <div id="logo">
            <a href="index.php">CarRental</a>
        </div>
        <nav>
            <ul>
                <li class="active">
                    <a href="login.php">Login</a>
                </li>
                <li>
                    <a href="register.php">Register</a>
                </li>
            </ul>
        </nav>
    </div>
    <div id="header-image-menu">
    </div>
</header>
    <h2>Login</h2>
    <?php if ($errors) {?>
    <ul>
        <?php foreach ($errors as $error) {?>
        <li><?=$error?></li>
        <?php }?>
    </ul>
    <?php }?>
    <form action="" method="post">
        <label for="email">Email Address: </label>
        <input id="email" name="email" type="text"><br>
        <label for="password">Password: </label>
        <input id="password" name="password" type="password"><br>
        <input type="submit" value="Login">
    </form>
    <a href="register.php">Register</a>
</body>

</html>
