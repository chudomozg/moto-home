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
        ->set_type( array( 'image' ) ),

        Field::make( 'select', 'motohome_region', 'Регион' )
        ->set_options(mth_reion_select_fill())
        ->set_width( 50 ),

        Field::make( 'select', 'motohome_city', 'Город' )
        ->set_options( array(
                '0' => 'Сначала выберите Регион',
                '1' => 'Работает',
        ) )
        ->set_width( 50 ),

	//Местоположение, карта, координаты
	Field::make( 'text', 'motohome_loc', "Местоположение" )
	->set_attribute( 'readOnly', 'true' )
	->set_classes( 'mthome_ad_coord_text' ),
	Field::make("html", "crb_information_text", 'Предупреждение')
		 ->set_html(
			 '<div id="map" style="width: 100%; height: 400px"></div>'
		 ),

	//Комнаты
	Field::make( 'complex', 'rooms', 'Комнаты МотоДома' )
		->add_fields( array(
			Field::make( 'text', 'room_name', 'Название' )
					->set_width( 33 ),
			Field::make( 'text', 'room_cost', 'Цена' )
					->set_width( 10 ),
			
		))
		->set_visible_in_rest_api( true )

) );
?>