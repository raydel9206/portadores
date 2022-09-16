Ext.onReady(function () {

    let gridEquiposTecn = Ext.getCmp('gridEquiposTecn');

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeTipoCombustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeMarcasTecn',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/marcas_tecn/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeModelosTecn',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/modelos_tecn/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeActividades',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/actividad/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'storeDenominaciones',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/denominaciones_tecn/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false
    });

    Ext.define('Portadores.equipos_tecn.Window', {
        extend: 'Ext.window.Window',
        width: 600,
        minHeight: 320,
        modal: true,
        resizable: false,
        items: [{
            xtype: 'form',
            items: [
                {
                    xtype: 'tabpanel',
                    id: 'eqiposTecnTabPanel',
                    width: '100%',
                    height: '100%',
                    defaults: { padding: '10' },
                    items:[
                        {
                            title: 'Datos Generales',
                            defaults: {
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                labelWidth: 110,
                                labelAlign: 'right',
                                allowBlank: false
                            },
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'combobox',
                                    name: 'denominacion_id',
                                    id: 'denominacion_id',
                                    fieldLabel: 'Denominación',
                                    store: Ext.getStore('storeDenominaciones'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la denominación...',
                                    selectOnFocus: true,
                                    listeners: {
                                        change: function (This, value) {
                                            let calderaTab = Ext.getCmp('caldera_tab');
                                            calderaTab.tab.setHidden(value !== 'static_tec_denomination_1');
                                            calderaTab.setDisabled(value !== 'static_tec_denomination_1');

                                            let grupoElectrogenoTab = Ext.getCmp('grupo_electrogeno_tab');
                                            grupoElectrogenoTab.tab.setHidden(value !== 'static_tec_denomination_2');
                                            grupoElectrogenoTab.setDisabled(value !== 'static_tec_denomination_2');

                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nro. Inventario',
                                    name: 'numero_inventario'
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Descripción',
                                    name: 'descripcion'
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Indice de Consumo',
                                    name: 'norma',
                                    decimalSeparator: '.',
                                    decimalPrecision: 4,
                                    minValue: 0
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Indice de consumo de fábrica',
                                    name: 'norma_fabricante',
                                    decimalSeparator: '.',
                                    decimalPrecision: 4,
                                    minValue: 0
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'marca_id',
                                    id: 'marca_id',
                                    fieldLabel: 'Marca',
                                    store: Ext.getStore('storeMarcasTecn'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la marca...',
                                    selectOnFocus: true,
                                    listeners: {
                                        select: function (This, record) {
                                            Ext.getStore('storeModelosTecn').load({ params: { marca_id: record.data.id } });
                                            Ext.getCmp('modelo_id').enable();
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'modelo_id',
                                    id: 'modelo_id',
                                    fieldLabel: 'Modelo',
                                    store: Ext.getStore('storeModelosTecn'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione el modelo...',
                                    selectOnFocus: true,
                                    disabled: true
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'tipo_combustible_id',
                                    id: 'tipo_combustible_id',
                                    fieldLabel: 'Tipo de Combustible',
                                    store: Ext.getStore('storeTipoCombustible'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione tipo de combustible...',
                                    selectOnFocus: true,
                                    listeners: {
                                        select: function(This, record) {
                                            Ext.getStore('storeActividades').load({ params: { tipo_combustibleid: record.data.id } });
                                            Ext.getCmp('actividad_id').enable();
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'actividad_id',
                                    id: 'actividad_id',
                                    fieldLabel: 'Actividad',
                                    store: Ext.getStore('storeActividades'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione la actividad...',
                                    selectOnFocus: true,
                                    disabled: true
                                },
                            ]
                        },
                        {
                            id: 'caldera_tab',
                            title: 'Datos de Caldera',
                            defaults: {
                                afterLabelTextTpl: [
                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                ],
                                labelWidth: 110,
                                labelAlign: 'top',
                                allowBlank: false
                            },
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            hidden: true,
                            disabled: true,
                            items: [
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Indice consumo recirculación',
                                    name: 'norma_recirculacion',
                                    decimalSeparator: '.',
                                    decimalPrecision: 4,
                                    minValue: 0
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Indice consumo recirculación de fábrica',
                                    name: 'norma_recirculacion_fabricante',
                                    decimalSeparator: '.',
                                    decimalPrecision: 4,
                                    minValue: 0
                                },
                                {
                                    xtype: 'combobox',
                                    name: 'tipo_combustible_recirculacion_id',
                                    fieldLabel: 'Tipo Combustible recirculación',
                                    store: Ext.getStore('storeTipoCombustible'),
                                    displayField: 'nombre',
                                    valueField: 'id',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    forceSelection: true,
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione tipo de combustible...',
                                    selectOnFocus: true,
                                },
                            ]
                        },
                        {
                            id: 'grupo_electrogeno_tab',
                            title: 'Datos de Grupo Electrógeno',
                            hidden: true,
                            disabled: true,
                            items: []
                        }
                    ]
                }
            ],
            listeners: {
                fieldvaliditychange: function(This, field, isValid) {
                    if (isValid && field.id !== 'denominacion_id') {
                        let component = Ext.getCmp(field.id);
                        if (component) component.enable();
                    }
                }
            }
        }]
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/equipos_tecn/${action}`),
            selection = (action !== 'add' && gridEquiposTecn.getSelectionModel().hasSelection()) ? gridEquiposTecn.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar equipo tecnológico?',
                message: `¿Está seguro que desea eliminar el equipo tecnológico <span class="font-italic font-weight-bold">${selection.get('descripcion')}</span>?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', url, { id: selection.get('id') }, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridEquiposTecn.getStore().reload();
                            }
                        });
                    }
                }
            });
        } else {
            let winform = Ext.create('Portadores.equipos_tecn.Window', {
                title: !selection ? 'Adicionar equipo tecnológico' : `Modificar <span class="font-italic font-weight-bold">${selection.get('descripcion')}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.get('id') : null;
                                params.unidad_id = Ext.getCmp('arbolunidades').getSelection()[0].data.id;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridEquiposTecn.getStore().load();
                                        winform.close();
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winform.close();
                        }
                    }
                ],
                listeners: {
                    beforerender: function () {
                        if (action === 'add') {
                            Ext.getStore('storeDenominaciones').load();
                            Ext.getStore('storeTipoCombustible').load();
                            Ext.getStore('storeMarcasTecn').load();
                        }
                    },
                    afterrender: function () {
                        if (action === 'upd') Ext.getCmp('denominacion_id').disable();
                    }
                }
            });
            if (action === 'upd') {
                App.mask();

                let selected = gridEquiposTecn.getSelection()[0].data;

                let marcaPromise = new Promise((resolve, reject) => {
                    Ext.getStore('storeMarcasTecn').load({
                        callback: resolve,
                        error: reject
                    });
                });

                let modeloPromise  = new Promise((resolve, reject) => {
                    Ext.getStore('storeModelosTecn').load({
                        params: { marca_id: selected.marca_id },
                        callback: resolve,
                        error: reject
                    });
                });

                let tipoCombustiblePromise = new Promise((resolve, reject) => {
                    Ext.getStore('storeTipoCombustible').load({
                        callback: resolve,
                        error: reject
                    });
                });

                let actividadPromise = new Promise((resolve, reject) => {
                    Ext.getStore('storeActividades').load({
                        params: { tipo_combustibleid: selected.tipo_combustible_id },
                        callback: resolve,
                        error: reject
                    });
                });

                let denominacionPromise = new Promise((resolve, reject) => {
                    Ext.getStore('storeDenominaciones').load({
                        callback: resolve,
                        error: reject
                    });
                });

                Promise.all([denominacionPromise, marcaPromise, modeloPromise, tipoCombustiblePromise, actividadPromise])
                    .then(() => {
                        winform.show();
                        winform.down('form').getForm().loadRecord(selection);
                        App.unmask()
                    })
                    .catch(() => {
                        App.unmask();
                        App.showAlert('Error cargando los datos.', 'danger');
                    });
            }
            else winform.show();
        }
    };

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler.bind(this, 'add')
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        bind: {disabled: '{!gridEquiposTecn.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridEquiposTecn.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbar = Ext.getCmp('gridEquiposTecnTbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btnDel);
});
