<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephen
 * Date: 2022-01-03
 * Time: 4:27 PM
 */

// Notify MedWebCMS@ucf.edu of any post updates
/*add_action( 'publish_page', 'notify_admin', 0 );
add_action( 'publish_news', 'notify_admin', 0 );
add_action( 'publish_newsletters', 'notify_admin', 0 );
add_action( 'publish_page', 'notify_admin', 0, 2 );
add_action( 'publish_news', 'notify_admin', 0, 2 );
add_action( 'publish_newsletters', 'notify_admin', 0, 2 );*/
//add_action( 'publish_my-custom-post-type', 'notify_admin', 0 );

add_action( 'transition_post_status', 'notify_me', 10, 3);

/**
 * Notify admins about page changes for specific post types and statuses.
 * @param         $new_status
 * @param         $old_status
 * @param WP_Post $post
 */
function notify_me(  $new_status,  $old_status,  WP_Post $post ) {

	// define the post types to watch and notify
	$post_types_to_watch = [
		'page',
		'news',
		'newsletters'
	];

	$content_status = "";
	$care_about_this_change = false; // lots of changes cause this hook to run, multiple times.

	if (in_array($post->post_type, $post_types_to_watch) ) {

		if ( $old_status === "publish" && $new_status === "publish" ) {
			$content_status         = "updated";
			$care_about_this_change = true;
		} elseif ( $old_status === $new_status ) {
			// we don't care about statuses that stay the same (aside from publish->publish), so ignore those
			$care_about_this_change = false;
		} elseif ( $old_status === "trash" && $new_status === "draft") {
			$content_status         = "restored";
			$care_about_this_change = true;
		} elseif ( $new_status === "publish" ) {
			$content_status         = "published"; // assume any other action ending in publish is a new publish.
			$care_about_this_change = true;

		} elseif ( $new_status === "trash" ) {
			$content_status         = "deleted";
			$care_about_this_change = true;

		} elseif ( $old_status === "new" && $new_status === "inherit" ) {
			$care_about_this_change = false; // this happens all the time. ignore it.

		} elseif ( $old_status === "new" && $new_status === "auto-draft" ) {
			$care_about_this_change = false; // this happens all the time. ignore it.

		} elseif ( $old_status === "auto-draft" && $new_status === "draft" ) {
			$care_about_this_change = false; // this happens all the time. ignore it.

		} else {
			// we can add new rules as needed in the future. for now, any other request, lets notate them in emails.
			$content_status         = "{$old_status} to {$new_status}";
			$care_about_this_change = true;
		}
	}

	if ($care_about_this_change) {
		// check if we've already sent an email in the last 10 seconds. when sending, we set a 10 second transient to prevent race conditions.

		$transient_name_for_race_condition = "email-handled-for-this-post-{$post->ID}";
		$minimum_seconds_before_allowing_another_email = 10;

		if (false === get_transient($transient_name_for_race_condition)) {
			// haven't sent an email for this post in the last 10 seconds. send an email, and set the transient to prevent other emails.
			set_transient($transient_name_for_race_condition, true, $minimum_seconds_before_allowing_another_email);
			remove_action('transition_post_status', 'notify_me', 10); // try to prevent other hooks from firing unnecessarily

			notify_admin_email($content_status, $post);

		}
	}
}

/**
 * @param         $status
 * @param WP_Post $modified_post
 */
function notify_admin_email($status, WP_Post $modified_post){
	$to = "medwebcms@ucf.edu";


	$message = "";

	// get the permalink, then use it to build a clickable link
	$post_view_url = get_permalink($modified_post);

	$host = parse_url($post_view_url, PHP_URL_HOST);
	$post_view_relative_url = parse_url($post_view_url, PHP_URL_PATH) . parse_url($post_view_url, PHP_URL_QUERY); // remove the domain, to prevent from being turned into an active link and then obfuscated by outlook safe protection
	$post_view_html = "<a href='{$post_view_url}'>$post_view_url</a>";

	$post_edit_url = get_edit_post_link($modified_post);
	$post_edit_relative_url = parse_url($post_edit_url, PHP_URL_PATH) . parse_url($post_edit_url, PHP_URL_QUERY); // remove the domain, to prevent from being turned into an active link and then obfuscated by outlook safe protection
	$post_edit_html = "<a href='{$post_edit_url}'>$post_edit_url</a>";

	$post_revision_id = array_shift(wp_get_post_revisions($modified_post->ID))->ID; // get the most recent revision
	$post_revision_admin_url = admin_url("revision.php?revision={$post_revision_id}");
	$post_revision_html = "<a href='{$post_revision_admin_url}'>$post_revision_admin_url</a>";

	$edit_message = "and it can be edited at {$post_edit_html}";
	$revision_message = "View the differences for the latest page revision at {$post_revision_html}";

	$admin_edit_url = admin_url("edit.php?post_status=trash&post_type={$modified_post->post_type}");
	$admin_edit_html = "<a href='{$admin_edit_url}'>$admin_edit_url</a>";

	$page_status = "";

	$post_type = get_post_type_object($modified_post->post_type); // get the entire definition of the custom post type
	$post_type_label = $post_type->labels->singular_name;


	// get the last editor for the page. we might not be in the main loop, so we have to grab it via meta keys and find the name from there
	$last_author_id = get_post_meta( $modified_post->ID, '_edit_last', true );
	$author = get_user_by( "ID", $last_author_id );
	$author_name = "{$author->first_name} {$author->last_name}";

	if ($status == "published") {
		$page_status = "
		<div>A new {$post_type_label} has been published.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";

	} elseif ($status == "updated") {
		$page_status = "
		<div>An existing {$post_type_label} has been updated.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		<div>Changes: {$post_revision_html}</div>
		";
	} elseif ($status == "deleted") {
		$page_status = "
		<div>An existing {$post_type_label} has been deleted.</div>
		<div>Restore: {$admin_edit_html}</div>
		";
	} elseif ($status == "restored") {
		$page_status = "
		<div>A previously deleted {$post_type_label} has been restored to draft.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";
	} else {
		$page_status = "
		<div>An existing {$post_type_label} has transitioned from {$status}.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";
	}


	$message = "
	<html>
	<body>
	<h1><u>{$modified_post->post_title}</u> {$status}</h1>
	<p>{$page_status}</p>
    
    <p>Change by: {$author_name}</p>

    <p>Please review the changes.</p>
    </body>
    </html>
    ";

	$subject = "Content Update Alert - {$host} - \"{$modified_post->post_title}\" {$status}";


	$from = "content-update@{$host}";
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Create email headers
	$headers .= 'From: '.$from."\r\n".
	            'Reply-To: '.$from."\r\n" .
	            'X-Mailer: PHP/' . phpversion();
	//write_log("#############" . $to . $subject . $message . $headers);

	wp_mail( $to, $subject, $message, $headers );
}


if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}


/*function notify_admin( $post_id, $post ) {
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
}*/