<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();

$data = new MSu_DataHandler();




$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection: View DB');
$template->set('userfirstname', $_SESSION['userfirstname']);
$template->load('templates/header.html');
echo $template->render();

$result = $data->viewDB();
echo '<table>';
foreach ($result as $row) {
    echo '<tr><td>' . $row['id'] . '</td><td>' . $row['comicid'] . 
         '</td><td>' . $row['firstname'] . '</td><td>' . $row['lastname'] .
         '</td></tr>';
}
echo '</table>';


$template->load('templates/footer.html');
echo $template->render();
?>
