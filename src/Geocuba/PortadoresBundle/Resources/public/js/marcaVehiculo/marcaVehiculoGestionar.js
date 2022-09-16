/**
 * Created by adonis on 24/09/2015.
 */
Ext.onReady(function () {
    Ext.define('Portadores.marcaVehiculo.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        width: 350,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        //labelAlign: 'top',
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            fieldLabel: 'Nombre',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            flex: 1,
                            labelWidth: 100,
                            name: 'nombre',
                        },

                    ]
                }
            ];

            this.callParent();
        }
    });


    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_marca_modelo',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelovehiculo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    Ext.define('Portadores.modelo.Window', {
        extend: 'Ext.window.Window',
        modal: true,
        plain: true,
        resizable: false,
        width: 300,

        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        //labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            name: 'nombre',
                            fieldLabel: 'Modelo',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 50,
                            allowBlank: false
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });


    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'marcaVehiculo_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        handler: function (This, e) {
            let winaddMarca = Ext.create('Portadores.marcaVehiculo.Window', {
                title: 'Adicionar marca',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = winaddMarca.down('form').getForm();
                            if (form.isValid()) {
                                winaddMarca.hide();
                                App.request('POST', App.buildURL('/portadores/marcavehiculo/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('storeMarcaVehiculoId').loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                winaddMarca.down('form').getForm().markInvalid(response.errors);
                                            }
                                            winaddMarca.show();
                                        }

                                    },
                                    function (response) { // failure_callback
                                        winaddMarca.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winaddMarca.close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'marcaVehiculo_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        handler: function (This, e) {
            let selection = Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected();
            let winmodMarca = Ext.create('Portadores.marcaVehiculo.Window', {
                title: 'Modificar marca',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = winmodMarca.down('form').getForm();
                            if (form.isValid()) {
                                winmodMarca.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/marcavehiculo/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('storeMarcaVehiculoId').loadPage(1);
                                            winmodMarca.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                winmodMarca.down('form').getForm().markInvalid(response.errors);
                                            }
                                            winmodMarca.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        winmodMarca.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winmodMarca.close();
                        }
                    }
                ]
            });
            winmodMarca.show();
            winmodMarca.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'marcaVehiculo_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        //width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('gridMarcaVehiculoId').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar marca?' : '¿Eliminar marcas?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la marca <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las marcas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/marcavehiculo/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('gridMarcaVehiculoId').getStore().reload();
                            }
                        });
                    }
                }
            });


        }
    });

    var _tbar = Ext.getCmp('marcaVehiculo_tbar');
    _tbar.add(_btnAdd);
    _tbar.add(_btnMod);
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);

    var _btnAddModelo = Ext.create('Ext.button.MyButton', {
        id: 'modelo_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        handler: function (This, e) {
            var marca_selected = Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected();
            let winaddModelo = Ext.create('Portadores.modelo.Window', {
                title: 'Adicionar modelo',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = winaddModelo.down('form').getForm();
                            if (form.isValid()) {
                                winaddModelo.hide();
                                var obj = form.getValues();
                                obj.nmarca_vehiculoid = marca_selected.data.id;
                                obj.limit = App.page_limit;
                                App.request('POST', App.buildURL('/portadores/modelovehiculo/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_modelo').loadPage(1);
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                winaddModelo.down('form').getForm().markInvalid(response.errors);
                                            }
                                            winaddModelo.show();
                                        }

                                    },
                                    function (response) { // failure_callback
                                        winaddModelo.show();
                                    }
                                );
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winaddModelo.close()
                        }
                    }
                ]

            }).show();
        }
    });

    var _btnModModelo = Ext.create('Ext.button.MyButton', {
        id: 'modelo_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_modelo').getSelectionModel().getLastSelected();
            var marca_selected = Ext.getCmp('gridMarcaVehiculoId').getSelectionModel().getLastSelected();
            let winmodModelo = Ext.create('Portadores.modelo.Window', {
                title: 'Modificar modelo',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var form = winmodModelo.down('form').getForm();
                            if (form.isValid()) {
                                winmodModelo.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_modelo').getSelectionModel().getLastSelected().data.id;
                                obj.nmarca_vehiculoid = marca_selected.data.id;
                                obj.limit = App.page_limit;
                                App.request('POST', App.buildURL('/portadores/modelovehiculo/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_modelo').loadPage(1);
                                            winmodModelo.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                winmodModelo.down('form').getForm().markInvalid(response.errors);
                                            }
                                            winmodModelo.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        winmodModelo.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winmodModelo.close();
                        }
                    }
                ]
            });
            winmodModelo.show();
            winmodModelo.down('form').loadRecord(selection);
        }
    });

    var _btn_DelModelo = Ext.create('Ext.button.MyButton', {
        id: 'modelo_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        //width: 100,
        handler: function (This, e) {

            let selection = Ext.getCmp('id_grid_modelo').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar modelo?' : '¿Eliminar modelos?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el modelo <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar los modelos?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
// console.log(params)
                        App.request('DELETE', App.buildURL('/portadores/modelovehiculo/del'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_modelo').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbarModelo = Ext.getCmp('modelo_tbar');
    _tbarModelo.add(_btnAddModelo);
    _tbarModelo.add(_btnModModelo);
    _tbarModelo.add(_btn_DelModelo);
    _tbarModelo.setHeight(36);
});
