<?php

Class MSu_DataHandler extends PDO {

    private $salt1 = 'Tb53!r';
    private $salt2 = 'yHaS6,';
    
    public function __construct($dbname = 'comicscollection') {
        require 'MSu_login_info.php';
        try {
            parent::__construct("mysql:host=localhost;dbname=$dbname;".
                                "charset=utf8", $db_username, $db_password);
        } catch (Exception $e) {
            echo "<pre>" . print_r($e) . "</pre>";
        }
    }
    
    //Adds a comic to the collection, owned by the user given as an argument
    public function addComic ($comic, $userid) {
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
            $users = $result->fetchAll(PDO::FETCH_ASSOC);
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
    
    //Checks if a username and password is valid, returns userinfo if 
    //found, otherwise NULL
    public function checkUser ($username, $password) {
        $token = hash('ripemd128', $this->salt1 . $password . $this->salt2);
        $query = "SELECT * FROM users WHERE username=:un AND password=:pw";
        $sth = $this->prepare($query);
        if ($sth->execute(array(':un' => $username, ':pw' => $token))) {
            $user = $sth->fetch(PDO::FETCH_ASSOC);
            return ($user) ? $user : NULL;
        } else {
            print_r($sth->errorInfo());
            return NULL;
        }
    }

    //Check if a user is admin. Returns true if admin, otherwise false
    public function userIsAdmin ($userid) {
        $query = "SELECT * FROM users where userid=:id";
        $sth = $this->prepare($query);
        if ($sth->execute(array(':id' => $userid))) {
            $user = $sth->fetch(PDO::FETCH_ASSOC);
            if ($user['isadmin']) {
                return true;
            } else {
                return false;
            }
        } else {
            print_r($sth->errorInfo());
            return;
        }
    }

        

}
