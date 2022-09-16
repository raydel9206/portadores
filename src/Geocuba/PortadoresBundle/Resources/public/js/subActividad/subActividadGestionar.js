/**
 * Created by kireny on 4/11/15.
 */

Ext.onReady(function () {

Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_actividad_subactividad',
    fields: [
        {name: 'id'},
        {name: 'nombre'},
        {name: 'codigogae'},
        {name: 'codigomep'},
        {name: 'um_actividad'},
        {name: 'um_actividad_nombre'},
        {name: 'portador'},
        {name: 'portadornombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/actividad/loadCombo'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: true
});

Ext.define('Portadores.subActividad.Window', {
    extend: 'Ext.window.Window',
    modal: true,
    plain: true,
    resizable: false,
    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                frame: true,
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                fieldDefaults: {
                    msgTarget: 'side',
                    allowBlank: false
                },
                defaultType: 'textfield',
                bodyPadding: 5,
                items: [
                    {
                        xtype: 'textfield',
                        width: 350,
                        name: 'nombre',
                        id: 'nombre',
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 60
                    },
                    {
                        xtype: 'combobox',
                        width: 350,
                        name: 'nactividadid',
                        id: 'nactividadid',
                        fieldLabel: 'Actividad',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 60,
                        store: Ext.getStore('id_store_actividad_subactividad'),
                        displayField: 'nombre',
                        valueField: 'id',
                        queryMode: 'local',
                        typeAhead: true,
                        editable: true,
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione la actividad',
                        selectOnFocus: true
                    }
                ]
            }
        ];

        this.callParent();
    }

});

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'subActividad_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.subActividad.Window', {
                title: 'Adicionar subactividad',
                id: 'window_subActividad_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_subActividad_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/subactividad/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_subActividad').getStore().loadPage(1);
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
                            Ext.getCmp('window_subActividad_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'subActividad_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_subActividad').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.subActividad.Window', {
                title: 'Modificar subactividad',
                id: 'window_subActividad_id',
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

                                App.request('POST', App.buildURL('/portadores/subactividad/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_subActividad').getStore().loadPage(1);

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
                            Ext.getCmp('window_subActividad_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'subActividad_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            selection = Ext.getCmp('id_grid_subActividad').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar subactividad?',
                message: Ext.String.format('¿Está seguro que desea eliminar la Subactividad <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/subactividad/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_subActividad').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('subActividad_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});