<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();
unset($_SESSION['userid']);
unset($_SESSION['userfirstname']);

$data = new MSu_DataHandler();

$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection: Login');
$template->set('userfirstname', 'Not logged in');
$template->load('templates/header.html');
echo $template->render();

$users = $data->getUsers();

foreach ($users as $user) {
    echo '<a href="index.php?login=' . $user['userid'] . '">' .
         $user['firstname'] . ' ' . $user['lastname'] . ' (' .
         $user['username'] . ')</a><br>';
}


$template->load('templates/footer.html');
echo $template->render();
?>
