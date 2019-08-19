<?php

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2017-12-18
 * Time: 12:59 PM
 */
class instagramfeed implements socialfeed {

    const access_token = '1190672314.d90570a.8c9c2572ee10427a948eb9ad787aa69f';
    // to regenerate the access_toke, go to https://outofthesandbox.com/pages/instagram-access-token
    // instagram is stupid hard to get set up. you have to use a redirect and accept connections from their endpoint
    // just to authorize a single user to get an access token. way too much work just to be able to use their
    // api to view a user's posts. so we use a 3rd party who already has this set up and get an access code from there.
    // The access_token *shouldn't* expire, but if it does, it must be generated again.
    const user_id = '1190672314';

    public function __construct($post) {

    }

    /**
     * @param int $count Number of posts to return. Defaults to 20.
     * @param     $min_id The Media ID of the oldest media you have. The API will return up to $count of *older* posts than the one listed here.
     *
     * @return array Array of media objects.
     */
    public static function fetch_posts($count = 20, $min_id = null){
        $userid_url = 'https://api.instagram.com/v1/users/'.self::user_id.'/media/recent/?access_token='.self::access_token.'&count='.$count;
        $json_posts = file_get_contents($userid_url);
        $posts = json_decode($json_posts,true)['data'];
        $instagramfeed = array();
        
        foreach ($posts as $post){
            $instagramfeed[] = new instagramfeedpost($post);
            
        }
        return $instagramfeed;

    }
    
}

class instagramfeedpost implements socialfeedpost{
    private $date; // the timestamp of the post
    private $post_object; // contains the entire post in object format

    function __construct($post = null) {
        $this->post_object = $post;
        $this->date = $post['created_time'];
    }

    public function get_item() {
        return $this->post_object;
    }

    public function get_date() {
        return $this->date;
    }

    function get_date_formatted(){
        date_default_timezone_set('EST');

        return date("F j, Y", $this->date);
    }
    function print_date() {
        echo $this->get_date_formatted();
    }

    public function print_item() {
        echo "<div class='grid-item grid-item--width2 white-box image instagram-post' data-instagram-id='{$this->post_object['id']}'>";
            echo "<img src='{$this->post_object["images"]["standard_resolution"]["url"]}' />";
            echo "<span class='caption'>{$this->post_object['caption']['text']}</span>";
        echo "</div>";
    }
}