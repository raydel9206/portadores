
Ext.define('Portadores.unidad.Window', {
    extend: 'Ext.window.Window',
    width: 300,
    modal: true,
    resizable: false,
    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                frame: true,
                bodyPadding: 5,
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                fieldDefaults: {
                    msgTarget: 'side',
                    labelWidth: 80,
                    allowBlank: false
                },
                items: [
                    {
                        fieldLabel: 'Nombre',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'nombre',
                        xtype: 'textfield'

                    },
                    {
                        fieldLabel: 'Siglas',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'siglas',
                        xtype: 'textfield'
                    },
                    {
                        fieldLabel: 'C&oacute;digo',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'codigo',
                        xtype: 'textfield'
                    },{
                        fieldLabel: 'C&oacute;digo Fincimex',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        name: 'codfincimex',
                        xtype: 'textfield'
                    },
                    {
                      xtype: 'checkboxfield',
                      boxLabel: 'Empresa Mixta',
                      name: 'mixta',
                      value: false
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Nivel estructural',
                        labelAlign: 'top',
                        cls: 'x-check-group-alt',
                        items: [{
                            xtype: 'radiogroup',
                            items: [
                                {boxLabel: 'OSDE', name: 'nivel', inputValue: 'osde'},
                                {boxLabel: 'EMPRESA', name: 'nivel', inputValue: 'empresa'},
                                {boxLabel: 'UEB', name: 'nivel', inputValue: 'ueb', margin: '0 0 0 30'}]
                        }]
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Provincia',
                        queryMode: 'local',
                        displayField: 'nombre',
                        name: 'provincia',
                        valueField: 'id',
                        store: Ext.create('Ext.data.JsonStore', {
                            storeId: 'storeProvinciaUnidad',
                            fields: [
                                {name: 'id'},
                                {name: 'nombre'}
                            ],
                            proxy: {
                                type: 'ajax',
                                url: App.buildURL('/portadores/provincia/list'),
                                extraParams: {simple: true},
                                reader: {rootProperty: 'rows'}
                            },
                            autoLoad: true,
                            autoDestroy: true
                        }),
                        listeners: {
                            change: function (This, value) {
                                var comboMunicipio = This.nextSibling();
                                comboMunicipio.getStore().removeAll();

                                if (value) {
                                    comboMunicipio.getStore().load({params: {id: value}});
                                    comboMunicipio.setDisabled(false);

                                }
                                else{
                                    comboMunicipio.setDisabled(true);
                                    comboMunicipio.getStore().removeAll();
                                    comboMunicipio.setValue(null);
                                }
                            }
                        }

                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Municipio',
                        queryMode: 'local',
                        displayField: 'nombre',
                        valueField: 'id',
                        name: 'municipio',
                        disabled: true,
                        store: Ext.create('Ext.data.JsonStore', {
                            storeId: 'storeMunicipioUnidad',
                            fields: [
                                {name: 'id'},
                                {name: 'nombre'}
                            ],
                            proxy: {
                                type: 'ajax',
                                url: App.buildURL('/portadores/municipio/listMunicipio'),
                                extraParams: {simple: true},
                                reader: {rootProperty: 'rows'}
                            },
                            autoLoad: false,
                            autoDestroy: true
                        })

                    }
                ]
            }
        ];

        this.callParent();
    }
});

Ext.onReady(function(){
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'unidad_btn_add',
        text: 'Adicionar',
        disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        // iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        style: {
            letterSpacing: '0.5px',
            borderColor: '#d6d6d6',
            // background: 'linear-gradient(to top,  #eeeeee 50%,#ffffff 90%)'
            background: '#ffffff'
        },
        handler: function (This, e) {
            Ext.create('Portadores.unidad.Window', {
                title: 'Adicionar unidad',
                id: 'window_unidad_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_unidad_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                if (Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected())
                                    obj.padreid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/unidad/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('arbolunidades').getStore().loadPage(1);
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
                            Ext.getCmp('window_unidad_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnUpd = Ext.create('Ext.button.MyButton', {
        id: 'unidad_btn_upd',
        text: 'Modificar',
        disabled: true,
        iconCls: 'fas fa-edit text-primary',
        width: 100,
        style: {
            letterSpacing: '0.5px',
            borderColor: '#d6d6d6',
            // background: 'linear-gradient(to top,  #eeeeee 50%,#ffffff 90%)'
            background: '#ffffff'
        },
        handler: function (This, e) {
            var selection = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected();
            Ext.create('Portadores.unidad.Window', {
                title: 'Modificar unidad',
                id: 'window_unidad_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_unidad_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/unidad/upd'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            Ext.getCmp('arbolunidades').getStore().loadPage(1);
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
                            Ext.getCmp('window_unidad_id').close()
                        }
                    }
                ]
            }).show()
                .down('form').loadRecord(selection);
        }
    });
    var _btnDel = Ext.create('Ext.button.MyButton', {
        id: 'unidad_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        style: {
            letterSpacing: '0.5px',
            borderColor: '#d6d6d6',
            // background: 'linear-gradient(to top,  #eeeeee 50%,#ffffff 90%)'
            background: '#ffffff'
        },
        handler: function (This, e) {
            var selection = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar unidad?',
                message: Ext.String.format('¿Está seguro que desea eliminar la unidad seleccionada?'),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('POST', App.buildURL('/portadores/unidad/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('arbolunidades').getStore().reload();
                            }
                        });
                    }
                }
            });
        }
    });


    var _tbar = Ext.getCmp('unidad_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnUpd);
    _tbar.add('-');
    _tbar.add(_btnDel);
});