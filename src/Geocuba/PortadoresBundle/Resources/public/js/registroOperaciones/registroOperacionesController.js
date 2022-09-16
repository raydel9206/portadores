Ext.onReady(function () {

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
                    while (!Ext.getCmp('gridRegistroOperaciones') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('gridRegistroOperaciones'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let storeEquiposTecnologicos = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeEquiposTecnologicos',
        fields: ['id', 'descripcion', 'nro_inventario', 'unidad_id', 'unidad_nombre'],
        groupField: 'unidad_nombre',
        sorters: 'unidad_nombre',
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/equipos_tecn/load'),
            extraParams: {tipo: 'otro'},
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                })
            }

        }

    });

    let storeRegistroOperaciones = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeRegistroOperaciones',
        fields: ['id', 'medicion', 'litros', 'consumo_litros', 'fecha'],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/registro_operaciones/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (store, operation) {
                operation.setParams({
                    equipo_tecnologico_id: gridEquiposTecnologicos.getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                })
            }
        }

    });

    let groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '<b>Unidad: {name} ' + ' ({rows.length})</b>',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
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

    let panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: '20%',
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
            select: function (This) {
                gridEquiposTecnologicos.enable();
                gridEquiposTecnologicos.getStore().load();
                gridEquiposTecnologicos.focus();
                panetree.collapse();
            },
            collapse: function () {
                gridRegistroOperaciones.setWidth('78%');
            },
            expand: function () {
                gridRegistroOperaciones.setWidth('58%');
            }
        }
    });

    let columnsBase1 = [{text: 'Fecha', dataIndex: 'fecha', flex: 0.5, align: 'center'}];

    let columnsBase2 = [
        {
            text: 'Combustible<br/>Inicial',
            dataIndex: 'combustible_inicial',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Combustible<br/>Abastecido',
            dataIndex: 'combustible_abastecido',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Combustible<br/>Final',
            dataIndex: 'combustible_final',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Consumo<br/>Real',
            dataIndex: 'consumo_real',
            flex: 0.6,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Consumo<br/>Normado',
            dataIndex: 'consumo_normado',
            flex: 0.6,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        }
    ];

    let columnsOtros = [
        {
            text: 'Hora<br/>Arranque',
            dataIndex: 'hora_arranque',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Hora<br/>Parada',
            dataIndex: 'hora_parada',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        }
    ];

    let columnsCalderas = [
        {
            text: 'Hora Arranque<br/>Recirculación',
            dataIndex: 'hora_arranque_recirculacion',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Hora Parada<br/>Recirculación',
            dataIndex: 'hora_parada_recirculacion',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Consumo Real<br/>Recirculación',
            dataIndex: 'consumo_real_recirculacion',
            flex: 0.6,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Consumo Normado<br/>Recirculación',
            dataIndex: 'consumo_normado_recirculacion',
            flex: 0.6,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        }
    ];

    let columnsMontacargas = [
        {
            text: 'Horámetro<br/>Arranque',
            dataIndex: 'horametro_arranque',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Horámetro<br/>Parada',
            dataIndex: 'horametro_parada',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        },
        {
            text: 'Tiempo<br/>Empleado(H)',
            dataIndex: 'horas_trabajadas',
            flex: 0.5,
            align: 'center',
            renderer: function (value, meta) {
                meta += 'text-align: right;';
                return value;
            }
        }
    ];

    let gridEquiposTecnologicos = Ext.create('Ext.grid.Panel', {
        id: 'gridEquiposTecnologicos',
        reference: 'gridEquiposTecnologicos',
        title: 'Equipos Tecnológicos',
        features: [groupingFeature],
        store: storeEquiposTecnologicos,
        region: 'center',
        disabled: true,
        width: '22%',
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No existen equipos tecnológicos registrados</div>'},
        columns: [
            {text: '<strong>Nro. Inv.</strong>', dataIndex: 'numero_inventario', align: 'center', flex: 0.4},
            {text: '<strong>Descripción</strong>', dataIndex: 'descripcion', filter: 'string', flex: 1},
        ],
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('storeEquiposTecnologicos'),
            displayInfo: true,
        },
        tbar: {
            xtype: 'toolbar',
            items: [{
                xtype: 'combo',
                id: 'combo_denominacion',
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'nombre'],
                    data: [
                        {value: 'otro', name: 'Otros'},
                        {value: 'static_tec_denomination_1', name: 'Calderas'},
                        {value: 'static_tec_denomination_3', name: 'Montacargas'}
                    ]
                }),
                value: 'otro',
                valueField: 'value',
                displayField: 'name',
                editable: false,
                listeners: {
                    select: function (This, record) {
                        storeEquiposTecnologicos.getProxy().setExtraParam('tipo', record.data.value);
                        gridEquiposTecnologicos.getSelectionModel().deselectAll();
                        gridEquiposTecnologicos.getStore().load();

                        if (record.data.value === 'otro') gridRegistroOperaciones.reconfigure([ ...columnsBase1, ...columnsOtros, ...columnsBase2]);
                        else if (record.data.value === 'static_tec_denomination_1')
                            gridRegistroOperaciones.reconfigure([ ...columnsBase1,  ...columnsOtros, ...columnsBase2, ...columnsCalderas]);
                        else if (record.data.value === 'static_tec_denomination_3')
                            gridRegistroOperaciones.reconfigure([ ...columnsBase1,  ...columnsMontacargas, ...columnsBase2 ]);
                    }
                }
            }]
        },
        listeners: {
            selectionchange: function (This, selected) {
                gridRegistroOperaciones.getStore().removeAll();

                if (selected.length) {
                    gridRegistroOperaciones.getStore().load();
                    gridRegistroOperaciones.enable();
                } else {
                    gridRegistroOperaciones.disable();
                }
            }
        }
    });

    let gridRegistroOperaciones = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Operaciones',
        id: 'gridRegistroOperaciones',
        reference: 'gridRegistroOperaciones',
        region: 'east',
        width: '58%',
        disabled: true,
        viewModel: {},
        viewConfig: {emptyText: '<div class="text-center">No se han realizado operaciones.</div>'},
        store: storeRegistroOperaciones,
        columns: [...columnsBase1, ...columnsOtros, ...columnsBase2],
        tbar: {
            id: 'gridRegistroTbar',
            height: 30,
            padding: '3 0 6 8',
            items: [mes_anno, '-']
        },
        tools: [{
            type: 'refresh',
            tooltip: 'Actualiza el listado de mediciones',
            callback: function (gridpanel) {
                gridpanel.getStore().reload();
            }
        }]
    });

    let panel = Ext.create('Ext.panel.Panel', {
        id: 'panel',
        title: 'Registro de Operaciones',
        frame: true,
        closable: true,
        layout: 'border',
        items: [panetree, gridEquiposTecnologicos, gridRegistroOperaciones]
    });

    App.render(panel);
});
