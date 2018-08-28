<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();

$data = new MSu_DataHandler();
$api = new MSu_APIHandler();

//Check if a user is logged in 
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}
$message = 'Hello ' . $_SESSION['userfirstname'] . '! Welcome to the ' .
           'Marvel Comic Collection.<br>'; 




//Add comic to DB if user requested that
if (isset($_GET['addComic'])) {
    $id = filter_input(INPUT_GET, 'addComic', FILTER_SANITIZE_NUMBER_INT);
    $comic = $api->getComic($id);
    $result = $data->addComic($comic, $_SESSION['userid']);
    $message =  'Comic added<br>';
}

//Delete comic from DB if user requested that 
if (isset($_GET['delComic'])) {
    $id = filter_input(INPUT_GET, 'delComic', FILTER_SANITIZE_NUMBER_INT);
    $data->deleteComic($id, $_SESSION['userid']);
    $message  = 'Comic deleted<br>';
}

//Change favourite status for a comic if user requested that
if (isset($_GET['changeFav'])) {
    $id = filter_input(INPUT_GET, 'changeFav', FILTER_SANITIZE_NUMBER_INT);
    $data->flipFavourite($id, $_SESSION['userid']);
    $message =  'Favourite status changed';
}

$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection');
$template->set('userfirstname', $_SESSION['userfirstname']);
$template->load('templates/header.html');
echo $template->render();
echo '<p>' . $message . '</p>';
$template->load('templates/footer.html');
echo $template->render();
?>
