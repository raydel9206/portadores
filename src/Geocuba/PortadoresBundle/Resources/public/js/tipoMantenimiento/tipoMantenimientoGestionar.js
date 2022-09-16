
Ext.onReady(function () {

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_mantenimiento_clasificacion',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipoMantenimiento/loadTipoMantenimientoClasificacion'),
            reader: {
                rootProperty: 'rows',
                totalProperty: 'total'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.define('Portadores.tipomantenimiento.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    bodyPadding: 5,
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Nombre',
                            labelWidth: 80,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'nombre',
                            id: 'nombre'
                        },
                        {
                            xtype: 'combobox',
                            name: 'clasificacionid',
                            id: 'clasificacionid',
                            fieldLabel: 'Clasificación',
                            labelWidth: 80,
                            store: Ext.getStore('id_store_tipo_mantenimiento_clasificacion'),
                            displayField: 'nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la unidad...',
                            selectOnFocus: true,
                            editable: true,
                            allowBlank: false
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'tipomantenimiento_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.tipomantenimiento.Window', {
                title: 'Adicionar tipo de mantenimiento',
                id: 'window_tipomantenimiento_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_tipomantenimiento_id');
                            var form = window.down('form').getForm();

                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('portadores/tipoMantenimiento/addTipoMantenimiento'), form.getValues(), null, null,
                                    function (response) {
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_tipomantenimiento').loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                            window.show();
                                        }

                                    },
                                    function (response) { // failure_callback
                                        window.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tipomantenimiento_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'tipomantenimiento_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_tipomantenimiento').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.tipomantenimiento.Window', {
                title: 'Modificar tipo de mantenimiento',
                id: 'window_tipomantenimiento_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('portadores/tipoMantenimiento/modTipoMantenimiento'), obj, null, null,
                                    function (response) {
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_tipomantenimiento').loadPage(1);
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
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_tipomantenimiento_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'tipomantenimiento_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_tipomantenimiento').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar Tipo de Mantenimiento?' : '¿Eliminar Tipos de Mantenimiento?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el tipo de mantenimiento <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar los tipos de mantenimientos seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/tipoMantenimiento/delTipoMantenimiento'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_marca_norma').setCollapsed(true);
                                Ext.getCmp('id_grid_tipomantenimiento').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('tipomantenimiento_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
