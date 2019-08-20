<?php

class twitterfeed implements socialfeed {
    const url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

    public static function fetch_posts($count = 20, $older_than = null){
        $data = get_option('ucf-com-main-screen-options');

        $oauth_access_token =           esc_attr($data['twitter_oauth_access_token']);
        $oauth_access_token_secret =    esc_attr($data['twitter_oauth_access_token_secret']);
        $oauth_consumer_key =           esc_attr($data['twitter_oauth_consumer_key']);
        $oauth_consumer_secret =        esc_attr($data['twitter_oauth_consumer_secret']);

        $oauth = array( 'count' => $count,
                        'oauth_consumer_key' => $oauth_consumer_key,
                        'oauth_nonce' => time(),
                        'oauth_signature_method' => 'HMAC-SHA1',
                        'oauth_token' => $oauth_access_token,
                        'oauth_timestamp' => time(),
                        'oauth_version' => '1.0',
                        'tweet_mode' => 'extended');
        $base_params = $oauth;
        $base_params['tweet_mode'] = 'extended';
        $base_info = self::buildBaseString(self::url, 'GET', $oauth);
        $composite_key = rawurlencode($oauth_consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        // Make Requests
        $header = array(self::buildAuthorizationHeader($oauth), 'Expect:');
        $options = array( CURLOPT_HTTPHEADER => $header,
                          //CURLOPT_POSTFIELDS => $postfields,
                          CURLOPT_HEADER => false,
                          CURLOPT_URL => self::url . '?count=' . $count . '&tweet_mode=extended',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => false);

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        $twitter_data = json_decode($json);
        $twitter_posts = array();

        foreach ($twitter_data as $post){
            $twitter_posts[] = new twitterfeedpost($post);
        }

        return $twitter_posts;
    }
    private static function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    private static function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }
}

class twitterfeedpost implements socialfeedpost {
    public $date; // the timestamp of the post
    public $post_object; // contains the entire post in object format

    function __construct($post = null) {
        $this->post_object = $post;
        $this->date = strtotime($post->created_at);
    }

    function get_date() {
        return $this->date;
    }

    function get_date_formatted(){
        date_default_timezone_set('EST');

        return date("F j, Y", $this->date);
    }
    function print_date() {
        echo $this->get_date_formatted();
    }

    function get_item() {
        return $this->post_object;
    }

    function print_item() {
        // only show tweets by us, not any replies to our tweets. also, don't show any posts of ours that are retweets of other posts.
        if (
            ($this->post_object->user->screen_name == 'ourmedschool') // only show tweets by us, not any likes or replies to our tweets.
            && (!($this->post_object->retweeted_status)) // don't show any posts of ours that are retweets of other posts.
            && (!($this->post_object->in_reply_to_status_id)) // don't show any posts of ours that are replies to other posts.
            && ( (!$this->post_object->is_quote_status) || (self::json_tweet_text_to_HTML( $this->post_object ) > 10)) // show all non-quoted tweets, and only show quoted tweets if there's any substance (more than 10 characters, excluding links and hashtags)
        ){
            /*echo '<div class="grid-item grid-item--width2 index-tweet white-box image">';
            highlight_string( "<?php\n\$data =\n" . var_export( $this->post_object, true ) . ";\n?>" );
            echo '</div>';*/
            if ( isset( $this->post_object->extended_entities->media ) ) {

                echo "<div class='grid-item grid-item--width2 index-tweet white-box image' data-twitter-id='{$this->post_object->id}'>";
                // uncomment in order to show all media from a tweet
                /*foreach ( $this->post_object->extended_entities->media as $media ) {
                    echo "<a href='{$media->media_url_https}' class='twitter-media fancybox' >";
                    echo "<img src='{$media->media_url_https}' />";
                    echo "</a >";
                }*/
                // show only the first media item in a tweet
                echo "<a href='{$this->post_object->extended_entities->media[0]->media_url_https}' class='twitter-media fancybox' >";
                echo "<img src='{$this->post_object->extended_entities->media[0]->media_url_https}' />";
                echo "</a >";

            } else {
                echo "<div class='grid-item index-tweet white-box standard' data-twitter-id='{$this->post_object->id}'>";
            }
            echo self::json_tweet_text_to_HTML( $this->post_object );
            echo "<div class='respond'>";
            echo "<a class='reply' target='_blank' href='https://twitter.com/intent/tweet?in_reply_to={$this->post_object->id}'></a>";
            echo "<a class='retweet' target='_blank' href='https://twitter.com/intent/retweet?in_reply_to={$this->post_object->id}'></a>";
            echo "<a class='favorite' target='_blank' href='https://twitter.com/intent/favorite?in_reply_to={$this->post_object->id}'></a>";
            echo "</div></div>";
        }
    }

    /**
     * PHP string replace isn't utf8 aware, so special characters mess up the indexes. This corrects that.
     * @param $original
     * @param $replacement
     * @param $position
     * @param $length
     *
     * @return string
     *
     * @author https://shkspr.mobi/blog/2012/09/a-utf-8-aware-substr_replace-for-use-in-app-net/
     */
    private static function utf8_substr_replace($original, $replacement, $position, $length) {
        $startString = mb_substr($original, 0, $position, "UTF-8");
        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

        $out = $startString . $replacement . $endString;

        return $out;
    }

    /**
     * Takes a tweet's full text and replaces it with the same text, plus urls for any hashtags, user mentions, and links
     * @param      $tweet
     * @param bool $links
     * @param bool $users
     * @param bool $hashtags
     *
     * @return string
     * @author https://stackoverflow.com/questions/11533214/php-how-to-use-the-twitter-apis-data-to-convert-urls-mentions-and-hastags-in/25514650#25514650
     *         Edited by Stephen Schrauger, to update to utf8 compatible and extended_mode tweets.
     */
    private static function json_tweet_text_to_HTML($tweet, $links=true, $users=true, $hashtags=true) {
        // sometimes, media urls show up on the end of the tweet, but twitter doesn't index them. It does say they aren't part of the standard tweet, so just cut them out of the text.
        $return = mb_substr($tweet->full_text, $tweet->display_text_range[0],$tweet->display_text_range[1]);
        $entities = array();

        /*if($links && is_array($tweet->entities->urls))
        {
            foreach($tweet->entities->urls as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " <a href='".$e->expanded_url."' target='_blank'>".$e->display_url."</a>";
                $entities[] = $temp;
            }
        }
        if($users && is_array($tweet->entities->user_mentions))
        {
            foreach($tweet->entities->user_mentions as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " <a href='https://twitter.com/".$e->screen_name."' target='_blank'>@".$e->screen_name."</a>";
                $entities[] = $temp;
            }
        }
        if($hashtags && is_array($tweet->entities->hashtags))
        {
            foreach($tweet->entities->hashtags as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " <a href='https://twitter.com/hashtag/".$e->text."?src=hash' target='_blank'>#".$e->text."</a>";
                $entities[] = $temp;
            }
        }

        usort($entities, function($a,$b){return($b["start"]-$a["start"]);});

        foreach($entities as $item)
        {
            $return =  utf8_substr_replace($return, $item["replacement"], $item["start"], $item["end"] - $item["start"]);
        }
        */

        return($return);
    }

    /**
     * Takes a tweet's full text and returns only real text content. Any user mentions, hashtags, and urls are completely removed.
     * Do not use for display. Use only to calculate how much real content is in the tweet, to determine if it should be shown.
     * @param      $tweet
     *
     * @return string
     * @author https://stackoverflow.com/questions/11533214/php-how-to-use-the-twitter-apis-data-to-convert-urls-mentions-and-hastags-in/25514650#25514650
     *         Edited by Stephen Schrauger, to update to utf8 compatible and extended_mode tweets.
     */
    private static function json_tweet_text_content_only($tweet) {
        // sometimes, media urls show up on the end of the tweet, but twitter doesn't index them. It does say they aren't part of the standard tweet, so just cut them out of the text.
        $return = mb_substr($tweet->full_text, $tweet->display_text_range[0],$tweet->display_text_range[1]);
        $entities = array();

        if(is_array($tweet->entities->urls))
        {
            foreach($tweet->entities->urls as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " ";
                $entities[] = $temp;
            }
        }
        if(is_array($tweet->entities->user_mentions))
        {
            foreach($tweet->entities->user_mentions as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " ";
                $entities[] = $temp;
            }
        }
        if(is_array($tweet->entities->hashtags))
        {
            foreach($tweet->entities->hashtags as $e)
            {
                $temp["start"] = $e->indices[0];
                $temp["end"] = $e->indices[1];
                $temp["replacement"] = " ";
                $entities[] = $temp;
            }
        }

        usort($entities, function($a,$b){return($b["start"]-$a["start"]);});

        foreach($entities as $item)
        {
            $return =  utf8_substr_replace($return, $item["replacement"], $item["start"], $item["end"] - $item["start"]);
        }


        return($return);
    }
}
