let map;
let MAP_SETTINGS;

var clientMarker, storeMarker, riderMarker = undefined;

function waitToLoadAssets(initialData) {
    MAP_SETTINGS = initialData;

    let interval = setInterval(function() {
        try {
            $('#glovo_tracking_map');

            new google.maps.Marker({title: 'My Store', zIndex: 1});

            map = new GMaps({
                div: '#glovo_tracking_map',
                lat: MAP_SETTINGS.client.lat,
                lng: MAP_SETTINGS.client.lng,
                zoom: 15,
                mapTypeId: 'roadmap'
            });

            clearInterval(interval);
            drawTrackingMap();
            startVerifier();
        } catch (e) {}
    }, 100);
}

function drawTrackingMap() {
    map.addMarker({
        lat: MAP_SETTINGS.client.lat,
        lng: MAP_SETTINGS.client.lng,
        zIndex: 9,
        infoWindow: {content: MAP_SETTINGS.client.description}
    });
    map.addMarker({
        lat: MAP_SETTINGS.store.lat,
        lng: MAP_SETTINGS.store.lng,
        icon: MAP_SETTINGS.store.icon,
        zIndex: 9,
        infoWindow: {content: MAP_SETTINGS.store.description}
    });

    console.log(1);

    // let centerLat = MAP_SETTINGS.track.lat;
    // let centerLng = MAP_SETTINGS.track.lng;
    let centerLat = MAP_SETTINGS.client.lat;
    let centerLng = MAP_SETTINGS.client.lng;

    if(MAP_SETTINGS.track === undefined) {
        map.setCenter(centerLat, centerLng);
    } else {
        map.setCenter(MAP_SETTINGS.track.lat, MAP_SETTINGS.track.lng);

        riderMarker = map.createMarker({
            lat: centerLat,
            lng: centerLng,
            zIndex: 99999,
            icon: MAP_SETTINGS.track.icon,
            infoWindow: {content: MAP_SETTINGS.track.description}
        });
        map.addMarker(riderMarker);
    }

    console.log(2);

    map.drawRoute({
        origin: [MAP_SETTINGS.store.lat, MAP_SETTINGS.store.lng],
        destination: [MAP_SETTINGS.client.lat, MAP_SETTINGS.client.lng],
        travelMode: 'driving',
        strokeColor: '#00765a',
        strokeOpacity: .8,
        strokeWeight: 6
    });
    console.log(3);

    map.fitLatLngBounds([
        new google.maps.LatLng(MAP_SETTINGS.store.lat, MAP_SETTINGS.store.lng),
        new google.maps.LatLng(MAP_SETTINGS.client.lat, MAP_SETTINGS.client.lng)
    ]);
    console.log(4);
}

function startVerifier() {
    if (MAP_SETTINGS.track !== undefined) {
        let interval = setInterval(function() {
            $.ajax({
                url: MAP_SETTINGS.track_url,
                headers: {
                    'Authorization': `Basic ${MAP_SETTINGS.authorization}`,
                    'Content-Type': 'application/json'
                },
                method: 'GET',
                dataType: 'json',
                success: function(result){
                    console.log('result:', result);
                    riderMarker.setPosition({lat: result.lat, lng: result.lon});
                    console.log('refreshed');
                }
            });
        }, 5000);
    }
}