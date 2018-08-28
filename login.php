<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();

$data = new MSu_DataHandler();

unset($_SESSION['userid']);
unset($_SESSION['userfirstname']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "Invalid username and/or password";
    } else {
        $un = $_POST['username'];
        $pw = $_POST['password'];
        $user = $data->checkUser($un, $pw);
        if ($user) {
            session_regenerate_id();
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['userfirstname'] = $user['firstname'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}



$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection: Login');
$template->set('userfirstname', 'Not logged in');
$template->set('loginlink', 'Log in');
$template->load('templates/header.html');
echo $template->render();
echo $error;
$template->loginForm('login.php');
echo $template->render();



$template->load('templates/footer.html');
echo $template->render();
?>
