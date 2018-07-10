<?php
Class MSu_APIHandler {
    private $_publickey = '' //FIXME Marvel public API key goes here
    private $_privatekey = '' // FIXME Marvel private API key goes here
    
    //Creates a key value for the api usage
    private function createKey() {
        $ts = time();
        $hash = md5($ts . $this->_privatekey . $this->_publickey);
        $key = 'ts=' . $ts . '&apikey=' . $this->_publickey . 
               '&hash=' . $hash;
        return $key;
    }

    //Retrieves info from the URL given as argument
    private function retrieve($url) {
        if (ini_get('allow_url_fopen')) {
            $response = $this->accessDirect($url);
        } elseif (function_exists('curl_init')) {
            $response = $this->useCurl($url);
        } else {
            $response = $this->useSocket($url);
        }
        return $response;
    }
    
    //Retrieve info through direct access
    private function accessDirect($url) {
        $response = file_get_contents($url);
        return $response;
    }
    
    //Retrieve info through curl
    private function useCurl($url) {
        if ($session = curl_init($url)) {
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($session);
            curl_close($session);
            return $response;
        } else {
            print_r('Cannot establish cURL session', 1);
        }
    }
    
    //Retrieve info through sockets
    private function useSocket($url) {
        $urlparts = parse_url($url);
        $port = isset($urlparts['port']) ? $urlparts['port'] : 80;
        $remote = @ fsockopen($urlparts['host'], $port, $errno, $errstr, 30);
        if (!$remote) {
            $response = false;
            print_r('Couldn\'t create a socket connection: ');
            if ($errstr) {
                print_r($errstr);
            } else {
                print_r('Check the domain name or IP address.');
            }
        } else {
            if (isset($urlparts['query'])) {
                $path = $urlparts['path'] . '?' . $urlparts['query'];
            } else {
                $path = $urlparts['path'];
            }
            $out = "GET $path HTTP/1.1\r\n";
            $out .= "Host: {$urlparts['host']}\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($remote, $out);
            $response = stream_get_contents($remote);
            fclose ($remote);
            if ($response) {
                $parts = preg_split('/\r\n\r\n|\n\n/', $response);
                if (is_array($parts)) {
                    $headers = array_shift($parts);
                    $response = implode("\n\n", $parts);
                }
            }
            return $response;
        }
    }

    //Returns a resultset of comics from the series with 
    //the id given as argument
    public function searchSeries($id, $offset) {
        $key = $this->createKey();
        $limit=20;
        $url = 'http://gateway.marvel.com/v1/public/series/' . $id . '/comics?' .
               'offset=' . $offset . '&limit='. $limit . '&' . $key;
        $response = $this->retrieve($url);
        $data = json_decode($response, true);
        $result = new APIResult();
        $result->code = $data['code'];
        if ($result->code != 200) {
            return $result;
        }
        $result->count = $data['data']['count'];
        $result->total = $data['data']['total'];
        $result->offset = $data['data']['offset'];
        $result->limit = $data['data']['limit'];
        foreach ($data['data']['results'] as $entry) {
            $comicid = $entry['id'];
            $issue = $entry['issueNumber'];
            $cover = $entry['thumbnail']['path'] . "." .
                     $entry['thumbnail']['extension'];
            $URL = $entry['urls'][0]['url'];
            $seriesid = basename($entry['series']['resourceURI']);
            $seriestitle = $entry['series']['name'];
            if (($entry['events']['available'] == 1)) {
                $eventid = basename($entry['events']['items'][0]['resourceURI']);
                $eventtitle = $entry['events']['items'][0]['name'];
            } else {
                $eventid = NULL;
                $eventtitle = NULL;
            }
            $result->comics[] = MSu_ComicFactory::create($comicid, $issue,
                                                         $cover, $URL, $seriesid, 
                                                         $seriestitle, $eventid,
                                                         $eventtitle);
        }
        return $result;
    }   
    
    //Returns a resultset of comics from the event with 
    //the id given as argument
    public function searchEvent($id, $offset) {
        $key = $this->createKey();
        $limit = 20;
        $url = 'http://gateway.marvel.com/v1/public/events/' . $id . '/comics?' .
               'offset=' . $offset . '&limit=' . $limit . '&' . $key;
        $response = $this->retrieve($url);
        $data = json_decode($response, true);
        $result = new APIResult();
        $result->code = $data['code'];
        if ($result->code != 200) {
            return $result;
        }
        $result->count = $data['data']['count'];
        $result->total = $data['data']['total'];
        $result->offset = $data['data']['offset'];
        $result->limit = $data['data']['limit'];
        foreach ($data['data']['results'] as $entry) {
            $comicid = $entry['id'];
            $issue = $entry['issueNumber'];
            $cover = $entry['thumbnail']['path'] . "." .
                     $entry['thumbnail']['extension'];
            $URL = $entry['urls'][0]['url'];
            $seriesid = basename($entry['series']['resourceURI']);
            $seriestitle = $entry['series']['name'];
            if (($entry['events']['available'] == 1)) {
                $eventid = basename($entry['events']['items'][0]['resourceURI']);
                $eventtitle = $entry['events']['items'][0]['name'];
            } else {
                $eventid = NULL;
                $eventtitle = NULL;
            }
            $result->comics[] = MSu_ComicFactory::create($comicid, $issue,
                                                         $cover, $URL, $seriesid, 
                                                         $seriestitle, $eventid,
                                                         $eventtitle);
        }
        return $result;
    }



    //Fetches info about a single comic with id $comicid
    public function getComic($comicid) {
        $key = $this->createKey();
        $url = 'http://gateway.marvel.com/v1/public/comics/' . $comicid . 
               '?' . $key;
        $response = $this->retrieve($url);
        $data = json_decode($response, true);
        $comicid = $data['data']['results'][0]['id'];
        $issue = $data['data']['results'][0]['issueNumber'];
        $cover = $data['data']['results'][0]['thumbnail']['path'] . "." .
                 $data['data']['results'][0]['thumbnail']['extension'];
        $URL = $data['data']['results'][0]['urls'][0]['url'];
        $seriesid = basename($data['data']['results'][0]['series']['resourceURI']);
        $seriestitle = $data['data']['results'][0]['series']['name'];
        if (($data['data']['results'][0]['events']['available'] == 1)) {
            $eventid = basename($data['data']['results'][0]['events']['items'][0]['resourceURI']);
            $eventtitle = $data['data']['results'][0]['events']['items'][0]['name'];
        } else {
            $eventid = NULL;
            $eventtitle = NULL;
        }
        $comic = MSu_ComicFactory::create($comicid, $issue, $cover, $URL, 
                                          $seriesid, $seriestitle, $eventid, 
                                          $eventtitle);
        return $comic;
    }

}

//searchEvent and searchSeries returns objects of this class as result
class APIResult {
    public $count;
    public $total;
    public $offset;
    public $limit;
    public $comics = array();
    public $code;
}
?>
        
