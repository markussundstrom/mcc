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

$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection: List');
$template->set('userfirstname', $_SESSION['userfirstname']);
$template->load('templates/header.html');
echo $template->render();

//Get comicid list from database
$comicid = $data->getComics($_SESSION['userid']);
$comics = array();
//Create comic objects from id:s
foreach ($comicid as $id) {
    $temp = $api->getComic($id['comicid']);
    $temp->dbid = $id['id'];
    $temp->favourite = $id['favourite'];
    $comics[] = $temp;
}
//Request a table from HTML renderer
$template->comicTableHeader(true);
foreach ($comics as $comic) {
    $template->comicTableRow($comic, TRUE, 'DELETE');
}
$template->comicTableFooter();
echo $template->render();

$template->load('templates/footer.html');
echo $template->render();
?>
