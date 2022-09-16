Ext.onReady(function () {

    let grid = Ext.getCmp('gridMotores');

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

    Ext.define('Portadores.motores.Window', {
        extend: 'Ext.window.Window',
        width: 280,
        // height: 135,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    defaults: {
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        labelWidth: 70,
                        allowBlank: false
                    },
                    items: [
                        {
                            fieldLabel: 'No. Serie',
                            name: 'no_serie',
                            //maskRe: /^[a-zA-Z-áéíóúñÁÉÍÓÚÑ ]/,
                            //regex: /^[A-Za-z-áéíóúñÁÉÍÓÚÑ]*\s?([A-Za-z-áéíóúñÁÉÍÓÚÑ]+\s?)+[A-Za-z-áéíóúñÁÉÍÓÚÑ]$/,
                            //regexText: 'El nombre no es válido'
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
                                    Ext.getStore('storeModelosTecn').load({params: {marca_id: record.data.id}});
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
                            xtype: 'numberfield',
                            fieldLabel: 'Hp',
                            name: 'hp'
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Rpm',
                            name: 'rpm'
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/grupos_electrogenos_motores/${action}`),
            selection = (action !== 'add' && grid.getSelectionModel().hasSelection()) ? grid.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar motor?',
                message: `¿Está seguro que desea eliminar el motor?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {
                            id: selection.get('id'),
                        };
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                grid.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let winform = Ext.create('Portadores.motores.Window', {
                title: !selection ? 'Adicionar motor' : `Modificar <span class="font-italic font-weight-bold">${selection.get('no_serie')}</span>`,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.get('id') : null;

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        grid.getStore().load();
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
                            winform.close()
                        }
                    }
                ],
                listeners: {
                    beforerender: function () {
                        if (action === 'add') {
                            Ext.getStore('storeMarcasTecn').load();
                        }
                    }
                }
            });
            if (action === 'upd') {
                App.mask();

                let selected = grid.getSelection()[0].data;

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

                Promise.all([marcaPromise, modeloPromise])
                    .then(() => {
                        winform.show();
                        winform.down('form').getForm().loadRecord(selection);
                        App.unmask();
                        Ext.getCmp('modelo_id').enable();
                    })
                    .catch(() => {
                        App.unmask();
                        App.showAlert('Error cargando los datos.', 'danger');
                    });
            }
            else winform.show();
            // if (action === 'upd') winform.down('form').getForm().loadRecord(selection);
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
        bind: {disabled: '{!gridMotores.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'upd')
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridMotores.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbar = Ext.getCmp('motores_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
});
