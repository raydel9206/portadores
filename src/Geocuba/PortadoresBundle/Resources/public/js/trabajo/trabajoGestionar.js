/**
 * Created by yosley on 05/10/2015.
 */
Ext.onReady(function () {

var store_tipo_combustible = Ext.create('Ext.data.JsonStore', {
    storeId:'id_store_tipo_combustible',
    fields:[
        {name:'id'},
        {name:'nombre'},
        {name:'codigo'},
        {name:'precio'},
        {name:'maximo_tarjeta'},
        {name:'nro'}
    ],
    proxy:{
        type:'ajax',
        url: App.buildURL('/portadores/tipocombustible/loadCombo'),
        reader:{
            rootProperty:'rows'
        }
    },
    pageSize:1000,
    autoLoad:true
});

var store_moneda = Ext.create('Ext.data.JsonStore', {
    storeId:'id_store_moneda',
    fields:[
        {name:'id'},
        {name:'nombre'},
        {name:'unica'}
    ],
    proxy:{
        type:'ajax',
        url: App.buildURL('/portadores/moneda/loadMoneda'),
        reader:{
            rootProperty:'rows'
        }
    },
    pageSize:1000,
    autoLoad:true
});

var store_centro_costo = Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_centro_costo',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/centrocosto/loadCombo'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: false,
    listeners: {
        beforeload: function (This, operation, eOpts) {
            Ext.getCmp('id_grid_trabajo').getSelectionModel().deselectAll();
            operation.setParams({
                unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
            });
        }
    }
});

Ext.create('Ext.data.JsonStore', {
    storeId: 'id_store_destino',
    fields: [
        {name: 'id'},
        {name: 'nombre'}
    ],
    proxy: {
        type: 'ajax',
        url: App.buildURL('/portadores/destino/load'),
        reader: {
            rootProperty: 'rows'
        }
    },
    pageSize: 1000,
    autoLoad: true
});

Ext.define('Portadores.trabajo.Window', {
    extend:'Ext.window.Window',
    width:500,
    modal:true,
    resizable:false,
    initComponent:function () {
        this.items = [
            {
                xtype:'form',
                frame:true,
                defaultType:'textfield',
                bodyPadding:5,
                layout:{
                    type:'hbox',
                    align:'stretch'
                },
                fieldDefaults:{
                    msgTarget:'side',
                    labelAlign:'top',
                    allowBlank:false
                },
                items:[
                    {
                        xtype:'fieldcontainer',
                        flex:1,
                        bodyPadding:5,
                        margin:'10 10 10 10',
                        layout:{
                            type:'vbox',
                            align:'stretch'
                        },
                        items:[
                            {
                                xtype: 'textfield',
                                fieldLabel:'Código',
                                afterLabelTextTpl:[
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                name:'codigo'
                            },
                            {
                                xtype: 'combobox',
                                name: 'ncentrocosto',
                                id: 'ncentrocosto',
                                fieldLabel: 'Centro de costo',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                store: store_centro_costo,
                                displayField: 'nombre',
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione centro de costo...',
                                selectOnFocus: true,
                                editable: true
                            },
                            {
                                xtype:'datefield',
                                name:'fecha_ini',
                                id:'fecha_ini_id',
                                afterLabelTextTpl:[
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                editable:false,
                                format:'d/m/Y',
                                fieldLabel:'Fecha de inicio',
                                listeners:{
                                    select:function (This) {
                                        Ext.getCmp('fecha_fin_id').setMinValue(This.value)
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype:'fieldcontainer',
                        flex:1,
                        bodyPadding:5,
                        margin:'10 10 10 10',
                        layout:{
                            type:'vbox',
                            align:'stretch'
                        },
                        items:[
                            {
                                xtype: 'textfield',
                                fieldLabel:'Nombre',
                                afterLabelTextTpl:[
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                name:'nombre'
                            },
                            {
                                xtype: 'combobox',
                                name: 'destinoid',
                                id: 'destinoid',
                                fieldLabel: 'Destino',
                                store: Ext.getStore('id_store_destino'),
                                displayField: 'nombre',
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                valueField: 'id',
                                typeAhead: true,
                                queryMode: 'local',
                                forceSelection: true,
                                triggerAction: 'all',
                                emptyText: 'Seleccione...',
                                selectOnFocus: true,
                                editable: true
                            },
                            {
                                xtype:'datefield',
                                name:'fecha_fin',
                                id:'fecha_fin_id',
                                afterLabelTextTpl:[
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                editable:false,
                                format:'d/m/Y',
                                fieldLabel:'Fecha fin'
                            }
                        ]
                    }
                ]
            }
        ];

        this.callParent();
    },
    // listeners: {
    //     afterrender: function (This, operation, eOpts) {
    //         Ext.getStore('id_store_centro_costo').load();
    //         Ext.getStore('id_store_destino').load();
    //     }
    // }
});

Ext.define('Portadores.asignacion.Window', {
    extend:'Ext.window.Window',
    width:350,
    height:185,
    modal:true,
    plain:true,
    resizable:false,
    initComponent:function () {
        this.items = [
            {
                xtype:'form',
                frame:true,
                width:350,
                height:185,
                defaultType:'textfield',
                bodyPadding:10,
                fieldDefaults:{
                    msgTarget:'side',
                    allowBlank:false
                },
                items:[
                    {
                        xtype:'combobox',
                        fieldLabel:'Tipo de combustible',
                        name:'tipo_combustibleid',
                        id:'tipo_combustibleid',
                        store:store_tipo_combustible,
                        valueField:'id',
                        displayField:'nombre',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        typeAhead:true,
                        forceSelection:true,
                        labelWidth:125,
                        width:'98%',
                        queryMode:'local'
                    },
                    {
                        xtype:'combobox',
                        fieldLabel:'Moneda',
                        name:'monedaid',
                        id:'monedaid',
                        margin:'10 0 0 0',
                        store:store_moneda,
                        valueField:'id',
                        displayField:'nombre',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        typeAhead:true,
                        forceSelection:true,
                        labelWidth:125,
                        width:'98%',
                        queryMode:'local'
                    },
                    {
                        xtype:'numberfield',
                        margin:'10 0 0 0',
                        name:'cantidad',
                        id:'cantidad_id',
                        fieldLabel:'Cantidad',
                        afterLabelTextTpl:[
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        value:0,
                        minValue:0,
                        width:'98%',
                        labelWidth:125
                    }
                ]
            }
        ];

        this.callParent();
    }
});

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id:'trabajo_btn_add',
        text:'Adicionar',
        // disabled: true,
        iconCls: 'fas fa-plus-square text-primary',
        width:100,
        handler:function (This, e) {
            Ext.create('Portadores.trabajo.Window', {
                title:'Adicionar trabajo',
                id:'window_trabajo_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var window = Ext.getCmp('window_trabajo_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                App.request('POST', App.buildURL('/portadores/trabajo/add'), form.getValues(), null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_trabajo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id:'trabajo_btn_mod',
        text:'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            var selection = Ext.getCmp('id_grid_trabajo').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.trabajo.Window', {
                title:'Modificar trabajo',
                id:'window_trabajo_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/trabajo/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);

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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_trabajo_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id:'trabajo_btn_del',
        text:'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            selection = Ext.getCmp('id_grid_trabajo').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Trabajo/Proyecto?',
                message: Ext.String.format('¿Está seguro que desea eliminar Trabajo/Proyecto <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('nombre')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/trabajo/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_trabajo').getStore().loadPage(1);
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar = Ext.getCmp('trabajo_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);


    var _btnAddAsignacion = Ext.create('Ext.button.MyButton', {
        id:'asignacion_btn_add',
        text:'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            Ext.create('Portadores.asignacion.Window', {
                title:'Adicionar asignación',
                id:'window_asignacion_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var selection = Ext.getCmp('id_grid_trabajo').getSelectionModel().getLastSelected();
                            var window = Ext.getCmp('window_asignacion_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.trabajoid = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/trabajo/asignacion/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_asignacion').getStore().load();
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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_asignacion_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnModAsignacion = Ext.create('Ext.button.MyButton', {
        id:'asignacion_btn_mod',
        text:'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            var selection = Ext.getCmp('id_grid_asignacion').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.asignacion.Window', {
                title:'Modificar asignación',
                id:'window_asignacion_id',
                buttons:[
                    {
                        text:'Aceptar',
                        width:70,
                        handler:function () {
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                obj.trabajoid = Ext.getCmp('id_grid_trabajo').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/trabajo/asignacion/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_asignacion').getStore().load();

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
                        text:'Cancelar',
                        width:70,
                        handler:function () {
                            Ext.getCmp('window_asignacion_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });
    var _btn_DelAsignacion = Ext.create('Ext.button.MyButton', {
        id:'asignacion_btn_del',
        text:'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled:true,
        width:100,
        handler:function (This, e) {
            selection = Ext.getCmp('id_grid_asignacion').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Asignación?',
                message: '¿Está seguro que desea eliminar la asignación seleccionada?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/trabajo/asignacion/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_asignacion').getStore().load();
                            }
                        });
                    }
                }
            });
        }
    });

    var _tbar1 = Ext.getCmp('asignacion_tbar');
    _tbar1.add(_btnAddAsignacion);
    _tbar1.add('-');
    _tbar1.add(_btnModAsignacion);
    _tbar1.add('-');
    _tbar1.add(_btn_DelAsignacion);
    _tbar1.setHeight(36);
});