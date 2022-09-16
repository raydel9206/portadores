Ext.onReady(function () {

    var store_vehiculos_unidad = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculo',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
            {name: 'nmarca_vehiculo'}
        ],
        proxy: {
            type: 'ajax',
            // url: App.buildURL('/portadores/vehiculo/load'),
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

    var store_personas_unidad = Ext.create('Ext.data.JsonStore', {
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

    Ext.define('Portadores.paralizacion.Window', {
        extend: 'Ext.window.Window',
        width: 350,
        height: 520,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: 350,
                    height: 520,
                    defaultType: 'textfield',
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    bodyPadding: 10,
                    items: [
                        {
                            xtype: 'combobox',
                            name: 'vehiculoid',
                            id: 'vehiculoid',
                            fieldLabel: 'Vehículo',
                            labelWidth: 65,
                            store: Ext.getStore('id_store_vehiculo'),
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
                            editable: true,
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            xtype: 'datefield',
                            name: 'fecha',
                            fieldLabel: 'Fecha',
                            labelWidth: 65,
                            selectOnFocus: true,
                            editable: false,
                            format: 'd/m/Y',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            allowBlank: false,
                        },
                        {
                            fieldLabel: 'Taller',
                            xtype: 'textfield',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'taller',
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            fieldLabel: 'Motivo',
                            xtype: 'textarea',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'motivo',
                            allowBlank: false,
                            margin: '0 0 10 0'
                        },
                        {
                            fieldLabel: 'Observaciones',
                            xtype: 'textarea',
                            labelWidth: 65,
                            labelAlign: 'top',
                            name: 'observaciones',
                            allowBlank: true,
                            margin: '0 0 10 0'
                        },
                        {
                            xtype: 'checkboxfield',
                            boxLabel: 'En SASA',
                            name: 'en_sasa',
                            id: 'en_sasa',
                            listeners: {
                                change: function (value) {
                                    Ext.getCmp('nro_pedido').setDisabled(!value.checked);
                                }
                            }
                        },
                        {
                            labelWidth: 80,
                            name: 'nro_pedido',
                            disabled: true,
                            maskRe: /^[0-9.]$/,
                            id: 'nro_pedido',
                            fieldLabel: 'No. Pedido',
                            allowBlank: false,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                    ]
                }
            ];

            this.callParent();
        },
    });


    Ext.define('Portadores.poner_marcha.Window', {
        extend: 'Ext.window.Window',
        width: 280,
        //height: 160,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            name: 'fecha_marcha',
                            fieldLabel: 'Fecha puesta en marcha',
                            labelAlign: 'top',
                            labelWidth: 65,
                            selectOnFocus: true,
                            editable: false,
                            format: 'd/m/Y',
                            allowBlank: true,
                            listeners: {
                                afterrender: function (This) {
                                    var selected = Ext.getCmp('id_grid_paralizacion').getSelectionModel().getLastSelected();
                                    var dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                    var anno = App.selected_year;
                                    var max = new Date(App.selected_month + '/' + dias + '/' + anno);
                                    This.setMinValue(selected.data.fecha);
                                    This.setMaxValue(max);
                                }
                            },
                        },
                    ]
                }
            ];

            this.callParent();
        }
    });

    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'btn_menu',
        text: 'Menu',
        iconCls: 'fas fa-bars text-primary',
        width: 100,
        menu: [
            {
                id: 'paralizacion_btn_add',
                text: 'Adicionar',
                // iconCls: 'fas fa-plus-square text-primary',
                glyph: 0xf0fe,
                width: 120,
                // disabled: true,
                handler: function (This, e) {
                    Ext.Msg.show({
                        // id:'inforid',
                        title: 'Información',
                        message: 'Antes de paralizar un Vehículo usted debe liquidar el combustible y anexo único del mismo',
                        buttons: Ext.Msg.OKCANCEL,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'ok') {
                                // Ext.getCmp('inforid').close();
                                Ext.create('Portadores.paralizacion.Window', {
                                    title: 'Adicionar paralización',
                                    id: 'window_paralizacion_id',
                                    buttons: [
                                        {
                                            text: 'Aceptar',
                                            width: 70,
                                            handler: function () {
                                                var window = Ext.getCmp('window_paralizacion_id');
                                                var form = window.down('form').getForm();
                                                if (form.isValid()) {
                                                    window.hide();
                                                    App.request('POST', App.buildURL('/portadores/paralizacion/add'), form.getValues(), null, null,
                                                        function (response) { // success_callback
                                                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                form.reset();
                                                                Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
                                                                Ext.getCmp('id_grid_cdt').getStore().load();
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
                                                Ext.getCmp('window_paralizacion_id').close()
                                            }
                                        }
                                    ]
                                }).show();
                            } else {
                            }
                        }
                    });

                }
            },
            {
                id: 'paralizacion_btn_mod',
                text: 'Modificar',
                glyph: 0xf044,
                disabled: true,
                width: 120,
                handler: function (This, e) {
                    var selection = Ext.getCmp('id_grid_paralizacion').getSelectionModel().getLastSelected();
                    var window = Ext.create('Portadores.paralizacion.Window', {
                        title: 'Modificar paralización',
                        id: 'window_paralizacion_id',
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
                                        App.request('POST', App.buildURL('/portadores/paralizacion/mod'), obj, null, null,
                                            function (response) { // success_callback
                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                    Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
                                                    Ext.getCmp('id_grid_cdt').getStore().load();
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
                                    Ext.getCmp('window_paralizacion_id').close();
                                }
                            }
                        ]
                    });
                    window.show();
                    window.down('form').loadRecord(selection);
                }
            },
            {
                id: 'paralizacion_btn_del',
                text: 'Eliminar',
                // iconCls: 'fas fa-trash-alt text-primary',
                glyph: 0xf2ed,
                disabled: true,
                width: 120,
                handler: function (This, e) {
                    selection = Ext.getCmp('id_grid_paralizacion').getSelectionModel().getLastSelected();
                    Ext.Msg.show({
                        title: '¿Eliminar Paralización?',
                        message: '¿Está seguro que desea eliminar la paralización seleccionada?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                App.request('DELETE', App.buildURL('/portadores/paralizacion/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        Ext.getCmp('id_grid_paralizacion').getStore().loadPage(1);
                                        Ext.getCmp('id_grid_cdt').getStore().load();
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                id: 'paralizacion_btn_marcha',
                text: 'Poner Marcha',
                // iconCls: 'fas fa-trash-alt text-primary',
                glyph: 0xf1b9,
                disabled: true,
                width: 120,
                handler: function (This, e) {
                    Ext.Msg.show({
                        title: '¿Culminar Paralización?',
                        message: '¿Está seguro que desea poner en marcha el vehículo paralizado?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                Ext.create('Portadores.poner_marcha.Window', {
                                    title: 'Poner en marcha',
                                    id: 'window_poner_marcha_id',
                                    buttons: [
                                        {
                                            text: 'Aceptar',
                                            width: 70,
                                            handler: function () {
                                                let selection = Ext.getCmp('id_grid_paralizacion').getSelectionModel().getLastSelected();
                                                let window = Ext.getCmp('window_poner_marcha_id');
                                                let form = window.down('form').getForm();
                                                let obj = form.getValues();
                                                obj.id = selection.data.id;
                                                if (form.isValid()) {
                                                    App.request('POST', App.buildURL('/portadores/paralizacion/ponerMarcha'), obj, null, null, function (response) {
                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                            form.reset();
                                                            window.close();
                                                            Ext.getCmp('id_grid_paralizacion').getStore().load();
                                                            Ext.getCmp('id_grid_cdt').getStore().load();

                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        {
                                            text: 'Cancelar',
                                            width: 70,
                                            handler: function () {
                                                Ext.getCmp('window_poner_marchapa_id').close()
                                            }
                                        }
                                    ]
                                }).show();
                            }
                        }
                    });

                }
            },

        ],

    });

    var btnPrintEstado = Ext.create('Ext.button.MyButton', {
        id: 'paralizacion_btn_printParque',
        text: 'Estado',
        iconCls: 'fas fa-print text-primary',
        // disabled: true,
        handler: function (This, e) {
            var obj = {};
            obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            App.request('GET', App.buildURL('/portadores/paralizacion/printParque'), obj, null, null, function (response) { // success_callback
                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                    var newWindow = window.open('', '', 'width=1200, height=700'),
                        document = newWindow.document.open();
                    document.write(response.html);
                    setTimeout(() => {
                        newWindow.print();
                    }, 500);
                    document.close();
                }
            });
        }
    });
    var btnPrint = Ext.create('Ext.button.MyButton', {
        id: 'paralizacion_btn_print',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        disabled: true,
        handler: function (This, e) {
            var store = Ext.getCmp('id_grid_cdt').getStore();
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            App.request('GET', App.buildURL('/portadores/paralizacion/print'), {
                store: Ext.encode(send),
                unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.nombre
            }, null, null, function (response) { // success_callback
                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                    var newWindow = window.open('', '', 'width=1200, height=700'),
                        document = newWindow.document.open();
                    document.write(response.html);
                    setTimeout(() => {
                        newWindow.print();
                    }, 500);
                    document.close();
                }
            });
        }
    });
    var btnExport = Ext.create('Ext.button.MyButton', {
        id: 'paralizacion_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: function (This, e) {
            var obj = {};
            obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            let stringQuery = `?view_id=${App.route}`;
            stringQuery = Object.keys(obj).reduce((stringQuery, key) => stringQuery + `&${key}=${obj[key]}`, stringQuery);

            window.open(App.buildURL('/portadores/paralizacion/export') + stringQuery);
        }
    });


    var _tbarcdt = Ext.getCmp('cdt_tbar');
    _tbarcdt.add('->');
    _tbarcdt.add(btnPrint);
    _tbarcdt.add(btnPrint);

    var _tbar = Ext.getCmp('paralizacion_tbar');
    _tbar.add(_btnMenu);
    _tbar.add('->');
    _tbar.add(btnPrintEstado);
    _tbar.add(btnExport);
    _tbar.setHeight(36);
});
