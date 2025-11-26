
<script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />

<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css" type="text/css">


@isset($separator)
    <br />
    <h4 class="display-4 mb-0">{{ __($separator) }}</h4>
    <hr />
@endisset
<div id="form-group-{{ $id }}" class="form-group{{ $errors->has($id) ? ' has-danger' : '' }}  @isset($class) {{$class}} @endisset">
     @if(!(isset($type)&&$type=="hidden"))
        <label class="form-control-label" for="{{ $id }}">{{ __($name) }}@isset($link)<a target="_blank" href="{{$link}}">{{$linkName}}</a>@endisset</label>
    @endif
    <div id='map' style='height: 500px; width:600px' class="w-100"></div>
    <input @isset($lat) value="{{ $lat }}" @endisset type="hidden" name="lat" id="lat" class="form-control form-controll"  >
    <input @isset($lng) value="{{ $lng }}" @endisset type="hidden" name="lng" id="lng" class="form-control form-controll"  >
    
    @isset($additionalInfo)
        <small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>

<script>
    var startingLat="0";
    var startingLng="0";
</script>

@isset($lat)
<script>
    startingLat="<?php echo $lat; ?>";
    startingLng="<?php echo $lng; ?>";
</script>
@endisset


<script>
    var startingCoordinate=[startingLng, startingLat];

    mapboxgl.accessToken = "{{ config('fields.token')}}";
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/satellite-v9', // style URLss
        center: startingCoordinate, // starting position [lng, lat]
        zoom: 14, // starting zoom
        // pitch: 60,
    });

     /* Given a query in the form "lng, lat" or "lat, lng"
        * returns the matching geographic coordinate(s)
        * as search results in carmen geojson format,
        * https://github.com/mapbox/carmen/blob/master/carmen-geojson.md */
        const coordinatesGeocoder = function (query) {
            // Match anything which looks like
            // decimal degrees coordinate pair.
            const matches = query.match(
            /^[ ]*(?:Lat: )?(-?\d+\.?\d*)[, ]+(?:Lng: )?(-?\d+\.?\d*)[ ]*$/i
            );
            if (!matches) {
            return null;
            }
            
            function coordinateFeature(lng, lat) {
                return {
                    center: [lng, lat],
                    geometry: {
                        type: 'Point',
                        coordinates: [lng, lat]
                    },
                    place_name: 'Lat: ' + lat + ' Lng: ' + lng,
                    place_type: ['coordinate'],
                    properties: {},
                    type: 'Feature'
                };
            }
            
            const coord1 = Number(matches[1]);
            const coord2 = Number(matches[2]);
            const geocodes = [];
            
            if (coord1 < -90 || coord1 > 90) {
                // must be lng, lat
                geocodes.push(coordinateFeature(coord1, coord2));
            }
            
            if (coord2 < -90 || coord2 > 90) {
                // must be lat, lng
                geocodes.push(coordinateFeature(coord2, coord1));
            }
            
            if (geocodes.length === 0) {
                // else could be either lng, lat or lat, lng
                geocodes.push(coordinateFeature(coord1, coord2));
                geocodes.push(coordinateFeature(coord2, coord1));
            }
            
            return geocodes;
        };
 
        // Add the control to the map.
        map.addControl(
            new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                localGeocoder: coordinatesGeocoder,
                zoom: 4,
                placeholder: 'Search...',
                mapboxgl: mapboxgl,
                reverseGeocode: true
            })
        );

        // Add geolocate control to the map.
        map.addControl(
            new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                // When active the map will receive updates to the device's location as it changes.
                trackUserLocation: true,
                // Draw an arrow next to the location dot to indicate which direction the device is heading.
                showUserHeading: true
            })
        );

        // Add zoom and rotation controls to the map.
        map.addControl(new mapboxgl.NavigationControl());

        map.addControl(new mapboxgl.FullscreenControl());

        


        const marker = new mapboxgl.Marker({
            draggable: true
        })
        .setLngLat(startingCoordinate)
        .addTo(map);
        
        function setLatLng(lat,lng){
            $('#lat').val(lat);
            $('#lng').val(lng);
        }

        function onDragEnd() {
            const lngLat = marker.getLngLat();
            
            setLatLng(lngLat.lat,lngLat.lng)
        }
        marker.on('dragend', onDragEnd);

        map.on('dblclick', (e) => {
            setLatLng(e.lngLat.lat,e.lngLat.lng)
            //marker.setLngLat([e.lngLat.lng, e.lngLat.lat]);
        });

        map.on('moveend', function(e){
            const {lng, lat} = map.getCenter();
            marker.setLngLat([lng, lat]);
            setLatLng(lat,lng)
        });
</script>
