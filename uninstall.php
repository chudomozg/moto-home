<?php 
if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

// удаляем типы записи
unregister_post_type("motohome");
unregister_post_type("mt_booking");
unregister_taxonomy( 'mth_city' );


?>