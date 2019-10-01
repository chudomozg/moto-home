<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;


Container::make( 'post_meta', 'Настройки брони МотоДома' )
    ->show_on_post_type('mt_booking')
    ->set_context('normal')
    ->set_priority('low')
    ->add_fields( array(

    //Ассоциация с МотоДомом
	Field::make( 'association', 'mth_assoc', __( 'Выберите Мотодом' ) )
        ->set_types( array(
            array(
                'type'      => 'post',
                'post_type' => 'motohome',
            )
        ) )
        ->set_min(1)
        ->set_max(1)
        ->set_duplicates_allowed(false)
        ->set_required( true ),
    
    //Комната
    Field::make( 'select', 'mth_rooms_select', 'Выберите комнату' )
        ->add_options( array(
            '0' => 'Загрузка...'
        ) )
        ->set_required( true )
        ->set_visible_in_rest_api( true )
        ->set_width( 50 ),

    //Дата брони старая
    // Field::make( 'date', 'mth_bookdate_start','Бронь с' )
    //      ->set_storage_format( 'd.m.Y' )
    //      ->set_input_format( 'd.m.Y', 'd.m.Y' )
    //      ->set_width( 33 )
    //      ->set_required( true )
    //      ->set_picker_options( array(
    //         'mode' => 'range',
    //         //'disable' => mth_get_disable_dates(),
    //     ) ),

    // Field::make( 'date', 'mth_bookdate_end', "До" )
    //      ->set_storage_format( 'd.m.Y' )
    //      ->set_input_format( 'd.m.Y', 'd.m.Y' )
    //      ->set_width( 33 )
    //      ->set_required( true ),

    //Дата брони 
    Field::make( 'text', 'mth_bookdate', 'Дата бронирвоания' )
    ->set_classes( 'mth_booking_date_input' )
    //->set_required( true )
    ->set_visible_in_rest_api( true )
    ->set_width( 50 )
    ->set_default_value( "" ),
    
    //скрытое поле для id комнаты (костыль из-за динамических опций)
    Field::make( 'hidden', 'mth_rooms_select_hidden','')
    ->set_visible_in_rest_api( true ),
         //->set_attribute( 'readOnly', 'true' ),

     //Пользоветель
     Field::make( 'select', 'mth_user', 'Пользователь' )
     ->add_options( mth_get_users_to_select() )
     ->set_required( true )
     ->set_visible_in_rest_api( true )
     ->set_width( 100 ),

     //Имя
     Field::make( 'text', 'mth_user_firstname', 'Имя' )
     ->set_width( 25 )
    //  ->set_required( true )
     ->set_visible_in_rest_api( true ),

     //Фамилия
     Field::make( 'text', 'mth_user_lastname', 'Фамилия' )
     ->set_width( 25 )
    //  ->set_required( true )
     ->set_visible_in_rest_api( true ),

     //Телефон
     Field::make( 'text', 'mth_user_phone', 'Телефон' )
     ->set_width( 25 )
    //  ->set_required( true )
     ->set_visible_in_rest_api( true ),

     //Почта
     Field::make( 'text', 'mth_user_email', 'Почта' )
     ->set_width( 25 )
    //  ->set_required( true )
     ->set_visible_in_rest_api( true ),

) );

Container::make( 'post_meta', 'Статус заявки' )
    ->show_on_post_type('mt_booking')
    ->set_context('side')
    ->set_priority('low')
    ->add_fields( array(
        Field::make( 'select', 'mth_stat_select', 'Выберите статус заявки' )
        ->add_options( array(
            'received' => 'Получена',
            'confirmed' => 'Подтверждена',
            'paid' => 'Оплачена',
            'canceled' => 'Отменена',
        ) )
        ->set_required( true )
        ->set_visible_in_rest_api( true ),
       // ->set_width( 33 ),
    ) );
?>