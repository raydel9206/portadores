Ext.onReady(function () {

    let storeDistribucion = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeDistribucion',
        fields: ['id', 'cantidad', 'cantidad_plan', 'equipo_id', 'equipo_desc', 'indice_consumo', 'precio_comb'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/distribucion_tecnologicos/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    unidad_id: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    tipo_combustible_id: tipo_combustible.getValue(),
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                })
            },
            load: function (This, records) {
                if (records.length > 0) {
                    let cantidadAsignacion = This.getProxy().getReader().rawData.extraData.cantidad_asignacion;
                    records.forEach(record => cantidadAsignacion -= record.data.cantidad);
                    Ext.getCmp('disponible').setValue(cantidadAsignacion);
                }
            }
        }

    });

    let tree_store = Ext.create('Ext.data.TreeStore', {
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

    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve) => {
                    let i = 0;
                    while (!Ext.getCmp('gridDistribucion') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('gridDistribucion'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let tipo_combustible = Ext.create('Ext.form.ComboBox', {
        id: 'tipo_combustible_combo',
        name: 'tipo_combustible_id',
        fieldLabel: 'Tipo de combustible',
        labelWidth: 120,
        width: 280,
        store: Ext.create('Ext.data.JsonStore', {
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
            autoLoad: true,
        }),
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        editable: false,
        listeners: {
            select: function () {
                gridDistribucion.getStore().load();
            }
        }
    });

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
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
            select: function () {
                gridDistribucion.enable();
                if (tipo_combustible.getValue()) gridDistribucion.getStore().load();
            },
            beforeexpand: function () {
                gridDistribucion.focus();
            }
        }
    });

    let gridDistribucion = Ext.create('Ext.grid.Panel', {
        id: 'gridDistribucion',
        reference: 'gridDistribucion',
        store: storeDistribucion,
        region: 'center',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No se ha realizado la distribución</div>'},
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        plugins: [
            new Ext.grid.plugin.CellEditing({
                clicksToEdit: 2,
                listeners: {
                    beforeedit: function () {
                        return Ext.getCmp('btn_save') !== undefined;
                    },
                    edit: function (This, e) {
                        let value = e.originalValue;

                        if (e.value !== parseFloat(value)) {
                            Ext.getCmp('btn_save').enable();
                            value = Ext.util.Format.number(e.value, '0.0000');
                        }

                        let disponibleCmp = Ext.getCmp('disponible');
                        console.log(disponibleCmp.getValue());
                        console.log(e.originalValue);
                        console.log(e.value);
                        console.log(disponibleCmp.getValue() - e.value + e.originalValue);

                        disponibleCmp.setValue(parseFloat(disponibleCmp.getValue()) + parseFloat(e.originalValue) - parseFloat(e.value));
                        e.record.data['incremento_reduccion'] = value - e.record.data['cantidad_plan'];
                        e.record.data[e.field] = value;
                        e.grid.getView().refresh();
                    }
                }
            })
        ],
        columns: [
            {text: '<strong>Equipo</strong>', dataIndex: 'equipo_desc', filter: 'string', flex: 0.5},
            {
                text: '<strong>Cantidad</strong>',
                dataIndex: 'cantidad',
                flex: 0.5,
                align: 'center',
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return Ext.util.Format.number(value, '0.0000');
                },
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                },
                editor: {
                    xtype: 'numberfield',
                    decimalSeparator: '.',
                    decimalPrecision: 4
                }
            },
            {
                text: '<strong>Incremento/Reducción</strong>',
                dataIndex: 'incremento_reduccion',
                flex: 0.5,
                align: 'center',
                renderer: function (value, meta) {
                    meta += 'text-align: right;';
                    return value;
                },
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            }
        ],
        tbar: {
            id: 'gridDistribucionTbar',
            height: 36,
            items: [mes_anno, tipo_combustible, '-']
        },
        listeners: {
            boxready: function () {
                setTimeout(() => {
                    Ext.getCmp('gridDistribucionTbar').add('->');
                    Ext.getCmp('gridDistribucionTbar').add({
                        xtype: 'textfield',
                        fieldLabel: 'Disponible',
                        id: 'disponible',
                        width: 155,
                        labelWidth: 65,
                        fieldStyle: 'text-align: right;',
                        listeners: {
                            change: function (This, newValue) {
                                if (newValue === 0)
                                    This.setFieldStyle('font-weight: bold; color: black');
                                else if (newValue < 0)
                                    This.setFieldStyle('font-weight: bold; color: red');
                                else
                                    This.setFieldStyle('font-weight: bold; color: green');
                            }
                        }
                    });
                });
            }
        }
    });

    let panelDistribucion = Ext.create('Ext.panel.Panel', {
        id: 'panelDistribucion',
        title: 'Distribución de Combustible para Equipos Tecnológicos',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridDistribucion]
    });

    App.render(panelDistribucion);
});