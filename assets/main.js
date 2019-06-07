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
        MARK_ADDED: 'mark-added',
        MARK_ADDED_ON_MAP: 'mark-added-on-map'
    };

    var $app = $({}),
        tags = [],
        pins = [],
        restBase = '/wp-json/wp/v2',
        restNonce;

    $app.on(EVENTS.TEMPLATES_LOADED, fetchData);
    $app.on(EVENTS.DATA_FETCHED, renderPage);
    $app.on(EVENTS.PAGE_RENDERED, renderMap);
    $app.on(EVENTS.PAGE_RENDERED, initTagFilter);
    $app.on(EVENTS.CLICK_ON_MAP, createNewMarkerPopup);
    $app.on(EVENTS.INIT_NEW_MARK_MODAL, initTagsInput);
    $app.on(EVENTS.INIT_NEW_MARK_MODAL, initNewMarkForm);
    $app.on(EVENTS.MARK_ADDED_ON_MAP, triggerAppliedTags); // Filter marks when one was added
    $app.on(EVENTS.CLICK_ON_MARKER, initMarkerDetailsModal);

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
        var $mainPromise = $.Deferred(),
            $newMarkPromise = $.Deferred(),
            $markInfoPromise = $.Deferred();

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

        Twig.twig({
            id: "mark-info",
            href: getTemplatePath('popup-marker-info.twig'),
            load: function () {
                $markInfoPromise.resolve();
            }
        });

        return $.when($mainPromise, $newMarkPromise, $markInfoPromise);
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
        var $tagFilters = $('.js-tag-filter');

        $tagFilters.change(triggerAppliedTags);
    }

    function triggerAppliedTags() {
        $app.trigger(EVENTS.APPLIED_TAGS, [getAppliedTags()]);
    }

    function getAppliedTags() {
        var $tagFilters = $('.js-tag-filter');
        var $checked = $tagFilters.filter(':checked');
        var appliedTags = [];

        $checked.each(function (index, tagInput) {
            appliedTags.push( $(tagInput).val() );
        });

        return appliedTags;
    }

    function renderMap(e) {
        var map = new mapboxgl.Map({
            container: 'at-map', // container id
            style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
            center: [-74.50, 40], // starting position [lng, lat]
            zoom: 0 // starting zoom
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

            $marker.click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                $app.trigger(EVENTS.CLICK_ON_MARKER, [pin]);
            });

            $app.on(EVENTS.APPLIED_TAGS, toggleOnFilter);
            $app.trigger(EVENTS.MARK_ADDED_ON_MAP);

            function toggleOnFilter(e, tags) {
                var pinTags = pin['pin-tags'] || [];
                tags = tags || [];

                if (tags.length === 0) {
                    $marker.show();

                    return;
                }

                var hasPinAnyTag = tags.every(function (tag) {
                    return pinTags.indexOf(+tag) !== -1;
                });

                if (hasPinAnyTag) {
                    $marker.show();
                } else {
                    $marker.hide();
                }
            }
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

    function initMarkerDetailsModal(e, pin) {
        var tags = pin['pin-tags'] || [];
        tags = tags.map(function (tagId) {
            var tag = getTags().find(function (tag) {
                return tag.id === tagId;
            });

            return tag.name;
        });

        var date = new Date(pin['date_gmt']);
        date = date.getDay() + '.' + date.getMonth() + '.' + date.getFullYear();

        var modal = Twig.twig({ref: 'mark-info'}).render({
            title: pin.title.rendered,
            tags: tags,
            created_at: date
        });

        $('.js-modal-mark-info-wrap').html(modal);

        var $modal = $('#at-modal-pin-info');

        $modal.modal({
            keyboard: false
        });

        $modal.modal('show');
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
        return $.get(restBase + "/pin-tags?per_page=100");
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