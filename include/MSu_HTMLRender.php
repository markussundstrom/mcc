<?php
Class MSu_HTMLRender {
    private $_values;
    private $_output;

    public function __construct() {
        $this->_values = array();
        $this->_output = '';
    }

    //Loads a template file and places the contents in the output queue. 
    // Requires the template file as an argument
    public function load($file) {
        try {
            if (!file_exists($file)) {
                throw new Exception("Error loading template");
            } else {
                $this->_output .= file_get_contents($file);
            }
        } catch (Exception $e) {
            echo $e; //FIXME?
        }
    }
    //Returns accumulated output, substituting placeholders in templates for
    // actual data. Output and value collections are reset after 
    //completion of this method
    public function render() {
        foreach ($this->_values as $key => $val) {
            $this->_output = str_replace("|$key|", $val, $this->_output);
        }
        $output = $this->_output;
        $this->_output = '';
        $this->_values = array();
        return $output;
    }

    //Sets a value for substitution of values from template files.
    // Requires the key to be replaced in the template, and the 
    //value to replace it with as arguments
    public function set($key, $value) {
        $this->_values[$key] = $value;
    }
    

    //renders a header for a table of comics. Set argument $favourite to true 
    //or false to control whether the favourite field should be rendered.
    public function comicTableHeader($favourite) {
        $this->_output .= '<table><tr><th>Cover</th><th>Series</th><th>'.
                          'Issue</th><th>Event</th><th>Info</th>';
        if ($favourite) {
            $this->_output .= '<th>Favourite</th>';
        }
        $this->_output .= '<th>Add/Delete</th></tr>';
    }

    //renders a tablerow detailing a comic. Set argument $favourite to true
    //or false to control whether the favourite filed should be rendered.
    //Set $addOrDelete to 'ADD' or 'DELETE' to control whether a link for 
    //adding comic to database or deleting from database is rendered.
    public function comicTableRow($comic, $favourite, $addOrDelete) {
        $this->_output .= '<tr><td><img src="' . $comic->cover . 
                          '" style="width: 200px; height: auto;"' .
                          ' alt="Cover"></td>';
        $this->_output .= '<td><a href="series.php?id=' .
                          $comic->seriesdata->id . '">'.
                          $comic->seriesdata->title . '</a></td>';
        $this->_output .= '<td>' . $comic->issue . '</td>';
        if ($comic->eventdata) {
            $this->_output .= '<td><a href="event.php?id=' .
                              $comic->eventdata->id . '">'.
                              $comic->eventdata->title . '</a></td>';
        } else {
            $this->_output .= '<td></td>';
        }
        $this->_output .= '<td><a href="' . $comic->URL . '" target="_blank"' .
                          '>Info</a></td>';
        if ($favourite) {
            $this->_output .= $comic->favourite ? '<td>✔' : '<td>✗';
            $this->_output .= '<sub><a href="index.php?changeFav=' . $comic->dbid .
                              '">Change</a></sub></td>';
        }
        switch (strtoupper($addOrDelete)) {
            case 'ADD':
                $this->_output .= '<td><a href="index.php?addComic=' .
                                  $comic->comicid . '">Add</a></td>';
                break;
            case 'DELETE':
                $this->_output .= '<td><a href="index.php?delComic=' .
                                  $comic->dbid . '">Delete</a></td>';
                break;
            default:
                $this->_output .= '<td></td>';
        }
        $this->_output .= '</tr>';
    }
                


    //renders a footer for a table of comics
    public function comicTableFooter() {
        $this->_output .= '</table>';
    }

    //Renders a form for searching comic series
    public function seriesSearchForm() {
        $this->_output = '<form method="get" action="series.php">' .
                         'Id: <input type="text" name="id">' .
                         '<input type="hidden" name="offset" value="0">' .
                         '<input type="submit" value="Submit"></form>';
    }
    
    //Renders a form for searching comic events
    public function eventSearchForm() {
        $this->_output = '<form method="get" action="event.php">' .
                         'Id: <input type="text" name="id">' .
                         '<input type="hidden" name="offset" value="0">' .
                         '<input type="submit" value="Submit"></form>';
    }

    //Returns links for displaying several pages of results.
    //Needs total number of results, results per page, offset of current
    //results and the target of page links (offset number for each 
    //page will be added to the end of target) as arguments 
    public function pageNumbers($total, $perPage, $offset, $target) {
        $numPages = ceil($total / $perPage);
        $currentPage = (ceil($offset / $perPage) + 1);
        $this->_output .= '<br>';
        for ($i = 1; $i <= $numPages; $i++) {
            if ($i == $currentPage) {
                $this->_output .= '[' . $i . '] ';
            } else {
                $this->_output .= '<a href="' . $target . 
                                  (($i - 1) * $perPage) . '">[' . $i . ']</a> ';
            }
        }
        $this->_output .= '<br><br>';
    }

    public function loginForm ($target) {
        $this->_output .= '<form action="' . $target . '" method="post">' .
                          'Username: <input type="text" name="username">' .
                          '<br>Password: <input type="password" ' .
                          'name="password"><input type="submit" ' .
                          'value="submit"></form>';
    }

}
?>
