(function ($, Twig) {
    var EVENTS = {
        TEMPLATES_LOADED: 'templates-loaded',
        TAGS_FETCHED: 'tags-fetched',
        MARKERS_FETCHED: 'markers-fetched',
        DATA_FETCHED: 'data-fetched',
        PAGE_RENDERED: 'page-rendered',
        APPLIED_TAGS: 'applied-tags',
        CLICK_ON_MAP: 'click-on-map',
        CLICK_ON_MARKER: 'click-on-MARKER',
        INIT_NEW_MARK_MODAL: 'init-new-mark-modal',
        MARK_ADDED: 'mark-added'
    };

    var $app = $({}),
        tags = [],
        pins = [],
        restBase = '/wp-json/wp/v2',
        restNonce;

    $app.on(EVENTS.TEMPLATES_LOADED, fetchData);
    $app.on(EVENTS.DATA_FETCHED, renderPage);
    $app.on(EVENTS.PAGE_RENDERED, renderMap);
    $app.on(EVENTS.CLICK_ON_MAP, createNewMarkerPopup);
    $app.on(EVENTS.INIT_NEW_MARK_MODAL, initTagsInput);
    $app.on(EVENTS.INIT_NEW_MARK_MODAL, initNewMarkForm);

    $(document).ready(runApp);

    function runApp() {
        try {
            restBase = window.agentfire.rest.root + 'wp/v2';
            restNonce = window.agentfire.rest.nonce;
        } catch (e) {
            restBase = '/wp-json/wp/v2';
        }

        if (mapboxgl) {
            mapboxgl.accessToken = getMapToken();
        }

        loadTemplates().then(function () {
            $app.trigger(EVENTS.TEMPLATES_LOADED);
        });
    }

    function loadTemplates() {
        var $mainPromise = $.Deferred();
        var $newMarkPromise = $.Deferred();

        Twig.twig({
            id: "main",
            href: getTemplatePath('main.twig'),
            load: function () {
                $mainPromise.resolve();
            }
        });

        Twig.twig({
            id: "new-mark",
            href: getTemplatePath('new-mark.twig'),
            load: function () {
                $newMarkPromise.resolve();
            }
        });

        return $.when($mainPromise, $newMarkPromise);
    }

    function fetchData() {
        $.when(
            fetchTags(),
            fetchPins()
        ).then(function (tagsRes, pinsRes) {
            var tags = tagsRes[0];
            var pins = pinsRes[0];

            setTags(tags);
            setPins(pins);

            $app.trigger(EVENTS.DATA_FETCHED);
        });
    }

    function renderPage(e) {
        var $mapArea = $('#at-map-page');
        var page = Twig.twig({ref: "main"}).render({
            tags: getTags()
        });
        var $page = $(page);

        $mapArea.html($page);

        $app.trigger(EVENTS.PAGE_RENDERED);
    }
    
    function initTagFilter() {
        
    }

    function renderMap(e) {
        var map = new mapboxgl.Map({
            container: 'at-map', // container id
            style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
            center: [-74.50, 40], // starting position [lng, lat]
            zoom: 9 // starting zoom
        });

        getPins().forEach(addToMapPin);

        map.on('click', function (e) {
            var coordinates = {
                lng: e.lngLat.lng,
                lat: e.lngLat.lat
            };

            $app.trigger(EVENTS.CLICK_ON_MAP, [coordinates]);
        });

        $app.on(EVENTS.MARK_ADDED, function (e, pin) {
            addToMapPin(pin);
        });

        function addToMapPin(pin) {
            if (
                !(
                    pin.meta.lng &&
                    pin.meta.lat
                )
            ) {
                return;
            }

            var marker = new mapboxgl.Marker();
            var $marker = $(marker.getElement());

            marker.setLngLat(pin.meta);

            marker.addTo(map);
        }
    }

    function createNewMarkerPopup(e, coordinates) {
        initNewMarkModal(coordinates);
    }

    function initNewMarkModal(coordinates) {
        var modal = Twig.twig({ref: "new-mark"}).render({
            coordinates: coordinates,
            action: restBase + "/pins",
            tags: tags
        });

        $('.js-new-mark-modal-wrap').html(modal);

        var $modal = $('#at-new-pin-modal');

        $modal.modal({
            keyboard: false
        });

        $modal.modal('show');

        $app.on(EVENTS.MARK_ADDED, function () {
            $modal.modal('hide');
        });

        $app.trigger(EVENTS.INIT_NEW_MARK_MODAL);
    }

    function initTagsInput() {
        var $tagsInput = $('#at-mark-tag-input');

        $tagsInput.chosen({
            disable_search_threshold: 10,
            width: "100%"
        });
    }

    function initNewMarkForm() {
        var $form = $('#at-new-marker-form');

        $form.submit(handleFormSubmit);
    }

    /**
     * @param e
     * @return void
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        var data = new FormData(e.target);

        addPin(data)
            .then(function (pin) {
                $app.trigger(EVENTS.MARK_ADDED, [pin]);
            }, function (e) {
                console.error(e);
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

    function setTags(_tags) {
        tags = _tags;
    }

    function setPins(_pins) {
        pins = _pins;
    }

    function getTags() {
        return tags;
    }

    function getPins() {
        return pins;
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