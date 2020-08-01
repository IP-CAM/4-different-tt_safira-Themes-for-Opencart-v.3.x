var glovoSettings = undefined;
var glovoPrice = undefined;
var map = undefined;
var storeMarker = undefined;
var locationMarker = undefined;

var _selectedCity = '';

function glovoInitMapDraw(code, parent) {
    if (code === 'glovo.glovo') {
        parent.find('#glovo_container').remove();
        parent.append('<div id="glovo_container"></div>');

        let str = glovoSettings.cities.map(el => {return `<option value="${el.code}">${el.title}</option>`;});
        let additionalEl = '<p>Por favor elige una ciudad: <select onchange="selectGlovoCity(this.value)">'+str.join()+'</select></p>';
        additionalEl += '<h5>Dirección de partida: <b id="glovo_address_store">-</b></h5>';
        additionalEl += '<h5>Dirección de destino: <b id="glovo_address_text">-</b></h5>';
        additionalEl += '<h5>Hora programada: <input type="text" class="timepicker text-center"></h5>';

        parent.find('#glovo_container').append('<hr><div id="glovo_error"></div>'+additionalEl+'<h5>Horario de Atención: <b id="glovo_working_time">-</b></h5><div id="map" style="width: 100% !important;height: 350px !important;">');
        delayToInitMap();

        $('.timepicker').timepicker({
            timeFormat: 'h:mm p',
            interval: 30,
            minTime: '9',
            maxTime: '6:00pm',
            defaultTime: 'now',
            startTime: '9:00am',
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function(time, b) {
                console.log(time);
                console.log(moment(time, 'h:m').format('H:mm A'));
            }
        });
    }
}

function selectGlovoCity(code) {
    let city = undefined;
    glovoSettings.cities.forEach(cc => {if (cc.code === code) city = cc;});
    _selectedCity = code;

    if (city === undefined) return;
    console.log(city);

    let latLng = (new google.maps.LatLng(city.lat, city.lng));
    let workingTime = glovoSettings.working_time[city.code];

    map.setCenter(latLng);
    map.setZoom(city.zoom);
    $('#glovo_address_store').text(city.store_address);
    $('#glovo_working_time').text(workingTime.from + ' - ' + workingTime.to);

    // ------------------------------------------------------------------------
    // https://developers.google.com/maps/documentation/javascript/markers
    storeMarker.setPosition(latLng);
    storeMarker.setMap(map);

    locationMarker.setPosition(latLng);
    locationMarker.setMap(map);
}

function delayToInitMap() {
    setTimeout(function () {initMap();}, 100)
}

function initMap() {
    var points = JSON.parse(glovoSettings.map_points);
    map = new google.maps.Map(document.getElementById('map'), {mapTypeId: 'terrain'});
    storeMarker = new google.maps.Marker({title: 'My Store', zIndex: 1});
    locationMarker = new google.maps.Marker({draggable: true, animation: google.maps.Animation.DROP, icon: glovoSettings.carrier_icon, title: 'My location!', zIndex: 99999});
    selectGlovoCity(glovoSettings.cities[0].code);

    points.forEach(function(figure) {
        let lineTemp = [];
        figure.forEach(el => {
            lineTemp.push(new google.maps.LatLng(el[0],el[1]));
        });

        test = new google.maps.Polygon({path: lineTemp, strokeColor: '#950201', strokeOpacity: 1.0, strokeWeight: 1, fillColor: '#FF0000', fillOpacity: 0.05});
        test.setMap(map);
    });

    locationMarker.addListener('click', toggleBounce);
    locationMarker.addListener('dragend', handleEvent);

    // --------------------------
    function toggleBounce() {
        if (locationMarker.getAnimation() !== null) {
            locationMarker.setAnimation(null);
        } else {
            locationMarker.setAnimation(google.maps.Animation.BOUNCE);
        }
    }

    function handleEvent(event)
    {
        let address = '-';
        let lat = event.latLng.lat();
        let lng = event.latLng.lng();
        $('#shipping_glovo_loader').addClass('fa fa-spinner fa-spin');
        $('#button-shipping-method').button('loading');

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({latLng: {lat: lat, lng: lng}}, function (result, status) {
            if ('OK' === status) {
                address = result[0].formatted_address;
                console.log(address);
            } else {
                address = '-';
                console.log('Hubo un error al encontrar la dirección');
            }

            let url = glovoSettings.estimate_url + `&lat=${lat}&lng=${lng}&address=${address}&city=${_selectedCity}`;

            $.get(url, function(data) {
                let res = JSON.parse(data);

                $('#glovo_address_text').text(address);
                $('#button-shipping-method').button('reset');
                $('#shipping_glovo_loader').removeClass('fa fa-spinner fa-spin');

                if (!res.status)
                {
                    glovoPrice = undefined;
                    console.log('%c Error: ' + res.message, 'color: #DA3422');
                    $('#shipping_glovo_price').text('(No disponible)');
                    $('#glovo_error').html('').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + res.message + ' </div>');
                    return;
                }

                glovoPrice = res.price;

                $('#glovo_error').html('');
                $('#shipping_glovo_price').text(glovoPrice);
                console.log('%c Price: ' + glovoPrice, 'color: #27166f');
            });
        });
    }
}

function checkGlovoConditions(el)
{
    if (glovoPrice === undefined) {
        $('#glovo_error').html('').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ¡Por favor elige otra dirección de envio! </div>');
        return false;
    }

    $('.alert-dismissible, .text-danger').remove();
    return true;
}

$('#button-shipping-method').on('click', function() {
    if (glovoPrice === undefined) {
        $('#glovo_error').html('').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ¡Por favor elige otra dirección de envio! </div>');
        return false;
    }

    $('.alert-dismissible, .text-danger').remove();
    return true;
});