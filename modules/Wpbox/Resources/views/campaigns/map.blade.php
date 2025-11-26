@section('head')
<style>
    .custom-marker{position: absolute;display: block;-webkit-transform: translate(-50%, -50%);transform: translate(-50%, -50%);z-index: 1;cursor: pointer;}
    .custom-marker:hover{z-index: 2;}
    .custom-marker.hide > * {display: none !important;}
    .overview-marker > .marker{width: 8px;height: 8px;background: #ffffff;-webkit-border-radius: 50%;border-radius: 50%;}
    .overview-marker.xl > .marker{width: 20px;height: 20px;}
    .overview-marker.lg > .marker{width: 14px;height: 14px;}
    .overview-marker.sm > .marker{width: 6px;height: 6px;}
    .overview-marker > .marker:before, .overview-marker > .marker:after{content: " ";position: absolute;top: 50%;left: 50%;width: 128px;height: 128px;border: 20px solid #fffff;border-radius: 50%;-webkit-animation: map-marker-pulse 1s linear infinite;animation: map-marker-pulse 1s linear infinite;visibility: hidden;z-index: -1;}
    .overview-marker > .marker:after {animation-delay: .3s;background: #FFF;border: none;}
    .overview-marker > .info{position: absolute;top: 100%;left: 50%;-webkit-transform: translate(-50%, 16px);transform: translate(-50%, 16px);background: #233d57;padding: 12px;text-align: center;border-radius: 3px;opacity: 0;-webkit-transition: all .1s ease-in-out;transition: all .1s ease-in-out;}
    .overview-marker:not(:hover) > .info{pointer-events: none;}
    .overview-marker:hover > .info{transition-delay: .1;opacity: 1;-webkit-transform: translate(-50%, 8px);transform: translate(-50%, 8px);}
    .overview-marker > .info > .country-name{color: #e9eff3;font-weight: 600;font-size: 10px;letter-spacing: 0;text-transform: uppercase;}
    .overview-marker > .info > .number{font-weight: 700;margin-top: 4px;font-size: 24px;color: #FFF;padding: 0 16px;}
    @-webkit-keyframes map-marker-pulse{
      0%{border-width:20px;opacity:.5;-webkit-transform:translateZ(0) translate(-50%, -50%) scale(0);visibility: visible;}
      to{border-width:0;opacity:0;-webkit-transform:translateZ(0) translate(-50%, -50%) scale(1);visibility: visible;}
    }
    @keyframes map-marker-pulse{
      0%{border-width:10px;opacity:.5;transform:translateZ(0) translate(-50%, -50%) scale(0);visibility: visible;}
      to{border-width:0;opacity:0;transform:translateZ(0) translate(-50%, -50%) scale(1);visibility: visible;}
    }
</style>
@endsection
<div class="row">
    <div class="col mt--6">
        <div class="card border-0">
            <div data-settings='map_options' id="mapcampign" class="mapstats map-canvas" style="height: 500px; position: relative; overflow: hidden;">
            </div>
        </div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('wpbox.google_maps_api_key',"") }}&libraries=visualization"></script>
<script>

    <?php echo "var countries_count = ". json_encode($setup['countriesCount']) . ";\n"; ?>
    theItems=[];
    countries_count.forEach(cc => {
                var theItem=cc;
                theItem.weight=1;
                theItem.marker={ 'html' : '<a class="overview-marker xl"><div onClick="" class="marker"></div><div class="info"><div class="country-name">'+cc.name+'</div><div class="number">Messages:'+cc.number_of_messages+'</div></div></a>'};
                theItems.push(theItem);
              });
    
    var map_options = {
                'items' : theItems
    };

    initMapStats = function(){
        
            if (typeof(google) == "undefined") return false;
            alert("Google is ok");
            
    }

    window.onload = function () {

        
        class MapLocationIcon extends google.maps.OverlayView{
                constructor(latlng, html, map){
                    super();
                    this.latlng = latlng;
                    this.html = html;
                    this.setMap(map);
                }
                onAdd(){
                    var div = document.createElement('DIV');
                    $(div).addClass("custom-marker");
                    this.div_ = div;
                    $(div).html(this.html);
                    var panes = this.getPanes();
                    this.getPanes().overlayImage.appendChild( div );
                }
                hide(){
                    if (this.div_){ this.div_.classList.add("hide"); }
                }
                show(){
                    if (this.div_){ this.div_.classList.remove("hide"); }
                }
                draw(){
                    var overlayProjection = this.getProjection();
                    var position = overlayProjection.fromLatLngToDivPixel(this.latlng);
                    var div = this.div_;
                    div.style.left = position.x + 'px';
                    div.style.top = position.y + 'px';
                }
                onRemove(){}
            }


        $('.mapstats').each(function($pos, element) {
  
            var $this = $(this);
            var $mapclass = "mapstats_" + $pos;
            
            $this.addClass( $mapclass );
            var $settings = eval( $this.attr("data-settings") );

            if (typeof($settings) == "object"){
                $settings.style="blue";
                var $bounds = new google.maps.LatLngBounds();
                var $mapOptions = {
                zoom: 3,
                center: new google.maps.LatLng(40.748817, -73.985428),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                fullscreenControl: false, mapTypeControl: false, panControl : false, scrollwheel: false, streetViewControl: false,
                zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL,position: google.maps.ControlPosition.LEFT_TOP},
                styles : [{"featureType":"administrative","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"color":"#ffffff"},{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"administrative.country","elementType":"labels.text.stroke","stylers":[{"color":"#236f09"},{"visibility":"on"}]},{"featureType":"administrative.neighborhood","stylers":[{"visibility":"off"}]},{"featureType":"landscape","stylers":[{"color":"#5ea830"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"color":"#eff5f9"},{"visibility":"simplified"}]},{"featureType":"water","elementType":"labels.text","stylers":[{"visibility":"off"}]}]
                };
                if (typeof($settings.style) != "undefined" && $settings.style == "blue"){
                $mapOptions.styles = [{"featureType":"administrative","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"color":"#ffffff"},{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"administrative.country","elementType":"labels.text.stroke","stylers":[{"color":"#2A3C50"},{"visibility":"on"}]},{"featureType":"administrative.neighborhood","stylers":[{"visibility":"off"}]},{"featureType":"landscape","stylers":[{"color":"#435970"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"color":"#eff5f9"},{"visibility":"simplified"}]},{"featureType":"water","elementType":"labels.text","stylers":[{"visibility":"off"}]}]
                }
                var $map = new google.maps.Map($this[0], $mapOptions);
                $map.__filter_items = [];
                var $heatmap = new google.maps.visualization.HeatmapLayer({ dissipating: false, gradient : ["#EFF5F9", "rgba(239,245,249,0)"], gradient : ["rgba(0,143,251,0)", "#233d57"], radius : 30, opacity: .5, maxIntensity: 1, data: [] });
                $.each( $settings.items, function(i, e){
                var $position = new google.maps.LatLng(e.lat, e.lng);
                $heatmap.data.push( { location: $position, weight: e.weight });
                $bounds.extend($position);
                if (typeof e.marker != "undefined"){
                    var $marker_settings = { position : $position };
                    Object.assign($marker_settings, e.marker);
                    var icon = new MapLocationIcon( $position,  e.marker.html, $map );
                    $map.__filter_items.push( { 'item' : e,  'icon' : icon });

                }
                });
                $map.fitBounds( $bounds );
                $heatmap.setMap($map);
                google.maps.event.addListenerOnce($map, 'idle', function(){
                           // js.mapFilter($map, $settings);
                });
            }



        });

        

       
    }



</script>
