ymaps.ready(init);

function init() {
    var mthome_tinput = document.querySelector('.mth_front_loc .mth_front_loc_value'); // Указываем наш инпут с координатами
    var coords_from_input;

    if (mthome_tinput !== undefined) {
        if (mthome_tinput.value) {
            coords_from_input = mthome_tinput.value.split(", ").map(Number);
            //console.log(coords_from_input);
            var mth_home_title = document.querySelector('.site-main .entry-title').innerHTML;
        } else {
            coords_from_input = [55.753994, 37.622093];
            console.log("Ошибка загрузки метки");
            var mth_home_title = "Ошибка загрузки метки";
        }
        //создаем карту
        var myPlacemark,
            myMap = new ymaps.Map('map', {
                center: coords_from_input,
                zoom: 12
            }, {
                searchControlProvider: 'yandex#search'
            });

        //Создаем балун
        myMap.geoObjects.add(new ymaps.Placemark(coords_from_input, {
            balloonContent: mth_home_title
        }, {
            preset: 'islands#dotIcon',
            iconColor: '#23afe5'
        }));

    }
}