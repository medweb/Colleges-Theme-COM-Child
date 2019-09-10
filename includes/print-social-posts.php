<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 2018-01-12
 * Time: 12:33 PM
 */
// To change the output number, either call ::output(XY) with another count, or change the class' default count and call with ::output()
class print_social_posts{
    const default_count = 50;
    /**
     * Get all feeds, sort them all, then loop through and print them each individually.
     * @param int $count
     */
    public static function output($count = self::default_count) {
        locate_template( 'includes/social-feed.php', TRUE, TRUE );
        locate_template( 'includes/instagram-feed.php', TRUE, TRUE );
        locate_template( 'includes/twitter-feed.php', TRUE, TRUE );

	    $twitter_posts = get_transient("ucf-com-main-screen-twitter-posts-{$count}");
	    if (!$twitter_posts){
		    $twitter_posts = twitterfeed::fetch_posts($count);
		    set_transient('ucf-com-main-screen-twitter-posts', $twitter_posts, WP_FS__TIME_10_MIN_IN_SEC);
	    }

	    $instagram_posts = get_transient("ucf-com-main-screen-instagram_posts-{$count}");
	    if (!$instagram_posts){
		    $instagram_posts = instagramfeed::fetch_posts($count);
		    set_transient('ucf-com-main-screen-instagram_posts', $instagram_posts, WP_FS__TIME_10_MIN_IN_SEC);
	    }

        $all_posts_sorted = self::sort_posts(array_merge($twitter_posts,$instagram_posts));
        //var_dump($all_posts_sorted);

	    $i = 0;
        foreach ($all_posts_sorted as $post) {
        	$i++;
            /* @var $post socialfeedpost */
            //echo "count is $i, and max is $count";
            if ($i > $count) break; // our $count should be the total results, not the subtotal for each category. break once we reach our $count.
            $post->print_item();
        }
    }

    /**
     * Takes all instagram and twitter posts (and any other types) and sorts them by date into one large array.
     * The newest post is first.
     * @param $array_merge socialfeedpost[] An array containing every post. You should use array_merge on all your feeds.
     *
     * @return socialfeedpost[]
     */
    public static function sort_posts($array_merge){
        usort($array_merge, "print_social_posts::cmp");
        return $array_merge;
    }

    /**
     * @param $a socialfeedpost
     * @param $b socialfeedpost
     *
     * @return int
     */
    public static function cmp($a, $b){
        if ($a->get_date() == $b->get_date()) {
            return 0;
        }
        return ($a->get_date() < $b->get_date()) ? 1 : -1;
    }
}
print_social_posts::output(30);


