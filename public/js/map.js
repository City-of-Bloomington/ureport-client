"use strict";
let map,
    geocoder,
    autocomplete;

function initMap()
{
    map = new google.maps.Map(document.getElementById('map'), {
        zoom:   14,
        center: { lat: DEFAULT_LATITUDE, lng: DEFAULT_LONGITUDE },
        mapId:  'ureport_map',
        fullscreenControl: false,
        streetViewControl: false,
        mapTypeControl:    false
    });
    map.addListener('dragend', chooseLocation);

    geocoder = new google.maps.Geocoder();


    autocomplete = new google.maps.places.Autocomplete(document.getElementById('address_string'), {
        fields:                ['formatted_address', 'geometry'],
        componentRestrictions: {country: 'us'},
        types:                 ['address'],
        locationRestriction: {
            north: 39.220849,
            south: 39.121037,
            east: -86.465456,
            west: -86.593859
        }
    });
    autocomplete.addListener('place_changed', function() {
        const p = autocomplete.getPlace();

        document.getElementById('address_string').value = p.formatted_address.split(',')[0];
        document.getElementById('lat' ).value = p.geometry.location.lat();
        document.getElementById('long').value = p.geometry.location.lng();
        map.setCenter(p.geometry.location);
    });
    document.getElementById('address_string').addEventListener('keydown', (e) => {
        if (e.keyCode == 13) { e.preventDefault(); }
    });
}

function chooseLocation()
{
    let c = map.getCenter();

    document.getElementById('lat' ).value = c.lat();
    document.getElementById('long').value = c.lng();

    geocoder.geocode({location: c}, function(results, status) {
        if (status == 'OK') {
            document.getElementById('address_string').value = results[0].formatted_address.split(',')[0];
        }
        else {
            alert('Geocode failed: ' + status);
        }
    });
}

function getCurrentLocation() 
{
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(setCurrentPosition);
    } else { 
      x.innerHTML = "Geolocation is not supported by this browser.";
    }
  }
function setCurrentPosition(position)
{
    const currentCoordinates = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };
    
    map.setCenter(currentCoordinates);

    geocoder.geocode({ location: currentCoordinates }, function (results, status) {
        if (status === 'OK') {
          document.getElementById('address_string').value = results[0].formatted_address.split(',')[0];
        } else {
          alert('Geocode failed: ' + status);
        }
    });
}