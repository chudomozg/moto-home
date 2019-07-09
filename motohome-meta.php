<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


Container::make( 'motohome_meta', 'Настройки МотоДома' )
   ->add_fields( array(
        Field::make( 'text', 'my_text', 'Text Field' ),
        
        Field::make("map", "crb_company_location", "Местоположение")
        ->help_text('Перетащите указатель на карту, чтобы выбрать местоположение')
	) );

   

?>