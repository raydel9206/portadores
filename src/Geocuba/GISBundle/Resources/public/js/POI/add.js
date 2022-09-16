Ext.onReady(function () {
    const map = window.Map,
        default_hover = map.getInteractions().item(map.getInteractions().getLength() - 1);

    // -----------------------------------------------------------------------------------------------------------------

    Ext.define('GISRoute.POI.Form', {
        extend: 'Ext.window.Window',

        title: 'Insertar Punto de interés (POI)',
        glyph: 0xf3c5,
        width: 400,
        // height: 200,
        modal: true,
        resizable: false,

        defaultFocus: '[name=point_name]',

        layout: 'fit',
        items: [{
            xtype: 'form',
            layout: 'fit',

            items: {
                xtype: 'container',
                padding: 10,

                layout: 'vbox',
                defaults: {
                    labelClsExtra: 'font-weight-bold',
                    labelAlign: 'right',
                    labelWidth: 90,
                    width: '100%',

                    maxLength: 150,
                    enforceMaxLength: true,
                    allowBlank: false,

                    listeners: {
                        change: function (field, newValue, oldValue, opts) {
                            field.getTrigger('clear').setVisible(!Ext.isEmpty(newValue));
                        }
                    },

                    triggers: {
                        clear: {
                            cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                            weight: -1, // negative to place before default triggers
                            hidden: true,
                            handler: function () {
                                this.setValue(null);
                                this.updateLayout();
                            }
                        }
                    }
                },
                items: [{
                    xtype: 'label',
                    cls: 'font-weight-bold',
                    text: 'Coordenadas: [XY]',
                    padding: {left: 3},
                    style: {
                        // borderBottom: '1px solid #afafaf66 !important'
                    },
                }, {
                    xtype: 'textfield',
                    name: 'point_name',
                    fieldLabel: 'Nombre',
                    margin: {top: 0, bottom: 5},
                }, {
                    xtype: 'textfield',
                    name: 'point_description',
                    fieldLabel: 'Descripción',
                    margin: {top: 5, bottom: 5},
                }],
            },

            bbar: {
                ui: 'footer',
                layout: {
                    pack: 'center'
                },

                items: [{
                    text: 'Adicionar',
                    formBind: true,

                    handler: function (button) {
                        const form = button.up('form'),
                            winform = form.up('window');

                        let feature = winform.getInitialConfig('feature');

                        if (!form.isValid() || !feature) {
                            return;
                        }

                        winform.hide();

                        const url = App.buildURL('/gis/poi/add'),
                            params = Ext.Object.merge(form.getValues(), {point_coordinates: feature.getGeometry().getCoordinates()});

                        App.request('POST', url, params, null, null,
                            function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    winform.close();

                                    // https://docs.geoserver.org/stable/en/user/services/wfs/reference.html
                                    // https://openlayers.org/en/latest/examples/vector-wfs-getfeature.html

                                    App.request('GET', map.get('geoserver_url') + 'wfs', {
                                        service: 'wfs',
                                        version: '2.0.0',
                                        request: 'GetFeature',
                                        typeNames: 'GISROUTE:POI',
                                        outputFormat: 'application/json',
                                        count: 1,
                                        CQL_FILTER: 'node_id=' + response.node_id
                                    }, null, null, function (wfs) {
                                        if (wfs && wfs.hasOwnProperty('features') && Array.isArray(wfs.features) && wfs.features.length === 1) {
                                            map.getLayers().getArray()[2].getSource().removeFeature(feature);
                                            draw.removeLastPoint();

                                            feature = (new ol.format.GeoJSON()).readFeature(wfs.features[0]);
                                            map.getLayers().getArray()[2].getSource().addFeature(feature);
                                            map.locateFeature(feature, true);
                                        } else {
                                            console.error(wfs);
                                            map.get('panel').showMsg('<i class="fas fa-exclamation-triangle"></i>&nbsp;<span>El Punto de interés (POI) no se pudo adicionar al mapa.</span>', 'danger');
                                        }
                                    }, function (response) {
                                        map.get('panel').showMsg('<i class="fas fa-exclamation-triangle"></i>&nbsp;<span>El Punto de interés (POI) no se pudo localizar.</span>', 'danger');
                                    }, null, true);
                                } else {
                                    if (response && response.hasOwnProperty('errors') && response.errors) {
                                        winform.down('form').getForm().markInvalid(response.errors);
                                    }

                                    winform.show();
                                }
                            },
                            function (response) { // failure_callback
                                winform.show();
                            }
                        );
                    }
                }, {
                    text: 'Cancelar',
                    handler: function (button) {
                        const winform = button.up('window'),
                            feature = winform.getInitialConfig('feature');

                        if (feature) {
                            draw.removeLastPoint();
                        }

                        button.up('window').close();
                    }
                }]
            }
        }],

        listeners: {
            boxready: function (self) {
                const feature = self.getInitialConfig('feature');

                if (feature) {
                    const coordinates = feature.getGeometry().getCoordinates();
                    self.down('label').setText(Ext.String.format('Coordenadas: [{0},{1}]', coordinates[0], coordinates[1]));
                }
            }
        }
    });

    // -----------------------------------------------------------------------------------------------------------------

    let draw;

    $('#add-point').click(function (evt) {
        const panel = map.get('panel');

        if (!$(evt.currentTarget).hasClass('active')) {
            panel.enableAction(evt.currentTarget.id, false);

            default_hover.setActive(false);

            draw = new ol.interaction.Draw({
                source: map.getLayers().getArray()[2].getSource(),
                type: 'Point'
            });
            draw.on('drawend', function (evt) {
                Ext.create('GISRoute.POI.Form', {feature: evt.feature}).show();
            });
            map.addInteraction(draw);
        } else {
            panel.disableAction(evt.currentTarget.id);

            default_hover.setActive(true);
            map.removeInteraction(draw);
            draw = null;
        }
    });
});