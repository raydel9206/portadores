/**
 * Created by yosley on 20/05/2016.
 */
Ext.onReady(function () {

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo_unidad',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
            {name: 'nmarca_vehiculo'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_persona',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/persona/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
        }
    });

    Ext.define('Portadores.accidentes.Window', {
        extend: 'Ext.window.Window',
        width: 350,
        height: 425,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 350,
                    height: 425,
                    defaultType: 'textfield',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    bodyPadding: 5,
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'vehiculoid',
                            id: 'vehiculoid',
                            fieldLabel: 'Vehículo accidentado',
                            labelWidth: 65,
                            store: Ext.getStore('id_store_vehiculo_unidad'),
                            displayField: 'matricula',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione el vehículo...',
                            selectOnFocus: true,
                            labelAlign: 'top',
                            editable: true,
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            xtype: 'datefield',
                            name: 'fecha_accidente',
                            fieldLabel: 'Fecha del accidente',
                            labelWidth: 65,
                            selectOnFocus: true,
                            editable: false,
                            labelAlign: 'top',
                            format: 'd/m/Y',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false,
                        },
                        {
                            fieldLabel: 'Asignado a',
                            xtype: 'textfield',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'asignado',
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            fieldLabel: 'Nota informativa',
                            xtype: 'textfield',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'nota_informativa',
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            xtype: 'datefield',
                            name: 'fecha_indemnizacion',
                            fieldLabel: 'Fecha de la indemnización',
                            labelAlign: 'top',
                            labelWidth: 65,
                            selectOnFocus: true,
                            editable: false,
                            format: 'd/m/Y',
                            allowBlank: true
                        },
                        {
                            fieldLabel: 'Importe de la indemnización',
                            xtype: 'numberfield',
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'importe_indemnizacion',
                            allowBlank: true,
                            margin: '0 0 10 0',
                            decimalSeparator: '.'
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    Ext.define('Portadores.accidentes_export.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        height: 180,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 300,
                    height: 300,
                    defaultType: 'textfield',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    bodyPadding: 5,
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'elaboradoid',
                            id: 'elaboradoid',
                            fieldLabel: 'Elaborado por',
                            labelAlign: 'top',
                            store: Ext.getStore('id_store_persona'),
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione ...',
                            editable: true,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false
                        },
                        {
                            xtype: 'combobox',
                            name: 'revisadoid',
                            id: 'revisadoid',
                            fieldLabel: 'Revisado por',
                            labelAlign: 'top',
                            store: Ext.getStore('id_store_persona'),
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione ...',
                            editable: true,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false
                        },
                        {
                            xtype: 'combobox',
                            name: 'aprobadoid',
                            id: 'aprobadoid',
                            fieldLabel: 'Aprobado por',
                            labelAlign: 'top',
                            store: Ext.getStore('id_store_persona'),
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione ...',
                            editable: true,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false
                        }
                    ]
                }
            ];
            this.callParent();
        },
        // listeners: {
        //     beforerender: function (This, eOpts) {
        //         Ext.getCmp('id_panel_accidentes').mask('Cargando...');
        //         Ext.getStore('id_store_persona').load();
        //         // This.body.unmask();
        //     }
        // }
    });


    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'accidentes_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        // disabled: true,
        handler: function (This, e) {
            Ext.create('Portadores.accidentes.Window', {
                title: 'Adicionar accidente',
                id: 'window_accidentes_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_accidentes_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/accidente/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                        }
                                        window.show();
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
                            Ext.getCmp('window_accidentes_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'accidentes_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_accidentes').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.accidentes.Window', {
                title: 'Modificar accidente',
                id: 'window_accidentes_id',
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

                                App.request('POST', App.buildURL('/portadores/accidente/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_accidentes').getStore().loadPage(1);

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
                            Ext.getCmp('window_accidentes_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'accidentes_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_accidentes').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Accidente?',
                message: '¿Está seguro que desea eliminar accidente seleccionado?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/accidente/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_accidentes').getStore().load();
                            }
                        });
                    }
                }
            });
        }
    });

    var btnExport = Ext.create('Ext.button.MyButton', {
        id: 'accidentes_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {
            Ext.create('Portadores.accidentes_export.Window', {
                title: 'Exportar solicitudes',
                width: 300,
                height: 250,
                id: 'window_accidentes_export_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_accidentes_export_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                App.request('POST', App.buildURL('/portadores/accidente/export'), obj, null, null, null, null);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_accidentes_export_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    /*var _btn_visualizarCdt = Ext.create('Ext.button.MyButton', {
        text: 'Visualizar CDT',
        width: 100,
        handler: function (This) {
            Ext.create('Portadores.accidentes_cdt.Window', {
                id: 'window_accidentes_cdt_id',
                buttons: [
                    {
                        text: 'Exportar',
                        handler: function () {
                            Ext.create('Portadores.accidentes_export.Window', {
                                title: 'Exportar solicitudes',
                                width: 300,
                                height: 250,
                                id: 'window_accidentes_export_id',
                                buttons: [
                                    {
                                        text: 'Aceptar',
                                        width: 70,
                                        handler: function () {
                                            var window1 = Ext.getCmp('window_accidentes_export_id');
                                            var form = window1.down('form').getForm();
                                            var obj = form.getValues();
                                            var store = Ext.getCmp('grid_cdt_accidentes').getStore();
                                            var send = [];
                                            Ext.Array.each(store.data.items,function(valor){
                                                send.push(valor.data);
                                            });
                                            obj.store = Ext.encode(send);
                                            if (form.isValid()) {
                                                App.ShowWaitMsg();
                                                window1.close();
                                                window.open(Routing.generate('exportCdtToExcel', obj),
                                                    '_blank', '', false);
                                                App.HideWaitMsg();
                                            }
                                        }
                                    },
                                    {
                                        text: 'Cancelar',
                                        width: 70,
                                        handler: function () {
                                            Ext.getCmp('window_accidentes_export_id').close()
                                        }
                                    }
                                ]
                            }).show();
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function () {
                            Ext.getCmp('window_accidentes_cdt_id').close();
                        }
                    }
                ]
            }).show();
            Ext.getCmp('grid_cdt_accidentes').getStore().load();
        }
    });*/

    var _tbar = Ext.getCmp('accidentes_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.add('->');
    // _tbar.add(_cmb_unidad);
    // _tbar.add('-');
    _tbar.add(btnExport);
    _tbar.setHeight(36);
});
