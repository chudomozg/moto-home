$(document).ready(function() {
    if ($('.wp-admin.post-type-motohome').length) {
        ymaps.ready(init);
    }
})

function init() {
    var mthome_tinput = document.querySelector('.mthome_ad_coord_text input[type="text"]'); // Указываем наш инпут с координатами
    var coords_from_input;
    if (mthome_tinput !== undefined) {
        if (mthome_tinput.value) {
            coords_from_input = mthome_tinput.value.split(", ").map(Number);
            //console.log(coords_from_input);
        } else {
            coords_from_input = [55.753994, 37.622093];
        }
    }
    //создаем карту
    var myPlacemark,
        myMap = new ymaps.Map('map', {
            center: coords_from_input,
            zoom: 9
        }, {
            searchControlProvider: 'yandex#search'
        });
    //создаем балун
    myPlacemark = createPlacemark(coords_from_input);
    balun = myMap.geoObjects.add(myPlacemark);
    getAddress(coords_from_input);

    //если балун передвинули меняем координаты в input
    myPlacemark.events.add('dragend', function(e) {
        var coords = myPlacemark.geometry.getCoordinates()
        mthome_tinput.value = [
            coords[0].toFixed(6),
            coords[1].toFixed(6)
        ].join(', ');
    });

    //Изменение координат в input 
    //Слушаем клик на карте.
    myMap.events.add('click', function(e) {
        var coords = e.get('coords');
        //console.log(coords);
        mthome_tinput.value = [
            coords[0].toFixed(6),
            coords[1].toFixed(6)
        ].join(', ');

        // Если метка уже создана – просто передвигаем ее.
        if (myPlacemark) {
            myPlacemark.geometry.setCoordinates(coords);
        }
        // Если нет – создаем.
        else {
            myPlacemark = createPlacemark(coords);
            myMap.geoObjects.add(myPlacemark);
            // Слушаем событие окончания перетаскивания на метке.
            myPlacemark.events.add('dragend', function() {
                getAddress(myPlacemark.geometry.getCoordinates());
            });
        }
        getAddress(coords);
    });

    // Создание метки.
    function createPlacemark(coords) {
        return new ymaps.Placemark(coords, {
            iconCaption: 'поиск...'
        }, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: true
        });
    }

    // Определяем адрес по координатам (обратное геокодирование).
    function getAddress(coords) {
        myPlacemark.properties.set('iconCaption', 'поиск...');
        ymaps.geocode(coords).then(function(res) {
            var firstGeoObject = res.geoObjects.get(0);

            myPlacemark.properties
                .set({
                    // Формируем строку с данными об объекте.
                    iconCaption: [
                        // Название населенного пункта или вышестоящее административно-территориальное образование.
                        firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                        // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                        firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                    ].filter(Boolean).join(', '),
                    // В качестве контента балуна задаем строку с адресом объекта.
                    balloonContent: firstGeoObject.getAddressLine()
                });
        });
    }
}