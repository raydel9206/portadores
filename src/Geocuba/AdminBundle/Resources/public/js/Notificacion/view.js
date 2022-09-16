Ext.onReady(function () {
    var action_handler = function (action) {
            var url = App.buildURL('/admin/notificacion/' + action),
                selection;

            if (action === 'delete') {
                if (!gridpanel.getSelectionModel().hasSelection()) {
                    return;
                }

                selection = gridpanel.getSelection();

                Ext.Msg.show({
                    title: selection.length === 1 ? '¿Eliminar notificación?' : '¿Eliminar notificaciones?',
                    message: selection.length === 1 ?
                        '¿Está seguro que desea eliminar la notificación seleccionada?' :
                        'Está seguro que desea eliminar las notificaciones seleccionadas?',
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function (btn) {
                        if (btn === 'yes') {
                            var params = {};

                            selection.forEach(function (record, index) {
                                params['ids[' + index + ']'] = record.getId();
                            });

                            App.request('DELETE', url, params, null, null, function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    gridpanel.getStore().reload();
                                }
                            });
                        }
                    }
                });
            } else {
                if (action === 'edit') {
                    if (!gridpanel.getSelectionModel().hasSelection()) {
                        return;
                    }

                    selection = gridpanel.getSelection()[0];
                }

                var formpanel = Ext.create('Ext.window.Window', {
                    title: action === 'edit' ? 'Modificar notificación' : 'Adicionar notificación',

                    glyph: action === 'add' ? 0xf0fe : 0xf044,

                    // resizable: false,
                    modal: true,
                    layout: 'fit',
                    bodyPadding: 10,
                    minWidth: 450,
                    height: 350,

                    defaultFocus: '[name=mensaje]',

                    items: {
                        xtype: 'form',
                        layout: 'vbox',

                        defaults: {
                            labelClsExtra: 'font-weight-bold',
                            afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                            labelAlign: 'top',
                            width: '100%',
                            allowBlank: false
                        },

                        items: [{
                            xtype: 'hiddenfield',
                            name: 'id',
                            submitValue: action === 'edit'
                        }, {
                            xtype: 'textarea',
                            name: 'mensaje',
                            fieldLabel: 'Mensaje',
                            maxLength: 255,
                            flex: 1,
                            enforceMaxLength: true
                        }, {
                            xtype: 'numberfield',
                            id: 'numberfield',
                            anchor: '100%',
                            name: 'bottles',
                            fieldLabel: 'Bottles of Beer',
                            value: 99,
                            decimalPrecision: 3,
                            decimalSeparator: '.',
                            // maxValue: 99,
                            hidden: true,
                            disabled: true,
                            minValue: 0,
                            // https://stackoverflow.com/questions/4868556/how-do-i-stop-parsefloat-from-stripping-zeroes-to-right-of-decimal
                            _fixPrecision: function (value) {
                                var me = this,
                                    separator = me.decimalSeparator,
                                    precision = me.decimalPrecision,
                                    ret = "",
                                    str = value.toString(),
                                    array = str.split(separator);

                                if (array.length === 2) {
                                    ret += array[0] + separator;
                                    for (i = 0; i < precision; i++) {
                                        if (i >= array[1].length) {
                                            ret += '0';
                                        } else {
                                            ret += array[1][i];
                                        }
                                    }
                                } else if (array.length === 1) {
                                    ret += array[0] + separator;
                                    for (i = 0; i < precision; i++) {
                                        ret += '0'
                                    }
                                }

                                return ret; // parseFloat(ret).toFixed(ret.split('.')[1].length)
                            },

                            valueToRaw: function (value) {
                                var me = this,
                                    decimalSeparator = me.decimalSeparator;
                                value = me.parseValue(value);
                                value = me.fixPrecision(value);
                                value = Ext.isNumber(value) ? value : parseFloat(String(value).replace(decimalSeparator, '.'));
                                value = isNaN(value) ? '' : String(value).replace('.', decimalSeparator);

                                value = this._fixPrecision(value);

                                return value;
                            }
                        }, {
                            xtype: 'combobox',
                            fieldLabel: 'Tipo',
                            name: 'tipo',
                            width: 200,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id', 'name'],
                                data: [{
                                    id: 1, name: 'GLOBAL'
                                }, {
                                    id: 2, name: 'GRUPO'
                                }, {
                                    id: 3, name: 'USUARIO'
                                }]
                            }),
                            displayField: 'name',
                            valueField: 'id',
                            queryMode: 'local',
                            // forceSelection: true,
                            editable: false,
                            hidden: action === 'edit',
                            submitValue: action === 'add',
                            triggerAction: 'all',
                            queryCaching: false,

                            allowBlank: false,

                            // typeAhead: true,
                            listeners: {
                                expand: function (field, eOpts) {
                                    field.getStore().clearFilter();
                                },

                                change: function (field, newValue, oldValue, eOpts) {
                                    field.getTrigger('clear').setVisible(newValue);

                                    if (action === 'add') {
                                        var groups_cmp = field.nextSibling(), user_cmp = groups_cmp.nextSibling();

                                        groups_cmp.disable().hide();
                                        user_cmp.disable().hide();

                                        if (newValue) {
                                            switch (newValue) {
                                                case 2: // grupos
                                                    groups_cmp.enable().show();
                                                    user_cmp.reset();
                                                    break;
                                                case 3: // usuarios
                                                    user_cmp.enable().show();
                                                    groups_cmp.reset();
                                                    break;
                                                case 1: // global
                                                default:
                                                    groups_cmp.reset();
                                                    user_cmp.reset();
                                            }
                                        } else {
                                            groups_cmp.reset();
                                            user_cmp.reset();
                                        }
                                    }
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
                        }, {
                            xtype: 'tagfield',
                            fieldLabel: 'Grupos',
                            name: 'grupos_ids',
                            store: Ext.getStore('grupos_store'),
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            forceSelection: true,
                            disabled: true,
                            hidden: true,
                            submitValue: action === 'add',
                            allowBlank: false,
                            filterPickList: true
                        }, {
                            xtype: 'tagfield',
                            fieldLabel: 'Usuarios',
                            name: 'usuarios_ids',
                            store: Ext.getStore('usuarios_store'),
                            displayField: 'usuario',
                            valueField: 'id',
                            listConfig: {
                                itemTpl: '<div>{usuario} <span class="text-muted">({nombre_completo})</span></div>'
                            },
                            queryMode: 'local',
                            forceSelection: true,
                            disabled: true,
                            hidden: true,
                            submitValue: action === 'add',
                            allowBlank: false,
                            filterPickList: true
                        }],
                        /* Events */
                        listeners: {
                            validitychange: function (form, valid, eOpts) {
                                formpanel.down('toolbar').items.each(function (item) {
                                    if (item.$className === 'Ext.button.Button' && item.getInitialConfig('formBind')) {
                                        item.setDisabled(!valid);
                                    }
                                })
                            }
                        }
                    },
                    bbar: {
                        ui: 'footer',
                        layout: {
                            pack: 'center'
                        },
                        items: [{
                            xtype: 'button',
                            text: 'Aceptar',
                            disabled: true,
                            formBind: true,
                            handler: function (button, event) {
                                var params = Ext.Object.merge({limit: gridpanel.getStore().getPageSize()}, formpanel.down('form').getValues());

                                formpanel.hide();

                                App.request('POST', url, params, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            gridpanel.getStore().loadPage(1);
                                            formpanel.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                formpanel.down('form').getForm().markInvalid(response.errors);
                                            }
                                            formpanel.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        formpanel.show();
                                    });
                            },
                            width: 70
                        }, {
                            xtype: 'button',
                            text: 'Cancelar',
                            handler: function (button, event) {
                                formpanel.close();
                            },
                            width: 70
                        }]
                    },

                    listeners: {
                        boxready: function (self) {
                            if (action === 'edit') {
                                self.down('form').loadRecord(selection);
                            }

                            // self.down('form').isValid();
                        }
                    }
                }).show();
            }
        },
        gridpanel = Ext.create('Ext.grid.Panel', {
            title: 'Notificaciones',
            id: 'gridpanel',
            closable: true,

            viewModel: {},
            reference: 'grid',
            selModel: {
                mode: 'MULTI'
            },

            viewConfig: {
                emptyText: '<div class="text-center">No existen notificaciones registradas</div>'
            },

            reserveScrollbar: true,
            scrollable: 'vertical',
            columnLines: true,

            store: Ext.create('Ext.data.JsonStore', {
                storeId: 'notificaciones_store',
                fields: ['id', 'mensaje', 'tipo', {
                    name: 'user',
                    type: 'auto'
                }, {
                    name: 'fecha_creacion',
                    type: 'date',
                    dateFormat: 'd/m/Y H:i'
                }, {
                    name: 'fecha_aceptacion',
                    type: 'date',
                    dateFormat: 'd/m/Y H:i'
                }],
                proxy: {
                    type: 'ajax',
                    url: App.buildURL('/notificacion/list'),
                    reader: {
                        rootProperty: 'rows'
                    },
                    extraParams: {
                        scope: 'all'
                    },
                    listeners: {
                        exception: function (proxy, request, operation) {
                            if (!request.aborted) {
                                var message = request.hasOwnProperty('statusText') ? request.statusText : 'La solicitud no tiene respuesta disponible',
                                    error = request.hasOwnProperty('responseText') ? ':<br><em><small>' + App.parseError(request.responseText) + '</small></em>' : '';

                                App.showAlert(message + error, 'danger', 10000);
                            }
                        }
                    }
                },
                groupField: 'type',
                autoLoad: true,
                autoDestroy: true
            }),

            columns: [{
                text: 'Mensaje',
                dataIndex: 'mensaje',
                flex: 1
            }, {
                text: 'Tipo',
                dataIndex: 'tipo',
                align: 'center',
                width: 175
            }, {
                text: 'Usuario',
                xtype: 'templatecolumn',
                //tpl: '{session_user} <small class="text-muted">{session_user_fullname}</small>',
                tpl: '<tpl if="usuario">{usuario.usuario} <small class="text-muted">{usuario.nombre_completo}</small><tpl else><strong>admin</strong></tpl>',
                align: 'center',
                width: 300
            }, {
                text: 'Creado en',
                dataIndex: 'fecha_creacion',
                xtype: 'datecolumn',
                format: 'd/m/Y H:i',
                align: 'center',
                width: 150
            }, {
                text: 'Leído en',
                dataIndex: 'fecha_aceptacion',
                // xtype: 'datecolumn',
                // format: 'Y-m-d H:i:s',
                align: 'center',
                width: 150,
                renderer: function (v) {
                    return v ? Ext.util.Format.date(v, 'd/m/Y H:i') : '<span class="badge badge-danger">PENDIENTE</span>';
                }
            }],

            // features: [{
            //     ftype: 'grouping',
            //     groupHeaderTpl: '{name}',
            //     enableGroupingMenu: false,
            //     hideGroupedHeader: true
            // }],

            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                hidden: true,
                items: [{
                    text: 'Adicionar',
                    id: 'btn_add',

                    tooltip: 'Adiciona una nueva notificación',
                    glyph: 0xf0fe,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,

                    handler: action_handler.bind(this, 'add'),

                    listeners: {
                        render: function (button) {
                            gridpanel.getStore().on('load', function () {
                                button.enable();
                            });
                        }
                    }
                }, {
                    text: 'Modificar',
                    id: 'btn_upd',

                    tooltip: 'Modifica la notificación seleccionada',
                    glyph: 0xf044,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,

                    handler: action_handler.bind(this, 'edit'),

                    listeners: {
                        render: function (button) {
                            gridpanel.on('selectionchange', function (self, selected, eOpts) {
                                button.setDisabled(selected.length !== 1);
                            });
                        }
                    }
                }, {
                    text: 'Eliminar',
                    id: 'btn_del',

                    tooltip: 'Elimina la notificación seleccionada',
                    glyph: 0xf2ed,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,

                    handler: action_handler.bind(this, 'delete'),

                    listeners: {
                        render: function (button) {
                            gridpanel.on('selectionchange', function (self, selected, eOpts) {
                                button.setTooltip(selected.length !== 1 ? 'Elimina las notificaciones seleccionadas' : 'Elimina la notificación seleccionada');
                                button.setDisabled(selected.length === 0);
                            });
                        }
                    }
                }],

                listeners: {
                    render: function (toolbar) {
                        var selection = [],
                            sel_model = gridpanel.getSelectionModel();

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
                            }
                        });
                    }
                }
            }, {
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: Ext.getStore('notificaciones_store'),
                displayInfo: true
            }]
        });

    App.render(gridpanel);
});