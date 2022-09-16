/**
 * Created by yosley on 06/10/2015.
 */
Ext.onReady(function () {
    var _store_unidad_medida = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_unidad_actividad',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad_medida/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    Ext.define('Portadores.producto.Window', {
        extend: 'Ext.window.Window',
        width: 350,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 5,
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'left',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            flex: 1,
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            border: false,
                            bodyPadding: 5,
                            margin: '10 10 10 10',
                            collapsible: false,
                            items: [
                                {
                                    xtype: 'textfield',
                                    id: 'fila',
                                    name: 'fila',
                                    fieldLabel: 'Fila',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    regex: /^[0-9]{3}$/,
                                    regexText: 'Este campo debe contener 3 valores numéricos'
                                },
                                {
                                    xtype: 'textfield',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    name: 'nombre',
                                    id: 'nombre',
                                    fieldLabel: 'Nombre'
                                    //maskRe: /[0-9-a-zA-ZáéíóúñÁÉÍÓÚÑ ]/

                                },
                                {
                                    xtype: 'combobox',
                                    name: 'um',
                                    id: 'unidad_medidaid',
                                    margin: '10 0 0 0',
                                    fieldLabel: 'Unidad Medida',
                                    afterLabelTextTpl: [
                                        '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                    ],
                                    store: _store_unidad_medida,
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    editable: true,
                                    forceSelection: true,
                                    queryMode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione ...'
                                },
                                {
                                    xtype: 'checkbox',
                                    name: 'enblanco',
                                    id: 'enblanco',
                                    fieldLabel: 'En Blanco 5073',
                                    inputValue: true,
                                    labelWidth: 120,
                                    labelAlign: 'left',
                                    allowBlank: true
                                }
                            ]
                            ,
                            bodyPadding: 5,
                            margin: '10 10 10 10',
                            collapsible: false
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'producto_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.producto.Window', {
                title: 'Adicionar tipo de combustible',
                id: 'window_producto_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let window = Ext.getCmp('window_producto_id');
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/producto/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            window.show();
                                            Ext.getStore('id_store_producto').loadPage(1);
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
                            Ext.getCmp('window_producto_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'producto_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        // iconCls: 'fa fa-edit fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_producto').getSelectionModel().getLastSelected();
            let window = Ext.create('Portadores.producto.Window', {
                title: 'Modificar tipo de combustible',
                id: 'window_producto_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                let obj = form.getValues();
                                obj.id = Ext.getCmp('id_grid_producto').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/producto/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getStore('id_store_producto').loadPage(1);
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
                                // App.ShowWaitMsg();
                                // window.hide();
                                // let obj = form.getValues();
                                // obj.id = selection.data.id;
                                // let _result = App.PerformSyncServerRequest(Routing.generate('modTipoCombustible'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     Ext.getCmp('id_grid_producto').getStore().load();
                                // }
                                // else {
                                //     window.show();
                                //     form.markInvalid(_result.message);
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_producto_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'producto_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        // iconCls: 'fa fa-minus-square fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_producto').getSelectionModel().getLastSelected();
            let obj = {}
            obj.id = selection.data.id

            Ext.Msg.show({
                title: '¿Eliminar tipo de combustible?',
                message: Ext.String.format('¿Está seguro que desea eliminar el producto <span class="font-italic font-weight-bold">{0}</span>?', selection.data.nombre),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/producto/del'), obj, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_producto').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    let _tbar = Ext.getCmp('producto_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
