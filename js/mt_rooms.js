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

//функция получения ID и отправка AJAX запроса
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

//функция создания опций в селекте комнаты
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

        var room_id = [];
        var hid_selected_date = "";
        var date_input = 'input[name="carbon_fields_compact_input[_mth_bookdate]"]';

        //Если была сохранена дата (не пустая строка)
        if ($(date_input).val()) {
            hid_selected_date = $(date_input).val();
        }

        //Если комната уже была выбрана и сохранена, загружаем.
        var hidden_input = 'input[name="carbon_fields_compact_input[_mth_rooms_select_hidden]"]';
        if ($(hidden_input).val() != "") {
            var hidden_input_val = $(hidden_input).val();
            $(select).val(hidden_input_val);
            room_id.push(hidden_input_val);
            //Если нет то присваеваем везде значение первой опции селекта
        } else {
            $(hidden_input).attr('value', rooms[0].id);
            $(select + ' [value="' + rooms[0].id + '"]').attr("selected", "selected");
            room_id.push(rooms[0].id);
        }

        //DatePicker
        var selector = '.wp-admin.post-type-mt_booking .mth_booking_date_input .cf-field__body input';
        mth_get_date_picker(room_id, false, selector, hid_selected_date);
    })(jQuery);

}

function mth_get_date_picker(room_id, is_front, selector, selected_date = "") {
    console.log(room_id);
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
                // console.log('calendar_data');
                // console.log(data);
                var disable_date_arr = [];
                var rooms = [];
                // Выборка забронированных дат////////////////////////////////////////////////////
                if (Array.isArray(data) && (data.length > 0)) {
                    if (is_front == false) { //В админке
                        for (var z = 0; z < data.length; z++) {
                            if (z == 0) {
                                //формируем массив dissable date
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

                                //Если передали дату из инпута, которая была сохранена ранее не будем вставлять ее в disable_date
                                if (selected_date) {
                                    if (selected_date.includes('to')) {
                                        var selected_date_arr = [];
                                        var filtred_selected_date = {};
                                        selected_date_arr = selected_date.split(' to ');
                                        filtred_selected_date.from = selected_date_arr[0];
                                        filtred_selected_date.to = selected_date_arr[1];
                                    } else {
                                        var filtred_selected_date = selected_date;
                                    }
                                    disable_date_arr.forEach(function(date_disable, index, object) {
                                        if (JSON.stringify(date_disable) == JSON.stringify(filtred_selected_date)) {
                                            object.splice(index, 1);
                                        }
                                    })
                                }
                                // console.log(disable_date_arr);
                            }
                        }

                    }
                    if (is_front == true) { // на Сайте
                        var disable_arr = [];
                        // переведем из строки в дату
                        for (var z = 0; z < data.length; z++) {
                            var room = [];
                            if (data[z].booking_time.length > 0) {
                                data[z].booking_time.forEach(function(date_disable) {
                                    if (date_disable.meta_value.includes('to')) {
                                        var date_range_arr = date_disable.meta_value.split(' to ');
                                        var date_range = {};
                                        date_range.from = moment(date_range_arr[0], 'DD.MM.YYYY').toDate();
                                        date_range.to = moment(date_range_arr[1], 'DD.MM.YYYY').toDate();
                                        room.push(date_range);
                                    } else {
                                        room.push(moment(date_disable.meta_value, 'DD.MM.YYYY').toDate());
                                    }
                                });
                            }
                            rooms.push(room);
                        }
                        // console.log(rooms);

                        //комнаты
                        for (var i = 0; i < rooms.length; i++) {
                            // console.log('цикл № ' + i)

                            if (i == 0) {
                                //даты броней
                                for (var z = 0; z < rooms[i].length; z++) {
                                    //если диапозон создаем массив дат и закидываем в disable_arr
                                    if ((rooms[i][z].hasOwnProperty('from')) == true) {
                                        var range_arr = [];
                                        var currentDate = moment(rooms[i][z].from);
                                        var stopDate = moment(rooms[i][z].to);
                                        while (currentDate <= stopDate) {
                                            disable_arr.push(currentDate.toDate());
                                            currentDate = moment(currentDate).add(1, 'days');
                                        }
                                    } else {
                                        //если просто дата закидываем в disable_arr
                                        disable_arr.push(rooms[i][z]);
                                    }
                                }
                            } else {
                                //даты броней
                                var range_arr = [];
                                for (var z = 0; z < rooms[i].length; z++) {
                                    //если диапозон дат создаем массив дат 
                                    if ((rooms[i][z].hasOwnProperty('from')) == true) {
                                        var currentDate = moment(rooms[i][z].from);
                                        var stopDate = moment(rooms[i][z].to);
                                        while (currentDate <= stopDate) {
                                            range_arr.push(currentDate.toDate());
                                            currentDate = moment(currentDate).add(1, 'days');
                                        }
                                    } else { //если дата одна 
                                        range_arr.push(rooms[i][z]);
                                    }
                                }
                                // console.log('range_arr');
                                // console.log(range_arr);
                                //удаляем несовпавшие даты
                                for (var h = disable_arr.length - 1; h >= 0; h--) {
                                    //Перебираем в обратном порядке Потому что при удалении через splice пересчитываются индексы и в обычном порядке пропустим элемент 
                                    var delete_log = true;
                                    for (var t = 0; t < range_arr.length; t++) {
                                        //сравнивать даты нужно через .getTime()
                                        // console.log(disable_arr[h] + '=?' + range_arr[t]);
                                        if (disable_arr[h].getTime() == range_arr[t].getTime()) {
                                            delete_log = false;
                                        }
                                    }
                                    if (delete_log == true) {
                                        // console.log('Удаляем ' + disable_arr[h]);
                                        disable_arr.splice(h, 1);
                                    }
                                }
                            }
                            //console.log('disable_arr');
                            //console.log(disable_arr);
                        }
                        disable_date_arr = disable_arr;
                    }

                    //Вывод календаря

                    var today = new Date();
                    if (is_front) { //на Сайте
                        var newselector = selector.substring(1, selector.length);
                        // console.log(newselector);
                        var picker = new Lightpick({
                            field: document.getElementById(newselector),
                            inline: true,
                            minDate: today,
                            singleDate: false,
                            lang: 'ru',
                            locale: {
                                tooltip: {
                                    one: 'день',
                                    few: 'дня',
                                    many: 'дней',
                                },
                                pluralize: function(i, locale) {
                                    if ('one' in locale && i % 10 === 1 && !(i % 100 === 11)) return locale.one;
                                    if ('few' in locale && i % 10 === Math.floor(i % 10) && i % 10 >= 2 && i % 10 <= 4 && !(i % 100 >= 12 && i % 100 <= 14)) return locale.few;
                                    if ('many' in locale && (i % 10 === 0 || i % 10 === Math.floor(i % 10) && i % 10 >= 5 && i % 10 <= 9 || i % 100 === Math.floor(i % 100) && i % 100 >= 11 && i % 100 <= 14)) return locale.many;
                                    if ('other' in locale) return locale.other;

                                    return '';
                                }
                            },
                            format: 'DD.MM.YYYY',
                            disabledDatesInRange: false,
                            dropdowns: false,
                            disableDates: disable_date_arr,
                            onSelect: function(start, end) {
                                var str = '';
                                str += start ? start.format('DD.MM.YYYY') + ' to ' : '';
                                str += end ? end.format('DD.MM.YYYY') : '...';
                                document.querySelector(selector).innerHTML = str;
                                // document.getElementById(newselector).innerHTML = rangeText(start, end); // Локале не сработало

                                // console.log(selector + "=" + str);
                                // $(selector).val(str);
                            }
                        });
                    } else if (!is_front) { //В админке
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
                        $(selector).flatpickr(optional_config);
                    }
                }
            }

        });
    })(jQuery);
}

function mth_date_range_no_include(disable_date, date_compare_range) {
    if ((typeof date_compare_range) == 'string') {
        if ((disable_date.hasOwnProperty('from')) == true) {
            if (moment(date_compare_range).isBetween(disable_date.from, disable_date.to)) {
                return false;
            }
        }
    }

}

function isInArray(array, value) {
    return !!array.find(item => { return item.getTime() == value.getTime() });
}