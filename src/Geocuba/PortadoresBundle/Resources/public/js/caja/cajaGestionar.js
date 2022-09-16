/**
 * Created by yosley on 07/10/2015.
 */
Ext.onReady(function () {

    // var tree_store_unidad = Ext.create('Ext.data.TreeStore', {
    //     storeId: 'id_store_unidad_caja',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'nombre'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('/portadores/unidad/loadTree'),
    //         reader: {
    //             type: 'json',
    //             // rootProperty: 'children'
    //         }
    //     },
    //     pageSize: 1000,
    //     autoLoad: false
    // });

    Ext.define('Portadores.caja.Window', {
        extend: 'Ext.window.Window',
        width: 350,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 5,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelWidth: 50,
                        allowBlank: false
                    },
                    items: [
                        {
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: ' nombre',
                            labelWidth: 60,
                            id: 'nombre',
                            fieldLabel: 'Caja'
                        },
                        /*{
                            xtype: 'treepicker',
                            fieldLabel: 'Unidad',
                            labelWidth: 60,
                            store: tree_store_unidad,
                            autoScroll: true,
                            selectOnTab: true,
                            name: 'nunidadid',
                            id: 'nunidadid',
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',

                        },*/
                        // {
                        //     xtype: 'treecombobox',
                        //     fieldLabel: 'Unidad',
                        //     valueField: 'id',
                        //     displayField: 'nombre',
                        //     name: 'nunidadid',
                        //     id: 'nunidadid',
                        //     emptyText: 'Seleccione la unidad...',
                        //     afterLabelTextTpl: [
                        //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        //     ],
                        //     store: tree_store_unidad,
                        //     queryMode: 'remote',
                        //     forceSelection: true,
                        //     allowBlank: false,
                        //     editable: false,
                        //     anyMatch: true,
                        //     allowFolderSelect: true,
                        //     treeConfig: {
                        //         maxHeight: 200,
                        //         scrolling: true
                        //     }
                        // },
                        // {
                        // xtype: 'multiselector',
                        // fieldLabel: 'Unidad',
                        // valueField: 'id',
                        // displayField: 'nombre',
                        // name: 'nunidadid',
                        // id: 'nunidadid',
                        // emptyText: 'Seleccione la unidad...',
                        // afterLabelTextTpl: [
                        //     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        // ],
                        // store: tree_store_unidad,
                        // queryMode: 'remote',
                        // forceSelection: true,
                        // allowBlank: false,
                        // editable: false,
                        // anyMatch: true,
                        // allowFolderSelect: true,
                        // treeConfig: {
                        //     maxHeight: 200,
                        //     scrolling: true
                        // }

                        // xtype: 'multiselector',
                        // valueField: 'id',
                        // labelWidth: 60,
                        // displayField: 'nombre',
                        // showDefaultSearch: true,
                        // plusButtonType: 'add',
                        // hideHeaders: true,
                        // colspan: 2,
                        // removeRowText: '✖',
                        // search: {
                        //     xtype: 'multiselector-search',
                        //     store: tree_store_unidad,
                        //     field: 'nombre',
                        //     width: 300
                        //
                        // },
                        // showRemoveButton: true,
                        // columns: [{
                        //     text: "Name",
                        //     dataIndex: "nombre",
                        //     flex: 1
                        // }]
                        // }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'caja_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.caja.Window', {
                title: 'Adicionar caja',
                id: 'window_caja_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_caja_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                var obj = {};
                                obj = form.getValues();
                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id;

                                window.hide();

                                App.request('POST', App.buildURL('/portadores/caja/addCaja'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_caja').loadPage(1);
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
                            Ext.getCmp('window_caja_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'caja_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_caja').getSelectionModel().getLastSelected();
            //console.log(selection)
            var window = Ext.create('Portadores.caja.Window', {
                title: 'Modificar caja',
                id: 'window_caja_id',

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
                                obj.nunidadid = Ext.getCmp('arbolunidades').getSelection()[0].data.id

                                App.request('POST', App.buildURL('/portadores/caja/modCaja'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getStore('id_store_caja').loadPage(1);
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
                            Ext.getCmp('window_caja_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'caja_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_caja').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar caja?' : '¿Eliminar cajas?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar la caja <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las cajas seleccionadas?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        var params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });
                        App.request('DELETE', App.buildURL('/portadores/caja/delCaja'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_caja').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _tbar = Ext.getCmp('caja_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});
