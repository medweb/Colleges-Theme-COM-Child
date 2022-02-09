<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2022-01-03
 * Time: 4:27 PM
 */

//@TODO old function isn't even working. might be because it's moved to a new file.

// Notify MedWebCMS@ucf.edu of any post updates
add_action( 'publish_page', 'notify_admin', 0 );
add_action( 'publish_news', 'notify_admin', 0 );
add_action( 'publish_newsletters', 'notify_admin', 0 );
add_action( 'publish_page', 'notify_admin', 0, 2 );
add_action( 'publish_news', 'notify_admin', 0, 2 );
add_action( 'publish_newsletters', 'notify_admin', 0, 2 );
//add_action( 'publish_my-custom-post-type', 'notify_admin', 0 );

// posts of these types will send alert emails when they are updated or deleted

//$array_post_types_to_watch = [
//	'page',
//	'news',
//	'newsletters'
//];

$array_actions_to_watch = [
	'trash',
	'draft',
	'private',
	'publish',
	'future',
	'pending'
];

// nested loop, to watch for trash->publish and publish->trash, etc
foreach ($array_actions_to_watch as $action_old){
	foreach ($array_actions_to_watch as $action_new) {

		// use a closure (anonymous function) so that we can pass in the variable of the status defined here into the function later.
		// also expect the POST object from the normal do_action - call it $post_from_action.
		add_action(
			"{$action_old}_to_{$action_new}",
			function ($post_from_action) use ( $action_old, $action_new ) {
				notify_admin_new($action_old, $action_new, $post_from_action);
			},
			10, // priority
			1 // expect the do_action to pass in one variable, the post object
		);

	}
}

function notify_admin( $post_id, $post ) {
	$permalink = get_permalink( $post_id );
	$authorname = get_the_modified_author();

	if ($authorname) {
		// notify_admin is being called more than once. however, authorname is only set during one of those calls.
		// therefore, simply check that variable before sending an email.
		// wordpress doesn't guarantee that this hook will only be called once.

		$message = 'Content has been updated, published or deleted by ';
		$message .= $authorname;
		$message .= '. Please review it.' . "\n\n";
		$message .= 'If you receive a 404 error, the page was deleted.' . "\n\n";
		$message .= $permalink;

		wp_mail( 'medwebcms@ucf.edu', 'Content Update Alert - med.ucf.edu', $message );

	}
}


//add_action( "transition_post_status", 'notify_admin', 10, 3 );


/**
 * @param $new_status string
 * @param $old_status string
 * @param $post WP_Post
 */
function notify_admin_new( $new_status, $old_status, $post ) {
	// Check if it's a post type we care about. If not, bail.
	$array_post_types_to_watch = [
		'page',
		'news',
		'newsletters'
	];
	if (!(in_array($post->post_type, $array_post_types_to_watch ))){
		return;
	}

	// Remove subsequent actions for this trigger
	remove_action("transition_post_status", 'notify_admin', 10); // prevent double emails, as this hook fires twice. remove the action after the first trigger.

	// Get the details of the post for the email.
	$permalink = get_permalink( $post->ID );
	$authorname = get_the_modified_author();

	// If status is unchanged, then content was simply updated.
	if ($new_status == $old_status) {
		$status = "has been updated";
	} else {
		// status was changed. it moved from draft to published, or published to trash, etc.
		$status = "has transitioned from {$old_status} to {$new_status}";
	}

	// if there is an author for the most recent edit, send an email.
	// I'm guessing this was a previous code check to prevent double emails or something. Leaving it in here since it likely won't hurt anything.
	if ($authorname) {
		// notify_admin is being called more than once. however, authorname is only set during one of those calls.
		// therefore, simply check that variable before sending an email.
		// wordpress doesn't guarantee that this hook will only be called once.

		// The {newsletter} "Newsletter title" {has transitioned from published to private} by {Some Author}. Please review it.
		$message =
			"
The {$post->post_type} \"{$post->post_title}\" {$status} by {$authorname}. Pleave review it.

If you receive a 404 error, the page was deleted.

{$permalink}        
        
";

		wp_mail( 'medwebcms@ucf.edu', 'Content Update Alert - med.ucf.edu - ' . $post->post_title, $message );

	}
}
