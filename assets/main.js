(function ($, Twig) {
    var $mapArea,
        tags = [],
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

        Twig.twig({
            id: "new-mark",
            href: getTemplatePath('new-mark.twig')
        });
    }

    function renderPage(template) {
        $.when(
            fetchTags(),
            fetchPins()
        ).then(function (tagsRes, pinsRes) {
            tags = tagsRes[0];

            var page = template.render({tags: tags});
            var $page = $(page);

            $mapArea.html($page);

            var map = new mapboxgl.Map({
                container: 'at-map', // container id
                style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
                center: [-74.50, 40], // starting position [lng, lat]
                zoom: 9 // starting zoom
            });

            map.on('click', handleMapClick);
        });
    }

    function handleMapClick(e) {
        var coordinates = {
            lng: e.lngLat.lng,
            lat: e.lngLat.lat
        };

        var pin = {
            title: 'Pin',
            status: 'publish',
            meta: coordinates
        };

        initNewMarkModal(coordinates);

        // addPin(pin).done(function (res) {
        //     console.log(res);
        // });
    }

    function initNewMarkModal(coordinates) {
        var modal = Twig.twig({ref: "new-mark"}).render({
            coordinates: coordinates,
            action: restBase + "/pins",
            tags: tags
        });

        $('.js-new-mark-modal-wrap').html(modal);

        var $modal = $('#at-new-pin-modal');
        var $tagsInput = $('#at-mark-tag-input');
        var $form = $('#at-new-marker-form');

        $modal.modal({
            keyboard: false
        });
        $tagsInput.chosen({
            disable_search_threshold: 10,
            width: "100%"
        });

        $modal.modal('show');
        $form.submit(handleFormSubmit);

        function handleFormSubmit(e) {
            e.preventDefault();

            var data = new FormData(e.target);

            addPin(data).then(function (res) {
                console.log(res);
            });
        }
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
            processData: false,
            contentType: false,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', restNonce );
            }
        });
    }
})(jQuery, Twig);