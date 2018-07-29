<?php

    function findKeyword($string, $from, $to){

        //Locate a substring between desired search terms.
        $sub = substr($string, strpos($string,$from)+strlen($from),strlen($string));
        return substr($sub,0,strpos($sub,$to));
    }

    function extractKeyword($str, $removeStr){
        
        //Remove everything except the desired string.
        return str_replace("str", "", "#" . $removeStr . " ");
    }

    function removeString ($string, $search){
        
        //Remove nothing except the desired string.
        return str_replace($search, "", $string);
    }
?>