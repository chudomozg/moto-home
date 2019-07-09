<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


Container::make( 'post_meta', 'Настройки брони МотоДома' )
->show_on_post_type('mt_booking')
->set_context('normal')
->set_priority('low')
->add_fields( array(

	Field::make( 'association', 'mth_assoc', __( 'Association' ) )
    ->set_types( array(
        array(
            'type'      => 'post',
            'post_type' => 'motohome',
        )
    ) )
	 ->set_min(1)
	 ->set_max(1)
	 ->set_duplicates_allowed(false)

) );


?>