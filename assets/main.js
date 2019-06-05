(function ($, Twig) {
    var $mapArea,
        restBase = '/wp-json/wp/v2';

    if (mapboxgl) {
        mapboxgl.accessToken = getMapToken();
    }

    $(document).ready(runApp);

    function runApp() {
        $mapArea = $('#at-map-area');

        var template = Twig.twig({
            id: "main",
            href: getTemplatePath('main.twig'),
            load: renderPage
        });
    }

    function renderPage(template) {
        $.when(
            fetchTags(),
            fetchTags()
        ).then(function (tagsRes) {
            var tags = tagsRes[0];

            var page = template.render({tags: tags});
            var $page = $(page);

            $mapArea.html($page);

            var map = new mapboxgl.Map({
                container: 'at-map', // container id
                style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
                center: [-74.50, 40], // starting position [lng, lat]
                zoom: 9 // starting zoom
            });

            map.on('click', function (e) {
                console.log(e);
            });
        });
    }

    /**
     * @param tpl {string}
     * @returns {string}
     */
    function getTemplatePath(tpl) {
        return getTemplatesBase() + tpl;
    }

    /**
     * @returns {string}
     */
    function getTemplatesBase() {
        var agentfire = window.agentfire || {};

        return agentfire.tplBaseUrl || '';
    }

    /**
     * @returns {string}
     */
    function getMapToken() {
        var agentfire = window.agentfire || {};

        return agentfire.mapsToken || '';
    }

    function fetchTags() {
        return $.get(restBase + "/pin-tags");
    }
})(jQuery, Twig);