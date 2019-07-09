<?php
/**
 * Plugin Name: MotoHome
 * Description: Плагин бронирования мотодомов: добавление, бронирование, управление.
 * Plugin URI:  Ссылка на инфо о плагине
 * Author URI:  https://freelansim.ru/freelancers/chudomozg
 * Author:      Иван Тимошенко	
 * Version:     1.0
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     false.
 */






 
// require_once( dirName(__FILE__). '/../carbon-fields/carbon-fields-plugin.php' );
// use Carbon_Fields\Container;
// use Carbon_Fields\Field;
// add_action( 'carbon_fields_register_fields', 'crb_register_custom_fields' ); // Для версии 2.0 и выше
// function crb_register_custom_fields() {
//     require_once( dirName(__FILE__). '/../carbon-fields/post-meta.php' );
// }















// Container::make( 'post_meta', 'Произвольные настройки мои' )
//    ->add_fields( array(
// 		Field::make( 'text', 'my_text', 'Text Field' ),
// 	) );


// add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
// function crb_attach_theme_options() {
//     Container::make( 'theme_options', __( 'Theme Options' ) )->add_fields( array(
//             Field::make( 'text', 'crb_text', 'Text Field' ),
//         ) );
// }

// add_action( 'after_setup_theme', 'crb_load' );
// function crb_load() {
//     require_once( dirName(__FILE__). '/../carbon-fields/vendor/autoload.php' );
//     \Carbon_Fields\Carbon_Fields::boot();
// }



global $motohome_db_version;
$motohome_db_version = "1.0";
// require_once(ABSPATH . 'wp-content/plugins/moto-home/cmb2-functions.php');//ф-ции для cmb2 полей
//ф-ция установки плагина, тут добавим таблицы в БД, тип POST, meta-поля и др. настройки
 function motohome_install(){
    global $wpdb;
    global $motohome_db_version;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //таблица комнат
    $table_name = $wpdb->prefix . "mthome_rooms";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
        $sql = "CREATE TABLE " . $table_name . " (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        id_dom int(11) NOT NULL default '0',
        name tinytext NOT NULL,
        UNIQUE KEY id (id)
      );";
  
        dbDelta($sql);
    }

    // таблица заявок
     $table_name = $wpdb->prefix . "mthome_booking";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
        $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        id_room mediumint(9) NOT NULL default '0',
        date_start datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        date_end datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        UNIQUE KEY id (id)
      );";
  
        dbDelta($sql);
  
        // $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
   
        add_option("motohome_db_version", $motohome_db_version);
     }

     //  добавим сообщение про то, что для работы нужен плагин CMB2
    //add_action( 'admin_notices', array( $this, 'test_notice' ) );

 }
//хук активации (установки) плагина
register_activation_hook( __FILE__, 'motohome_install' );


add_action( 'init', 'motohome_type_register' ); // Использовать функцию регистрации типа можно только внутри хука init
//функция регистрации типа POST: motohome
function motohome_type_register() {
	$labels = array(
		'name' => 'МотоДома',
		'singular_name' => 'МотоДом', // админ панель Добавить->Функцию
		'add_new' => 'Добавить МотоДом',
		'add_new_item' => 'Добавить новый МотоДом', // заголовок тега <title>
		'edit_item' => 'Редактировать МотоДом',
		'new_item' => 'Новый МотоДом',
		'all_items' => 'Все МотоДома',
		'view_item' => 'Просмотр МотоДома',
		'search_items' => 'Искать МотоДома',
		'not_found' =>  'МотоДомов не найдено.',
		'not_found_in_trash' => 'В корзине нет МотоДомов.',
		'menu_name' => 'МотоДома' // ссылка в меню в админке
	);
	$args = array(
		'labels' => $labels,
		'public' => true, // благодаря этому некоторые параметры можно пропустить
		'menu_icon' => 'dashicons-location-alt', // иконка корзины
		'menu_position' => 5,
		'has_archive' => false,
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail'),//тут можно отзывы сделать на основе комментов
        'rewrite' => array('with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ), //постоянные ссылки URL
        // 'taxonomies' => array('post_tag') //сначала была идея сделать города Мотодомов через таксономию
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'exclude_from_search' => false,
		//'capability_type'     => array ("МотоДом","МотоДома"), //Пока какая-то проблема с разрешениями и ролями, не разобрался
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'query_var'           => true,
	);
    register_post_type('motohome',$args);
}





/////////////////////////////////////////////////////////////////////////
//Добавляем мета-поля через CarbonFields
// Container::make( 'post_meta', 'Custom Data' )
//     ->where( 'post_type', '=', 'page' )
//     ->add_fields( array(
//         Field::make( 'map', 'crb_location' )
//             ->set_position( 37.423156, -122.084917, 14 ),
//         Field::make( 'sidebar', 'crb_custom_sidebar' ),
//         Field::make( 'image', 'crb_photo' ),
//     ));





//  добавим сообщение про то, что для работы нужен плагин CMB2
function test_notice() {
    return '<div class="notice notice-info"><p>Важно! Для коректной работы необходим плагин <a href="https://ru.wordpress.org/plugins/cmb2/">CMB2</a></p></div>';
}//не работает чет =) потом разберусь


 ?>