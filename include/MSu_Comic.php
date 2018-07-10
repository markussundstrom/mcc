<?php

Class MSu_EntitySummary {
    public $id;
    public $title;
    
    public function __construct($id, $title) {
        $this->id = $id;
        $this->title = $title;
    }
}

Class MSu_Comic {
    public $comicid;
    public $issue;
    public $cover;
    public $URL;
    public $seriesdata;
    public $eventdata;
    public $favourite;
    public $dbid;

    public function __construct ($comicid, $issue, $cover, $URL, $seriesid, 
                                 $seriestitle, $eventid = NULL, 
                                 $eventtitle = NULL, $favourite = false) {
        $this->comicid = $comicid;
        $this->issue = $issue;
        $this->cover = $cover; 
        $this->URL = $URL;
        $this->seriesdata = new MSu_EntitySummary($seriesid, $seriestitle);
        if ($eventid && $eventtitle) {
            $this->eventdata = new MSu_EntitySummary($eventid, $eventtitle);
        } else {
            $this->eventdata = NULL;
        }
        $this->favourite = $favourite;
    }
}
            
class MSu_ComicFactory {
    //Instantiate the MSu_Comic class
    public static function create ($comicid, $issue, $cover, $URL, $seriesid,
                                   $seriestitle, $eventid = NULL, 
                                   $eventtitle = NULL, $favourite = NULL) {
        return new MSu_Comic ($comicid, $issue, $cover, $URL, $seriesid,
                              $seriestitle, $eventid, $eventtitle, $favourite);
    }
}
?>



