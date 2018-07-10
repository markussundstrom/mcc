<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();

$data = new MSu_DataHandler();
$api = new MSu_APIHandler();

//Check if a user is logged in, if not login first user
if (isset($_GET['login'])) {
    $userid = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_NUMBER_INT);
    $user = $data->getUser($userid);
    if (!$user) {
        print_r('<pre>Error: No such user</pre>');
    } else {
        $_SESSION['userfirstname'] = $user['firstname'];
        $_SESSION['userid'] = $user['userid'];
    }
}

if (!isset($_SESSION['userfirstname']) || !isset($_SESSION['userid'])) {
    $users = $data->getUsers();
    $_SESSION['userfirstname'] = $users[0]['firstname'];
    $_SESSION['userid'] = $users[0]['userid'];
}


$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection');
$template->set('userfirstname', $_SESSION['userfirstname']);
$template->load('templates/header.html');
echo $template->render();

//Add comic to DB if user requested that
if (isset($_GET['addComic'])) {
    $id = filter_input(INPUT_GET, 'addComic', FILTER_SANITIZE_NUMBER_INT);
    $comic = $api->getComic($id);
    $result = $data->addComic($comic, $_SESSION['userid']);
    echo 'Comic added<br>';
}

//Delete comic from DB if user requested that 
if (isset($_GET['delComic'])) {
    $id = filter_input(INPUT_GET, 'delComic', FILTER_SANITIZE_NUMBER_INT);
    $data->deleteComic($id, $_SESSION['userid']);
    echo 'Comic deleted<br>';
}

//Change favourite status for a comic if user requested that
if (isset($_GET['changeFav'])) {
    $id = filter_input(INPUT_GET, 'changeFav', FILTER_SANITIZE_NUMBER_INT);
    $data->flipFavourite($id, $_SESSION['userid']);
    echo 'Favourite status changed';
}

$template->load('templates/footer.html');
echo $template->render();
?>
