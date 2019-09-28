$(document).ready(function() {
    if ($('.post-type-motohome.wp-admin').length) {
        var city_select = 'select[name="carbon_fields_compact_input[_motohome_city]"]';
        var region_select = 'select[name="carbon_fields_compact_input[_motohome_region]"]';
        //если меняется селект региона меняем и селект города
        $(document).on('change', region_select, function() {
            if ($(city_select).length) {
                if ($(region_select).length) {
                    mth_get_city_to_select($(region_select).val());
                }
            }
        });
        //костыль (сами вызываем change) потому что нет доступа к JS carbon fields
        $(region_select).val(function() {
            return $(region_select).val();
        });
        $(region_select).trigger('change');
        mth_get_city_term_id($("#post_ID").val());
    }

    //На странице редактирования Брони
    if ($('.post-type-mt_booking.wp-admin').length) {
        var user_select = 'select[name="carbon_fields_compact_input[_mth_user]"]';
        $(document).on('change', user_select, function() {
            var user_id = $(user_select).val();
            $('input[name="carbon_fields_compact_input[_mth_user_firstname]"]').val('Загрузка...');
            $('input[name="carbon_fields_compact_input[_mth_user_lastname]"]').val('Загрузка...');
            $('input[name="carbon_fields_compact_input[_mth_user_phone]"]').val('Загрузка...');
            $('input[name="carbon_fields_compact_input[_mth_user_email]"]').val('Загрузка...');
            $.ajax({
                url: site_url + '/wp-admin/admin-ajax.php',
                type: "POST",
                data_type: 'json',
                data: {
                    "action": 'mth_get_user_meta',
                    'user_id': user_id,
                },
                success: function(data) {
                    //alert('success');
                    console.log(data);
                    $('input[name="carbon_fields_compact_input[_mth_user_firstname]"]').val(data.first_name);
                    $('input[name="carbon_fields_compact_input[_mth_user_lastname]"]').val(data.last_name);
                    $('input[name="carbon_fields_compact_input[_mth_user_phone]"]').val(data.phone);
                    $('input[name="carbon_fields_compact_input[_mth_user_email]"]').val(data.email);
                },
                error: function(error) { console.error(error) }
            });
        });
        $(user_select).val(function() {
            return $(user_select).val();
        });
        $(user_select).trigger('change');
    }
})

//загружаем города по выбранному региону и выводим их в селект городов
function mth_get_city_to_select(parent_id) {
    (function($) {
        var city_select = 'select[name="carbon_fields_compact_input[_motohome_city]"]';
        $(city_select + ' > option').remove();
        $(city_select).append($('<option>', {
            value: 0,
            text: 'Загрузка...'
        }));
        $(function() {
            $.ajax({
                url: site_url + '/wp-json/wp/v2/wp_cn_city?parent=' + parent_id + '&per_page=100',
                type: 'GET',
                data_type: 'json',
                success: function(data) {
                    //console.log(data);
                    $(city_select + ' > option').remove();
                    $.each(data, function(i, name) {
                        $(city_select).append($('<option>', {
                            value: name.id,
                            text: name.name
                        }));
                    });
                    if (data.length > 99) { //надо исправить при рефакторинге, написать нормальный цикл (сейчас используется только в одном случае (Москва))
                        $.ajax({
                            url: site_url + '/wp-json/wp/v2/wp_cn_city?parent=' + parent_id + '&per_page=100&page=2',
                            type: 'GET',
                            data_type: 'json',
                            success: function(data) {
                                //console.log(data);
                                $.each(data, function(i, name) {
                                    $(city_select).append($('<option>', {
                                        value: name.id,
                                        text: name.name
                                    }));
                                });
                            }
                        });
                    }
                    var hidden_city = 'input[name="carbon_fields_compact_input[_mth_hidden_city]"]';
                    if ($(hidden_city).val() != 0) {
                        $(city_select + ' option[value="' + $(hidden_city).val() + '"]').prop('selected', true);
                        $(hidden_city).val(0);
                    }
                }
            });

        });
    })(jQuery);
}

function mth_get_city_term_id(post_id) {
    (function($) {
        $.ajax({
            url: site_url + '/wp-admin/admin-ajax.php',
            type: "POST",
            data_type: 'json',
            data: {
                "action": 'mth_get_term_to_hid_city_inp',
                'post_id': post_id,
            },
            //async: false, //blocks window close
            success: function(data) {
                if (data.length > 1) {
                    alert("Внимание, выбрано больше одного города! Присвойте только один термин таксономии и повторите попытку.")
                } else {
                    $('input[name="carbon_fields_compact_input[_mth_hidden_city]"]').val(data[0].term_id);
                }
            },
            error: function(error) { console.log(error) }
        });
    })(jQuery);
}