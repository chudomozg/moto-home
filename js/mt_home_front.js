(function($) {
    $(document).ready(function() {
        //!!!!!!!!!!!!!НАДО ДОПИСАТЬ ЕСЛИ ГОРОДОВ БОЛЬШЕ 99!!! REST API выводит по 99!
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
                url: site_url + '/wp-json/wp/v2/wp_cn_city?parent=' + parent_id + '&per_page=100&page=2',
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
        if (mth_homes_array.length) {
            mth_homes_array.forEach(function(mth_home) {
                var home_title = '<a href="' + mth_home.link + '">' + mth_home.title.rendered + '</a>';
                var home_html = '' +
                    '<div>' +
                    '   <div class="mth_list_cal">' +
                    '   </div>' +
                    '   <div class="mth_minimap">' +
                    '   </div>' +
                    '   <div class="mth_list_gallery">' +
                    '   </div>' +
                    '   <div class="mth_list_content articleTxt__box">' +
                    '      <div class="articleTxt__header">' + home_title +
                    '      </div>' +
                    '      <div class="articleTxt__text">' + mth_home.excerpt.rendered +
                    '      </div>' +
                    '   </div>' +
                    '</div>';
                home_list_html.push(home_html);
            });
            for (var i = 0; i < home_list_html.length; i++) {
                $('.motohomeContentWrapper').append(home_list_html[i]);
            }

        }
    })(jQuery);
}