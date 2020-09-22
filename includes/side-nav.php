<?php

// output a side nav, if the user hasn't disabled it.
// side nav shows current page's children, or if none, current page's siblings.
// also outputs a legacy side nav if previously defined.

$args = array (
    'child_of' => get_the_ID(),
    'depth' => 1,
    'title_li' => '',
    'echo' => 0,
    'post_status' => 'publish'
);


$links_to_show = wp_list_pages( $args );
if (!$links_to_show){
    // no children. get siblings
    $args['child_of'] = wp_get_post_parent_id(get_the_ID()); // get this page's parent, then find its children.
	$links_to_show = wp_list_pages( $args );
}

$show_right_side_nav = get_field( 'show_rsn');
if ($show_right_side_nav === null) {
	// we want to default to showing. if the page hasn't been edited since the migration, this might not exist.
	// so a null value should be equated to the default of true.
	$show_right_side_nav = true;
}
$legacy_right_side_custom_data = get_field( 'right_side_custom' ) ;

// if we have a right side nav, or if the legacy nav is defined, output the side-nav div

if ($show_right_side_nav || $legacy_right_side_custom_data){
    echo "
    <div class='side-nav'>
    ";

    if ($show_right_side_nav){
        echo "
        <ul class='autonav'>
            <h4>In This Section</h4>
            {$links_to_show}
        </ul>
        ";
    } else {
        // no children. output siblings.
    }

	// right_side_custom is a legacy field.
	// output it if it was previously defined.
    if ($legacy_right_side_custom_data) {
        echo "
        <aside class='right-side-info'>
            {$legacy_right_side_custom_data}
        </aside>
        ";
    }

    echo "</div>";
};