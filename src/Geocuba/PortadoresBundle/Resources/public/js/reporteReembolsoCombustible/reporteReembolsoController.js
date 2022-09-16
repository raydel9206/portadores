Ext.onReady(function () {
    var tree_store = Ext.create('Ext.data.TreeStore', {
        id: 'store_unidades',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'nombre', type: 'string'},
            {name: 'siglas', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'municipio', type: 'string'},
            {name: 'municipio_nombre', type: 'string'},
            {name: 'provincia', type: 'string'},
            {name: 'provincia_nombre', type: 'string'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/unidad/loadTree'),
            reader: {
                type: 'json',
                rootProperty: 'children'
            }
        },
        sorters: 'nombre',
        listeners: {
            beforeload: function () {
                if (Ext.getCmp('arbolunidades') !== undefined)
                    Ext.getCmp('arbolunidades').getSelectionModel().deselectAll();
            }
        }
    });

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        border: true,
        id: 'arbolunidades',
        hideHeaders: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,

        collapseDirection: 'left',
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
        layout: 'fit',

        columns: [
            {xtype: 'treecolumn', iconCls: Ext.emptyString, width: 450, dataIndex: 'nombre'}
        ],
        root: {
            text: 'root',
            expanded: true
        },
        listeners: {
            select: function (This, record, tr, rowIndex, e, eOpts) {
                grid.enable();
                Ext.getCmp('id_comboSearch').getStore().load({
                    params: {
                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                    }
                });
            }
        }
    });


    var store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda_reembolso_semanal_combustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        labelWidth: 140,
        id:'id_comboSearch',
        store: store_moneda,
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        // forceSelection: true,
        emptyText: 'Moneda...',
        // selectOnFocus: true,
        editable: true,
        allowBlank: false,
        listeners: {
            change: function (This, newValue) {
                if (newValue !== null) {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').enable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').enable();
                } else {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').disable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').disable();
                }
            }
        }
    });

    var fechaDesde = Ext.create('Ext.form.field.Date', {
        labelWidth: 140,
        id:'fecha_desde',
        emptyText: 'Fecha desde...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        format: 'd/m/Y',
        listeners: {
            change: function (This, newValue) {

                fechaHasta.setMinValue(newValue);

                if (newValue !== null) {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').enable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').enable();
                } else {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').disable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').disable();
                }
            }
        }
    });

    var fechaHasta = Ext.create('Ext.form.DateField', {
        labelWidth: 140,
        id:'fecha_hasta',
        emptyText: 'Fecha hasta...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        format: 'd/m/Y',
        listeners: {
            change: function (This, newValue) {

                fechaDesde.setMaxValue(newValue);

                if (newValue !== null) {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').enable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').enable();
                } else {
                    Ext.getCmp('reembolso_semanal_combustible_btn_print').disable();
                    Ext.getCmp('reembolso_semanal_combustible_btn_export').disable();
                }
            }
        }
    });

    var btnSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        iconCls: 'fa fa-search fa-1_4 text-primary',
        handler: function () {
            if (cmbSearch.getValue() === null)
                cmbSearch.validate();
            if (fechaDesde.getRawValue() === '')
                fechaDesde.validate();
            if (fechaHasta.getRawValue() === '')
                fechaHasta.validate();

            if (cmbSearch.getValue() !== null &&
                fechaDesde.getRawValue() !== '' &&
                fechaHasta.getRawValue() !== '') {
                grid.getStore().load();
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reembolso_semanal_combustible',
        width: '100%',
        region: 'center',
        disabled: true,
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'store_reembolso_semanal_combustible',
            fields: [
                {name: 'dia'},
                {name: 'vale'},
                {name: 'importe'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/reembolso/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            pageSize: 84,
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    operation.setParams({
                        monedaid: cmbSearch.getValue(),
                        fechaDesde: fechaDesde.getRawValue(),
                        fechaHasta: fechaHasta.getRawValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                    });
                }
            }
        }),
        columns: [
            {
                text: '<strong>Día</strong>',
                dataIndex: 'dia',
                align: 'center',
                flex: 1
            },
            {
                text: '<strong>No. Anticipo</strong>',
                dataIndex: 'vale',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Importe</strong>',
                dataIndex: 'importe',
                align: 'right',
                flex: 1
            }
        ],

        tbar: {
            id: 'tbar_reembolso_semanal_combustible',
            height: 36,
            items: [cmbSearch, fechaDesde, fechaHasta, btnSearch, '->']
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'panel_id_store_reembolso_semanal_combustible',
        title: 'Reembolso semanal de combustible',
        frame: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });
    App.render(_panel);
});