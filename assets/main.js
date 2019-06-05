(function ($) {
    if (mapboxgl) {
        mapboxgl.accessToken = getMapToken();
    }

    $(document).ready(runApp);

    function runApp() {
    }

    function getMapToken() {
        var agentfire = window.agentfire || {};

        return agentfire.mapsToken || '';
    }
})(jQuery);