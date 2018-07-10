<?php
require_once 'include/MSu_HTMLRender.php';
require_once 'include/MSu_Comic.php';
require_once 'include/MSu_APIHandler.php';
require_once 'include/MSu_DataHandler.php';

session_start();

$data = new MSu_DataHandler();
$api = new MSu_APIHandler();

//Check if a user is logged in, if not log in first user in list
if (!isset($_SESSION['userfirstname']) || !isset($_SESSION['userid'])) {
    $users = $data->getUsers();
    $_SESSION['userfirstname'] = $users[0]['firstname'];
    $_SESSION['userid'] = $users[0]['userid'];
}

$template = new MSu_HTMLRender();
$template->set('title', 'Comic Collection: Series');
$template->set('userfirstname', $_SESSION['userfirstname']);
$template->load('templates/header.html');
echo $template->render();

//Check if user requested search
if (isset ($_GET['id'])) {
    $seriesid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (isset($_GET['offset'])) {
        $offset = filter_input(INPUT_GET, 'offset', 
                               FILTER_SANITIZE_NUMBER_INT);
    } else {
        $offset = 0;
    }
    $result = $api->searchSeries($seriesid, $offset);
    if ($result->code != 200) {
        echo 'API error: ' . $result->code;
    } else {
        //Request a table from HTML renderer
        $template->comicTableHeader(false);
        foreach ($result->comics as $comic) {
            $template->comicTableRow($comic, false, 'ADD');
        }
        $template->comicTableFooter();
        echo $template->render();
        //Check if there are more results, in that case call for page numbering
        if ($result->total > $result->count) {
            $template->pageNumbers($result->total, $result->limit, 
                                   $result->offset, 'series.php?id=' . 
                                   $seriesid . '&offset=');
            echo $template->render();
        }
    }
}

$template->seriesSearchForm();
echo $template->render();


$template->load('templates/footer.html');
echo $template->render();
?>
