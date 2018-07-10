<?php
Class MSu_DataHandler extends PDO {
    
    public function __construct($dbname = 'comicscollection') {
        try {
            parent::__construct("mysql:host=localhost;dbname=$dbname;".
                                "charset=utf8", "DBUSERNAME", "DBPASSWORD");
        } catch (Exception $e) {
            echo "<pre>" . print_r($e) . "</pre>";
        }
    }
    
    //Adds a comic to the collection, owned by the user given as an argument
    public function addComic ($comic, $userid) {
        $query = "INSERT INTO comicdata (comicid) VALUES(:comicid)";
        $sth = $this->prepare($query);
        if (!$sth->execute(array(':comicid' => $comic->comicid))) {
            print_r ($sth->errorInfo(), 1);
        }
        $query = "INSERT INTO collection (userid, comicid) VALUES(:userid, ".
                 ":comicid)";
        $sth = $this->prepare($query);
        if (!$sth->execute(array(':userid' => $userid,
                                 ':comicid' => $comic->comicid))) {
            print_r ($sth->errorInfo());
        }
    }

    //Deletes a comic from the collection
    public function deleteComic($dbid, $userid) {
        $query = "DELETE FROM collection WHERE id=:id AND userid=:userid";
        $sth = $this->prepare($query);
        if (!$sth->execute(array(':id' => $dbid, ':userid' => $userid))) {
            print_r($this->errorInfo());
        }
    }

    //Changes favourite status for a comic
    public function flipFavourite($dbid, $userid) {
        $query = "UPDATE collection SET favourite=!favourite WHERE " .
                 "id=:id && userid=:userid";
        $sth = $this->prepare($query);
        if (!$sth->execute(array(':id' => $dbid, ':userid' => $userid))) {
            print_r($this->errorInfo());
        }
    }


    
    //Returns an array of arrays of id:s and comic id:s from the collection, 
    //filtered by the userid supplied as argument
    public function getComics ($userid) {
        $query = "SELECT * FROM collection WHERE ".
                 "userid=:userid";
        $sth = $this->prepare($query);
        if ($sth->execute(array(':userid' => $userid))) {
            $data = $sth->fetchAll();
            return $data;
        } else {
            print_r($this->errorInfo());
        }
    }

    //Returns an array of info about users
    public function getUsers() {
        $query = "SELECT * FROM users";
        if ($result = $this->query($query)) {
            $users = $result->fetchAll();
            return $users;
        } else {
            print_r($this->errorInfo());
        }
    }
    
    //gets userinfo for the user with the id given as argument
    public function getUser($userid) {
        $query = "SELECT * FROM users WHERE userid=:id";
        $sth = $this->prepare($query);
        if ($sth->execute(array(':id' => $userid))) {
            $user = $sth->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            print_r($sth->errorInfo());
        }
    }

    //Returns info from several DB tables
    public function viewDB() {
        $query = "SELECT id, comicid, firstname, lastname FROM collection " .
                 "LEFT JOIN users on collection.userid = users.userid";
        if ($result = $this->query($query)) {
            $data = $result->fetchAll();
            return $data;
        } else {
            print_r($this->errorInfo());
        }
    }
}
