Ext.onReady(function () {
    var tree_store = Ext.create('Ext.data.TreeStore', {
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        autoLoad: true
    });

    var tree_store_by_unidad = Ext.create('Ext.data.TreeStore', {
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                //rootProperty: 'children'
            }
        },
        root: {
            expanded: true,
            children: []
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                operation.setParams({
                    unidad_id: (Ext.getCmp('unidad_id') !== undefined) ? Ext.getCmp('unidad_id').getValue() : null,
                    checked: true
                });
            },
            load: function (This, records, successful, operation, node, eOpts) {
                var selection = Ext.getCmp('gridpanel').getSelectionModel().getSelection();
                if (selection[0]) {
                    App.request('GET', App.buildURL('/admin/usuario/loadDominio'), {userid: selection[0].data.id}, null, null,
                        function (response) {
                            if (response && response.hasOwnProperty('success') && response.success && response.unidades.length !== 0) { // success_callback but check if exists errors
                                Ext.Array.each(response.unidades, function (valor) {
                                    if (tree_store_by_unidad.findRecord('id', valor))
                                        tree_store_by_unidad.findRecord('id', valor).data.checked = true;
                                });
                                This.getRoot().child().data.checked = true;

                                Ext.getCmp('grid_unidades_dominio_id').getView().refresh();
                            }
                        }, null, null, true);
                }

            }
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'grupos_store',
        fields: ['id', 'nombre'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/admin/grupo/list'),
            extraParams: {simple: true},
            reader: {rootProperty: 'rows'}
        },
        pageSize: 0,
        autoLoad: false
    });

    var action_handler = function (action) {
            var url = App.buildURL('/admin/usuario/' + action),
                selection;

            if (action === 'delete') {
                if (!gridpanel.getSelectionModel().hasSelection()) {
                    return;
                }

                selection = gridpanel.getSelection();

                Ext.Msg.show({
                    title: selection.length === 1 ? '¿Eliminar usuario?' : '¿Eliminar usuarios?',
                    message: selection.length === 1 ?
                        Ext.String.format('¿Está seguro que desea eliminar el usuario <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('usuario')) :
                        'Está seguro que desea eliminar los usuarios seleccionados?',
                    buttons: Ext.Msg.YESNO,
                    icon: Ext.Msg.QUESTION,
                    fn: function (btn) {
                        if (btn === 'yes') {
                            var params = {limit: App.page_limit};

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
                    title: action === 'edit' ? Ext.String.format('Modificar <span class="font-italic">{0}</span>', selection.get('usuario')) : 'Adicionar usuario',

                    glyph: action === 'add' ? 0xf0fe : 0xf044,

                    resizable: false,
                    modal: true,
                    layout: 'fit',
                    width: 850,

                    defaultFocus: '[name=nombre_completo]',

                    items: {
                        xtype: 'form',
                        layout: 'column',
                        bodyPadding: 10,
                        defaults: {
                            width: '100%'
                        },
                        items: [
                            {
                                xtype: 'fieldset',
                                title: 'Datos del usuario',
                                columnWidth: .48,
                                // height: '100%',
                                // margin: '0 5 0 0',
                                fieldDefaults: {
                                    labelWidth: 145,
                                    margin: '5 0 0 0',
                                    anchor: '100%',
                                    msgTarget: 'side',
                                    autoFitErrors: true,
                                    labelAlign: 'top'
                                },
                                defaultType: 'textfield',
                                items: [
                                    {
                                        xtype: 'hiddenfield',
                                        name: 'id',
                                        submitValue: action === 'edit'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Nombre completo',
                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                        name: 'nombre_completo',
                                        minLength: 10,
                                        maxLength: 70,
                                        flex: 1,
                                        listeners: {
                                            change: function (field, newValue, oldValue, eOpt) {
                                                field.getTrigger('clear').setVisible(newValue);
                                            },

                                            disable: function (field, eOpts) {
                                                field.setValue(null);
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
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Cargo',
                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                        name: 'cargo',
                                        minLength: 10,
                                        maxLength: 70,
                                        // flex: 1,
                                        width: 238,
                                        listeners: {
                                            change: function (field, newValue, oldValue, eOpt) {
                                                field.getTrigger('clear').setVisible(newValue);
                                            },

                                            disable: function (field, eOpts) {
                                                field.setValue(null);
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
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'ID Usuario',
                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                        name: 'usuario',
                                        minLength: 3,
                                        maxLength: 15,
                                        vtype: 'alphanum',
                                        width: 175
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: 'Email',
                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                        name: 'email',
                                        vtype: 'email',
                                        // margin: {left: 10},
                                        // width: 175,
                                        value: 'dummy@fake.com'
                                    },
                                    action === 'add' ? {
                                        xtype: 'container',
                                        id: 'pass_container',
                                        layout: 'vbox',
                                        width: '100%',

                                        defaults: {
                                            labelAlign: 'top',
                                            allowBlank: false,
                                            enforceMaxLength: true
                                        },

                                        items: [
                                            {
                                                xtype: 'container',
                                                layout: 'hbox',
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'contrasena',
                                                        inputType: 'password',
                                                        fieldLabel: 'Contraseña',
                                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                                        minLength: 5,
                                                        maxLength: 15,
                                                        width: 175,
                                                        listeners: {
                                                            change: function (self, newValue, oldValue) {
                                                                var next = self.nextSibling();

                                                                if (self.isValid()) {
                                                                    var form = self.up('form'), value_2 = next.getValue();
                                                                    if (value_2.trim() !== newValue.trim()) {
                                                                        // next.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                                        Ext.getCmp('error_pass').setData({error: 'Las contraseñas no coinciden'});
                                                                        if (form.isValid()) {
                                                                            form.down('[text=Aceptar]').disable();
                                                                        }
                                                                    } else {
                                                                        // next.nextSibling().setData(null);
                                                                        Ext.getCmp('error_pass').setData(null);
                                                                        if (form.isValid()) {
                                                                            form.down('[text=Aceptar]').enable();
                                                                        }
                                                                    }
                                                                } else {
                                                                    // next.nextSibling().setData(null);
                                                                    Ext.getCmp('error_pass').setData(null);
                                                                }
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        inputType: 'password',
                                                        fieldLabel: 'Verificar contraseña',
                                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                                        margin: {left: 10, top: 5},
                                                        submitValue: false,
                                                        minLength: 5,
                                                        maxLength: 15,
                                                        width: 175,
                                                        listeners: {
                                                            change: function (self, newValue, oldValue) {
                                                                if (self.isValid()) {
                                                                    var form = self.up('form'),
                                                                        previous = self.previousSibling(),
                                                                        value_1 = previous.getValue();

                                                                    if (value_1.trim() !== newValue.trim()) {
                                                                        // self.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                                        Ext.getCmp('error_pass').setData({error: 'Las contraseñas no coinciden'});
                                                                        // Ext.getCmp('error_pass').setHidden(false);
                                                                        if (form.isValid()) {
                                                                            form.down('[text=Aceptar]').disable();
                                                                        }
                                                                    } else {
                                                                        // self.nextSibling().setData(null);
                                                                        Ext.getCmp('error_pass').setData(null);
                                                                        if (form.isValid()) {
                                                                            form.down('[text=Aceptar]').enable();
                                                                        }
                                                                    }
                                                                } else {
                                                                    // self.nextSibling().setData(null);
                                                                    Ext.getCmp('error_pass').setData(null);
                                                                }
                                                            }
                                                        }
                                                    }]
                                            },
                                            {
                                                xtype: 'container',
                                                id: 'error_pass',
                                                style: 'padding-top: 10px; color: #cf4c35',
                                                tpl: '<tpl if="error"><small><i class="fa fa-info-circle"></i> {error}</small></tpl>',
                                                margin: {left: 7}
                                            }]
                                    } : null
                                    /*{
                                        xtype: 'fieldset',
                                        id: 'pass_container',
                                        title: 'Cambiar contraseña',

                                        checkboxToggle: true,
                                        collapsed: true,

                                        layout: 'hbox',
                                        margin: {top: 10, bottom: 0},
                                        width: '100%',

                                        defaults: {
                                            labelAlign: 'top',
                                            allowBlank: false,
                                            enforceMaxLength: true
                                        },

                                        items: [{
                                            xtype: 'textfield',
                                            name: 'contrasena',

                                            inputType: 'password',
                                            fieldLabel: 'Contraseña',
                                            afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                            width: 175,

                                            minLength: 5,
                                            maxLength: 15,
                                            disabled: true,

                                            listeners: {
                                                change: function (self, newValue, oldValue) {
                                                    var next = self.nextSibling();

                                                    if (self.isValid()) {
                                                        var form = self.up('form'),
                                                            value_2 = next.getValue();

                                                        if (value_2.trim() !== newValue.trim()) {
                                                            next.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                            if (form.isValid()) {
                                                                form.down('[text=Aceptar]').disable();
                                                            }
                                                        } else {
                                                            next.nextSibling().setData(null);
                                                            if (form.isValid()) {
                                                                form.down('[text=Aceptar]').enable();
                                                            }
                                                        }
                                                    } else {
                                                        next.nextSibling().setData(null);
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'textfield',
                                            inputType: 'password',
                                            fieldLabel: 'Verificar contraseña',
                                            afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                            width: 175,
                                            margin: {left: 10},

                                            submitValue: false,
                                            minLength: 5,
                                            maxLength: 15,
                                            disabled: true,

                                            listeners: {
                                                change: function (self, newValue, oldValue) {
                                                    if (self.isValid()) {
                                                        var form = self.up('form'),
                                                            previous = self.previousSibling(),
                                                            value_1 = previous.getValue();

                                                        if (value_1.trim() !== newValue.trim()) {
                                                            self.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                            if (form.isValid()) {
                                                                form.down('[text=Aceptar]').disable();
                                                            }
                                                        } else {
                                                            self.nextSibling().setData(null);
                                                            if (form.isValid()) {
                                                                form.down('[text=Aceptar]').enable();
                                                            }
                                                        }
                                                    } else {
                                                        self.nextSibling().setData(null);
                                                    }
                                                }
                                            }
                                        }, {
                                            xtype: 'container',
                                            style: 'padding-top: 30px; color: #cf4c35',
                                            tpl: '<tpl if="error"><small><i class="fa fa-info-circle"></i> {error}</small></tpl>',
                                            margin: {left: 7}
                                        }],
                                        listeners: {
                                            expand: function (fieldset, eOpts) {
                                                fieldset.items.each(function (item) {
                                                    item.enable();
                                                });
                                            },

                                            collapse: function (fieldset, eOpts) {
                                                fieldset.items.each(function (item) {
                                                    item.disable();
                                                });
                                            }
                                        }
                                    }*/,
                                    {
                                        xtype: 'tagfield',
                                        name: 'grupos_ids',
                                        fieldLabel: '<strong>Grupos</strong>',
                                        afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                                        labelWidth: 60,
                                        margin: {top: 5, left: 0},
                                        store: Ext.getStore('grupos_store'),
                                        displayField: 'nombre',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        forceSelection: true,
                                        allowBlank: false,
                                        filterPickList: true,
                                        listeners: {
                                            change: function () {
                                                if (action === 'add')
                                                    return null;
                                                else
                                                    Ext.getCmp('groups_change').setHidden(false);
                                            }
                                        }
                                    },
                                    action === 'add' ? null : {
                                        xtype: 'container',
                                        id: 'groups_change',
                                        hidden: true,
                                        margin: 5,
                                        html: '<div class="text-center"><small class="text-dark"><i class="fa fa-info-circle"></i> <strong>Los cambios en los grupos se aplicarán cuando el usuario reinicie la sesión.</strong></small></div>'
                                    }
                                ]
                            },

                            {
                                xtype: 'fieldset',
                                title: 'Dominio del usuario',
                                columnWidth: .50,
                                margin: {left: 10, top: 0},
                                fieldDefaults: {
                                    labelWidth: 145,
                                    margin: '5 0 0 0',
                                    anchor: '100%',
                                    msgTarget: 'side',
                                    autoFitErrors: true,
                                    labelAlign: 'top'
                                },
                                items: [
                                    {
                                        xtype: 'treecombobox',
                                        fieldLabel: 'Unidad',
                                        valueField: 'id',
                                        displayField: 'nombre',
                                        name: 'unidad_id',
                                        id: 'unidad_id',
                                        emptyText: 'Seleccione la unidad...',
                                        store: tree_store,
                                        queryMode: 'remote',
                                        forceSelection: true,
                                        allowBlank: false,
                                        editable: false,
                                        anyMatch: true,
                                        allowFolderSelect: true,
                                        treeConfig: {
                                            maxHeight: 200,
                                            scrolling: true
                                        },
                                        listeners: {
                                            change: function (This, record) {
                                                Ext.getCmp('grid_unidades_dominio_id').getStore().load();
                                                // Ext.getCmp('centro_costo').getStore().load();
                                            },
                                            select: function () {
                                                Ext.getCmp('grid_unidades_dominio_id').getStore().load();
                                            }

                                        }
                                    },
                                    {
                                        xtype: 'treepanel',
                                        id: 'grid_unidades_dominio_id',
                                        title: 'Unidades que visualiza',
                                        margin: '5 0 0 0',
                                        columnLines: true,
                                        rowLines: true,
                                        height: 360,
                                        store: tree_store_by_unidad,
                                        rootVisible: false,
                                        frame: true,
                                        loader: {
                                            preloadedChildren: true
                                        },
                                        columns: [
                                            {
                                                xtype: 'treecolumn',
                                                text: "<b>Unidades</b>",
                                                flex: 1,
                                                dataIndex: 'nombre',
                                                iconCls: 'fa fa-arrows-h'
                                            }
                                        ],
                                        listeners: {
                                            beforerender: function () {
                                                tree_store_by_unidad.removeAll();
                                            },
                                            checkchange: function (node, checked, eOpts) {
                                                switch (node.getDepth()) {
                                                    case 1:
                                                        node.eachChild(function (route_node) {
                                                            route_node.set('checked', checked);
                                                            route_node.eachChild(function (resource_node) {
                                                                resource_node.set('checked', checked);
                                                            });
                                                        });

                                                        break;
                                                    case 2:
                                                        node.eachChild(function (resource_node) {
                                                            resource_node.set('checked', checked);
                                                        });

                                                        if (checked) {
                                                            node.parentNode.set('checked', true);
                                                        } else {
                                                            node.parentNode.set('checked', !Ext.isEmpty(node.parentNode.findChild('checked', true, true)));
                                                        }

                                                        break;
                                                    case 3:
                                                        if (checked) {
                                                            node.parentNode.set('checked', true);
                                                            node.parentNode.parentNode.set('checked', true);
                                                        } else {
                                                            node.parentNode.set('checked', !Ext.isEmpty(node.parentNode.findChild('checked', true, true)));
                                                            node.parentNode.parentNode.set('checked', !Ext.isEmpty(node.parentNode.parentNode.findChild('checked', true, true)));
                                                        }
                                                        break;
                                                }

                                                if (checked) {
                                                    node.expand(true);
                                                } else {
                                                    if (node.getDepth() === 1) {
                                                        node.collapseChildren(true);
                                                    } else {
                                                        node.collapse(true);
                                                    }
                                                }
                                            },
                                        }
                                    }
                                ]
                            }

                        ],
                        bbar: {
                            ui: 'footer',
                            layout: {pack: 'center'},
                            items: [{
                                xtype: 'button',
                                text: 'Aceptar',
                                formBind: true,
                                width: 70,
                                handler: function (button, event) {
                                    var params = Ext.Object.merge({limit: gridpanel.getStore().getPageSize()}, formpanel.down('form').getValues());
                                    var send = [];
                                    Ext.Array.each(Ext.getCmp('grid_unidades_dominio_id').getStore().data.items, function (valor) {
                                        if (Ext.getCmp('unidad_id').getValue() !== valor.data.id) {
                                            if (valor.data.checked) {
                                                var add = {};
                                                add.id = valor.data.id;
                                                add.nombre = valor.data.nombre;
                                                add.siglas = valor.data.siglas;
                                                add.parentid = valor.data.parentId;
                                                send.push(add);
                                            }
                                        }
                                    });
                                    params.dominio = Ext.encode(send);

                                    formpanel.hide();

                                    App.request('POST', url, params, null, null,
                                        function (response) { // success_callback
                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                gridpanel.getStore().loadPage(response.page || gridpanel.getStore().currentPage);
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
                                        }
                                    );
                                }
                            }, {
                                xtype: 'button',
                                text: 'Cancelar',
                                width: 70,
                                handler: function (button, event) {
                                    formpanel.close();
                                }
                            }]
                        },
                        listeners: {
                            validitychange: function (form, valid, eOpts) {
                                if (valid) {
                                    if (action === 'add') {
                                        var field_container = this.down('#pass_container');
                                        if (field_container.down('[fieldLabel=Contraseña]').getValue().trim() !== field_container.down('[fieldLabel=Verificar contraseña]').getValue().trim()) {
                                            field_container.down('container').setData({error: 'Las contraseñas no coinciden'});
                                            this.down('[text=Aceptar]').disable();
                                        } else {
                                            field_container.down('container').setData(null);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    listeners: {
                        boxready: function (self) {
                            if (action === 'edit') {
                                self.down('form').loadRecord(selection);
                                var record = tree_store.findRecord('id', selection.data.unidad);
                                Ext.getCmp('unidad_id').onPicked(record);
                            }
                        }
                    }
                }).show();
            }
        },
        gridpanel = Ext.create('Ext.grid.Panel', {
            title: 'Usuarios',
            id: 'gridpanel',
            closable: true,

            selModel: {
                mode: 'MULTI'
            },

            viewConfig: {
                emptyText: '<div class="text-center">No existen usuarios registrados</div>'
            },

            reserveScrollbar: true,
            scrollable: 'vertical',
            columnLines: true,

            store: Ext.create('Ext.data.JsonStore', {
                storeId: 'usuarios_store',
                fields: ['id', 'usuario', 'nombre_completo', 'cargo', 'email', {
                    name: 'grupos', type: 'auto'
                }, {
                    name: 'grupos_ids',
                    calculate: function (data) {
                        return data['grupos'] ? Ext.Array.map(data['grupos'], function (grupo) {
                            return grupo['id'];
                        }) : [];
                    }
                }, {
                    name: 'fecha_creacion',
                    type: 'date',
                    dateFormat: 'd/m/Y H:i'
                }, {
                    name: 'fecha_modificacion',
                    type: 'date',
                    dateFormat: 'd/m/Y H:i'
                }],
                proxy: {
                    type: 'ajax',
                    url: App.buildURL('/admin/usuario/list'),
                    reader: {
                        rootProperty: 'rows'
                    },
                    listeners: {
                        exception: function (proxy, request, operation) {
                            if (!request.aborted) {
                                var message = request.hasOwnProperty('statusText') ? request.statusText : 'La solicitud no tiene respuesta disponible',
                                    error = error = request.hasOwnProperty('responseText') ? ':<br><em><small>' + App.parseError(request.responseText) + '</small></em>' : '';

                                App.showAlert(message + error, 'danger', 10000);
                            }
                        }
                    }
                },
                autoLoad: true,
                autoDestroy: true
            }),

            columns: [{
                text: 'Usuario',
                dataIndex: 'usuario',
                width: 125,
                filter: {type: 'string'}
            }, {
                text: 'Nombre completo',
                dataIndex: 'nombre_completo',
                flex: 1,
                align: 'center'
            }, {
                text: 'Cargo',
                dataIndex: 'cargo',
                width: 200,
                align: 'center'
            }, {
                text: 'Email',
                xtype: 'templatecolumn',
                tpl: '<a href="mailto:{email}">{email}</a>',
                width: 175,
                align: 'center'
            }, {
                text: 'Grupos',
                xtype: 'templatecolumn',
                tpl: '<tpl for="grupos" between=",">{nombre}</tpl>',
                width: 200,
                align: 'center'
            }, {
                text: 'Creado en',
                dataIndex: 'fecha_creacion',
                xtype: 'datecolumn',
                format: 'd/m/Y H:i',
                width: 145,
                align: 'center'
            }],

            features: [{
                // startCollapsed: true,
                ftype: 'grouping',
                groupHeaderTpl: '{name}'
            }],

            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    text: 'Adicionar',
                    id: 'btn_add',
                    tooltip: 'Adiciona un nuevo usuario',
                    glyph: 0xf0fe,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,
                    hidden: true,
                    handler: action_handler.bind(this, 'add')
                }, {
                    text: 'Modificar',
                    id: 'btn_upd',
                    tooltip: 'Modifica el usuario seleccionado',
                    glyph: 0xf044,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,
                    hidden: true,
                    handler: action_handler.bind(this, 'edit')
                }, {
                    text: 'Eliminar',
                    id: 'btn_del',
                    tooltip: 'Elimina el usuario seleccionado',
                    glyph: 0xf2ed,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    width: 95,
                    disabled: true,
                    hidden: true,
                    handler: action_handler.bind(this, 'delete')
                }, {
                    xtype: 'tbseparator',
                    hidden: true
                }, {
                    text: 'Cambiar contraseña',
                    id: 'btn_pwd',
                    tooltip: 'Modifica la contraseña del usuario seleccionado',
                    glyph: 0xf084,
                    cls: 'border-secondary',
                    iconCls: 'text-dark',
                    // width: 165,
                    hidden: true,
                    disabled: true,
                    handler: function (button, e) {
                        var selection = gridpanel.getSelection()[0];

                        Ext.create('Ext.window.Window', {
                            title: Ext.String.format('Cambiar contraseña de <span class="font-italic">{0}</span>', selection.get('usuario')),

                            glyph: 0xf084,
                            defaultFocus: '[name=contrasena]',

                            modal: true,
                            layout: 'fit',
                            minWidth: 550,
                            resizable: false,

                            items: {
                                xtype: 'form',
                                layout: 'hbox',
                                items: [{
                                    xtype: 'hiddenfield',
                                    name: 'id'
                                }, {
                                    xtype: 'container',
                                    width: '100%',
                                    padding: '5px 10px 10px 10px',
                                    layout: 'hbox',
                                    defaults: {
                                        labelAlign: 'top',
                                        allowBlank: false,
                                        enforceMaxLength: true,
                                        flex: 1
                                    },
                                    items: [{
                                        xtype: 'textfield',
                                        inputType: 'password',
                                        fieldLabel: 'Contraseña',
                                        name: 'contrasena',
                                        minLength: 5,
                                        maxLength: 15,
                                        listeners: {
                                            change: function (self, newValue, oldValue) {
                                                var next = self.nextSibling();

                                                if (self.isValid()) {
                                                    var form = self.up('form'), value_2 = next.getValue();
                                                    if (value_2.trim() !== newValue.trim()) {
                                                        next.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                        if (!form.hasInvalidField()) {
                                                            form.down('[text=Aceptar]').disable();
                                                        }
                                                    } else {
                                                        next.nextSibling().setData(null);
                                                        if (!form.hasInvalidField()) {
                                                            form.down('[text=Aceptar]').enable();
                                                        }
                                                    }
                                                } else {
                                                    next.nextSibling().setData(null);
                                                }
                                            }
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        inputType: 'password',
                                        fieldLabel: 'Verificar contraseña',
                                        margin: {left: 7},
                                        submitValue: false,
                                        minLength: 5,
                                        maxLength: 15,
                                        listeners: {
                                            change: function (self, newValue, oldValue) {
                                                if (self.isValid()) {
                                                    var form = self.up('form'), previous = self.previousSibling();
                                                    var value_1 = previous.getValue();
                                                    if (value_1.trim() !== newValue.trim()) {
                                                        self.nextSibling().setData({error: 'Las contraseñas no coinciden'});
                                                        if (!form.hasInvalidField()) {
                                                            form.down('[text=Aceptar]').disable();
                                                        }
                                                    } else {
                                                        self.nextSibling().setData(null);
                                                        if (!form.hasInvalidField()) {
                                                            form.down('[text=Aceptar]').enable();
                                                        }
                                                    }
                                                } else {
                                                    self.nextSibling().setData(null);
                                                }
                                            }
                                        }
                                    }, {
                                        xtype: 'container',
                                        name: 'container',
                                        style: 'padding-top: 30px; color: #cf4c35',
                                        tpl: '<tpl if="error"><small><i class="fa fa-info-circle"></i> {error}</small></tpl>',
                                        margin: {left: 7}
                                    }]
                                }],
                                bbar: {
                                    ui: 'footer',
                                    layout: {pack: 'center'},

                                    items: [{
                                        xtype: 'button',
                                        text: 'Aceptar',
                                        formBind: true,
                                        handler: function (button, event) {
                                            var window = button.up('window').hide(),
                                                url = App.buildURL('admin/usuario/reset'),
                                                textfield = gridpanel.down('toolbar').down('textfield'),
                                                params = Ext.Object.merge({
                                                    limit: gridpanel.getStore().getPageSize()
                                                }, window.down('form').getValues());

                                            if (textfield.marked) {
                                                params.query = textfield.getValue();
                                            }

                                            App.request('POST', url, params, null, null,
                                                function (response) { // success_callback
                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                        gridpanel.getStore().loadPage(response.page || gridpanel.getStore().currentPage);
                                                        window.close();
                                                    } else {
                                                        if (response && response.hasOwnProperty('errors') && response.errors) {
                                                            window.down('form').getForm().markInvalid(response.errors);
                                                        }
                                                        window.show();
                                                    }
                                                },
                                                function (response) { // failure_callback
                                                    window.show();
                                                });
                                        },
                                        width: 70
                                    }, {
                                        xtype: 'button',
                                        text: 'Cancelar',
                                        handler: function (button, event) {
                                            button.up('window').close();
                                        },
                                        width: 70
                                    }]
                                },
                                listeners: {
                                    validitychange: function (form, valid, eOpts) {
                                        if (valid) {
                                            var panel = form.owner;
                                            if (panel.down('[fieldLabel=Contraseña]').getValue().trim() !== panel.down('[fieldLabel=Verificar contraseña]').getValue().trim()) {
                                                panel.down('[name=container]').setData({error: 'Las contraseñas no coinciden'});
                                                panel.down('[text=Aceptar]').disable();
                                            } else {
                                                panel.down('[name=container]').setData(null);
                                            }
                                        }
                                    }
                                }
                            },
                            listeners: {
                                show: function (window) {
                                    window.down('form').loadRecord(selection);
                                }
                            }
                        }).show();
                    }
                }, '->', {
                    xtype: 'textfield',
                    emptyText: 'Buscar...',
                    // hidden: true,
                    width: 250,
                    listeners: {
                        render: function (field) {
                            gridpanel.getStore().on({
                                beforeload: function (store, operation, eOpts) {
                                    if (field.marked) {
                                        var value = field.getValue();

                                        if (!Ext.isEmpty(Ext.String.trim(value))) {
                                            operation.setParams({
                                                query: value
                                            });
                                        }
                                    }
                                },
                                load: function () {
                                    field.enable();

                                    // if (field.marked) {
                                    //     gridpanel.setTitle(Ext.String.format('Usuarios [Filtro: <strong><em>{0}</em></strong>]', field.getValue()));
                                    // }
                                }
                            });
                        },
                        change: function (field, newValue, oldValue, eOpt) {
                            field.getTrigger('clear').setVisible(newValue);

                            if (Ext.isEmpty(Ext.String.trim(field.getValue()))) {
                                var marked = field.marked;
                                field.setMarked(false);

                                if (marked) {
                                    gridpanel.getStore().loadPage(1);
                                }

                                field.getTrigger('search').hide();
                            } else {
                                field.getTrigger('search').show();

                                if (field.marked) {
                                    field.setMarked(true);
                                }
                            }
                        },
                        specialkey: function (field, e) {
                            var value = field.getValue();

                            if (!Ext.isEmpty(Ext.String.trim(value)) && e.getKey() === e.ENTER) {
                                field.setMarked(true);
                                gridpanel.getStore().loadPage(1);
                            } else if (e.getKey() === e.BACKSPACE && e.getKey() === e.DELETE && (e.ctrlKey && e.getKey() === e.V)) {
                                field.setMarked(false);
                            }
                        }
                    },
                    triggers: {
                        search: {
                            cls: Ext.baseCSSPrefix + 'form-search-trigger',
                            hidden: true,
                            handler: function () {
                                var value = this.getValue();

                                if (!Ext.isEmpty(Ext.String.trim(value))) {
                                    this.setMarked(true);
                                    gridpanel.getStore().loadPage(1, {params: {query: value}});
                                }
                            }
                        },
                        clear: {
                            cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                            hidden: true,
                            handler: function () {
                                this.setValue(null);
                                this.updateLayout();

                                if (this.marked) {
                                    gridpanel.getStore().loadPage(1);
                                }
                                gridpanel.setTitle('Usuarios');

                                this.setMarked(false);
                            }
                        }
                    },

                    setMarked: function (marked) {
                        var el = this.getEl(),
                            id = '#' + this.getId();

                        this.marked = marked;

                        if (marked) {
                            el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                            el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                            el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
                        } else {
                            el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                            el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                            el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
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
                                });
                            },
                            load: function (store, records, successful, eOpts) {
                                selection = Ext.Array.map(selection, function (record, index) { // update the selected record
                                    return store.getById(record.getId());
                                });
                                selection = Ext.Array.clean(selection);
                                sel_model.select(selection);

                                toolbar.items.getAt(0).enable();
                            }
                        });
                    }
                }
            }, {
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: Ext.getStore('usuarios_store'),
                displayInfo: true
            }],

            listeners: {
                selectionchange: function (sel_model, selected, eOpts) {
                    var toolbar = gridpanel.down('toolbar');

                    if (selected.length === 0) {
                        toolbar.items.getRange(1, 2).forEach(function (item) {
                            item.disable();
                        });
                        toolbar.items.getAt(4).disable();
                    } else if (selected.length === 1) {
                        toolbar.items.getRange(1, 4).forEach(function (item) {
                            item.enable();
                        });
                    } else {
                        toolbar.items.getAt(1).disable();
                        toolbar.items.getAt(2).enable();
                        toolbar.items.getAt(4).disable();
                    }
                }
            }
        });

    App.render(gridpanel);
});