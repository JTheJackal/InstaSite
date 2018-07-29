<?php

    //Scraper for Instagram.
    function accessNodes($keyword){

        //Store the Instagram search term result as JSON.
        $json = json_decode(file_get_contents("https://www.instagram.com/explore/tags/$keyword/?__a=1", true));
        
        //Step through JSON until the node that contains post nodes is found.
        $nodes = $json->graphql->hashtag->edge_hashtag_to_media->edges;
        
        return $nodes;
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