Ext.onReady(function () {
    const map = window.Map;

    const _CONSTANTS = {
        PERFIL_SINGLE_ORDER: 'SINGLE',
        PERFIL_GO_COME_ORDER: 'GO_COME',
        PERFIL_N_WAYS: 'N_WAYS',
        PERFIL_CUSTOM_ORDER: 'CUSTOM_ORDER',
        PERFIL_FIRST_LAST_ORDER: 'PERFIL_FIRST_LAST_ORDER',
        PERFIL_FIRST_FIRST_ORDER: 'PERFIL_FIRST_FIRST_ORDER',

        MODO_SINGLE: 'MODO_SINGLE',
        MODO_MULTIPLE: 'MODO_MULTIPLE'
    };

    // -----------------------------------------------------------------------------------------------------------------

    Ext.define('GISRoute.Route.Form', {
        extend: 'Ext.window.Window',

        animateTarget: 'add-route',

        title: 'Crear Ruta',
        glyph: 0xf4d7,
        width: 990,
        height: 580,
        collapsible: true,
        // modal: true,
        // plain: true,
        resizable: false,
        defaultFocus: '[name=provincia_id]',

        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        defaults: {
            style: {
                padding: '0 !important'
            },
        },
        defaultType: 'container',

        items: [{
            xtype: 'container',
            flex: 1,
            // style: {padding: '0 !important'},
            // margin: '8 3 8 8',
            style: {
                borderRight: '1px solid #afafaf !important'
            },

            layout: {
                type: 'vbox',
                align: 'stretch'
            },

            items: [{
                xtype: 'form',
                margin: 10,
                flex: 2,

                layout: 'vbox',
                defaults: {
                    width: '100%'
                },

                items: [{
                    xtype: 'container',
                    layout: 'hbox',

                    defaults: {
                        labelClsExtra: 'font-weight-bold',
                        labelAlign: 'top',
                        flex: 1,

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
                        xtype: 'combobox',
                        name: 'provincia_id',
                        fieldLabel: 'Provincia',

                        store: Ext.getStore('states_store'),
                        // queryMode: 'local',
                        displayField: 'nombre',
                        valueField: 'id',

                        forceSelection: true,
                        editable: true,
                        triggerAction: 'all',
                        queryCaching: true,

                        listeners: {
                            expand: function (field, eOpts) {
                                field.getStore().clearFilter();
                            },

                            change: function (field, newValue, oldValue, opts) {
                                const sibling = field.nextSibling(),
                                    store = sibling.getStore();

                                if (newValue) {
                                    if (store.isLoaded()) {
                                        store.clearFilter();

                                        sibling.enable().getStore().filterBy(function (rec, id) {
                                            return newValue === rec.get('provincia_id');
                                        });

                                        if (store.count() === 1) {
                                            sibling.setValue(store.getAt(0).get('id'));
                                        }
                                    } else {
                                        sibling.enable();
                                    }
                                } else {
                                    sibling.disable().setValue(null);
                                }


                                field.getTrigger('clear').setVisible(!Ext.isEmpty(newValue));
                            }
                        }
                    }, {
                        xtype: 'combobox',
                        name: 'municipio_id',
                        fieldLabel: 'Municipio',

                        margin: {left: 10},

                        store: Ext.getStore('localities_store'),
                        // queryMode: 'local',
                        displayField: 'nombre',
                        valueField: 'id',

                        forceSelection: true,
                        editable: true,
                        triggerAction: 'all',
                        queryCaching: true,
                        disabled: true,

                        listeners: {
                            boxready: function (self) {
                                const listener = self.getStore().on('load', function (store, records, successful, operation, eOpts) {
                                    const provincia_cmp = self.previousSibling('[name=provincia_id]');

                                    if (provincia_cmp && provincia_cmp.getValue()) {
                                        const provincia_id = provincia_cmp.getValue();

                                        store.filterBy(function (rec, id) {
                                            return provincia_id === rec.get('provincia_id');
                                        });
                                    }
                                }, this, {destroyable: true});

                                self.up('window').on('close', function () {
                                    listener.destroy();
                                });
                            },

                            change: function (field, newValue, oldValue, opts) {
                                field.getTrigger('clear').setVisible(!Ext.isEmpty(newValue));
                            }
                        }
                    }]
                }, {
                    xtype: 'container',
                    layout: 'hbox',
                    margin: {top: 7},

                    defaults: {
                        labelClsExtra: 'font-weight-bold',
                        labelAlign: 'top',

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
                        xtype: 'textfield',
                        name: 'nombre',

                        fieldLabel: 'Nombre',
                        flex: 1,

                        maxLength: 150,
                        enforceMaxLength: true,

                        listeners: {
                            change: function (field, newValue, oldValue, opts) {
                                field.getTrigger('clear').setVisible(!Ext.isEmpty(newValue));
                            }
                        }
                    }, {
                        xtype: 'container',
                        flex: 1,
                        margin: {left: 10},
                    }]
                }, {
                    xtype: 'panel',
                    flex: 1,

                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'bottom',
                        padding: 0,

                        items: ['->', {
                            text: 'Buscar POI',
                            tooltip: 'Busca los Puntos de interés que cumplen con los criterios especificados',

                            glyph: 0xf002,
                            cls: 'border-secondary',
                            iconCls: 'text-dark',
                            width: 110,
                            // disabled: true,

                            handler: function (button) {
                                button.up('form').nextSibling('gridpanel').getStore().loadPage(1);
                            },
                        }, {
                            text: 'Limpiar',
                            tooltip: 'Elimina los valores seleccionados o insertados en los campos de búsqueda',

                            glyph: 0xf12d,
                            cls: 'border-secondary',
                            iconCls: 'text-dark',
                            width: 90,
                            disabled: true,

                            handler: function (button) {
                                button.up('panel').up('container').query('field').forEach(function (i) {
                                    i.setValue(null);
                                });
                            },

                            listeners: {
                                render: function (button) {
                                    const fields = button.up('form').query('field');

                                    fields.forEach(function (i) {
                                        i.on('change', function (field, newValue, oldValue, eOpts) {
                                            if (newValue) {
                                                button.enable();
                                            } else {
                                                const filtered_fields = Ext.Array.filter(fields, function (_i) {
                                                    return _i.getId() !== field.getId();
                                                });

                                                button.setDisabled(Ext.Array.every(filtered_fields, function (_i) {
                                                    return Ext.isEmpty(_i.getValue());
                                                }));
                                            }
                                        });
                                    });
                                }
                            }
                        }]
                    }]
                }]
            }, {
                xtype: 'gridpanel',

                reserveScrollbar: true,
                scrollable: 'vertical',
                columnLines: true,

                style: {
                    borderTop: '1px solid #afafaf !important'
                },
                flex: 5,

                store: Ext.getStore('points_store'),

                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',

                    items: [{
                        text: 'Localizar POI',
                        tooltip: 'Localiza en el mapa el Punto de interés seleccionado',

                        glyph: 0xf689,
                        cls: 'border-secondary',
                        iconCls: 'text-dark',
                        width: 120,
                        disabled: true,

                        handler: function (button) {
                            button.up('window').collapse();

                            const feature = map.getFeature(2, 'POI.' + button.up('gridpanel').getSelection()[0].get('gid'));
                            if (feature) {
                                map.locateFeature(feature, true);
                            }
                        }
                    }]
                }, {
                    xtype: 'pagingtoolbar',
                    dock: 'bottom',
                    store: Ext.getStore('points_store'),
                    displayInfo: true,
                }],

                columns: [{
                    xtype: 'rownumberer'
                }, {
                    text: 'Punto de interés (POI)',
                    dataIndex: 'point_name',
                    flex: 1
                }],

                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Arrastra y suelta para reorganizar',
                        ddGroup: 'dragdrop_group'
                    }
                },

                listeners: {
                    boxready: function (gridpanel) {
                        const toolbar = gridpanel.down('toolbar'),
                            form = gridpanel.up('container').down('form'),
                            _gridpanel = gridpanel.up('container').nextSibling('container').down('gridpanel'), // right gridpanel
                            listeners = gridpanel.getStore().on({
                                destroyable: true,
                                beforeload: function (store, operation) {
                                    store.removeAll();

                                    toolbar.items.each(function (i) {
                                        i.disable();
                                    });

                                    _gridpanel.mask();
                                    form.mask();

                                    operation.setParams(form.getValues());
                                },
                                load: function (store, records, successful, operation, eOpts) {
                                    _gridpanel.unmask();
                                    form.unmask();

                                    // if (!Ext.isEmpty(records)) {
                                    //     toolbar.items.getAt(2).enable();
                                    // }
                                }
                            });

                        gridpanel.on('selectionchange', function (model, selected, eOpts) {
                            toolbar.items.getAt(0).setDisabled(selected.length === 0);
                        });

                        gridpanel.up('window').on('close', function () {
                            listeners.destroy();
                        });

                        if (gridpanel.getStore().isLoaded()) {
                            gridpanel.getStore().removeAll();
                        }
                    }
                }
            }]
        }, {
            xtype: 'container',
            flex: 1,

            // margin: '8 10 10 4',

            layout: {
                type: 'vbox',
                align: 'stretch'
            },

            items: [{
                xtype: 'form',
                name: 'profiles',

                flex: 2,
                padding: '8 8 0',
                margin: '0 0 5',

                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },

                items: [{
                    xtype: 'container',
                    name: 'mode_group',

                    style: {
                        borderBottom: '1px solid #afafaf66 !important'
                    },

                    layout: 'hbox',
                    defaultType: 'radiofield',
                    defaults: {
                        flex: 1,
                        disabled: true,
                        name: 'mode'
                    },

                    items: [{
                        boxLabel: 'Destino simple',
                        checked: true,
                        inputValue: _CONSTANTS.MODO_SINGLE,

                        listeners: {
                            change: function (field, newValue, oldValue, eOpts) {
                                field.up('form').down('[name=card]').getLayout().setActiveItem(newValue ? 0 : 1);
                            }
                        }
                    }, {
                        boxLabel: 'Múltiples destinos',
                        inputValue: _CONSTANTS.MODO_MULTIPLE
                    }]
                }, {
                    xtype: 'container',
                    name: 'card',

                    layout: 'card',
                    defaults: {
                        margin: {top: 5, bottom: 0},

                    },

                    items: [{
                        xtype: 'form',
                        name: 'single_group',

                        layout: 'vbox',
                        defaults: {width: '100%'},

                        items: [{
                            xtype: 'radiofield',
                            boxLabel: 'Ruta óptima de origen a destino',
                            checked: true,
                            disabled: true,
                            inputValue: _CONSTANTS.PERFIL_SINGLE_ORDER,
                            name: 'profile'
                        }, {
                            xtype: 'radiofield',
                            boxLabel: 'Ruta óptima para ida y regreso',
                            disabled: true,
                            inputValue: _CONSTANTS.PERFIL_GO_COME_ORDER,
                            name: 'profile'
                        }, {
                            xtype: 'container',
                            layout: 'hbox',

                            items: [{
                                xtype: 'radiofield',
                                boxLabel: 'N mejores rutas de origen a destino',
                                disabled: true,
                                inputValue: _CONSTANTS.PERFIL_N_WAYS,
                                name: 'profile',
                                flex: 1,

                                listeners: {
                                    change: function (field, newValue, oldValue, opts) {
                                        const next = field.nextSibling('numberfield');

                                        next.setDisabled(!newValue);
                                        if (!newValue) {
                                            next.setValue(1);
                                        }
                                    }
                                }
                            }, {
                                xtype: 'numberfield',
                                fieldLabel: 'Nro. Rutas',
                                // padding: '0 0 0 35',
                                labelWidth: 70,
                                width: 150,
                                value: 1,
                                disabled: true,
                                minValue: 1,
                                maxValue: 7,
                                allowBlank: false,
                                allowDecimals: 0
                            }]
                        }]
                    }, {
                        xtype: 'form',
                        name: 'multiple_group',

                        layout: 'vbox',
                        items: [{
                            xtype: 'radiofield',
                            boxLabel: 'Partiendo del primer punto, respetar el orden seleccionado',
                            checked: true,
                            disabled: true,
                            inputValue: _CONSTANTS.PERFIL_CUSTOM_ORDER,
                            name: 'profile'
                        }, {
                            xtype: 'radiofield',
                            boxLabel: 'Proponer el mejor orden para salir del primer punto y llegar al último',
                            disabled: true,
                            inputValue: _CONSTANTS.PERFIL_FIRST_LAST_ORDER,
                            name: 'profile'
                        }, {
                            xtype: 'radiofield',
                            boxLabel: 'Proponer el mejor orden para salir del primer punto y llegar al mismo',
                            disabled: true,
                            inputValue: _CONSTANTS.PERFIL_FIRST_FIRST_ORDER,
                            name: 'profile'
                        }]
                    }]
                }]
            }, {
                xtype: 'gridpanel',

                reserveScrollbar: true,
                scrollable: 'vertical',
                columnLines: true,

                flex: 5,
                margin: {top: 15},
                style: {
                    borderTop: '1px solid #afafaf !important'
                },

                store: Ext.create('Ext.data.JsonStore', {
                    fields: ['id', 'point_name'],
                    // autoLoad: false,
                    autoDestroy: false
                }),

                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    layout: {
                        align: 'stretch'
                    },

                    items: [{
                        xtype: 'textfield',
                        fieldLabel: 'Nombre',
                        labelWidth: 55,
                        labelClsExtra: 'font-weight-bold',
                        // width: 200,
                        flex: 1,

                        disabled: true,

                        listeners: {
                            change: function (field, newValue, oldValue, opts) {
                                const toolbar = field.up('toolbar');

                                toolbar.down('button').setDisabled(toolbar.up('gridpanel').getStore().count() < 2 || Ext.isEmpty(newValue));
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
                        },

                        validator: function (val) {
                            return val && !Ext.isEmpty(val.trim()) ? true : 'Este campo es obligatorio';
                        }
                    }, {
                        text: 'Generar',
                        tooltip: 'Genera la Ruta a través de los Puntos de interés (POI) seleccionados',

                        glyph: 0xf058,
                        cls: 'border-secondary',
                        iconCls: 'text-dark',
                        width: 90,
                        disabled: true,

                        handler: function (button) {
                            const url = App.buildURL('/gis/route/add'),
                                gridpanel = button.up('gridpanel'),
                                form = gridpanel.up('container').down('[name=card]').getLayout().getActiveItem(),
                                params = Ext.Object.merge(
                                    form.getValues(),
                                    {name: button.previousSibling('textfield').getValue()},
                                    {points_ids: JSON.stringify(gridpanel.getStore().getData().getValues('id', 'data'))}
                                );

                            App.request('POST', url, params, null, null,
                                function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        button.nextSibling('button').el.dom.click();

                                        // https://docs.geoserver.org/stable/en/user/services/wfs/reference.html
                                        // https://openlayers.org/en/latest/examples/vector-wfs-getfeature.html

                                        App.request('GET', map.get('geoserver_url') + 'wfs', {
                                            service: 'wfs',
                                            version: '2.0.0',
                                            request: 'GetFeature',
                                            typeNames: 'GISROUTE:ROUTES',
                                            outputFormat: 'application/json',
                                            count: 1,
                                            CQL_FILTER: "route_name='" + response.route_name + "'"
                                        }, null, null, function (wfs) {
                                            if (wfs && wfs.hasOwnProperty('features') && Array.isArray(wfs.features) && wfs.features.length === 1) {
                                                const feature = (new ol.format.GeoJSON()).readFeature(wfs.features[0]);
                                                map.getLayers().getArray()[1].getSource().addFeature(feature);
                                            } else {
                                                console.error(wfs);
                                                map.get('panel').showMsg('<i class="fas fa-exclamation-triangle"></i>&nbsp;<span>El Punto de interés (POI) no se pudo adicionar al <mapa></mapa>.</span>', 'danger');
                                            }
                                        }, null, null, true);
                                    }
                                }
                            );
                        }
                    }, {
                        text: 'Limpiar',
                        tooltip: 'Limpia el nombre y listado de Puntos de interés (POI) asociados a la Ruta',

                        glyph: 0xf12d,
                        cls: 'border-secondary',
                        iconCls: 'text-dark',
                        width: 90,
                        disabled: true,

                        handler: function (button) {
                            button.up('gridpanel').getStore().removeAll();
                            button.previousSibling('textfield').setValue();

                            const store = button.up('window').down('form').nextSibling('gridpanel').getStore();
                            store.rejectChanges();
                            store.sort('point_name', 'DESC');
                        }
                    }],

                    listeners: {
                        boxready: function (toolbar) {
                            const container = toolbar.up('window').down('[name=profiles]'),
                                textfield = toolbar.down('textfield'),
                                mode_group_fields = container.down('[name=mode_group]').query('field'),
                                single_group = container.down('[name=single_group]'),
                                single_group_radiofields = single_group.query('radiofield'),
                                multiple_group_radiofields = container.down('[name=multiple_group]').query('radiofield'),
                                listener = toolbar.up('gridpanel').getStore().on('datachanged', function (store) {
                                    const records_count = store.count();
                                    // ---------------------------------------------------------------------------------
                                    textfield.setDisabled(records_count < 2);
                                    toolbar.down('[text=Generar]').setDisabled(records_count < 2 || Ext.isEmpty(textfield.getValue()));
                                    toolbar.down('[text=Limpiar]').setDisabled(records_count === 0);
                                    // -------------------------------------------------------------------------------------
                                    mode_group_fields[0].setDisabled(records_count !== 2).setValue(records_count <= 2 ? _CONSTANTS.MODO_SINGLE : _CONSTANTS.MODO_MULTIPLE);
                                    mode_group_fields[1].setDisabled(records_count <= 2);
                                    // ---------------------------------------------------------------------------------
                                    single_group_radiofields.forEach(function (radiofield) {
                                        radiofield.setDisabled(records_count !== 2);
                                    });
                                    // single_group.down('numberfield').setDisabled(!single_group_radiofields[2].getValue());
                                    // ---------------------------------------------------------------------------------
                                    multiple_group_radiofields.forEach(function (radiofield) {
                                        radiofield.setDisabled(records_count <= 2);
                                    });
                                    // ---------------------------------------------------------------------------------
                                    if (records_count === 0) {
                                        single_group_radiofields[0].setValue(_CONSTANTS.PERFIL_SINGLE_ORDER);
                                        multiple_group_radiofields[0].setValue(_CONSTANTS.PERFIL_CUSTOM_ORDER);
                                    }
                                }, this, {destroyable: true});

                            toolbar.up('window').on('close', function () {
                                listener.destroy();
                            });
                        }
                    }
                }],

                columns: [{
                    xtype: 'rownumberer'
                }, {
                    text: 'Punto de interés (POI)',
                    dataIndex: 'point_name',
                    flex: 1
                }],

                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Arrastra y suelta para reorganizar',
                        ddGroup: 'dragdrop_group'
                    }
                },
            }]
        }],

        listeners: {
            boxready: function (win) {
                const panel = map.get('panel');
                panel.enableAction(win.getInitialConfig('action_el'), true);

                const position = panel.getPosition();
                win.setPosition(position[0] + 60, position[1] + 80);
                win.focus();

                // -----------------------------------------------------------------------------------------------------

                const listener_key = panel.on('destroy', function () {
                    win.close();
                }, this, {destroyable: true});

                win.on('destroy', function () {
                    listener_key.destroy();
                });
            },

            close: function (win) {
                const store = win.down('gridpanel').getStore();
                if (store.isLoading()) {
                    try {
                        store.getProxy().lastRequest.getOperation().abort();
                        console.info('Request canceled');
                    } catch (e) {
                        console.warn(e);
                    }
                }

                map.get('panel').enableActions();

                win.query('gridpanel').forEach(function (g) {
                    g.getStore().removeAll();
                });
            }
        }
    });

    // -----------------------------------------------------------------------------------------------------------------

    $('#add-route').click(function (evt) {
        if (!$(evt.currentTarget).hasClass('active')) {
            Ext.create('GISRoute.Route.Form', {action_el: evt.currentTarget.id}).show();
        }
    });
});