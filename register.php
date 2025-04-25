<?php

require_once "storage/auth.php";
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
function validate($input, &$errors, $auth)
{

    if (is_empty($input, "fullname")) {
        $errors[] = "Full name is required";
    }
    if (is_empty($input, "email")) {
        $errors[] = "Email is required";
    }
    if (is_empty($input, "password")) {
        $errors[] = "Password is required";
    }
    if (count($errors) == 0) {
        if ($auth->user_exists($input['email'])) {
            $errors[] = "User already exists";
        }
   
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $_POST["admin"] = false;
        $auth->register($_POST);
        header('Location: login.php');
        exit();
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
    <h2>Registration</h2>
    <?php if ($errors) {?>
    <ul>
        <?php foreach ($errors as $error) {?>
        <li><?=$error?></li>
        <?php }?>
    </ul>
    <?php }?>
    <form action="" method="post">
        <label for="fullname">Full Name: </label>
        <input id="fullname" name="fullname" type="text"><br>
        <label for="email">Email: </label>
        <input id="email" name="email" type="text"><br>
        <label for="password">Password: </label>
        <input id="password" name="password" type="password"><br>
        <input type="submit" value="Register">
    </form>
    <a href="login.php">Login</a>
</body>

</html>
