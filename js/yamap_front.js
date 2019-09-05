var myMap; //Тут без глобальной переменной надо бы потом решить

(function($) {
    $(document).ready(function() {
        ymaps.ready(init);
    });

    $(window).resize(function() {
        var map_height = '300px';
        // var map_height = (screen.width > 900) ? ($('.fotorama').css('height')) : ('300px');
        if (document.body.clientWidth > 883) {
            map_height = $('.fotorama').css('height');
        }
        $('#map').css('height', map_height);
        myMap.container.fitToViewport();
    });

})(jQuery);


function init() {

    //создаем карту
    myMap = new ymaps.Map('map', {
        center: [55.753994, 37.622093],
        zoom: 12,
        autoFitToViewport: 'always',
    }, {
        searchControlProvider: 'yandex#search'
    });

    //если на странице всех мотодомов
    if (document.querySelector('.mth_page_all_motohome')) {
        var mth_city_all = document.querySelector('#wp_cn_front_city_select').value;
        //console.log(mth_city_all);s
        mth_get_homes(mth_city_all);

    }

    //если на странице одного мотодома
    if (document.querySelector('.single-motohome')) {
        var mthome_tinput = document.querySelector('.mth_front_loc .mth_front_loc_value'); // Указываем наш инпут с координатами
        var coords_from_input;

        if (mthome_tinput !== undefined) {
            if (mthome_tinput.value) {
                coords_from_input = mthome_tinput.value.split(", ").map(Number);
                //console.log(coords_from_input);
                var mth_home_title = document.querySelector('.single-motohome .entry-title').innerHTML;
            } else {
                coords_from_input = [55.753994, 37.622093];
                console.log("Ошибка загрузки метки");
                var mth_home_title = "Ошибка загрузки метки";
            }
            myMap.setCenter(coords_from_input);


            //Создаем балун
            myMap.geoObjects.add(new ymaps.Placemark(coords_from_input, {
                balloonContent: mth_home_title
            }, {
                preset: 'islands#dotIcon',
                iconColor: '#23afe5',
                iconImageSize: [200, 200],
            }));

        }
    }

}

function mth_yamap_add_city_collection(mth_homes_array) {
    mthCollectionArray = new ymaps.GeoObjectCollection(null, {
        preset: 'islands#dotIcon',
        iconColor: '#23afe5'
    });
    //console.log(mth_homes_array);
    if (mth_homes_array.length) {
        mth_homes_array.forEach(function(mth_home) {
            //console.log(mth_home);
            var placemark;
            var mth_cord = mth_home.motohome_loc.split(',').map(parseFloat);
            mthCollectionArray.add(placemark = new ymaps.Placemark(mth_cord, {
                balloonContent: mth_home.title.rendered,
            }));
            placemark.events.add(['click'], function(e) {
                // Ваш код
                window.location.href = `#mth_unit_${mth_home.id}`
                window.scrollBy(0, -90);
            });
        });

        //console.log(mthCollectionArray);
        myMap.geoObjects.add(mthCollectionArray);
        myMap.setBounds(mthCollectionArray.getBounds(), { zoomMargin: 100 });
    }

};