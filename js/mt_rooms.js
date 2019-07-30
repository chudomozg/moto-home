(function($) {
    $(document).ready(function() {

        $(".wp-admin.post-type-mt_booking.post-new-php .mth_booking_date_input .cf-field__body input").val("Сначала выберите Мотодом и комнату");
        $(".wp-admin.post-type-mt_booking.post-new-php .mth_booking_date_input .cf-field__body input").attr("readonly", "readonly");

        //вешаем обработчик на кнопку добавления мотодома в ассоциации
        if ($('.wp-admin.post-type-mt_booking').length) {
            $('input[name="post_title"]').prop('readonly', true);
            $(".wp-admin.post-type-mt_booking").on('DOMNodeInserted', ".cf-association__col.ui-sortable", function() { //событие на динамический элемент
                if (!$('.cf-association__col.ui-sortable .cf-association__option.ui-sortable-placeholder').length) { //костыль, событие срабатывало на перетаскивание-сортировку
                    if ($('.cf-association__col.ui-sortable .cf-association__option input').length) { //что бы не было underfinded


                        var room_id_from_input = $('.cf-association__col.ui-sortable .cf-association__option input').val();
                        mth_get_room(room_id_from_input);
                    }
                }

            });
            //чистим селект Комнаты по нажатию на удаление Мотодома из Ассоциаций
            $(".wp-admin.post-type-mt_booking").on('click', ".cf-association__option-action.dashicons.dashicons-dismiss", function() {
                var select = 'select[name="carbon_fields_compact_input[_mth_rooms_select]"]';
                if ($(select + ' option').length) {
                    $(select + ' option').remove();
                }
            });
        }

        $('select[name="carbon_fields_compact_input[_mth_rooms_select]"]').on('change', function(e) {
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            //DatePicker
            var room_id = [];
            room_id.push(valueSelected);
            var selector = '.wp-admin.post-type-mt_booking .mth_booking_date_input .cf-field__body input';
            mth_get_date_picker(room_id, false, selector);
            $('input[name="carbon_fields_compact_input[_mth_rooms_select_hidden]"]').attr('value', valueSelected);
        });
    })
})(jQuery);

//функция получения ID и отправка AJAX запроса к REST
function mth_get_room(room_id) {
    (function($) {
        //берем из строки данные о загруженном Мотодоме в поле ассоциации
        //помещаем в массив
        var mth_room_arr = room_id.split(':');
        //на всякий случай проверим все ли условия остались неизменными, если все хорошо - обернем в объект
        if (mth_room_arr.length == 3) {
            var mth_room = { type: mth_room_arr[0], post_type: mth_room_arr[1], post_id: mth_room_arr[2] }
        } else {
            alert("Ошибка: был передан неправильный объект: room_id")
        }

        //обращаемся к Мотодому
        $(function() {
            $.ajax({
                url: site_url + '/wp-admin/admin-ajax.php',
                type: 'POST',
                data_type: 'json',
                data: {
                    action: 'mth_select_update',
                    rooms: mth_room,
                },
                success: function(data) {
                    rooms_to_select(data);
                }
            });
        });
    })(jQuery);
}

//функция создания оций в селекте комнаты
function rooms_to_select(data) {
    (function($) {
        var rooms = data;
        var rooms_html = '';
        for (var i = 0; i < rooms.length; i++) {
            rooms_html += '<option value="' + rooms[i].id + '">' + rooms[i].id + ' ' + rooms[i].name + ' ' + rooms[i].cost + '</option>';
        }

        //удаляем опции с селека, если они есть
        var select = 'select[name="carbon_fields_compact_input[_mth_rooms_select]"]';
        if ($(select + ' option').length) {
            $(select + ' option').remove();
        }
        //добавляем новые
        $(select).append(rooms_html);

        //Если что-то уже было сохранено, загружаем.
        var hidden_input = 'input[name="carbon_fields_compact_input[_mth_rooms_select_hidden]"]';
        if ($(hidden_input).val() != "") {
            var hidden_input_val = $(hidden_input).val();
            $(select).val(hidden_input_val);
        }
        //меняем value hidden input
        $(hidden_input).attr('value', rooms[0].id);
        //DatePicker
        var room_id = [];
        room_id.push(rooms[0].id);
        var selector = '.wp-admin.post-type-mt_booking .mth_booking_date_input .cf-field__body input';
        mth_get_date_picker(room_id, false, selector);
    })(jQuery);

}

function mth_get_date_picker(room_id, is_front, selector) {
    //console.log(room_id);
    // if (is_front == false) {
    //     room_id = room_ids[0];
    // }
    (function($) {
        $.ajax({
            url: site_url + '/wp-admin/admin-ajax.php',
            type: 'POST',
            data_type: 'json',
            data: {
                action: 'mth_date_picker_get_booking_time',
                room_id: room_id,
            },
            success: function(data) {
                //console.log('data');
                //console.log(data);
                var disable_date_arr = [];
                if (Array.isArray(data) && (data.length > 0)) {
                    for (var z = 0; z < data.length; z++) {
                        if (z == 0) {
                            data[z].booking_time.forEach(function(date_disable) {
                                    if (date_disable.meta_value.includes('to')) {
                                        var date_range_arr = date_disable.meta_value.split(' to ');
                                        var date_range = {};
                                        date_range.from = date_range_arr[0];
                                        date_range.to = date_range_arr[1];
                                        disable_date_arr.push(date_range);
                                    } else {
                                        disable_date_arr.push(date_disable.meta_value);
                                    }
                                })
                                //console.log(disable_date_arr);
                        } else {
                            date_disable = data[z].booking_time;
                            for (var g = 0; g < disable_date_arr.length; g++) {
                                var delete_log = true;
                                for (var h = 0; h < date_disable.length; h++) {

                                    if (date_disable[h].meta_value.includes('to')) {
                                        var date_range_arr = date_disable[h].meta_value.split(' to ');
                                        var date_range = {};
                                        date_range.from = date_range_arr[0];
                                        date_range.to = date_range_arr[1];
                                    } else {
                                        var date_range = "";
                                        date_range = date_disable[h].meta_value;
                                    }
                                    if (disable_date_arr[g] == date_range) {
                                        delete_log = false;
                                    }
                                }
                                if (delete_log == true) {
                                    disable_date_arr.splice(h, 1);
                                }
                            }
                            //console.log(disable_date_arr);
                        }
                    }
                    var today = new Date();
                    //var disable_date_arr = ["30.07.2019", "29.07.2019", "28.07.2019"];
                    var optional_config = {
                        dateFormat: "d.m.Y",
                        minDate: today,
                        disable: disable_date_arr,
                        mode: "range",
                        locale: {
                            firstDayOfWeek: 1,
                            weekdays: {
                                shorthand: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                                longhand: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                            },
                            months: {
                                shorthand: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                                longhand: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                            },
                        },
                    };
                    if (is_front == true) {
                        optional_config.inline = true;
                    };
                    // $(".wp-admin.post-type-mt_booking .mth_booking_date_input .cf-field__body input").flatpickr(optional_config);
                    $(selector).flatpickr(optional_config);
                }
            }

        });
    })(jQuery);
}