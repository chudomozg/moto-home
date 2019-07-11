$(document).ready(function(){
    //вешаем обработчик на кнопку добавления мотодома в ассоциации
    if ($('.wp-admin.post-type-mt_booking').length){
          $(".wp-admin.post-type-mt_booking").on('DOMNodeInserted', ".cf-association__col.ui-sortable", function() {//событие на динамический элемент
            if (!$('.cf-association__col.ui-sortable .cf-association__option.ui-sortable-placeholder').length){//костыль, событие срабатывало на перетаскивание-сортировку
                if ($('.cf-association__col.ui-sortable .cf-association__option input').length){//что бы не было underfinded
                    var room_id_from_input= $('.cf-association__col.ui-sortable .cf-association__option input').val();
                    mth_get_room(room_id_from_input);
                }
            }
           
        });
        //чистим селект Комнаты по нажатию на удаление Мотодома из Ассоциаций
        $(".wp-admin.post-type-mt_booking").on('click', ".cf-association__option-action.dashicons.dashicons-dismiss", function() {
            var select = 'select[name="carbon_fields_compact_input[_mth_rooms_select]"]';
            if ($(select + ' option').length){
                $(select + ' option').remove();
            }
        });
    }

    $('select[name="carbon_fields_compact_input[_mth_rooms_select]"]').on('change', function (e) {
        var optionSelected = $("option:selected", this);
        var valueSelected = this.value;
        $('input[name="carbon_fields_compact_input[_mth_rooms_select_hidden]"]').attr('value',valueSelected);
    });
})

//функция получения ID и отправка AJAX запроса к REST
function mth_get_room(room_id){
    //берем из строки данные о загруженном Мотодоме в поле ассоциации
    //помещаем в массив
    var mth_room_arr = room_id.split(':');
    //на всякий случай проверим все ли условия остались неизменными, если все хорошо - обернем в объект
    if (mth_room_arr.length==3){
        var mth_room = {type:mth_room_arr[0],post_type:mth_room_arr[1],post_id:mth_room_arr[2]}
    }else{
        alert("Ошибка: был передан неправильный объект: room_id")
    }

    //обращаемся к Мотодому через REST API
    $(function(){
        $.ajax({
            url: site_url+'/wp-admin/admin-ajax.php',
            type: 'POST',
            data_type: 'json',
            data: {
				action:'mth_select_update',
				rooms: mth_room,
			},	
            success: function(data){
                rooms_to_select (data);
            }
        });
    });
}

//функция создания оций в селекте комнаты
function rooms_to_select (data){
    var rooms = data;
    var rooms_html = '';
    for (var i=0; i < rooms.length; i++){
        rooms_html += '<option value="'+rooms[i].id+'">'+rooms[i].id+' '+rooms[i].name+' '+rooms[i].cost+'</option>';
    }

    //удаляем опции с селека, если они есть
    var select = 'select[name="carbon_fields_compact_input[_mth_rooms_select]"]';
    if ($(select + ' option').length){
        $(select + ' option').remove();
    }
    //добавляем новые
    $(select).append(rooms_html);

    //Если что-то уже было сохранено, загружаем.
    var hidden_input = 'input[name="carbon_fields_compact_input[_mth_rooms_select_hidden]"]';
    if ( $(hidden_input).val()!=""){
        var hidden_input_val = $(hidden_input).val();
        $(select).val(hidden_input_val);
    }
    //меняем value hidden input
    $(hidden_input).attr('value',rooms[0].id);
}