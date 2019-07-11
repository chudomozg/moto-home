<?php
// use Carbon_Fields\Container;
// use Carbon_Fields\Field;

$select_options = [
    "1" => "one",
    "2" => "two"
];

function mth_select_update(){
    carbon_set_post_meta( 75, 'mth_rooms_select', $select_options );
}

add_action( 'wp_ajax_mth_select_update', 'mth_select_update' );
add_action( 'wp_ajax_nopriv_mth_select_update', 'mth_select_update' );
