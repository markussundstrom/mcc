<?php

require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_DataHandler.php';

session_start();

//Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$data = new MSu_DataHandler();

if ($data->userIsAdmin($_SESSION['userid'])) {
    $template = new MSu_HTMLRender();
    $template->set('title', 'Comic Collection: Admin');
    $template->set('userfirstname', $_SESSION['userfirstname']);
    $template->load('templates/header.html');
    echo $template->render();
    $users = $data->getUsers();
    $template->renderUserTable($users);
    echo $template->render();
} else {
    echo '<pre>Not logged in as administrator</pre>';
}
