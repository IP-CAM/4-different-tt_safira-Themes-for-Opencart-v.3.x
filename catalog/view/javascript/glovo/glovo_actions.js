class Glovo {
    constructor(settings) {
        this.settings = settings;
        this.city = this.settings.city;
        this.map_points = JSON.parse(this.settings.map_points);
        this.working_time = this.settings.working_time;
        this.latLng = {lat: 0, lng: 0};
        this.address = settings.customer.address;
        this.zoom = this.settings.zoom;
        this.google_address = this.settings.google_address;

        this.i18n = this.settings.i18n;

        this.price = undefined;
        this.scheduled_time = 0;

        // this.address = this.google_address.address;
        this.address_reference = this.settings.address_reference === '' ? '-' : this.settings.address_reference;

        this.address_search = this.settings.address_search;

        this.forceError = false;
    }

    // options() {
    //     return this.settings.cities.map(el => {return `<option value="${el.code}">${el.title}</option>`;});
    // }

    getStoreDetails() {
        return `<h3>${this.i18n.store_name}</h3><p>-${this.i18n.address}: <b>${this.city.store_address}</b></p><p>-${this.i18n.contact_person}: <b>${this.city.contact}</b></p><p>-${this.i18n.contact_phone}: <b>${this.city.phone}</b></p>`;
    }

    getCustomerAddress() {
        //return `<h3>${this.i18n.store_name}</h3><p>-${this.i18n.address}: <b>${this.address}</b></p><p>-${this.i18n.contact_person}: <b>${this.customer.full_name}</b></p><p>-${this.i18n.contact_phone}: <b>${this.city.phone}</b></p>`;
        return `<h3>${this.i18n.customer_name}</h3><div id="glovo_test1237">${this.address}</div>`;
    }
    setCustomerAddress(address) {
        this.address = address;
        $('#glovo_test1237').text(this.address);
    }

    setScheduledTime(status) {
        let $timePicker = $('#glovo_scheduled_container');
        // let $hourPicker = $('#glovo_timepicker_test');

        if (status) {
            $timePicker.removeClass('hidden');
            // $hourPicker.removeClass('hidden');
        } else {
            $timePicker.addClass('hidden');
            // $hourPicker.addClass('hidden');
        }
    }
    calculateScheduledTime() {
        this.scheduled_time = $('#glovo_timepicker_test').val();
        /*this.scheduled_time = !$('#glovo_is_scheduled_time').is(':checked')
            ? $('#glovo_timepicker_test').val()
            : 0;*/
    }

    /**
     * @deprecated
     */
    getEstimateUrl(lat, lng, address) {
        return this.settings.estimate_url + `&lat=${lat}&lng=${lng}&address=${address}&city=${this.city.code}&scheduled_time=${this.scheduled_time}&address_id=${this.google_address.address_id}&riders=${this.settings.riders}&force_error=${this.forceError}`;
    }

    buildRequestForEstimateOrder(estimateAddress) {
        this.calculateScheduledTime();

        return {
            city: this.city.id,
            // city: this.city.code,
            scheduled_time: this.scheduled_time,
            address_id: this.google_address.address_id,
            riders: this.settings.riders,
            lat: this.latLng.lat,
            lng: this.latLng.lng,
            address: estimateAddress,
            reference: this.address_reference,
            contact_person: '-',
            contact_phone: '-',
            force_error: this.forceError,
        };
    }

    buildCustomerInitialLatLng() {
        let that = this;

        return new Promise(function (resolve, reject) {
            if (false && that.google_address.lat !== '')  {
                // that.latLng = {lat: parseFloat(that.google_address.lat), lng: parseFloat(that.google_address.lng)};
                // model.estimateOrder();

                resolve({lat: parseFloat(that.google_address.lat), lng: parseFloat(that.google_address.lng)});
            } else {
                let geocoder = new google.maps.Geocoder();
                geocoder.geocode({address: that.address_search}, function (result, status) {
                    if ('OK' === status) {
                        resolve({
                            lat: result[0].geometry.location.lat(),
                            lng: result[0].geometry.location.lng(),
                        });
                    } else {
                        console.log('Error al traer el LatLng de la direccion');
                        resolve({lat: that.city.geo_lat, lng: that.city.geo_lng});
                    }
                });
            }

        });
    }

    // ------------------------------------------------------------------------

    estimateOrder() {
        if (this.latLng.lat === 0) return false;

        let geocoder = new google.maps.Geocoder();
        let address = this.address;

        let $glovoLoader = $('#shipping_glovo_loader');
        let $buttonShipping = $('#button-shipping-method');
        let $shippingGlovoPrice = $('#shipping_glovo_price');
        let $glovoError = $('#glovo_error');

        $glovoLoader.addClass('fa fa-spinner fa-spin');
        $buttonShipping.button('loading');

        geocoder.geocode({latLng: model.latLng}, function (result, status) {
            if ('OK' === status) {
                address = result[0].formatted_address;
                console.log(address);
            } else {
                address = '-';
                console.log('Hubo un error al encontrar la dirección');
            }
            //model.setCustomerAddress(address);
            console.log(model.buildRequestForEstimateOrder(address));

            $.post(model.settings.estimate_url, model.buildRequestForEstimateOrder(address), function (result) {
                result = JSON.parse(result);
                // console.log(result);

                $('#glovo_address_text').text(model.address);
                $buttonShipping.button('reset');
                $glovoLoader.removeClass('fa fa-spinner fa-spin');

                if (!result.status) {
                    model.price = undefined;
                    console.log('%c Error: ' + result.message, 'color: #DA3422');
                    $shippingGlovoPrice.text('(N/A)');
                    $glovoError.html('').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + result.message + ' </div>');
                    return;
                }

                model.price = result.price;
                model.google_address.lat = model.latLng.lat;
                model.google_address.lng = model.latLng.lng;

                $glovoError.html('');
                $shippingGlovoPrice.text(model.price);
                console.log('%c Price: ' + model.price, 'color: #27166f');
            });
        });
    }

    toggleBounce() {
        locationMarker.setAnimation((locationMarker.getAnimation() !== null) ? null : google.maps.Animation.BOUNCE);
    }
    handleEvent(event) {
        let lat = event.latLng.lat();
        let lng = event.latLng.lng();
        model.latLng = {lat: lat, lng: lng};
        model.forceError = !circle.contains(event.latLng);

        model.estimateOrder();
    }

    findWorkingTimes(date) {
        date = date.replace(/\//g, '-');
        let days = this.working_time.daysAvailable;

        return days.find(el => el.date === date);
    }

    handleChangeDate(element) {
        let $timePicker = $('#glovo_timepicker_test');
        let workTimes = model.findWorkingTimes(element.date.toLocaleDateString('en-GB'));
        let tempItems = '';

        $('#glovo_working_time').text(workTimes.range);

        workTimes.intervals.forEach(interval => {
            tempItems += `<option value="${interval.timestamp}">${interval.name}</option>`;
        });

        $timePicker.html('').append(tempItems);
        $timePicker.val($('#glovo_timepicker_test option:first').val());
        $timePicker.change();
    }
}

// This is for test
(function ($, window, document) {
    $(function () {
        console.log('xd');
    })
})(window.jQuery, window, document);

var model = new Glovo(glovoSettings);
var map, storeMarker, locationMarker, circle = undefined;

$(function() {
    let el = $('[name="shipping_method"]').first();

    if (el.val() === 'glovo.glovo') {
        var interval = setInterval(function() {
            try{
                new google.maps.Marker({title: 'My Store', zIndex: 1});
                console.log("invoked");
                clearInterval(interval);
                initGlovoMap(el.parent().parent());
                // initAutocomplete();

                google.maps.Circle.prototype.contains = function(latLng) {
                    return this.getBounds().contains(latLng) && google.maps.geometry.spherical.computeDistanceBetween(this.getCenter(), latLng) <= this.getRadius();
                }
            } catch(err){}
        }, 100);
    }
});

$(function() {
    $('[name="shipping_method"]').change(function () {
        let parent = $(this).parent().parent();

        if ($(this)[0].value === 'glovo.glovo') {
            initGlovoMap(parent);
        } else {
            $('#glovo_container').remove();
            console.log('q');
        }
    });
});

$('#button-shipping-method').on('click', function() {
    if (model.price === undefined) {
        $('#glovo_error').html('').prepend(`<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ${model.i18n.wrong_address} </div>`);
        $(window).scrollTop($("#glovo_error").offset().top);
        return false;
    }

    $('.alert-dismissible, .text-danger').remove();
    return true;
});

function initGlovoMap(parent) {
    parent.find('#glovo_container').remove();
    parent.append('<div id="glovo_container"></div>');

    let additionalEl = '';
    // additionalEl = `<h5>${model.i18n.destination}: <b id="glovo_address_text">-</b></h5>`;
    // additionalEl += `<h5>${model.i18n.reference}: <b id="glovo_reference_text">-</b></h5>`;
    // additionalEl += `<div id="locationField"> <input type="text" id="autocomplete" placeholder="Enter your address" onFocus="geolocate()" style="width: 100%;"/></div>`;
    // additionalEl += `<h5>${model.i18n.working_hour}: <b id="glovo_working_time">-</b></h5>`;
    // additionalEl += `<h5><input type="checkbox" id="glovo_is_scheduled_time">${model.i18n.scheduled} <input type="text" id="glovo_timepicker" class="timepicker text-center hidden" readonly><select id="glovo_timepicker_test" class="hidden"></select></h5>`;
    // additionalEl += `<h5><input type="checkbox" id="glovo_is_scheduled_time">${model.i18n.scheduled} <input type="text" class="timepicker" id="glovo_timepicker"></h5>`;
    // additionalEl += `<h5>Order volume: <b>${model.settings.volume.total} / ${model.settings.volume.max} cm³</b></h5>`;
    // additionalEl += `<h5>Order weight: <b>${model.settings.weight.total} / ${model.settings.weight.max} Kgs</b></h5>`;

    additionalEl += `
    <div class="row glovo-checkout-details">
        <div class="col-sm-12"><i style="color: #07b088">${model.settings.preparation_time}</i></div>
        <div class="col-sm-12">${model.i18n.scheduled_help1}</div>
        <div class="col-sm-12 hidden">
            <table>
            <tr>
                <td>
                    <label class="switch">
                    <input type="checkbox" id="glovo_is_scheduled_time" checked>
                    <span class="slider round"></span>
                    </label>
                </td>
                <td>&nbsp;&nbsp;${model.i18n.scheduled}</td>
            </tr>
            </table>
        </div>
    </div>
    `;

    additionalEl += `
    <div class="form-inline hidden" id="glovo_scheduled_container">
        <div class="form-group">
            <label for="glovo_timepicker">${model.i18n.day}:&nbsp;&nbsp;</label>
            <!--<input type="email" class="form-control" id="exampleInputEmail3" placeholder="Email">-->
            <input type="text" id="glovo_timepicker" class="timepicker text-center form-control" readonly>
        </div>
        <div class="form-group">
            <label for="glovo_timepicker_test">${model.i18n.hour}:&nbsp;&nbsp;</label>
            <!--<input type="password" class="form-control" id="exampleInputPassword3" placeholder="Password">-->
            <select id="glovo_timepicker_test" class="form-control""></select>
        </div>
    </div>
    <br>
    <b>${model.i18n.confirm_in_map}</b>
    `;

    additionalEl += `<!--<input type="text" id="glovo_timepicker" class="timepicker text-center hidden" readonly><select id="glovo_timepicker_test" class="hidden"></select>-->`;
    // additionalEl += `<h5>${model.i18n.riders}: <b>${model.settings.riders}</b></h5>`;

    parent.find('#glovo_container').append(`<div id="glovo_error"></div>`+additionalEl+`<div id="map" style="width: 100% !important;height: 550px !important;"></div>`);
    model.setScheduledTime(!$(this).is(':checked'));
    setTimeout(function () {initMap();}, 100);

    // ------------------------------------------------------------------------

    let $timePicker = $('#glovo_timepicker');
    let $scheduledTime = $('#glovo_is_scheduled_time');

    $timePicker.datepicker({
        language: 'es-ES',
        format: 'dd-mm-yyyy',
        startDate: model.settings.working_time.day_start,
        endDate: model.settings.working_time.day_end,
        filter: function(date, view) {
            let dayList = model.settings.working_time.daysNotAvailable;

            if (dayList.includes(date.getTime()/1000) && view === 'day') return false;
        }
    });

    $timePicker.on('pick.datepicker', model.handleChangeDate);

    $scheduledTime.change(function() {
        model.setScheduledTime(!$(this).is(':checked'));
    });
}

function initMap() {
    map = new GMaps({
        div: '#map',
        lat: model.city.geo_lat,
        lng: model.city.geo_lng,
        zoom: model.zoom,
        mapTypeId: 'terrain'
    });

    model.map_points.forEach(function(figure) {
        map.drawPolygon({
            paths: figure,
            strokeColor: '#950201',
            strokeOpacity: 0,
            strokeWeight: 1,
            fillColor: '#00765a',
            fillOpacity: 0.3,
        });
    });

    // ------------------------------------------------------------------------

    storeMarker = map.addMarker({
        title: model.i18n.store_name,
        lat: model.city.geo_lat,
        lng: model.city.geo_lng,
        icon: model.settings.store_icon,
        zIndex: 1,
        infoWindow: {content: model.getStoreDetails()}
    });

    model.buildCustomerInitialLatLng().then(function (res) {
        console.log('buildCustomerInitialLatLng', res);
        model.latLng = res;

        locationMarker = map.addMarker({
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: model.i18n.customer_name,
            lat: res.lat,
            lng: res.lng,
            zIndex: 99999,
            infoWindow: {content: model.getCustomerAddress()},
        });

        map.fitLatLngBounds([
            new google.maps.LatLng(model.city.geo_lat, model.city.geo_lng),
            new google.maps.LatLng(res.lat, res.lng)
        ]);
        circle = map.drawCircle({
            strokeColor: '#950201',
            strokeOpacity: 1,
            strokeWeight: 1,
            fillColor: '#950201',
            fillOpacity: 0.2,
            radius: model.city.geo_radius,
            lat: model.city.geo_lat,
            lng: model.city.geo_lng,
        });

        locationMarker.addListener('dragend', model.handleEvent);

        //map.setCenter(res.lat, res.lng);
        model.estimateOrder();
    });

    $('#glovo_timepicker_test').on('change', function (e) {
        console.log($(this).val());
        model.estimateOrder();
    });
}