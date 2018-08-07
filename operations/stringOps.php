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

    function shortenDescription($longDescription){
        
        if(strlen($longDescription) > 100){
            
            $tempDescription = substr($longDescription, 0, 100);
            return $tempDescription . "...";
        }else{
            
            return $longDescription . "...";
        }
    }

    function createTitle($shortDescription){
        
        //Shorten the description further to be a suitable title.
        if(strlen($shortDescription) > 15){
            
            $tempTitle = substr($shortDescription, 0, 15);
            return $tempTitle . "...";
        }else{

            return $shortDescription . "...";
        }
    }
    
    function createURL($title){
        
        //Trim the size.
        if(strlen($title) > 10){
            
            $tempURL = substr($title, 0, 10);
        }
        
        //Replace the spaces with underscores.
        $tempURL = str_replace(" ", "_", $tempURL);
        return $tempURL;
    }
?>