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

//ДЕБАГ - НЕ ЗАБУДЬ ЗАКОМЕНТИТЬ///////////////////////////////////////////////////////////////////////////////
if( WP_DEBUG && WP_DEBUG_DISPLAY && (defined('DOING_AJAX') && DOING_AJAX) ){
    @ ini_set( 'display_errors', 1 );
}

// require_once( dirName(__FILE__). '/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
include( dirName(__FILE__). '/ChromePhp.php' );//расширение для вывода php переменных в консоль Chrome
//ДЕБАГ - НЕ ЗАБУДЬ ЗАКОМЕНТИТЬ///////////////////////////////////////////////////////////////////////////////

function mth_install_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p>Настройки обновлены!</p>
	</div>
	<?php
}

global $motohome_db_version;
$motohome_db_version = "2.0";
// require_once(ABSPATH . 'wp-content/plugins/moto-home/cmb2-functions.php');//ф-ции для cmb2 полей
//ф-ция установки плагина, тут добавим таблицы в БД, тип POST, meta-поля и др. настройки
 function motohome_install(){	
	add_action( 'admin_notices', 'mth_install_notice' );	
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
function mthome_ad_yascript_add( $hook ){
	$screen = get_current_screen();
    if ( strpos($screen->post_type, 'motohome') === false){
        return;
	}
	//ВНИМАНИЕ ЗАМЕНИ НА СВОЙ ключ API в ссылке 
	wp_enqueue_script('yamaps_api', 'https://api-maps.yandex.ru/2.1/?apikey=01be389f-988a-42c3-8fb5-5a5fcd4179d2&lang=ru_RU');
	wp_enqueue_script('yamap.js', plugins_url('moto-home/js/yamap.js'));
    wp_enqueue_script('mt_rooms.js', plugins_url('moto-home/js/mt_rooms.js'));
    wp_enqueue_script('mt_home.js', plugins_url('moto-home/js/mt_home.js'));
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
add_action( 'wp_ajax_nopriv_mth_select_update', 'mth_select_update' ); //проверить будет ли работать без нее с правами на опр польз

//функция для добавления опций в селект регион
function mth_reion_select_fill (){
	$terms = get_terms( array(
		'taxonomy'      =>  'wp_cn_city' , // название таксономии с WP 4.5
		'orderby'       => 'id', 
		'order'         => 'ASC',
		'hide_empty'    => false, 
		'object_ids'    => null,
		'include'       => array(),
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'number'        => '', 
		'fields'        => 'id=>name', 
		'count'         => false,
		'slug'          => '', 
		'parent'         => '0',
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'get'           => '', // ставим all чтобы получить все термины
		'name__like'    => '',
		'pad_counts'    => false, 
		'offset'        => '', 
		'search'        => '', 
		'cache_domain'  => 'core',
		'name'          => '',    // str/arr поле name для получения термина по нему. C 4.2.
		'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
		'update_term_meta_cache' => true, // подгружать метаданные в кэш
		'meta_query'    => '',
	) );
	
	return $terms;
}

//добавляем Яндекс Карты АПИ на фронтэнд
add_action( 'wp_enqueue_scripts', 'mthome_ad_yascript_add_front' );
function mthome_ad_yascript_add_front( $hook ){
	// $screen = get_current_screen();
    // if ( strpos($screen->post_type, 'motohome') === false){
    //     return;
	// }
	if( is_page() || is_single() ){
		//ВНИМАНИЕ ЗАМЕНИ НА СВОЙ ключ API в ссылке 
		wp_enqueue_script('yamaps_api_front', 'https://api-maps.yandex.ru/2.1/?apikey=01be389f-988a-42c3-8fb5-5a5fcd4179d2&lang=ru_RU');
		wp_enqueue_script('yamap_front.js', plugins_url('moto-home/js/yamap_front.js'));
	}
	

}
//Страница списка мотодомов///////////////////////////////////////////////////////////////////////////////////////////////
//Функция, которая выводит контент списка мотодомов (шорткод [mth_motohome])
function mtc_page_motohome(){

	echo "Здрасти";
	
}
add_shortcode( 'mth_motohome' ,'mtc_page_motohome' );


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Страница одного мотодома

//Template Replacement
add_action("template_redirect", 'mth_template_redirect');

function mth_template_redirect() {
    global $wp;
    $plugindir = dirname( __FILE__ );

    //A Custom Post Type
	if (get_query_var('post_type') == 'motohome') {
        $templatefilename = 'single-motohome.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);

    //A Custom Taxonomy - еще пригодится
    // } elseif ($wp->query_vars["taxonomy"] == 'product_categories') {
    //     $templatefilename = 'taxonomy-product_categories.php';
    //     if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
    //         $return_template = TEMPLATEPATH . '/' . $templatefilename;
    //     } else {
    //         $return_template = $plugindir . '/themefiles/' . $templatefilename;
    //     }
    //     do_theme_redirect($return_template);

    //A Standard Page
    // } elseif ($wp->query_vars["pagename"] == 'somepagename') {
    //     $templatefilename = 'page-somepagename.php';
    //     if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
    //         $return_template = TEMPLATEPATH . '/' . $templatefilename;
    //     } else {
    //         $return_template = $plugindir . '/themefiles/' . $templatefilename;
    //     }
    //     do_theme_redirect($return_template);
	// }
	}
}

function do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}
 ?>