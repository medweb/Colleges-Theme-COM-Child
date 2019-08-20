<?php

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2017-12-18
 * Time: 12:59 PM
 */
class instagramfeed implements socialfeed {

    public function __construct($post) {

    }

    /**
     * @param int $count Number of posts to return. Defaults to 20.
     * @param     $min_id The Media ID of the oldest media you have. The API will return up to $count of *older* posts than the one listed here.
     *
     * @return array Array of media objects.
     */
    public static function fetch_posts($count = 20, $min_id = null){
        $data = get_option('ucf-com-main-screen-options');

        $user_id =      esc_attr($data['instagram_user_id']);
        $access_token = esc_attr($data['instagram_access_token']);

        $userid_url = "https://api.instagram.com/v1/users/$user_id/media/recent/?access_token=$access_token&count=$count";
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