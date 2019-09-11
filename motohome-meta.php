<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'post_meta', 'Настройки МотоДома' )
->show_on_post_type('motohome')
->set_context('normal')
->set_priority('low')
->add_fields( array(

	//Галерея - фотографии МотоДома
	Field::make( 'media_gallery', 'motohome_gallery', __( 'Media Gallery' ) )
        ->set_type( array( 'image' ) )
        ->set_visible_in_rest_api( true )
,

        //регион и город
        Field::make( 'select', 'motohome_region', 'Регион' )
        ->set_options(mth_reion_select_fill())
        ->set_width( 50 )
        ->set_classes( 'back_motohome_region' ),

        Field::make( 'select', 'motohome_city', 'Город' )
        ->set_options( array(
                '2370' => 'Москва',
                '1' => 'Работает',
        ) )
        ->set_visible_in_rest_api( true )
        ->set_width( 50 )
        ->set_classes( 'back_motohome_city' ),

        Field::make( 'hidden', 'mth_hidden_city', '' ),

	//Местоположение, карта, координаты
	Field::make( 'text', 'motohome_loc', "Местоположение" )
	->set_attribute( 'readOnly', 'true' )
        ->set_classes( 'mthome_ad_coord_text' )
        ->set_visible_in_rest_api( true ),
	Field::make("html", "crb_information_text", 'Предупреждение')
		 ->set_html(
			 '<div id="map" style="width: 100%; height: 400px"></div>'
		 ),

	//Комнаты
	Field::make( 'complex', 'rooms', 'Комнаты МотоДома' )
		->add_fields( array(
                        Field::make( 'hidden', 'room_id', '' ),
			Field::make( 'text', 'room_name', 'Название' )
					->set_width( 80 ),
			Field::make( 'text', 'room_cost', 'Цена' )
                                        ->set_width( 20 ),
                        Field::make( 'textarea', 'room_desc', 'Описание' )
                                        ->set_rows( 5 )
                                        ->set_attribute( 'maxLength', 485 )
			
		))
                ->set_visible_in_rest_api( true )

) );


Container::make( 'post_meta', 'Привязка к риелтору' )
    ->show_on_post_type('motohome')
    ->set_context('side')
    ->set_priority('core')
    ->add_fields( array(
        Field::make( 'select', 'mth_realtor', 'Выберите ответственного' )
        ->set_options(mth_get_realtor())
    ) );

?>