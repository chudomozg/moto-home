<?php 
if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

// проверка пройдена успешно. Начиная от сюда удаляем опции и все остальное.
delete_option('motohome_db_version');

// удаляем таблицы из БД
global $wpdb;
$table_name = $wpdb->prefix . 'mthome_rooms';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

$table_name = $wpdb->prefix . 'mthome_booking';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);

// удаляем тип записи
unregister_post_type("motohome");

?>