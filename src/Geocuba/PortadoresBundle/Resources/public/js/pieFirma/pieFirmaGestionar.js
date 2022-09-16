/**
 * Created by pfcadenas on 27/09/2016.
 */
Ext.onReady(function () {
    let store_confeccionado = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_confeccionado',
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
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    let store_cajera = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_cajera',
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
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    let store_revisado = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_revisado',
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
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    let store_aprobado = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_aprobado',
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
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
        }
    });

    Ext.define('Portadores.PieFirma.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        width: 450,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: true
                    },
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'documento',
                            id: 'documento',
                            fieldLabel: 'Documento ',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: Ext.create('Ext.data.JsonStore', {
                                fields: [
                                    {name: 'id'},
                                    {name: 'nombre'}
                                ],
                                data: [
                                    {id: '1', nombre: 'Plan de Combustible'},
                                    {id: '2', nombre: 'Análisis de los consumos Equipo a Equipo'},
                                    {id: '3', nombre: 'Distribución de Combustible'},
                                    {id: '4', nombre: 'Autorizo de cambio o entrega de tarjeta'},
                                    {id: '5', nombre: 'Equipos Paralizados'},
                                    // {id: '6', nombre: 'Plan de Agua'},
                                    {id: '7', nombre: 'Plan de Electricidad'},
                                    {id: '8', nombre: 'Control de Combustible por Depósitos'},
                                    {id: '9', nombre: 'Control de Combustible por Vehículos'},
                                    {id: '10', nombre: 'Estado de las Tarjetas'},
                                    {id: '11', nombre: 'Libro de Combustible en Caja'},
                                    // {id: '12', nombre: 'Parte Mensual de Explotación'},
                                    // {id: '13', nombre: 'Resumen de Explotación de los Vehículos'},
                                    {id: '14', nombre: 'Reporte Diario los Servicios Eléctricos'},
                                    // {id: '15', nombre: 'Reporte del Parte Diario de Agua'},
                                    // {id: '16', nombre: 'Relación de Equipos Ineficientes'},
                                    {id: '17', nombre: 'Modelo CDA 002'},
                                    {id: '18', nombre: 'Bitácora'},
                                    {id: '19', nombre: 'Anexo 1. Registro de Operaciones'},
                                    // {id: '20', nombre: 'Mediciones Diarias de los Grupos Electrógenos de Emergencia'},
                                    {id: '21', nombre: 'Entrega de Chip de Combustibles'},
                                    {id: '22', nombre: 'Anexo 8'},
                                    {id: '23', nombre: 'Conciliación Mensual de Transporte'},
                                    {id: '24', nombre: 'Registro de Combustible'},
                                    {id: '25', nombre: 'Cierre Mensual'},
                                    {id: '26', nombre: 'Modelo CDA 001'},
                                    {id: '27', nombre: 'Conciliación de motorrecursos'},
                                    {id: '28', nombre: 'Reembolso de combustible'},
                                    {id: '29', nombre: 'Modelo 5073'},
                                ]
                            }),
                            displayField: 'nombre',
                            valueField: 'id',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione el documento...',
                            selectOnFocus: true,
                            editable: true,
                            allowBlank: false
                        },

                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [{
                                xtype: 'combobox',
                                name: 'confecciona',
                                id: 'confecciona',
                                fieldLabel: 'Elaborado por ',
                                flex: 1,
                                margin: '0 5 0 0',
                                store: store_confeccionado,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                // triggerAction: 'all',
                                emptyText: 'Seleccione la persona...',
                                selectOnFocus: true,
                                editable: true
                            }, {
                                xtype: 'combobox',
                                name: 'cajera',
                                id: 'cajera',
                                fieldLabel: 'Cajera ',
                                flex: 1,
                                margin: '0 0 0 5',
                                store: store_cajera,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                // triggerAction: 'all',
                                emptyText: 'Seleccione la persona...',
                                selectOnFocus: true,
                                editable: true
                            }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [{
                                xtype: 'combobox',
                                name: 'revisa',
                                id: 'revisa',
                                fieldLabel: 'Revisado por',
                                flex: 1,
                                margin: '0 5 0 0',
                                store: store_revisado,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                // triggerAction: 'all',
                                emptyText: 'Seleccione la persona...',
                                selectOnFocus: true,
                                editable: true
                            }, {
                                xtype: 'combobox',
                                name: 'autoriza',
                                id: 'autoriza',
                                fieldLabel: 'Aprobado por',
                                flex: 1,
                                margin: '0 0 0 5',
                                store: store_aprobado,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                // triggerAction: 'all',
                                emptyText: 'Seleccione la persona...',
                                selectOnFocus: true,
                                editable: true
                            }
                            ]
                        }
                    ]
                }
            ]
            ;

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'PieFirma_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.PieFirma.Window', {
                title: 'Adicionar Pie de Firma',
                id: 'window_PieFirma_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_PieFirma_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/pie_firma/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('gridPieFirmaId').getStore().loadPage(1);
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
                            Ext.getCmp('window_PieFirma_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'PieFirma_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('gridPieFirmaId').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.PieFirma.Window', {
                title: 'Modificar Pie de Firma',
                id: 'window_PieFirma_id',
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
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/pie_firma/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('gridPieFirmaId').getStore().loadPage(1);
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
                            Ext.getCmp('window_PieFirma_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'PieFirma_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('gridPieFirmaId').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Pie de Firma?',
                message: '¿Está seguro que desea eliminar el Pie de Firma seleccionado?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/pie_firma/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridPieFirmaId').getStore().loadPage(1);
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('PieFirma_tbar');
    _tbar.add(_btnAdd);
    _tbar.add(_btnMod);
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});