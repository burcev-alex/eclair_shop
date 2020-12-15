'use strict';
$(function () {
    if (!!json_options) {
        if (json_options.enable_delivery_js === 'Y') {
            let api_key = json_options.api_key_yandex_maps;
            if (typeof ymaps !== 'undefined') {
                ymaps.ready(calculateDelivery)
            } else if (!!api_key) {
                let src = "//api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=" + api_key;
                BX.loadScript(src, () => {
                    ymaps.ready(calculateDelivery);
                });
            } else {
                console.error('api-yandex_maps api key key not found');
            }
        } else if (json_options.dadata_enable === 'Y') {
            BX.ready(calculateDelivery);
        }
    }
});

function calculateDelivery() {

    function util() {
        this.order = BX.Sale.OrderAjaxComponent;
    }

    function delivery(option, util) {
        this.map = false;
        this.polygons = false;
        this.placemarks = false;
        this.coordinates = false;
        this.start_option = false;
        this.price_all = false;
        this.util = util;
        this.map_container = 'ya_map_delivery';
        this.main_options = json_options;
        this.order = BX.Sale.OrderAjaxComponent;
        this.message = {};
        this.options = {};
        this.parameters = {};
        this.props = {};
        this.init(option);
    }

    delivery.prototype = {
        init: function (option) {
            this.start_option = option;
            this.sendPost({ action: 'init' }).then(options => {
                this.coordinates = false;
                this.options = options;
                this.message = options.message;
                if (parseFloat(jQuery.fn.jquery) >= 2) {
                    if (options.delivery.enable == 'Y'
                        && typeof ymaps !== 'undefined'
                        && option == 'start') {
                        this.createMaps();
                        $(document).on("click", '.calculate-cost', this.initPolygon.bind(this));
                        $(document).on("click", '.show-route-popup', this.popUp.bind(this));
                    }
                    this.initParameters();
                    this.onSuggestDadata();
                } else {
                    this.util.showLogs(this.message.jquery_version_info)
                }
            });
        },
        /**
         * Yandex
         */
        createMaps: function () {
            const width = window.innerWidth < 550 ? window.innerWidth - (window.innerWidth * 0.05) : '500' + +'px';
            $('<div/>', {
                id: this.map_container,
                css: {
                    display: 'none',
                    width: width,
                    height: '500px',
                }
            }).appendTo('body');
            this.map = new ymaps.Map(this.map_container, {
                center: [55.73, 37.75],
                zoom: 10,
                autoFitToViewport: 'always',
            }, { searchControlProvider: 'yandex#search' });
        },
        initParameters: function () {
            let person_type = this.getTypeForName('PERSON_TYPE'),
                profile_id = this.getTypeForName('PROFILE_ID'),
                delivery_id = this.getSelectedDelivery(), address;

            /**
             * Очистка если были старые куки при старте
             */
            if (this.start_option === 'start'
                && (!!BX.getCookie('yaStopAddress') || !!BX.getCookie('yaRouteLenght') || !!BX.getCookie('yaPrice')))
                this.resetCookie();

            if (person_type > 0) {
                if (this.start_option === 'ajax'
                    && (this.parameters.person_type !== person_type || this.parameters.profile_id !== profile_id))
                    this.resetCookie();

                if (this.options.dadata.enable === 'Y')
                    this.props = this.options.dadata.properties[person_type];

                address = this.util.getField(this.props.location.id).val();

                if (typeof this.parameters.address == "undefined")
                    this.parameters.address = address;

                /**
                 *TODO
                 * автоматический пересчет стоимости при смене доставки
                 */
                // if (typeof this.parameters.delivery !== 'undefined'
                //     && delivery_id.ID !== this.parameters.delivery.ID
                //     && address.length)
                //     this.initPolygon(address, delivery_id.ID);

                this.parameters.person_type = person_type;
                this.parameters.profile_id = profile_id;
                this.parameters.delivery = delivery_id;
                if (typeof this.parameters.error_message !== 'undefined') {
                    this.showError('out_zone');
                } else if (this.options.delivery.enable == 'Y'
                    && !!this.parameters.delivery.CALCULATE_ERRORS
                    && this.options.delivery.array.includes(this.parameters.delivery.ID)) {
                    this.showError('calculate');
                } else {
                    this.order.orderSaveBlockNode.style.display = 'block';
                }
            } else {
                this.util.showLogs('no person type parameters', 'error');
            }
        },
        showError: function (type, text) {
            let address = $('#extra-address');
            switch (type) {
                case 'calculate':
                    this.order.result.ERROR['DELIVERY'] = this.parameters.delivery.CALCULATE_ERRORS;
                    break;
                case 'out_zone':
                    if (typeof this.parameters.delivery.PRICE !== 'undefined') {
                        this.parameters.error_message = this.message.zone_out_disabled;
                        this.resetCookie();
                    } else {
                        delete this.parameters.error_message;
                        this.order.result.ERROR['DELIVERY'] = this.message.zone_out_disabled;
                    }
                    break;
                case 'show_text':
                    this.order.result.ERROR['DELIVERY'] = text;
                    break;
            }

            if (this.options.dadata.enable == 'Y'
                && this.options.delivery.show_input_address == 'Y'
                && !address.val())
                this.order.showValidationResult(address, [address.data('error')]);

            this.order.orderSaveBlockNode.style.display = 'none';
            this.order.showBlockErrors(this.order.deliveryBlockNode);
            this.order.endLoader();
        },
        /*Загружаем зону доставки*/
        polygonLoad: function (json, address) {
            this.map.geoObjects.removeAll();
            this.polygons = this.addToMap(json.z);
            this.placemarks = this.addToMap(json.w);
            this.calculateRoute(address);
        },
        addToMap: function (geoJson) {
            let polygon = ymaps.geoQuery(geoJson).addToMap(this.map);
            polygon.each(function (geo, i) {
                let p = geo.properties,
                    name = p.get('hint-content') || p.get('iconCaption');
                switch (geo.geometry.getType()) {
                    case "Point":
                        geo.options.set({
                            preset: p.get('preset'),
                            iconColor: p.get('marker-color'),
                        });
                        break;
                    case "Polygon":
                        geo.options.set({
                            fillColor: p.get('fill'),
                            fillOpacity: p.get('fill-opacity'),
                            strokeColor: p.get('stroke'),
                            strokeWidth: p.get('stroke-width'),
                            strokeOpacity: p.get('stroke-opacity'),
                            interactivityModel: 'default#transparent',
                            zIndex: i * 10,
                        });
                        break;
                }
                p.set('hintContent', name);
            });

            this.map.setBounds(this.map.geoObjects.getBounds(), {
                checkZoomRange: true,
                zoomMargin: 120
            });
            return polygon;
        },
        initPolygon: function (address, delivery) {
            if (typeof address !== 'string') {
                delivery = address.target.dataset.delivery;
                address = this.parameters.address;
                this.startLoader();
            }

            if (this.props.location.enable == 'N' || !this.coordinates) {
                this.sendPost({ action: 'coords', delivery: delivery }).then(json => {
                        BX.setCookie('yaStopAddress',
                            JSON.stringify({ delivery: delivery, value: address }),
                            { expires: 3600, path: '/' }
                        );
                        if (!!json.z && !!json.w) {
                            this.polygonLoad(json, address);
                        } else {
                            this.util.showLogs('Json is missing for delivery №' + delivery, 'error');
                        }
                    }
                );
            } else {
                this.polygonLoad(this.coordinates, address);
            }

        },
        calculateRoute: function (stop) {
            let _this = this, coords = {};
            ymaps.geocode(stop).then(function (r) {
                    coords.address = r.geoObjects.get(0).geometry.getCoordinates();
                    coords.stock = _this.placemarks.getClosestTo(coords.address);
                    coords.zone_stop = _this.searchContaining(coords.address);
                    if (_this.options.delivery.type_zone_calculate === 'closest_to') {
                        coords.zone_start = _this.polygons.getClosestTo(coords.address);
                    } else if (_this.options.delivery.type_zone_calculate === 'stock_to') {
                        coords.zone_start = _this.polygons.searchContaining(coords.stock).get(0);
                    }
                    // BX.onCustomEvent('yaCalculateRoute', _this.getInfoGeo(coords));
                    if (!coords.zone_stop) {
                        if (_this.options.delivery.disabled_out == 'Y') {
                            _this.showError('out_zone');
                        } else {
                            ymaps.route([coords.stock.geometry.getCoordinates(), coords.address]).then(function (first_route) {
                                let edges = [], routeObjects, boundaryObjects, lastBoundary;
                                ymaps.geoQuery(first_route.getPaths()).each(function (path) {
                                    let coordinates = path.geometry.getCoordinates();
                                    for (let i = 1, l = coordinates.length; i < l; i++) {
                                        edges.push({
                                            type: 'LineString',
                                            coordinates: [coordinates[i], coordinates[i - 1]]
                                        });
                                    }
                                });
                                routeObjects = ymaps.geoQuery(edges).addToMap(_this.map);
                                boundaryObjects = routeObjects.searchIntersect(coords.zone_start);
                                lastBoundary = boundaryObjects.get(boundaryObjects.getLength() - 1).geometry.getCoordinates();
                                routeObjects.each(pm => _this.map.geoObjects.remove(pm));

                                ymaps.route([lastBoundary[0], coords.address], {
                                    mapStateAutoApply: true
                                }).then(function (route) {
                                    let points = route.getWayPoints();
                                    points.get(0).options.set('preset', 'islands#blueDeliveryIcon');
                                    points.get(points.getLength() - 1).options.set('preset', 'islands#blueHomeIcon');
                                    route.getPaths().options.set({
                                        strokeColor: '0000ffff',
                                        opacity: 0.9
                                    });
                                    _this.parameters.bounds_zoom = route;
                                    _this.map.geoObjects.add(route);
                                    _this.calculatePrice('out', coords, route.getLength());
                                }, function (error) {
                                    _this.util.showLogs(error.message)
                                });
                            });
                        }
                    } else {
                        delete _this.parameters.bounds_zoom;
                        let deliveryPoint = new ymaps.GeoObject({
                            geometry: {
                                type: 'Point',
                                coordinates: coords.address
                            },
                        }, {
                            preset: 'islands#blueDeliveryIcon',
                        });
                        _this.parameters.address_coords = coords.address;
                        _this.polygons.setOptions('fillOpacity', 0.3);
                        coords.zone_stop.options.set('fillOpacity', 0.7);
                        _this.map.geoObjects.add(deliveryPoint);
                        _this.calculatePrice('in', coords);
                    }
                }, function (err) {
                    _this.order.endLoader();
                    _this.util.showLogs('Geocode error stop address', 'error');
                }
            );
        },
        calculatePrice: function (type, delivery, distance) {
            let price = 0, d, math_distance,
                delivery_id = this.parameters.delivery.ID;
            if (type == 'in') {
                price = this.getStockPrice({
                    w: delivery.stock.properties.get('delivery'),
                    c: delivery.zone_stop.properties.get('code')
                });
            } else if (type == 'out') {
                if (distance > 0) {
                    d = {
                        w: delivery.stock.properties.get('delivery'),
                        c: delivery.zone_start.properties.get('code'),
                        z: delivery.zone_start.properties.get('delivery'),
                    };
                    let stockPrice = this.getStockPrice(d),
                        zonePrice = this.getZonePrice(d);
                    if (zonePrice) {
                        math_distance = Math.ceil(distance / (parseInt(zonePrice.km) * 1000));
                        price = (math_distance * parseInt(zonePrice.price)) + parseInt(stockPrice);
                    } else if (stockPrice) {
                        price += parseInt(stockPrice);
                    }
                }
            }

            if (distance > 0)
                BX.setCookie('yaRouteLenght', JSON.stringify({
                    delivery: delivery_id,
                    value: distance
                }), { expires: 3600, path: '/' });
            BX.setCookie('yaPrice', JSON.stringify({
                delivery: delivery_id,
                value: price
            }), { expires: 3600, path: '/' });

            this.order.sendRequest();
        },
        getZonePrice: function (d) {
            let zone = false;
            if (!!d.z && !!d.z.zone) {
                zone = d.z.zone;
            } else if (!!d.z && !!d.z.all) {
                zone = d.z.all;
            } else {
                this.util.showLogs('Free shipping outside the area');
            }
            return zone;
        },
        getStockPrice: function (d) {
            let price = 0;
            if (!!d.w && !!d.w.warehouses && !!d.w.warehouses[d.c]) {
                price = d.w.warehouses[d.c];
            } else if (!!d.w && !!d.w.all && !!d.w.all.price) {
                price = d.w.all.price;
            } else {
                this.util.showLogs('Free shipping in zone');
            }
            return parseInt(price);
        },
        /**
         * DaData
         */

        onSuggestDadata: function () {
            if (this.options.dadata.enable == 'Y') {
                for (let id in this.props.order_props) {
                    let field = this.util.getField(id);
                    if (field.length) {

                        field.suggestions(this.initSuggestions({
                            code: this.props.order_props[id], id: id
                        }));
                    }
                }
            }
        },
        getInfoGeo: function (coords) {
            let result = {};
            for (let c in coords) {
                if (c !== 'address' && typeof coords[c] !== 'undefined') {
                    result[c] = {
                        code: coords[c].properties.get('code'),
                        name: coords[c].properties.get('hint-content')
                    };
                } else if (c == 'address') {
                    result[c] = coords[c]
                }
            }
            return result;
        },
        initSuggestions: function (prop) {
            let _this = this, suggestion, addition = {}, props = prop.code;
            if (!!props && typeof props === "object") {
                addition.type = prop.code = props.type;
                if (props.type === 'NAME') {
                    addition.params = { parts: [props.params] };
                } else if (props.type === 'EMAIL') {
                    addition[props.params.toLowerCase()] = false;
                }
            } else {
                addition.type = props;
                addition.geoLocation = props === 'ADDRESS' && this.options.dadata.geoLocation ? this.options.dadata.geoLocation : [];
                // if(props === 'ADDRESS'){
                //     addition.constraints = {
                //         locations: { kladr_id: "5200000000000" }
                //     };
                //     addition.restrict_value = true;
                // }
            }
            suggestion = {
                token: this.options.dadata.api_key,
                partner: this.options.dadata.partner,
                count: this.options.dadata.count_row,
                scrollOnFocus: false,
                onSelect: this.onSuggestion.bind(this, prop),
                onSelectNothing: function () {
                    if (typeof _this.props.suggestions_only == 'object'
                        && _this.props.suggestions_only.includes(prop.id)) {
                        this.value = '';
                        _this.nothingSuggestions(this, prop);
                    }
                },
            };
            return Object.assign(suggestion, addition);
        },
        nothingSuggestions: function (input, prop) {
            if ($("#tooltip-soa-property-" + prop.id).attr('data-state') !== 'open') {
                let container = $(input).parent('.soa-property-container')[0],
                    property = this.order.validation.properties[prop.id],
                    data = this.order.getValidationData(property, container);
                setTimeout(() => this.order.isValidProperty(data, true), 300);
            }
        },
        onSuggestion: function (prop, suggestion) {
            let address_fields, promise,
                location_id, location,
                address = BX.util.trim(suggestion.value),
                old_address = BX.util.trim(this.parameters.address);
            this.suggestion = suggestion.data;
            if (typeof this.props.order_props_before == 'object')
                this.setBeforeProps(this.props.order_props_before[prop.code.toLowerCase()]);
            this.parameters.address = address;
            if (address != old_address && this.props.location.id == prop.id) {
                this.resetCookie(false);
                this.startLoader();
                promise = new Promise((resolve) => {
                    address_fields = this.util.getField(prop.id);
                    location_id = this.util.findInObj('LOCATION', this.props.order_props);
                    location = {
                        field: this.util.getField(location_id),
                        data: {
                            action: 'location',
                            CITY: suggestion.data.city,
                            SUBREGION: (suggestion.data.area_type_full == 'район') ? suggestion.data.area : '',
                            REGION: suggestion.data.region,
                            DELIVERY: [this.parameters.delivery.ID],
                            PERSON_TYPE: this.parameters.person_type
                        }
                    };
                    /**
                     * TODO
                     *   Отфильтровываем все поселки и деревни
                     *   Данный функционал отключен
                     if (~_this.const.settlement.indexOf(suggestion.data.settlement_type) &&
                     !~_this.const.settlementExc.indexOf(suggestion.data.region)) {
           $(_this.getField(parent)).val('settlement');
           BX.Sale.OrderAjaxComponent.sendRequest();
           console.log('settlement location, restart');
           }
                     */
                    if (this.props.location.enable === 'Y') {
                        this.sendPost(location.data).then(
                            res => {
                                if (!!res.DELIVERY) {
                                    if (address_fields.length > 1)
                                        address_fields.val(address);

                                    if (location.field.val() !== res.CODE)
                                        location.field.val(res.CODE);

                                    if (!!res.coordinates.z && !!res.coordinates.w) {
                                        this.coordinates = res.coordinates;
                                    } else {
                                        this.util.showLogs('Json is missing for delivery №' + delivery, 'error');
                                    }
                                    resolve(parseInt(res.DELIVERY));
                                } else {
                                    this.util.showLogs(res, 'error');
                                    this.showError('show_text', res);
                                }
                            },
                            errors => {
                                this.util.showLogs(errors, 'error');
                            });
                    } else {
                        resolve(parseInt(this.parameters.delivery.ID));
                    }
                });

                promise.then(id => {
                    if (this.util.isInteger(parseInt(id))
                        && this.options.delivery.enable == 'Y'
                        && this.options.delivery.array.includes(this.parameters.delivery.ID)) {
                        this.initPolygon(address, this.parameters.delivery.ID);
                    } else {
                        this.util.showLogs('Delivery to the selected address is not possible');
                        this.order.sendRequest();
                    }
                    this.util.showLogs('New address, refresh order and calculate delivery!');
                });
            }
        },
        /**
         * Запись свойств после определения данных от DaDada
         * @param before
         */
        setBeforeProps: function (before) {
            let beforeSugg, beforeData, field;
            for (let id in before) {
                beforeSugg = before[id].split('.');
                beforeData = this.getBeforeProps(beforeSugg);
                field = this.util.getField(id);
                if (field.length && beforeData !== null) {
                    if (before[id].includes('date'))
                        beforeData = this.util.formatDate(beforeData);
                    field.val(beforeData);
                } else {
                    field.val('');
                }

                if (before[id] === 'postal_code')
                    sessionStorage.setItem('postal_code', JSON.stringify({ field: id, code: beforeData }));
            }
        },
        searchContaining: function (address) {
            const zoneContaining = this.polygons.searchContaining(address);
            if (zoneContaining.getLength() === 0) {
                return false;
            } else if (zoneContaining.getLength() === 1) {
                return zoneContaining.get(0);
            } else {
                let containing = { i: 0, zIndex: 0 };
                zoneContaining.each(function (zone, i) {
                    if (zone.options.get('zIndex') > containing.zIndex)
                        containing = { i: i, zIndex: zone.options.get('zIndex') }
                });
                return zoneContaining.get(containing.i);
            }
        },
        getBeforeProps: function (beforeSugg) {
            let beforeData;
            if (beforeSugg.length > 1) {
                beforeData = beforeSugg.reduce(function (suggestions, data) {
                    return suggestions[data] || false;
                }, this.suggestion);
            } else {
                if (beforeSugg[0] == 'street') {
                    beforeData = `${this.suggestion['street_type_full']} ${this.suggestion[beforeSugg[0]]}`
                } else {
                    beforeData = this.suggestion[beforeSugg[0]];
                }
            }
            if (!beforeData)
                beforeData = this.changeBeforeProps(beforeSugg);
            return beforeData;
        },
        changeBeforeProps: function (beforeSugg) {
            let before = false;
            if ('type' in this.suggestion) {
                if (beforeSugg[0] == 'management' && beforeSugg[1] == 'name') {
                    before = ['name', 'full'];
                }
            } else if ('bic' in this.suggestion) {
                if (beforeSugg[0] == 'name' && beforeSugg[1] == 'full') {
                    before = ['name', 'payment'];
                }
            }
            if (!!before) {
                return this.getBeforeProps(before);
            }
        },
        sendPost: function (data) {
            let url = this.main_options.path_core;
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: url,
                    type: "POST",
                    method: "POST",
                    data: data,
                    dataType: "json"
                }).done(function (res) {
                    resolve(res);
                }).fail(function (errors) {
                    reject(errors);
                });
            });
        },
        getTypeForName: function (name) {
            let type = $(`[name=${name}]`);
            if (type.length > 1) {
                return type.filter(":checked").val()
            } else {
                return type.val();
            }
        },
        getSelectedDelivery: function () {
            let deliveryCheckbox = $('input[type=checkbox][name=DELIVERY_ID]:checked'),
                currentDelivery = false, deliveryId, i,
                deliveries = this.order.result.DELIVERY;

            if (!deliveryCheckbox)
                deliveryCheckbox = $('input[type=hidden][name=DELIVERY_ID]');

            if (deliveryCheckbox) {
                deliveryId = deliveryCheckbox.val();

                for (i in deliveries) {
                    if (deliveries[i].ID == deliveryId) {
                        currentDelivery = deliveries[i];
                        break;
                    }
                }
            }
            return currentDelivery;
        },
        resetCookie: function (reset) {
            this.util.showLogs('resetCookie');
            const cookieNames = ['yaErrorMessage', 'yaPrice', 'yaRouteLenght', 'yaStopAddress'];
            cookieNames.map((name) => this.util.deleteCookie(name));
            if (typeof reset === 'undefined')
                this.order.sendRequest();
        },
        startLoader: function () {
            this.order.startLoader();
            this.order.BXFormPosting = false;
        },
        popUp: function () {
            BX.PopupWindowManager.create("mapPopup", null, {
                autoHide: true,
                offsetLeft: 0,
                offsetTop: 0,
                overlay: true,
                draggable: { restrict: true },
                closeByEsc: true,
                titleBar: this.message.title_popup,
                closeIcon: { right: "12px", top: "10px" },
                content: BX(this.map_container),
                events: {
                    onAfterPopupShow: BX.delegate(function () {
                        this.map.container.fitToViewport();
                        if (!!this.parameters.bounds_zoom) {
                            this.map.setBounds(this.map.geoObjects.getBounds());
                        } else {
                            this.map.setCenter(this.parameters.address_coords, 12);
                        }
                    }, this)
                }
            }).show();
        }
    };

    util.prototype = {
        formatDate: function (d) {
            let date = new Date(d),
                options = {
                    day: "2-digit",
                    month: "2-digit",
                    year: "numeric",
                };
            return date.toLocaleString("ru", options);
        },
        getField: function (id) {
            return $(`[name=ORDER_PROP_${id}]`);
        },
        deleteCookie: function (name) {
            BX.setCookie(name, "", { expires: -1, path: "/" })
        },
        findInObj: function (key, props) {
            return Object.keys(props).find(k => props[k] === key);
        },
        isInteger: function (n) {
            return typeof n === 'number'
                && Number.isFinite(n)
                && !(n % 1);
        },
        showLogs: function (message, type) {
            if (json_options.console_logs == 'Y') {
                switch (type) {
                    case 'warn':
                        console.warn(message);
                        this.order.endLoader();
                        break;
                    case 'error':
                        console.error(message);
                        this.order.endLoader();
                        break;
                    default:
                        console.log(message);
                        break;
                }
            }
        }
    };

    try {
        /**
         * Инициализирует и запуска подсказки и расчеты
         */
        var util = new util(),
            delivery = new delivery('start', util);

        /*
         * Инициализируем после перезапуска чекаута
         * */
        BX.addCustomEvent("onAjaxSuccess", BX.delegate(function () {
            delivery.init('ajax');
        }, this));
    }
    catch (e) {
        console.error(e.message);
    }
}