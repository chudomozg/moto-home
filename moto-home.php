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


//ДЕБАГ - НЕ ЗАБУДЬ УДАЛИТЬ///////////////////////////////////////////////////////////////////////////////
if( WP_DEBUG && WP_DEBUG_DISPLAY && (defined('DOING_AJAX') && DOING_AJAX) ){
    @ ini_set( 'display_errors', 1 );
}
//ДЕБАГ - НЕ ЗАБУДЬ УДАЛИТЬ///////////////////////////////////////////////////////////////////////////////


global $motohome_db_version;
$motohome_db_version = "1.0";
// require_once(ABSPATH . 'wp-content/plugins/moto-home/cmb2-functions.php');//ф-ции для cmb2 полей
//ф-ция установки плагина, тут добавим таблицы в БД, тип POST, meta-поля и др. настройки
 function motohome_install(){
    global $wpdb;
    global $motohome_db_version;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   // require_once(dirName(__FILE__) . 'mt_ajax_select.php');

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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Регистрируем Тип: motohome
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
        'show_in_rest' => true,
	);
    register_post_type('motohome',$args);
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//Добавляем мета-поля для типа motohome
require_once( dirName(__FILE__). '/../carbon-fields/carbon-fields-plugin.php' );

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'mthome_register_fields' ); // Для версии 2.0 и выше
function mthome_register_fields() {
	// путь к файлу определения поля (полей)
    require_once( dirName(__FILE__). '/motohome-meta.php' ); //поля описываются в этом файле
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Регистрируем Тип: mt_booking
add_action( 'init', 'mt_booking_type_register' ); // Использовать функцию регистрации типа можно только внутри хука init
//функция регистрации типа POST: mt_booking
function mt_booking_type_register() {
	$labels = array(
		'name' => 'Бронирование МотоДома',
		'singular_name' => 'Бронь МотоДома', // админ панель Добавить->Функцию
		'add_new' => 'Добавить Бронь',
		'add_new_item' => 'Добавить новую Бронь', // заголовок тега <title>
		'edit_item' => 'Редактировать Бронь',
		'new_item' => 'Новая Бронь МотоДома',
		'all_items' => 'Все Брони',
		'view_item' => 'Просмотр Брони',
		'search_items' => 'Искать Бронь',
		'not_found' =>  'Броней не найдено.',
		'not_found_in_trash' => 'В корзине нет Броней.',
		'menu_name' => 'Бронирования МотоДомов' // ссылка в меню в админке
	);
	$args = array(
		'labels' => $labels,
		'public' => true, // благодаря этому некоторые параметры можно пропустить
		'menu_icon' => 'dashicons-arrow-down-alt', // иконка
		'menu_position' => 5,
		'has_archive' => false,
        'supports' => array('title'),//тут можно отзывы сделать на основе комментов
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
        'show_in_rest' => true,
	);
    register_post_type('mt_booking',$args);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Добавляем Yandex Map к типу motohome

//подключаем скрипты Yamaps к админке
add_action( 'admin_enqueue_scripts', 'mthome_ad_yascript_add' );
function mthome_ad_yascript_add( $hook_suffix ){
    //ВНИМАНИЕ ЗАМЕНИ НА СВОЙ ключ API в ссылке 
	wp_enqueue_script('yamaps_api', 'https://api-maps.yandex.ru/2.1/?apikey=01be389f-988a-42c3-8fb5-5a5fcd4179d2&lang=ru_RU');
	wp_enqueue_script('yamap.js', plugins_url('moto-home/js/yamap.js'));
    wp_enqueue_script('mt_rooms.js', plugins_url('moto-home/js/mt_rooms.js'));
    wp_localize_script( 'mt_rooms.js', 'site_url', get_site_url());
    wp_localize_script( 'mt_rooms.js', 'dir_url', dirName(__FILE__));
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//Добавляем мета-поля для типа motohome

add_action( 'carbon_fields_register_fields', 'mt_booking_register_fields' ); // Для версии 2.0 и выше
function mt_booking_register_fields() {
	// путь к файлу определения поля (полей)
    require_once( dirName(__FILE__). '/mt_booking-meta.php' ); //поля описываются в этом файле
}

//функция добавления в ассоциативный массив
function array_push_assoc($array, $key, $value){
	$array[$key] = $value;
	return $array;
}

//функция обновления опций в селекте комнат
function mth_select_update(){
	global $wpdb;
	$rooms_id = $_REQUEST["rooms"];
	$sql = 'SELECT meta_id, meta_value, meta_key FROM wp_postmeta WHERE post_id = '.$rooms_id["post_id"];
	$sql_request =  $wpdb->get_results($sql);
	$rooms=[];
	$room=[];
	if ($sql_request){
		foreach ($sql_request as $meta_key){
			$key = $meta_key->meta_key;
			if (strripos($key,'|')){//опции в БД хранятся в виде meta_key = _rooms|room_name|0|0|value meta_value= Спальня №1
				$meta_key_arr = explode('|', $key);
				if ($meta_key_arr[0]=="_rooms"){
					if  ($meta_key_arr[1]=="room_name"){
						//array_push($room, ['id'], $rooms_id["post_id"].$meta_key_arr[2]);
						$room=array_push_assoc($room, 'id', $rooms_id["post_id"].'-'.$meta_key_arr[2]);
						//array_push($room, ['name'],$meta_key->meta_value);
						$room=array_push_assoc($room, 'name' ,$meta_key->meta_value);
					}elseif ($meta_key_arr[1]=="room_cost"){
						//array_push($room, ['cost'], $meta_key->meta_value);
						$room=array_push_assoc($room, 'cost', $meta_key->meta_value);
					}
				}
			}
			if (count($room)>2){	
				if (array_key_exists("id", $room) && array_key_exists("name", $room) && array_key_exists("cost", $room)){
					array_push($rooms, $room );	
					$room=[];
				}
			}
				
			
		}
	}
	wp_send_json($rooms);
}

add_action( 'wp_ajax_mth_select_update', 'mth_select_update' );
add_action( 'wp_ajax_nopriv_mth_select_update', 'mth_select_update' );
 ?>