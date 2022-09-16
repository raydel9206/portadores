Ext.onReady(function () {
    var title = 'Eventos',
        params = {},
        clear_trigger_handler = function () {
            this.setValue(null);
            this.updateLayout();

            delete params[this.getName()];
        },
        field_change_handler = function (self, newValue, oldValue, eOpts) {
            var trigger_clear = self.getTrigger('clear');
            if (newValue) {
                trigger_clear.show();
            } else {
                trigger_clear.hide();
            }
        };

    App.render(Ext.create('Ext.grid.Panel', {
        title: title,
        closable: true,

        viewModel: {},
        // selModel: {
        //     mode: 'MULTI'
        // },

        viewConfig: {
            emptyText: '<div class="text-center">No existen eventos registrados</div>',
            listeners: {
                refresh: function (view) {
                    Ext.each(Ext.query(Ext.String.format('.x-grid-cell-{0} > .x-grid-cell-inner', view.ownerCt.down('widgetcolumn').getId()), false), function (cell_el) {
                        cell_el.applyStyles({padding: "0 10px"});
                    });
                }
            }
        },

        reserveScrollbar: true,
        scrollable: 'vertical',
        columnLines: true,

        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'events',
            fields: ['id', 'tabla', 'entidad', 'usuario', 'usuario_nombre_completo',
                {name: 'tipo', type: 'number'},
                {name: 'datos', type: 'auto'},
                {name: 'datos_modificados', type: 'auto'},
                {name: 'fecha', type: 'date', dateFormat: 'd/m/Y H:i'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/admin/evento/list'),
                reader: {
                    rootProperty: 'rows'
                },
                listeners: {
                    exception: function (proxy, request, operation) {
                        if (!request.aborted) {
                            App.showAlert(request.statusText || 'La solicitud no tiene respuesta disponible', 'danger');
                        }
                    }
                }
            },
            autoLoad: false,
            autoDestroy: true,
            listeners: {
                beforeload: function (store, operation, eOpts) {
                    operation.setParams(params);
                }
            }
        }),

        columns: [{
            text: '#',
            dataIndex: 'id',
            align: 'center',
            width: 50
        }, {
            text: 'Usuario',
            // dataIndex: 'session_user',
            xtype: 'templatecolumn',
            tpl: '{usuario} <small class="text-muted">{usuario_nombre_completo}</small>',
            cls: 'x-column-header-inner-centered',
            flex: 1,
            minWidth: 100
        }, {
            text: 'Evento',
            // dataIndex: 'tipo',
            xtype: 'templatecolumn',
            tpl: '{[CONSTANTS.EVENTOS[values.tipo]]}',
            align: 'center',
            width: 135
        }, {
            text: 'Tabla',
            dataIndex: 'tabla',
            align: 'center',
            width: 200
        }, {
            text: 'Entidad',
            dataIndex: 'entidad',
            align: 'center',
            width: 275
        }, {
            text: 'Fecha',
            dataIndex: 'fecha',
            xtype: 'datecolumn',
            format: 'd/m/Y H:i',
            align: 'center',
            width: 140
        }, {
            xtype: 'widgetcolumn',
            dataindex: 'id',
            menuDisabled: true,
            hideable: false,
            align: 'center',
            width: 50,
            widget: {
                xtype: 'container',
                html: '<button type="button" class="btn btn-sm btn-outline-secondary pb-0 pt-0" data-toggle="tooltip" data-placement="left" title="Eliminar"><i class="fa fa-trash"></i></button>',
                listeners: {
                    boxready: function (container) {
                        container.el.down('button').on('click', function (el) {
                            var gridpanel = container.up('gridpanel'),
                                record = container.getWidgetRecord();

                            gridpanel.getSelectionModel().select(record);

                            Ext.Msg.show({
                                title: '¿Eliminar registro?',
                                message: '¿Está seguro que desea eliminar el registro seleccionado?',
                                buttons: Ext.Msg.YESNO,
                                icon: Ext.Msg.QUESTION,
                                fn: function (btn) {
                                    if (btn === 'yes') {
                                        var url = App.buildURL('/admin/evento/delete'),
                                            params = {id: record.get('id')};

                                        App.request('DELETE', url, params, null, null, function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                gridpanel.getStore().removeAll();
                                                gridpanel.getStore().loadPage(response.page || gridpanel.getStore().currentPage, {
                                                    callback: function () {
                                                        gridpanel.view.refresh();
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        }); // the scope is the container

                        App.initTooltips();
                    }
                }
            }
        }],

        plugins: [{
            ptype: 'rowexpander',
            rowBodyTpl: new Ext.XTemplate(
                '<div class="card p-1 text-center">',
                '   <div class="card">',
                '       <table class="table table-bordered table-hover table-responsive-md mb-0">',
                '           <thead>',
                '               <tr>',
                '                   <th scope="col">Atributo</th>',
                '                   <th scope="col">Valor</th>',
                '               </tr>',
                '             </thead>',
                '           <tbody>',
                '               <tpl switch="tipo">',
                '                   <tpl case="2">',
                '                       <tpl foreach="datos_modificados">',
                '                           <tr>',
                '                               <td class="w-50"><code>{$}</code></td>',
                '                               <td class="w-50"><samp class="d-inline-block text-truncate" style="max-width: 600px;">{[parent.datos[xkey]]}&nbsp;&nbsp;<i class="fa fa-angle-right text-danger" aria-hidden="true"></i>&nbsp;&nbsp;{.}</samp></td>',
                '                           </tr>',
                '                       </tpl>',
                '                    <tpl case="1">',
                '                       <tpl foreach="datos">',
                '                           <tr>',
                '                               <td class="w-50"><code>{$}</code></td>',
                '                               <td class="w-50"><samp class="d-inline-block text-truncate" style="max-width: 600px;">{.}</samp></td>',
                '                           </tr>',
                '                       </tpl>',
                '                    <tpl case="3">',
                '                       <tpl foreach="datos">',
                '                           <tr>',
                '                               <td class="w-50"><code>{$}</code></td>',
                '                               <td class="w-50">',
                '                                   <tpl if="xkey === \'activo\'"><kbd>{.}</kbd>',
                '                                   <tpl else><samp class="col-2 d-inline-block text-truncate" style="max-width: 600px;">{.}</samp></tpl>',
                '                               </td>',
                '                           </tr>',
                '                       </tpl>',
                '                    <tpl default>',
                '               </tpl>',
                '           </tbody>',
                '       </table>',
                '   </div>',
                '</div>'
            )
        }],

        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            defaults: {
                labelAlign: 'top'
            },
            items: [{
                xtype: 'datefield',
                name: 'fecha_inicio',
                fieldLabel: 'Desde',
                width: 125,
                value: Ext.Date.getFirstDateOfMonth(new Date()),
                maxValue: Ext.Date.getLastDateOfMonth(new Date()),
                listeners: {
                    change: function (self, newValue, oldValue, eOpts) {
                        var fecha_fin_cmp = self.nextSibling();

                        if (newValue) {
                            fecha_fin_cmp.setMinValue(newValue);
                        } else {
                            fecha_fin_cmp.setMinValue(null);
                        }

                        var trigger_clear = self.getTrigger('clear');
                        if (newValue) {
                            trigger_clear.show();
                        } else {
                            trigger_clear.hide();
                        }
                    }
                },
                triggers: {
                    clear: {
                        weight: -1,
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: false,
                        handler: function () {
                            this.setValue(null);
                            this.updateLayout();

                            this.nextSibling().setMinValue(null);

                            delete params[this.getName()];
                        }
                    }
                }
            }, {
                xtype: 'datefield',
                name: 'fecha_fin',
                fieldLabel: 'Hasta',
                width: 125,
                // disabled: true,
                minValue: Ext.Date.getFirstDateOfMonth(new Date()),
                value: Ext.Date.getLastDateOfMonth(new Date()),
                listeners: {
                    change: function (self, newValue, oldValue, eOpts) {
                        var fecha_inicio_cmp = self.previousSibling();

                        if (newValue) {
                            fecha_inicio_cmp.setMaxValue(newValue);
                        } else {
                            fecha_inicio_cmp.setMaxValue(null);
                        }

                        var trigger_clear = self.getTrigger('clear');
                        if (newValue) {
                            trigger_clear.show();
                        } else {
                            trigger_clear.hide();
                        }
                    }
                },
                triggers: {
                    clear: {
                        weight: -1,
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: false,
                        handler: function () {
                            this.setValue(null);
                            this.updateLayout();

                            this.previousSibling().setMaxValue(null);

                            delete params[this.getName()];
                        }
                    }
                }
            }, {
                xtype: 'combobox',
                fieldLabel: 'Evento',
                name: 'tipo',
                width: 120,
                store: Ext.create('Ext.data.Store', {
                    fields: ['name', {name: 'value', type: 'number'}],
                    data: Ext.Object.getAllKeys(CONSTANTS.EVENTOS).map(function (item) {
                        return {name: CONSTANTS.EVENTOS[item], value: item};
                    })
                }),
                valueField: 'value',
                displayField: 'name',
                queryMode: 'local',
                typeAhead: true,
                editable: true,
                forceSelection: true,
                listeners: {
                    change: field_change_handler
                },
                triggers: {
                    clear: {
                        weight: -1,
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: true,
                        handler: clear_trigger_handler
                    }
                }
            }, {
                xtype: 'combobox',
                fieldLabel: 'Usuario',
                name: 'usuario_id',
                width: 300,
                store: Ext.create('Ext.data.JsonStore', {
                    fields: ['id', 'nombre_completo', {name: 'activo', type: 'boolean'}],
                    proxy: {
                        type: 'ajax',
                        url: '/admin/usuario/list',
                        reader: {
                            rootProperty: 'rows'
                        },
                        extraParams: {simple: true, all: true}
                    },
                    pageSize: 0,
                    autoLoad: false
                }),
                valueField: 'id',
                tpl: '<ul class="x-list-plain"><tpl for="."><li role="option" class="x-boundlist-item">{nombre_completo} <tpl if="!activo"><small class="text-danger">(ELIMINADO)</small></tpl></li></tpl></ul>', // Template for the dropdown menu. Note the use of the "x-list-plain" and "x-boundlist class, this is required to make the items selectable.
                displayTpl: '<tpl for=".">{nombre_completo}</tpl>', // template for the content inside text field
                // displayField: 'nombre_completo',
                // queryCaching: false,
                typeAhead: true,
                editable: true,
                forceSelection: true,
                listeners: {
                    change: field_change_handler
                },
                triggers: {
                    clear: {
                        weight: -1,
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: true,
                        handler: clear_trigger_handler
                    }
                }
            }, {
                xtype: 'combobox',
                fieldLabel: 'Entidad',
                name: 'entidad',
                width: 250,
                hidden: true,
                store: Ext.create('Ext.data.JsonStore', {
                    fields: ['name'],
                    proxy: {
                        type: 'ajax',
                        url: App.buildURL('/admin/evento/entities'),
                        reader: {
                            rootProperty: 'rows'
                        }
                    },
                    pageSize: 0,
                    autoLoad: false
                }),
                valueField: 'name',
                displayField: 'name',
                // queryCaching: false,
                typeAhead: true,
                editable: true,
                forceSelection: true,
                listeners: {
                    change: field_change_handler
                },
                triggers: {
                    clear: {
                        weight: -1,
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: true,
                        handler: clear_trigger_handler
                    }
                }
            }, '->', {
                xtype: 'button',
                text: 'Buscar',
                width: 80,
                glyph: 0xf002,
                cls: 'border-secondary',
                iconCls: 'text-dark',
                tooltip: 'Busca los datos a partir de las opciones seleccionadas',
                handler: function (button) {
                    var toolbar = button.up('toolbar');

                    Ext.each(toolbar.query('field'), function (field) {
                        var value = field.getValue();
                        if (!Ext.isEmpty(value)) {
                            params[field.getName()] = value;
                        } else {
                            delete params[field.getName()];
                        }
                    });

                    toolbar.up('gridpanel').getStore().load();
                }
            }],
            listeners: {
                render: function (toolbar) {
                    var gridpanel = toolbar.up('gridpanel'),
                        sel_model = gridpanel.getSelectionModel(),
                        selection = [];

                    gridpanel.getStore().on({
                        beforeload: function () {
                            selection = sel_model.getSelection();
                            sel_model.deselectAll();

                            toolbar.items.each(function (item) {
                                item.disable();
                            })
                        },
                        load: function (store, records, successful, eOpts) {
                            selection = Ext.Array.map(selection, function (record, index) { // update the selected record
                                return store.getById(record.getId());
                            });
                            selection = Ext.Array.clean(selection);
                            sel_model.select(selection);

                            toolbar.items.each(function (item) {
                                item.enable();
                            })
                        }
                    });
                }
            }
        }, {
            xtype: 'pagingtoolbar',
            dock: 'bottom',
            store: Ext.getStore('events'),
            displayInfo: true
        }]
    }));
});