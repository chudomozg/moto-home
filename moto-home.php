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
    $role = "mth_realtor";
    $display_name = 'Риелтор';
    $capabilities = array (
        'read' => true,
        'edit_motohome' => true,
        'edit_motohomes'=> true,
        'publish_motohomes'=> true,
        'read_motohome'=> true,
        'read_private_motohomes'=> true,
		'delete_motohomes'=> true,
		'delete_motohome' => true,
        'edit_others_motohomes'=> true,
        'edit_mt_booking'=> true,
        'edit_mt_bookings'=> true,
        'publish_mt_bookings'=> true,
        'read_mt_booking'=> true,
        'read_private_mt_bookings'=> true,
        'delete_mt_booking'=> true,
        'delete_mt_bookings'=> true,
        'edit_others_mt_bookings'=> true,
    );
    add_role( $role, $display_name, $capabilities );	
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
        'capability_type'     => array ("motohome","motohomes"),
        'capabilities' => array(
            'edit_post' => 'edit_motohome',
            'edit_posts' => 'edit_motohomes',
            'publish_posts' => 'publish_motohomes',
            'read_post' => 'read_motohome',
            'read_private_posts' => 'read_private_motohomes',
			'delete_post' => 'delete_motohome',
			'delete_posts' => 'delete_motohomes',
            'edit_others_posts' => 'edit_others_motohomes'
        ),
		'map_meta_cap'        => false,
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
        'capability_type'     =>'mt_booking', 
        'capabilities' => array(
            'edit_post' => 'edit_mt_booking',
            'edit_posts' => 'edit_mt_bookings',
            'publish_posts' => 'publish_mt_bookings',
            'read_post' => 'read_mt_booking',
            'read_private_posts' => 'read_private_mt_bookings',
            'delete_post' => 'delete_mt_booking',
            'delete_posts' => 'delete_mt_bookings',
            'edit_others_posts' => 'edit_others_mt_bookings'
        ),
		'map_meta_cap'        => false,
		'hierarchical'        => false,
        'query_var'           => true,
        'show_in_rest' => true,
	);
    register_post_type('mt_booking',$args);
}

//Добавляем новые права к роли администратора
function mt_capabilities_add(){

    $admins = get_role( 'administrator' );

    $admins->add_cap( 'edit_mt_booking' ); 
    $admins->add_cap( 'edit_mt_bookings' ); 
    $admins->add_cap( 'publish_mt_bookings' ); 
    $admins->add_cap( 'read_mt_booking' ); 
    $admins->add_cap( 'read_private_mt_bookings' ); 
    $admins->add_cap( 'delete_mt_booking' ); 
    $admins->add_cap( 'delete_mt_bookings' ); 
    $admins->add_cap( 'edit_others_mt_bookings' ); 

    $admins->add_cap( 'edit_motohome' ); 
    $admins->add_cap( 'edit_motohomes' ); 
    $admins->add_cap( 'publish_motohomes' ); 
    $admins->add_cap( 'read_motohome' ); 
    $admins->add_cap( 'read_private_motohomes' ); 
    $admins->add_cap( 'delete_motohome' ); 
    $admins->add_cap( 'delete_motohomes' ); 
    $admins->add_cap( 'edit_others_motohomes' ); 
}
add_action( 'admin_init', 'mt_capabilities_add');


//Вывод риелторов в select на странице редактирования Мотодомов
function mth_get_realtor (){
	$users = get_users(['role'=>'mth_realtor']);
	$users_to_select =  [];
	foreach ($users as $user){
		$users_to_select = array_push_assoc($users_to_select, $user->data->ID, $user->data->user_nicename);
		// ChromePhp::log($user); 
	}
	return $users_to_select;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Добавляем Yandex Map к типу motohome

//подключаем скрипты Yamaps к админке
add_action( 'admin_enqueue_scripts', 'mthome_ad_yascript_add' );
function mthome_ad_yascript_add( $hook ){
	$screen = get_current_screen();
    if ( strpos($screen->post_type, 'motohome') === true){
        return;
	}
	//ВНИМАНИЕ ЗАМЕНИ НА СВОЙ ключ API в ссылке 
	wp_enqueue_script('yamaps_api', 'https://api-maps.yandex.ru/2.1/?apikey=01be389f-988a-42c3-8fb5-5a5fcd4179d2&lang=ru_RU');
	wp_enqueue_script('yamap.js', plugins_url('moto-home/js/yamap.js'));
    wp_enqueue_script('mt_rooms.js', plugins_url('moto-home/js/mt_rooms.js'));
	wp_enqueue_script('mt_home.js', plugins_url('moto-home/js/mt_home.js'));
	wp_localize_script( 'mt_home.js', 'site_url', get_site_url());
    wp_localize_script( 'mt_rooms.js', 'site_url', get_site_url());
    wp_localize_script( 'mt_rooms.js', 'dir_url', dirName(__FILE__));
}


//скрипты на страницу брони
add_action( 'admin_enqueue_scripts', 'mthome_ad_rooms_add' );
function mthome_ad_rooms_add( $hook ){
	$screen = get_current_screen();
    if ( strpos($screen->post_type, 'mt_booking') === true){
        return;
	}
    wp_enqueue_script('mt_rooms.js', plugins_url('moto-home/js/mt_rooms.js'));
	wp_enqueue_script('mt_home.js', plugins_url('moto-home/js/mt_home.js'));
	//wp_enqueue_script('flatpkr-loc-ru.js', 'https://unpkg.com/flatpickr@3.1.2/dist/l10n/ru.js', array('jquery', 'moment.min.js', 'underscore-min.js'));
	wp_localize_script( 'mt_home.js', 'site_url', get_site_url());
    wp_localize_script( 'mt_rooms.js', 'site_url', get_site_url());
    wp_localize_script( 'mt_rooms.js', 'dir_url', dirName(__FILE__));
    wp_localize_script( 'mt_home_front.js', 'user_id',get_current_user_id() );
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


//Создание брони пользователем сайта 
function mth_user_create_reserv(){
	$user_id = get_current_user_id();
	//если залогинен
	if ($user_id > 0){
		$room_id =  $_POST['room_id'];
		$send_user_id = $_POST['user_id'];
		$motohome_id = substr($room_id, 0, strpos($room_id, "-"));
		$reserv_date = str_replace('-', 'to', $_POST['date']) ;
		$response = [$room_id,$reserv_date];
		global $wpdb;

		// Создаем массив данных новой записи
		$post_data = array(
			'post_title'    => wp_strip_all_tags($room_id."-???"),
			'post_content'  =>	'',
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'post_type' => 'mt_booking',
		);

		// Вставляем запись в базу данных
		$post_id = wp_insert_post( $post_data );

		//Добавляем значение meta полей в БД
		// $table =$wpdb->prefix.'postmeta';
		// //Дата бронирования
		// $data=array('post_id'=>$post_id, 'meta_key'=>'_mth_bookdate', 'meta_value'=>$reserv_date);
		// $format = array('%d', '%s', '%s');
		// $wpdb->insert( $table, $data, $format );

		//Дата бронирования
		carbon_set_post_meta( $post_id, 'mth_bookdate', $reserv_date );
		//id комнаты
		carbon_set_post_meta( $post_id, 'mth_rooms_select_hidden', $room_id );
		//Статус брони
		carbon_set_post_meta( $post_id, 'mth_stat_select', 'received' );
		//Пользователь
		carbon_set_post_meta( $post_id, 'mth_user', $user_id );
		//Какая комната assoc
		$mth_assoc = array( 
			0 => array(
				'id' => $motohome_id,
				'type' => 'post',
				'subtype' => 'motohome',
				'value' => 'post:motohome:'.$motohome_id,
			));
		carbon_set_post_meta( $post_id, 'mth_assoc', $mth_assoc );

		//обновляем title post mt_booking
		$booking_post = array();
		$booking_post['ID']=$post_id;
		$booking_post["post_title"]=$room_id."-".$post_id;
		wp_update_post( wp_slash($booking_post) );

		//отправляем почту
		mth_send_mail ($user_id, $post_id, $motohome_id, $room_id);

		//Отправляем ответ
		wp_send_json(array (true, $post_id, $motohome_id, $user_id));
	}else{
		wp_send_json(array (false));
	}
	
}

add_action( 'wp_ajax_mth_user_create_reserv', 'mth_user_create_reserv' );
add_action( 'wp_ajax_nopriv_mth_user_create_reserv', 'mth_user_create_reserv' );

//ф-ция отправки писем
function mth_send_mail ($user_id, $booking_id, $motohome_id, $room_id, $update = false, $status = 'received'){
	$realtor_id = carbon_get_post_meta( $motohome_id, 'mth_realtor');
	$realtor = get_user_by('ID', $realtor_id);
	$user = get_user_by('ID', $user_id);
	$realtor_email = $realtor->data->user_email;
	$user_email = $user->data->user_email;
	$booking_url = '<a href="'.get_edit_post_link($booking_id).'">Бронь #'.$booking_id.'</a>';
	$date = mth_get_russian_booking_date(carbon_get_post_meta( $booking_id, 'mth_bookdate'));
	$user_meta = get_user_meta($user_id);
	$status = mth_get_russian_booking_status($status);

	if ($update == true){//При обновлении статуса или изменении любого параметра
		//Юзеру
		$subject = 'Ваша бронь обновлена: moto-tours.me';
		$message = 'Ваша бронь на сайте moto-tours.me была обновлена.<br/>
					<b>Детали</b><br/>
					№:<b>'.$booking_id.'</b><br/>
					Мотодом: <b>'.$motohome_id.'</b><br/>
					Комната: <b>'.$room_id.'</b><br/>
					Дата брони: <b>'.$date.'</b><br/>
					Статус брони: <b>'.$status.'</b><br/> 
					';
		$headers = 'content-type: text/html';
		wp_mail( $user_email, $subject, $message, $headers);

	}else{//При добавлении новой брони

		//Отправляем письмо риелтору
		$subject = 'Новая бронь: moto-tours.me';
		$message = 'Новая бронь на сайте moto-tours.me <br/>
					№:<b>'.$booking_id.'</b><br/>
					Ссылка: <b>'.$booking_url.'</b><br/>
					Пользователь: <b>'.$user->data->user_nicename.": ".$user_meta['first_name'][0]." ".$user_meta['last_name'][0].'</b><br/>
					Телефон: <b>'.$user_meta['billing_phone'][0].'</b><br/>
					Email: <b>'.$user_meta['billing_email'][0].'</b><br/>
					Мотодом: <b>'.$motohome_id.'</b><br/>
					Комната: <b>'.$room_id.'</b><br/>
					Дата брони: <b>'.$date.'</b><br/>
					Статус брони: <b>'.$status.'</b><br/> 
					';
		$headers = 'content-type: text/html';
		wp_mail( $realtor_email, $subject, $message, $headers);
		
		//Юзеру
		$subject = 'Ваша новая бронь: moto-tours.me';
		$message = 'Ваша бронь на сайте moto-tours.me <br/>
					Была принята, ожидайте звонка от риелтора, для подтверждения брони<br/>
					Детали<br/>
					№:<b>'.$booking_id.'</b><br/>
					Мотодом: <b>'.$motohome_id.'</b><br/>
					Комната: <b>'.$room_id.'</b><br/>
					Дата брони: <b>'.$date.'</b><br/>
					Статус брони: <b>'.$status.'</b><br/> 
					';
		$headers = 'content-type: text/html';
		wp_mail( $user_email, $subject, $message, $headers);
	}
}

function mth_date_picker_get_booking_time(){
	if (isset($_POST['room_id'])){
		$room_ids = $_POST['room_id'];
		global $wpdb;
		$send_data=[];
		foreach ($room_ids as $room_id){
			$booking_times =[];
			$stack =[];
			//Старая версия запроса без статуса брони 
			// $sql = 'SELECT meta_value FROM wp_postmeta WHERE meta_key= "_mth_bookdate" AND post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_key = "_mth_rooms_select_hidden" AND meta_value = "'.$room_id.'")';
			$sql = 'SELECT meta_value FROM wp_postmeta WHERE meta_key= "_mth_bookdate" AND post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_key = "_mth_rooms_select_hidden" AND meta_value = "'.$room_id.'") AND post_id NOT IN (SELECT post_id FROM wp_postmeta WHERE meta_key= "_mth_stat_select" AND meta_value="received") AND post_id NOT IN (SELECT post_id FROM wp_postmeta WHERE meta_key= "_mth_stat_select" AND meta_value="canceled")';
			//ChromePhp::log($sql); 
			$sql_request =  $wpdb->get_results($sql);
			if ($sql_request){
				foreach ($sql_request as $booking_time){
					array_push($booking_times, $booking_time);
					//ChromePhp::log($booking_time);
				}
			}
			// ChromePhp::log('тут');
			//ChromePhp::log($booking_times);
			$stack = array_push_assoc($stack, 'room_id', $room_id);
			$stack = array_push_assoc($stack, 'booking_time', $booking_times);
			array_push($send_data, $stack);
		}
		//ChromePhp::log($send_data);
		wp_send_json($send_data);
	}

}

add_action( 'wp_ajax_mth_date_picker_get_booking_time', 'mth_date_picker_get_booking_time' );
add_action( 'wp_ajax_nopriv_mth_date_picker_get_booking_time', 'mth_date_picker_get_booking_time' );

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
		wp_enqueue_script('yamap_front.js', plugins_url('moto-home/js/yamap_front.js'), array('jquery', 'mt_home_front.js'));
		wp_enqueue_script('mt_rooms.js', plugins_url('moto-home/js/mt_rooms.js'));
		wp_enqueue_script('mt_home_front.js', plugins_url('moto-home/js/mt_home_front.js'), array('jquery', 'mt_rooms.js'));
		wp_localize_script( 'mt_home_front.js', 'site_url', get_site_url());
		wp_localize_script( 'mt_home_front.js', 'plugin_url', plugins_url('moto-home/'));
		wp_enqueue_script('moment.min.js', plugins_url('moto-home/js/moment.min.js'), array('jquery'));
		wp_enqueue_script('moment-locale', plugins_url('moto-home/js/ru.js'), array('jquery', 'moment.min.js'));
		// wp_enqueue_script('underscore-min.js', plugins_url('moto-home/js/underscore-min.js'), array('jquery'));
		wp_enqueue_script('owl.carousel.min.js', plugins_url('moto-home/js/owl.carousel.min.js'), array('jquery'));
		wp_enqueue_script('lightpick.js', plugins_url('moto-home/js/lightpick.js'), array('jquery', 'moment.min.js'));
		// wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr');
		wp_enqueue_script('fotorama.js', 'https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js');
		wp_enqueue_style('fotorama.css', 'https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css');
		// wp_enqueue_style('flatpickr.css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
		wp_enqueue_style('main.css', plugins_url('moto-home/css/main.css'));
		wp_enqueue_style('owl.carousel.css', plugins_url('moto-home/assets/owl.carousel.css'));
		wp_enqueue_style('lightpick.css', plugins_url('moto-home/css/lightpick.css'));
		wp_enqueue_style('owl.theme.default.css', plugins_url('moto-home/assets/owl.theme.default.css'));
		//wp_enqueue_media();
	}
	

}
//Страница списка мотодомов///////////////////////////////////////////////////////////////////////////////////////////////
//Функция, которая выводит контент списка мотодомов (шорткод [mth_motohome])
function mtc_page_motohome(){
	//echo "Здрасти";
}
add_shortcode( 'mth_motohome' ,'mtc_page_motohome' );


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Добавляем шаблоны
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

	//Страница всех мотодомов
    //A Standard Page
    } elseif (get_query_var('name') == 'motohome') {
        $templatefilename = 'page-motohome.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
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


add_action('save_post_motohome', 'mth_set_city_to_taxonomy');

function mth_set_city_to_taxonomy(){	
	//echo "<pre>".print_r($_POST)."</pre>";
	$term_id = $_POST['carbon_fields_compact_input']['_motohome_city'];
	if ($term_id!=1){
		$post_ID = $_POST['post_ID'];
		$tags = $term_id + 0;
		$taxonomy='wp_cn_city';
		$append=false;
		wp_set_post_terms( $post_ID, $tags, $taxonomy, $append );
	}
}

//Создаем id брони в title
function mth_id_to_title($post_id ){
	$post_type = 'mt_booking';
	if (get_post_type($post_id) == $post_type){
		if (isset($_POST['post_ID'])&&isset($_POST['carbon_fields_compact_input']['_mth_rooms_select_hidden'])){
			global $wpdb;
			$user_id = $_POST['carbon_fields_compact_input']['_mth_user'];
			$motohome = explode ($_POST['carbon_fields_compact_input']['_mth_assoc'][0]);
			$motohome_id = $motohome[2];
			$room_id = $_POST['carbon_fields_compact_input']['_mth_rooms_select_hidden'];
			$title =  $_POST['carbon_fields_compact_input']['_mth_rooms_select_hidden']."-".$_POST['post_ID'];
			$status = $_POST['carbon_fields_compact_input']['_mth_stat_select'];
			mth_send_mail ($user_id, $post_id, $motohome_id, $room_id, true, $status);
			$wpdb->update( $wpdb->posts, array( 'post_title' =>  $title ), array( 'ID' => $post_id ) );	
		}
		//Отправляем письмо пользователю
		// mth_send_mail ($user_id, $booking_id, $motohome_id, $room_id, true);
		// $motohome_id = explode ('-', $_POST['carbon_fields_compact_input']['_mth_rooms_select_hidden']);
		// $motohome_id = $motohome_id[0];
		
	}
}
	
add_action('save_post', 'mth_id_to_title');





add_action( 'wp_ajax_mth_get_term_to_hid_city_inp', 'mth_get_term_to_hid_city_inp' );
add_action( 'wp_ajax_nopriv_mth_get_term_to_hid_city_inp', 'mth_get_term_to_hid_city_inp' );

function mth_get_term_to_hid_city_inp(){
	if ($_POST['post_id']){
		$post_id=$_POST['post_id'];
	}
	$taxonomy='wp_cn_city';
	$term= wp_get_post_terms( $post_id, $taxonomy,['ids'] );
	wp_send_json( $term);
}

## Удаление базовых элементов (ссылок) из админ тулбара
add_action('add_admin_bar_menus', function(){
	/* доступно для удаления:

	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );  // Внутренние ссылки меню профиля
	remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );      // поиск
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );  // Полностью меню профиля

	// Связанное с сайтом
	remove_action( 'admin_bar_menu', 'wp_admin_bar_sidebar_toggle', 0 );   // 
	remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );         // WordPress ссылки (WordPress лого)
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );   // мои сайты
	remove_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );       // сайты
	remove_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40 );  // настроить тему
	remove_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 50 );    // обновления

	// Content related.
	if ( ! is_network_admin() && ! is_user_admin() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );    // комментарии
		remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 ); // добавить запись, страницу, медиафайл и т.д.
	}
	remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 ); // редактировать

	remove_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 ); // вся дополнительная группа (поиск и аккаунт) расположена справа в меню
	*/

	// удаляем
	// remove_action( 'admin_bar_menu', 'wp_admin_bar_customize_menu', 40); // Настроить тему
	remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );    // поиск
    remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );      // WordPress ссылки (WordPress лого)
    remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );   // мои сайты
    remove_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );       // сайты
});

// Добавляет ссылку в админ бар
add_action( 'admin_bar_menu', 'mth_admin_bar_menu', 30 );
function mth_admin_bar_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'mth_admin_toolbar_menu',
		'title' => 'Мотодома и брони',
		// 'href'  => '#',
	) );
    
    $site_url = get_site_url();
	$wp_admin_bar->add_menu( array(
		'parent' => 'mth_admin_toolbar_menu', // параметр id из первой ссылки
		'id'     => 'mth_admin_toolbar_motohome', // свой id, чтобы можно было добавить дочерние ссылки
		'title'  => 'Мотодома',
		'href'   => $site_url.'/wp-admin/edit.php?post_type=motohome',
    ) );
    $wp_admin_bar->add_menu( array(
		'parent' => 'mth_admin_toolbar_menu', // параметр id из первой ссылки
		'id'     => 'mth_admin_toolbar_booking', // свой id, чтобы можно было добавить дочерние ссылки
		'title'  => 'Брони',
		'href'   => $site_url.'/wp-admin/edit.php?post_type=mt_booking',
	) );
}


//Добавляем пункты в менюшку woocommerce 
function mth_iconic_account_menu_items( $items ) {
	$items['booking'] = __( 'Мои бронирования', 'iconic' );
	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'mth_iconic_account_menu_items', 10, 1 );

/**
 * Add endpoint
 */
function mth_iconic_add_my_account_endpoint() {
	add_rewrite_endpoint( 'booking', EP_PAGES );
}
add_action( 'init', 'mth_iconic_add_my_account_endpoint' );

/**
* booking content
*/
function mth_iconic_booking_endpoint_content() {
	$title = '<h3>История бронирований Мотодомов</h3>';
	$user_id = get_current_user_id();
	echo $title;
	echo '<table><tr><th>№ Брони</th><th>Дата</th><th>Мотодом</th><th>Комната</th><th>Статус</th></tr>';
	$query = new WP_Query([
		'author' => $user_id,
		'post_type'      => 'mt_booking',
		// 'posts_per_page' => '10', //пагинацию потом надо дописать posts_nav_link(); после цикла не работает
	]);
	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id = get_the_ID();
		$motohome_assoc_array = carbon_get_post_meta( $post_id, 'mth_assoc');
		$motohome_id = $motohome_assoc_array[0]['id'];
		echo '<tr>';
		echo '<td>'.get_the_title().'</td>'; // выведем заголовок поста
		echo '<td>'.mth_get_russian_booking_date(carbon_get_post_meta( $post_id, 'mth_bookdate')).'</td>'; //дата
		echo '<td><a href="'.get_permalink($motohome_id).'" target="_blank">№ '.$motohome_id.'</a></td>'; //Мотодом
		echo '<td>№ '.carbon_get_post_meta( $post_id, 'mth_rooms_select_hidden').'</td>'; //комната
		echo '<td>'.mth_get_russian_booking_status(carbon_get_post_meta( $post_id, 'mth_stat_select')).'</td>'; //статус

		echo '<tr/>';
	}
	echo '</table>';
}
add_action( 'woocommerce_account_booking_endpoint', 'mth_iconic_booking_endpoint_content' );


function mth_get_russian_booking_status ($status){
	switch ($status){
		case 'received':
			return 'Получена';
			break;
		case 'confirmed':
			return 'Подтверждена';
			break;
		case 'paid':
			return 'Оплачена';
			break;
		case 'canceled':
			return 'Отменена';
			break;
	}
}

function mth_get_russian_booking_date ($booking_date){
	$delimiter = stripos ($booking_date, 'to');
	if ($delimiter!=false){
		$filtred_date = str_replace ('to', 'по', $booking_date);
		$filtred_date = 'c '.$filtred_date;
		return $filtred_date;
	};
	return $booking_date;
}


function mth_get_users_to_select(){
	$user_arr = get_users();
	$options = [];
	foreach ($user_arr as $user){
		$usermeta =  get_user_meta ($user->data->ID);
		$options = array_push_assoc($options, $user->data->ID, $user->data->user_nicename.': '.$usermeta['first_name'][0].' '.$usermeta['last_name'][0]);
	}
	return $options;
}

function mth_get_user_meta(){
	$user_meta = get_user_meta($_POST['user_id']);
	$meta_output = [
		'first_name' => $user_meta['first_name'][0],
		'last_name' => $user_meta['last_name'][0],
		'phone' => $user_meta['billing_phone'][0],
		'email' => $user_meta['billing_email'][0],
	];
	wp_send_json($meta_output);
}
add_action( 'wp_ajax_mth_get_user_meta', 'mth_get_user_meta' );
add_action( 'wp_ajax_nopriv_mth_get_user_meta', 'mth_get_user_meta' );

//добавим колонки для броней

function mth_booking_add_column( $post_columns ){
	// Изменяем...	
	foreach ($post_columns as $key=>$v){
		$out .=$key.', ';
	}
	$post_columns['user']='Пользователь';
	$post_columns['phone']='Телефон';
	$post_columns['email']='email';

	return $post_columns;
}
add_filter( 'manage_mt_booking_posts_columns', 'mth_booking_add_column' );

function mth_booking_add_column_custom($column, $id){
	$user_meta = get_user_meta(carbon_get_post_meta($id, 'mth_user'));
	if($column === 'user'){
		echo $user_meta['nickname'][0].": ".$user_meta['first_name'][0]." ".$user_meta['last_name'][0];
	}elseif($column === 'phone'){
		echo $user_meta['billing_phone'][0];
	}elseif($column === 'email'){
		echo $user_meta['billing_email'][0];
	}
}
add_action('manage_mt_booking_posts_custom_column', 'mth_booking_add_column_custom', 10, 2 );

 ?>