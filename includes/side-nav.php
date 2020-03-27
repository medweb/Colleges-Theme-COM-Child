<?php

$args = array (

    'child_of' => get_the_ID(),
    'depth' => 1,
    'title_li' => '',
    'echo' => 0

);

$children = wp_list_pages( $args );

//echo 'TROUBLESHOOT the Current Page ID: '.get_the_ID(); ?> 

<div class="side-nav">

    <?php //var_dump ( get_field( 'show_rsn') ) ?>

    <?php 

    $haschildset = ( $children && get_field( 'show_rsn') );
    $haschildunset = ( $children && get_field( 'show_rsn') === NULL );


    if ( $haschildunset || $haschildset ) { ?>

        <ul class="autonav">

            <h4>In This Section</h4>

            <?php echo $children; ?>

        </ul>

    <?php } if ( get_field( 'right_side_custom' ) ) { ?>

        <aside class="right-side-info">

            <?php the_field( 'right_side_custom' ); ?>

        </aside>

    <?php } ?>

</div>