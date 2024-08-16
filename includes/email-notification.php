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

const status_updated = "updated";
const status_restored = "restored";
const status_published = "published";
const status_deleted = "deleted";
const hosts_array = array("med.ucf.edu"); // define hosts that activate email notifications (in order to ignore dev/test)

$host = parse_url( get_site_url(), PHP_URL_HOST );
if (in_array($host, hosts_array)){
    // only notify on page changes for the main site. don't send emails when dev pages change.
    add_action( 'transition_post_status', 'notify_me', 10, 3);
}


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

	// cooldown time after the first email for a specific page is sent.
	// recommend no less than 10 seconds, to prevent double emails on a single edit.
	// Any edits made within this time limit after the first edit for a specific page
	// will not send an email notating changes.
	$minimum_seconds_before_allowing_another_email = 60 * 15;


	$content_status = "";
	$care_about_this_change = false; // lots of changes cause this hook to run, multiple times.

	if (in_array($post->post_type, $post_types_to_watch) ) {

		if ( $old_status === "publish" && $new_status === "publish" ) {
			$content_status         = status_updated;
			$care_about_this_change = true;
		} elseif ( $old_status === $new_status ) {
			// we don't care about statuses that stay the same (aside from publish->publish), so ignore those
			$care_about_this_change = false;
		} elseif ( $old_status === "trash" && $new_status === "draft") {
			$content_status         = status_restored;
			$care_about_this_change = true;
		} elseif ( $new_status === "publish" ) {
			$content_status         = status_published; // assume any other action ending in publish is a new publish.
			$care_about_this_change = true;

		} elseif ( $new_status === "trash" ) {
			$content_status         = status_deleted;
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

		$transient_name_for_cooldown_inhibitor = calculate_transient_name($post->ID,$content_status);

		if (false === get_transient($transient_name_for_cooldown_inhibitor)) {
			// haven't sent an email for this post in the last 10 seconds. send an email, and set the transient to prevent other emails.
			set_transient_for_status_and_clear_other_status_transients($post->ID, $content_status, $minimum_seconds_before_allowing_another_email);

			remove_action('transition_post_status', 'notify_me', 10); // try to prevent other hooks from firing unnecessarily

			notify_admin_email($content_status, $post);

		}
	}
}

/**
 * @param $status
 */

/**
 * This sets a transient for the current post and its action, and it clears the transients
 * for the other actions. This way, someone can edit the page multiple times within
 * the cooldown period without emails getting sent, but if they do a different status,
 * such as delete or publish the page, that will re-send an email and reset transients.
 * This prevents a page from getting a minor edit, then being deleted without a notification
 * email being sent that it was deleted after being edited.
 * Note: It does not delete transients for unknown old-to-new statuses (the 'else' clause
 * in checking if we care about the page's change)
 *
 * @param $post_id
 * @param $content_status
 */
function set_transient_for_status_and_clear_other_status_transients($post_id, $content_status, $transient_lifespan){
	set_transient(calculate_transient_name($post_id, $content_status), true, $transient_lifespan);

	if ($content_status !== status_deleted){
		delete_transient(calculate_transient_name($post_id, status_deleted));
	}
	if ($content_status !== status_published){
		delete_transient(calculate_transient_name($post_id, status_published));
	}
	if ($content_status !== status_restored){
		delete_transient(calculate_transient_name($post_id, status_restored));
	}
	if ($content_status !== status_updated){
		delete_transient(calculate_transient_name($post_id, status_updated));
	}

}

/**
 * Calculates the transient string, based on the post id and the content status.
 * This may need to change in the future, depending on transient name length restrictions or other purposes,
 * so it was made into a separate function.
 * @param $post_id
 * @param $content_status
 *
 * @return string
 */
function calculate_transient_name($post_id, $content_status){
	return sanitize_title_with_dashes("email-handled-for-this-post-{$post_id}-{$content_status}"); // replace spaces with dashes, if needed
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

	if ($status == status_published) {
		$page_status = "
		<div>A new {$post_type_label} has been published by {$author_name}.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";

	} elseif ($status == status_updated) {
		$page_status = "
		<div>An existing {$post_type_label} has been updated by {$author_name}.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		<div>Changes: {$post_revision_html}</div>
		";
	} elseif ($status == status_deleted) {
		$page_status = "
		<div>An existing {$post_type_label} has been deleted by {$author_name}.</div>
		<div>Restore: {$admin_edit_html}</div>
		";
	} elseif ($status == status_restored) {
		$page_status = "
		<div>A previously deleted {$post_type_label} has been restored to draft by {$author_name}.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";
	} else {
		$page_status = "
		<div>An existing {$post_type_label} has transitioned from {$status} by {$author_name}.</div>
		<div>View: {$post_view_html}</div>
		<div>Edit: {$post_edit_html}</div>
		";
	}


	$message = "
	<html>
	<body>
	<h1><u>{$modified_post->post_title}</u> {$status}</h1>
	<p>{$page_status}</p>

    <p>Please review the changes.</p>
    <p>Note: More edits may have been made within the last 15 minutes of this email. Please check the revision history.</p>
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
