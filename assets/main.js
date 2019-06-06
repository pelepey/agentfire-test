(function ($, Twig) {
    var $mapArea,
        restBase = '/wp-json/wp/v2',
        restNonce;

    try {
        restBase = window.agentfire.rest.root + 'wp/v2';
        restNonce = window.agentfire.rest.nonce;
    } catch (e) {
        restBase = '/wp-json/wp/v2';
    }

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
            fetchPins()
        ).then(function (tagsRes, pinsRes) {
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

                var pin = {
                    title: 'Pin',
                    status: 'publish',
                    meta: {
                        lng: e.lngLat.lng,
                        lat: e.lngLat.lat
                    }
                };

                addPin(pin).done(function (res) {
                    console.log(res);
                });
            });

            console.log(pinsRes);
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

    function fetchPins() {
        return $.get(restBase + "/pins");
    }

    /**
     * @param pin {Object}
     *
     * @return
     */
    function addPin(pin) {
        return $.ajax({
            method: 'POST',
            url: restBase + "/pins",
            data: pin,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restNonce );
            }
        });
    }
})(jQuery, Twig);