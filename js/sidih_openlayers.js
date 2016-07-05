var lon = 673815;
var lat = 455182;
var zoom = 1;
var map, layer;
var extent
 
function onFeatureSelect(feature) {

    selectedFeature = feature;

    popup = new OpenLayers.Popup.FramedCloud("", feature.geometry.getBounds().getCenterLonLat(),

    new OpenLayers.Size(100,100), "<div class='map_popup'>"+feature.attributes.desc+"</div>", null, true, onPopupClose);

    feature.popup = popup;
    map.addPopup(popup);

}

function onPopupClose(evt) {
    selectControl.unselect(selectedFeature);
}

function onFeatureUnselect(feature) {

    map.removePopup(feature.popup);
    feature.popup.destroy();
    feature.popup = null;

}
