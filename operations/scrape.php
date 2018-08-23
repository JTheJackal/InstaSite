<?php

    //Scraper for Instagram.
    function accessNodes($keyword){

        global $maxID;
        
        //Store the Instagram search term result as JSON.
        $json = json_decode(file_get_contents("https://www.instagram.com/explore/tags/$keyword/?__a=1" . $maxID, true, stream_context_create(array("http" => array("user_agent" => "any")))));
        
        //var_dump($json);
        
        //Now find the end cursor so that we can see all of the results returned by Instagram.
        if($json->graphql->hashtag->edge_hashtag_to_media->page_info->has_next_page){
            
            
            $maxID = scrapeEndpoint($json);
        }else{
            
            echo "does not have a next page";
        }
        
        //Step through JSON until the node that contains post nodes is found. This is specific to the nodes layout on Instagram.
        $nodes = $json->graphql->hashtag->edge_hashtag_to_media->edges;
        
        return $nodes;
    }

    function scrapeEndpoint($json){
        
        return "&max_id=" . $json->graphql->hashtag->edge_hashtag_to_media->page_info->end_cursor;
    }

    function scrapeImage($nodes, $targetNode){

        //Step through the given node until the image is found.
        $image = $nodes[$targetNode]->node->display_url;
        
        return $image;
    }

    function scrapeDescription($nodes, $targetNode){
        
        //Step through the given node until the post description is found.
        $description = $nodes[$targetNode]->node->edge_media_to_caption->edges[0]->node->text;
        
        return $description;
    }

    function scrapeLikes($nodes, $targetNode){
        
        //Step through the given node until the number of likes is found.
        $likes = $nodes[$targetNode]->node->edge_liked_by->count;
        
        return $likes;
    }

    function scrapePoster($nodes, $targetNode){

        //Step through the given node until the user who posted is found. The owner ID is returned.
        $poster = $nodes[$targetNode]->node->owner->id;
        
        return $poster;
    }

    function scrapeDate($nodes, $targetNode){

        //Step through the given node until the post date is found.
        $date = $nodes[$targetNode]->node->taken_at_timestamp;
        
        return $date;
    }

    function scrapeSource($nodes, $targetNode){
        
        //Step through until the source is found and append to Instagram site.
        $source = $nodes[$targetNode]->node->shortcode;
        
        return "https://www.instagram.com/p/" . $source . "/";
    }

    function scrapePostID($nodes, $targetNode){

        //Step through the given node until the post date is found. The post ID is returned.
        $postID = $nodes[$targetNode]->node->id;
        
        return $postID;
    }

    function scrapeIsVideo($nodes, $targetNode){

        //Step through the given node until the video check is found. A boolean is returned.
        $videoBool = $nodes[$targetNode]->node->is_video;
        
        return $videoBool;
    }
?>