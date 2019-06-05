(function ($) {
    if (mapboxgl) {
        mapboxgl.accessToken = getMapToken();
    }

    $(document).ready(runApp);

    function runApp() {
        // var map = new mapboxgl.Map({
        //     container: 'map', // container id
        //     style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
        //     center: [-74.50, 40], // starting position [lng, lat]
        //     zoom: 9 // starting zoom
        // });
    }

    function getMapToken() {
        var agentfire = window.agentfire || {};

        return agentfire.mapsToken || '';
    }
})(jQuery);