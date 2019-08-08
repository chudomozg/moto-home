(function($) {
    $(document).ready(function() {
        //подгружаем названия городов, если выбрали регион 
        if ($('.single-post #wp_cn_front_city_select').length) {
            var parent_id = $('#wp_cn_front_region_select').val();
            mth_get_option_city_select(parent_id);
            $('#wp_cn_front_region_select').change(function() {
                mth_get_option_city_select(parent_id);
            });
            $('#wp_cn_front_city_select').change(function() {
                var mth_city_all = document.querySelector('#wp_cn_front_city_select').value;
                mth_get_homes(mth_city_all);
            });

        }

    });
})(jQuery);

function mth_get_option_city_select(parent_id) {
    (function($) {
        $('#wp_cn_front_city_select > option').remove();
        $('#wp_cn_front_city_select').append($('<option>', {
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
                    $('#wp_cn_front_city_select > option').remove();
                    $.each(data, function(i, name) {
                        $('#wp_cn_front_city_select').append($('<option>', {
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
                                    $('#wp_cn_front_city_select').append($('<option>', {
                                        value: name.id,
                                        text: name.name
                                    }));
                                });
                            }
                        });
                    }
                }
            });

        });
    })(jQuery);
}


function mth_get_homes(city_num) {
    (function($) {
        $(function() {
            $.ajax({
                url: site_url + '/wp-json/wp/v2/motohome?wp_cn_city=' + city_num,
                type: 'GET',
                data_type: 'json',
                success: function(data) {
                    //console.log(data);
                    mth_yamap_add_city_collection(data);
                    mth_add_home_list(data);
                }
            });

        });
    })(jQuery);
}

function mth_add_home_list(mth_homes_array) {
    (function($) {
        var home_list_html = [];
        var home_list_id = [];
        if (mth_homes_array.length) {
            var media_id_arr = [];

            mth_homes_array.forEach(function(mth_home) {
                console.log(mth_home);
                var home_coords = mth_home.motohome_loc.split(", ").map(Number);
                //console.log(mth_home.motohome_gallery);
                //console.log(mth_get_media_by_id(mth_home.motohome_gallery));

                var media_html = '';
                for (i = 0; i < mth_home.motohome_gallery.length; i++) {
                    media_html += '<img src="" id="mth_list_img_' + mth_home.motohome_gallery[i] + '">';
                    media_id_arr.push(mth_home.motohome_gallery[i]);
                }
                var home_map = 'https://static-maps.yandex.ru/1.x/?ll=' + home_coords[1] + ',' + home_coords[0] + '&size=238,238&l=map&z=8&pt=' + home_coords[1] + ',' + home_coords[0] + ',pm2lbm';
                var home_title = '<a href="' + mth_home.link + '">' + mth_home.title.rendered + '</a>';
                var home_html = '' +
                    '<div>' +
                    '   <div class="mth_list_cal" id="mth_list_cal-' + mth_home.id + '">' +
                    '   </div>' + '<input type="hidden" id="mth_hid_cal_input_' + mth_home.id + '">' +
                    '   <div class="mth_minimap"><img src="' + home_map + '">' +
                    '   </div>' +
                    '   <div class="mth_list_gallery">' + media_html +
                    '   </div>' +
                    '   <div class="mth_list_content articleTxt__box">' +
                    '      <div class="articleTxt__header">' + home_title +
                    '      </div>' +
                    '      <div class="articleTxt__text">' + mth_home.excerpt.rendered +
                    '      </div>' +
                    '   </div>' +
                    '</div>';
                home_list_html.push(home_html);
                home_list_id.push(mth_home.id);
            });
            for (var i = 0; i < home_list_html.length; i++) {
                $('.motohomeContentWrapper').append(home_list_html[i]);
                //$('#mth_list_cal-' + home_list_id[i]).clndr();
            }
            //console.log(media_id_arr);
            //mth_get_media_by_id(media_id_arr);
            mth_homes_array.forEach(function(mth_home) {
                var room_id = [];
                for (var i = 0; i < mth_home.rooms.length; i++) {
                    room_id.push(mth_home.id + '-' + i)
                }

                hidden_cal_selector = '#mth_hid_cal_input_' + mth_home.id;
                mth_get_date_picker(room_id, true, hidden_cal_selector);
            });
        }
    })(jQuery);
}

function mth_get_media_by_id(id_arr) {
    for (i = 0; i < id_arr.length; i++) {
        jQuery.ajax({
            url: site_url + '/wp-json/wp/v2/media/' + id_arr[i],
            type: 'GET',
            data_type: 'json',
            success: function(data) {
                if (jQuery('#mth_list_img_' + data.id).length) {
                    jQuery('#mth_list_img_' + data.id).attr("src", data.media_details.sizes.medium_large.source_url);
                }

            }
        });
    }
}