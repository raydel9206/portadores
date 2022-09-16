Ext.onReady(function () {
    const map = new ol.Map({
        // target: 'map',
        layers: [],
        controls: [
            new ol.control.ScaleLine({units: 'metric'}),
            // new ol.control.FullScreen(),
            new ol.control.MousePosition({
                coordinateFormat: ol.coordinate.createStringXY(4),
                undefinedHTML: '<i class="fas fa-map-marker-alt"></i>'
            }),
            new ol.control.Control({
                element: document.getElementById('toolbar_control') // top-left
            }),
            new ol.control.Control({
                element: document.getElementById('logger_control') // top-right
            })
        ],
        view: new ol.View({
            projection: 'EPSG:4326',
            center: [-79.5, 22.214],
            extent: [-84.9559, 19.8251, -74.1324, 23.2729],
            zoom: 7,
            minZoom: 7,
            maxZoom: 18
        }),
        interactions: ol.interaction.defaults().extend([
            new ol.interaction.Select({ // hover
                layers: function (layer) {
                    return layer.getZIndex() === 3 || layer.getZIndex() === 999999;
                },
                condition: ol.events.condition.pointerMove,
                multi: true
            })
        ]),
        overlays: [
            new ol.Overlay({
                id: 'tooltip_overlay',
                element: document.getElementById('tooltip_overlay'),
                offset: [10, -10],
                positioning: 'bottom-left'
            })
        ]
    });

    map.initialize = function (geoserver_url, ol_version) {
        App.debug('Initializing the Map...')
            .debug(' > Libraries:', 'Openlayers@' + ol_version)
            .debug(' > GeoServer URL:', '"' + geoserver_url + '"');

        const roads_layer = new ol.layer.Tile({
            title: 'OpenStreetMap',
            fa_icon: 'far fa-object-group',
            fa_icon_color: '#117a8b',
            source: new ol.source.TileWMS({
                url: geoserver_url + 'wms',
                params: {layers: 'GISROUTE:OSM_ROADS'}
            }),
            // visible: false,
            zIndex: 1
        });

        roads_layer.getSource().on('tileloaderror', function (evt) {
            panel.showMsg('<i class="fas fa-exclamation-triangle"></i>&nbsp;<span>La capa ' + evt.tile.key.split('-')[1] + ' no se pudo cargar.</span>');
        });

        // -------------------------------------------------------------------------------------------------------------

        const routes_style = new ol.style.Style({
                fill: new ol.style.Fill({
                    color: '#B4DFB4'
                }),
                stroke: new ol.style.Stroke({
                    color: '#88B588',
                    width: 3,
                })
            }),
            routes_layer = new ol.layer.Vector({
                title: 'Rutas',
                fa_icon: 'fas fa-route',
                fa_icon_color: routes_style.getStroke().getColor(),
                source: new ol.source.Vector({
                    url: geoserver_url + 'ows?service=WFS&version=1.0.0&request=GetFeature&typeName=GISROUTE:ROUTES&outputFormat=application/json',
                    format: new ol.format.GeoJSON({
                        geometryName: 'the_geom'
                    })
                }),
                zIndex: 2,
                style: routes_style,
            });

        // -------------------------------------------------------------------------------------------------------------

        const points_style = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 4,
                    fill: new ol.style.Fill({
                        color: '#ffc107'
                    })
                }),
                text: new ol.style.Text({
                    font: '14px Calibri,sans-serif',
                    fill: new ol.style.Fill({color: '#000'}),
                    stroke: new ol.style.Stroke({
                        color: '#f8f9fa', width: 2
                    }),
                    offsetY: -14,
                })
            }),
            points_layer = new ol.layer.Vector({
                title: 'Puntos de interés',
                fa_icon: 'fas fa-map-marker-alt',
                fa_icon_color: points_style.getImage().getFill().getColor(),
                source: new ol.source.Vector({
                    url: geoserver_url + 'ows?service=WFS&version=1.0.0&request=GetFeature&typeName=GISROUTE:POI&outputFormat=application/json',
                    format: new ol.format.GeoJSON({
                        geometryName: 'the_geom'
                    })
                }),
                zIndex: 3,
                style: function (feature, resolution) {
                    points_style.getText().setText(map.getView().getZoom() >= 10 ? feature.get('point_name') : '');
                    return points_style;
                },
            });

        map.addLayer(roads_layer);
        map.addLayer(routes_layer);
        map.addLayer(points_layer);

        map.getLayers().forEach(function (layer) {
            layer.on('change:visible', function (evt) {
                panel.showMsg('<i class="fas fa-lightbulb"></i>&nbsp;<span>La capa <strong>' + evt.target.get('title') + '</strong> ha sido ' + (evt.oldValue ? 'desactivada' : 'activada') + '.</span>');
            });
        });

        // -------------------------------------------------------------------------------------------------------------

        map.on('pointermove', function (evt) {
            const tooltip = map.getOverlayById('tooltip_overlay'),
                feature = map.forEachFeatureAtPixel(evt.pixel, function (feature) {
                    const data = feature.get(feature.getGeometry().getType() === 'Point' ? 'point_name' : 'route_name'),
                        icon_cls = feature.getGeometry().getType() === 'Point' ? 'fas fa-map-marked-alt' : 'fas fa-route';

                    if (data) {
                        tooltip.setPosition(evt.coordinate);
                        tooltip.getElement().innerHTML = '<div><i class="' + icon_cls + '"></i> ' + data + '</div>';

                        return feature;
                    }

                    return null;
                });

            tooltip.getElement().style.display = feature ? '' : 'none';
            document.body.style.cursor = feature ? 'pointer' : '';
        });

        map.getView().on('change:resolution', function (evt) {
            const tooltip = map.getOverlayById('tooltip_overlay');

            tooltip.setPosition(null);
            tooltip.getElement().innerHTML = '';
            tooltip.getElement().style.display = 'none';
        });

        map.flashFeature = function (feature, duration) {
            const start = new Date().getTime(),
                listener = map.on('postcompose', animate);

            function animate(event) {
                const vectorContext = event.vectorContext,
                    frameState = event.frameState,
                    flashGeom = feature.getGeometry().clone(),
                    elapsed = frameState.time - start,
                    elapsedRatio = elapsed / (duration || 2000),
                    radius = ol.easing.easeOut(elapsedRatio) * 20 + 5, // radius will be 5 at start and 25 at end.
                    opacity = ol.easing.easeOut(1 - elapsedRatio);

                const style = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: radius,
                        stroke: new ol.style.Stroke({
                            color: 'rgba(255, 0, 0, ' + opacity + ')',
                            width: 0.25 + opacity
                        })
                    })
                });

                vectorContext.setStyle(style);
                vectorContext.drawGeometry(flashGeom);
                if (elapsed > (duration || 2000)) {
                    ol.Observable.unByKey(listener);
                    return;
                }
                // tell OpenLayers to continue postcompose animation
                map.render();
            }
        };

        map.locateFeature = function (feature, flash) { // TODO: cuando el feature tiene una geometría grande entonces el maxResolution del layer no permite ver la capa con .fit
            const callback = function () {
                map.getView().fit(feature.getGeometry(), {
                    // size: map.getSize(),
                    duration: 2000,
                    callback: function () {
                        if (flash) {
                            map.flashFeature(feature);
                        }
                    }
                });
            };

            if (map.getView().getZoom() > 12) {
                map.getView().animate({
                    duration: 500,
                    zoom: 12 // zoom - 2
                }, function () {
                    callback();
                });
            } else {
                callback();
            }

            // const zoom = map.getView().getZoom(),
            //     locate_cb = function () {
            //         map.getView().fit(feature.getGeometry(), {
            //             size: map.getSize(),
            //             duration: 2000,
            //             callback: function () {
            //                 flash(map, feature);
            //             }
            //         });
            //     };
            //
            // if (zoom > 12) {
            //     map.getView().animate({
            //         duration: 500,
            //         zoom: 12 // zoom - 2
            //     }, function () {
            //         locate_cb();
            //     });
            // } else {
            //     locate_cb();
            // }
        };

        map.getFeature = function (layer_idx, gid) {
            const layer = map.getLayers().getArray()[layer_idx];

            layer.setVisible(true);

            return layer.getSource().getFeatureById(gid);
        };

        map.set('geoserver_url', geoserver_url);
    };

    map.once('precompose', function () {
        panel.showMsg('<i class="fas fa-circle-notch fa-spin"></i>&nbsp;<span>Cargando capas...</span>', 'info', 999999);
    });

    map.once('rendercomplete', function () {
        $('#toolbar_control').animate({left: '4px'}, 'slow', 'swing');
        $('.ol-scale-line').animate({bottom: '8px'}, 'slow', 'swing');
        $('.ol-mouse-position').html('<i class="fas fa-map-marker-alt"></i>').animate({bottom: '8px'}, 'slow', 'swing');

        $('#tooltip_overlay').attr('hidden', null);

        panel.showMsg('<i class="fas fa-lightbulb"></i>&nbsp;La inicialización del mapa ha sido completada.');
        App.debug('Map was initialized in', ((performance.now() - start) / 1000).toFixed(3), 's. Enjoy it!');
    });

    // -----------------------------------------------------------------------------------------------------------------

    $('#zoom-in,#zoom-out,#zoom-reset').click(function (evt) {
        const view = map.getView();
        if (!view) {
            return;
        }

        const from_resolution = view.getResolution();
        if (from_resolution) {
            const id = $(this).attr('id'),
                delta = id === 'zoom-in' ? 1 : id === 'zoom-out' ? -1 : 0,
                to_resolution = delta !== 0 ? view.constrainResolution(from_resolution, delta) : view.getMaxResolution(),
                options = {resolution: to_resolution, easing: ol.easing.easeOut};

            if (id === 'zoom-reset') {
                options['center'] = [-79.5, 22.214];
            }

            if (view.getAnimating()) {
                view.cancelAnimations();
            }

            if (to_resolution) {
                view.animate(options);
            }
        }
    });

    $('button[title]').click(function (evt) {
        $(evt.currentTarget).tooltip('hide');
    }).tooltip();

    // -----------------------------------------------------------------------------------------------------------------

    let timeout_handler_key = -1,
        start;

    const panel = Ext.create('Ext.panel.Panel', {
        title: 'Cuba',
        closable: true,
        layout: 'fit',
        glyph: 0xf57d,
        html: ['<div id="map" class="h-100 w-100"></div>'],

        enableActions: function () {
            $("button", '#' + this.getId()).attr('disabled', null).removeClass('active');
        },

        enableAction: function (target_id) {
            $('#' + target_id, '#' + this.getId()).attr('disabled', false).addClass('active');
            $("button[id!=" + target_id + "]:not([id*=zoom])", '#' + this.getId()).attr('disabled', true);
        },

        disableAction: function (target_id) {
            $('#' + target_id, '#' + this.getId()).attr('disabled', null).removeClass('active');
            $("button[id!=" + target_id + "]", '#' + this.getId()).attr('disabled', null);
        },

        showMsg: function (message, type, delay) {
            const logger_el = $('#logger_control'),
                logger_text_el = logger_el.find('div');

            logger_text_el.html(message);
            logger_el.removeClass(function (index, className) {
                return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
            }).addClass('alert-' + (type || 'info')).stop().animate({right: '8px'}, 'fast', 'swing').fadeIn();

            clearTimeout(timeout_handler_key);
            timeout_handler_key = setTimeout(function () {
                logger_el.fadeOut(1000, function () {
                    logger_text_el.html('&nbsp;');
                });
            }, delay || 3000);
        },

        listeners: {
            boxready: function () {
                start = performance.now();

                map.set('panel', panel);
                map.setTarget('map');
            }
        }
    });

    App.render(panel);

    window.Map = map;
});