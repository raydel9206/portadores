/**
 * Created by javier on 17/05/2016.
 */
Ext.onReady(function () {

    let store_persona_entrega = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_registro_acta_resp_persona',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
        ],
        groupField: 'nombreunidadid',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/persona/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    let store_persona_recibe = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_registro_acta_resp_persona_recibe',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
        ],
        groupField: 'nombreunidadid',
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
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                });

            },
            load: function () {
                if (Ext.getCmp('entregaid'))
                    if (Ext.getCmp('entregaid').getValue()) {
                        let store = Ext.getCmp('recibeid').getStore();
                        let find = store.findRecord('id', Ext.getCmp('entregaid').getValue());
                        store.remove(find);
                        Ext.getCmp('recibeid').setStore(store);
                        Ext.getCmp('recibeid').enable();
                    }
            }
        }

    });
    let dif = 0;
    Ext.define('Portadores.registro_acta_resp.Window', {
        extend: 'Ext.window.Window',
        width: 320,
        height: 230,
        id: 'window_registro_acta_resp_id',
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    id: 'registro_form',
                    width: 320,
                    height: 230,
                    bodyPadding: 10,
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha',
                            id: 'fechaid',
                            labelWidth: 55,
                            width: '60%',
                            allowBlank: false,
                            editable: false,
                            format: 'd/m/Y',
                            fieldLabel: 'Fecha',
                            // listeners: {
                            //     afterrender: function (This) {
                            //         let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                            //         let anno = App.selected_year;
                            //         let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                            //         let max = new Date(App.selected_month + '/' + dias + '/' + anno);
                            //         This.setMinValue(min);
                            //         This.setMaxValue(max);
                            //         This.setValue(min);
                            //     }
                            // }
                        },
                        {
                            xtype: 'tagfield',
                            fieldLabel: 'Tarjetas',
                            name: 'tarjetaid',
                            id: 'tarjetaid',
                            margin: '10 0 0 0',
                            store: Ext.getStore('id_store_registro_acta_resp_tarjeta'),
                            valueField: 'id',
                            displayField: 'nro_tarjeta',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
//                        typeAhead : true,
                            allowBlank: false,
                            labelWidth: 55,
                            width: '96%',
                            queryMode: 'local',
                            multiSelect: true,
                            filterPickList: true,
                            listeners: {
                                afterrender: function (This) {
                                    dif = This.getHeight();
                                },
                                select: function (This) {
                                    if (This.getHeight() != dif) {
                                        let win_height = Ext.getCmp('window_registro_acta_resp_id').getHeight();
                                        let form_height = Ext.getCmp('registro_form').getHeight();
                                        Ext.getCmp('window_registro_acta_resp_id').setHeight(This.getHeight() - dif + win_height);
                                        Ext.getCmp('registro_form').setHeight(This.getHeight() - dif + form_height);
                                        dif = This.getHeight();
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            name: 'entregaid',
                            id: 'entregaid',
                            margin: '10 0 0 0',
                            fieldLabel: 'Entrega',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: store_persona_entrega,
                            displayField: 'nombre',
                            valueField: 'id',
                            labelWidth: 55,
                            width: '96%',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la persona...',
                            selectOnFocus: true,
                            editable: true,
                            allowBlank: false,
                            listeners: {
                                select: function (This) {
                                    Ext.getCmp('recibeid').getStore().load();
                                    Ext.getCmp('recibeid').setDisabled(false);

                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            margin: '10 0 0 0',
                            disabled: true,
                            name: 'recibeid',
                            id: 'recibeid',
                            fieldLabel: 'Recibe',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            store: store_persona_recibe,
                            displayField: 'nombre',
                            valueField: 'id',
                            labelWidth: 55,
                            width: '96%',
                            typeAhead: true,
                            queryMode: 'local',
                            forceSelection: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione la persona...',
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


    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'registro_acta_resp_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.registro_acta_resp.Window', {
                title: 'Adicionar acta de responsabilidad material',
                id: 'window_registro_acta_resp_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let selection = Ext.getCmp('arbolunidades').getSelection();
                            let window = Ext.getCmp('window_registro_acta_resp_id');
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                window.hide();
                                // console.log(selection)
                                obj.nunidadid = selection[0].data.id;
                                App.request('POST', App.buildURL('/portadores/registroActaResp/addRegistroActaResp'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            window.show();
                                            Ext.getStore('id_store_registro_acta_resp').loadPage(1);
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
                            Ext.getCmp('window_registro_acta_resp_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'registro_acta_resp_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_registro_acta_resp').getSelectionModel().getLastSelected();
            let window = Ext.create('Portadores.registro_acta_resp.Window', {
                title: 'Modificar acta de responsabilidad material',
                id: 'window_registro_acta_resp_id',
                listeners: {
                    afterrender: function () {
                        dif = Ext.getCmp('tarjetaid').getHeight();
                        Ext.getCmp('recibeid').getStore().load();
                        Ext.getCmp('window_registro_acta_resp_id').setHeight(dif + 250);
                        Ext.getCmp('registro_form').setHeight(dif + 250);
                    },
                    select: function () {
                        if (Ext.getCmp('tarjetaid').getHeight() != dif) {
                            let win_height = Ext.getCmp('window_registro_acta_resp_id').getHeight();
                            let form_height = Ext.getCmp('registro_form').getHeight();
                            Ext.getCmp('window_registro_acta_resp_id').setHeight(Ext.getCmp('tarjetaid').getHeight() - dif + win_height);
                            Ext.getCmp('registro_form').setHeight(Ext.getCmp('tarjetaid').getHeight() - dif + form_height);
                            dif = Ext.getCmp('tarjetaid').getHeight();
                        }
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                let obj = form.getValues();
                                window.close();
                                obj.id = Ext.getCmp('id_grid_registro_acta_resp').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/registroActaResp/modRegistroActaResp'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getStore('id_store_registro_acta_resp').loadPage(1);
                                            Ext.getCmp('window_registro_acta_resp_id').close();
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
                            Ext.getCmp('window_registro_acta_resp_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
            console.log(selection)
        }
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'registro_acta_resp_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_registro_acta_resp').getSelection();
            Ext.Msg.show({
                title: selection.length === 1 ? '¿Eliminar acta de responsabilida material?' : '¿Eliminar actas de responsabilidad material?',
                message: selection.length === 1 ?
                    Ext.String.format('¿Está seguro que desea eliminar el acta de responsabilidad material <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')) :
                    '¿Está seguro que desea eliminar las actas de responsabilidad material seleccionados?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {limit: App.page_limit};

                        selection.forEach(function (record, index) {
                            params['ids[' + index + ']'] = record.getId();
                        });

                        App.request('DELETE', App.buildURL('/portadores/registroActaResp/delRegistroActaResp'), params, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_registro_acta_resp').getStore().reload();
                            }
                        });
                    }
                }
            });


        }
    });

    let _tbar = Ext.getCmp('registro_acta_resp_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


});