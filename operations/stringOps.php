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

    function extractHashtags($str){
        
        //Regular expression, as found on https://stackoverflow.com/questions/3060601/retrieve-all-hashtags-from-a-tweet-in-a-php-function, Stores all occurences of hashtags in a given string.
        preg_match_all('/#([\p{Pc}\p{N}\p{L}\p{Mn}]+)/u', $str, $hashtags);
        
        //var_dump($hashtags);
        
        return $hashtags;
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
        if(strlen($shortDescription) > 25){
            
            $tempTitle = substr($shortDescription, 0, 25);
            return $tempTitle . "...";
        }else{

            return $shortDescription . "...";
        }
    }
    
    function removeQuotes($string){
        
        $tempString = str_replace('"', '', $string);
        return $tempString;
    }
    
    function createURL($title, $postID, $keywords){
        
        //Ensure the title is UTF-8 encoded.
        if(!mb_detect_encoding($title, 'UTF-8', true)){
            
            echo "URL is not UTF-8: " . $title;
            $title = mb_convert_encoding($title, "UTF-8");
                       
        }
        
        //Trim the size.
        if(strlen($title) > 10){

            $tempURL = substr($title, 0, 15);
        }

        //Remove the dots.
        //$tempURL = str_replace(".", "", $tempURL);
        
        //Remove the hash tags.
        //$tempURL = str_replace("#", "", $tempURL);
        
        //Remove the question marks.
        //$tempURL = str_replace("?", "", $tempURL);
        
        //Remove anything that isn't characters A-Z, a-z or 0-9
        $tempURL = preg_replace('/[^A-Za-z0-9\-]/', '', $tempURL);
        
        if($tempURL == ""){
            
            $tempURL = "" . $keywords . $postID . "-post";
        }

        //Replace the spaces with underscores.
        $tempURL = preg_replace('/\s+/', '-', $tempURL);

        return $tempURL;
    }
?>