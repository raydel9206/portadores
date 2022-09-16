Ext.onReady(function () {

    let mes_anno = Ext.create('Ext.form.field.Month', {
        format: 'm, Y',
        id: 'mes_anno',
        // fieldLabel: 'Date',
        width: 90,
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        renderTo: Ext.getBody(),
        listeners: {
            boxready: function () {
                let me = this;
                me.selectMonth = new Date(App.selected_month + '/1/' + App.selected_year);

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('grid_reporte_control_combustible_Deposito') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('grid_reporte_control_combustible_Deposito'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjetaReporteControlCombustibleDeposito',
        fields: [
            {name: 'nro_tarjeta', type: 'number'},
            {name: 'id' }
        ],
        groupField: 'nombreunidadid',

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'nro_tarjeta',
            direction: 'ASC'
        }],
        autoLoad: false,
        limit:10000
    });

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        width: 155,
        id:'id_comboSearch',
        store: store_tarjeta,
        displayField: 'nro_tarjeta',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'No. Tarjeta...',
        selectOnFocus: true,
        editable: true,
        afterLabelTextTpl: [
            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
        ],
        allowBlank: false,
        listeners: {
            change: function (This, newValue) {
                Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().currentPage = 1;
                Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().load();
                if(Ext.getCmp('reporteControlCombustibleDeposito_btn_print'))
                    Ext.getCmp('reporteControlCombustibleDeposito_btn_print').setDisabled(newValue === null);
                if(Ext.getCmp('reporteControlCombustibleDeposito_btn_export'))
                    Ext.getCmp('reporteControlCombustibleDeposito_btn_export').setDisabled(newValue === null);
            }
        }
    });

    /*var btnSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        iconCls: 'fa fa-search fa-1_4',
        handler: function () {
            Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().currentPage = 1;
            Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().load();
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        iconCls: 'fa fa-eraser fa-1_4',
        handler: function () {
            cmbSearch.setValue('');
            Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().currentPage = 1;
            Ext.getCmp('grid_reporte_control_combustible_Deposito').getStore().load();
        }
    });*/

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reporte_control_combustible_Deposito',
        // width: '100%',
        region: 'center',
        disabled: true,
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'store_reporte_control_combustible_Deposito',
            fields: [
                {name: 'anticipo'},
                {name: 'vale'},
                {name: 'fecha'},
                {name: 'comb_abastecido'},
                {name: 'entrada_litros'},
                {name: 'entrada_importe'},
                {name: 'salida_litros'},
                {name: 'salida_importe'},
                {name: 'existencia_litros'},
                {name: 'existencia_importe'},
                {name: 'chapa'},
                {name: 'combustible'},
                {name: 'centro_costo'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/controlCombustibleDeposito/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('grid_reporte_control_combustible_Deposito').getSelectionModel().deselectAll();
                    operation.setParams({
                        tarjeta: cmbSearch.getValue(),
                        mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                        anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                    });
                }
            }
        }),
        columns: [
            {
                text: '<strong>Anticipo</strong>',
                dataIndex: 'anticipo',
                filter: 'string',
                flex: 1
            },{
                text: '<strong>Vale</strong>',
                dataIndex: 'vale',
                filter: 'string',
                flex: 1
            }, {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Entrada(L)</strong>',
                dataIndex: 'entrada_litros',
                tooltip: 'Entrada(L)',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Entrada Imp.</strong>',
                dataIndex: 'entrada_importe',
                tooltip: 'Entrada Importe',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Salida(L)</strong>',
                dataIndex: 'salida_litros',
                tooltip: 'Salida(L)',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Salida Imp.</strong>',
                dataIndex: 'salida_importe',
                tooltip: 'Salida Importe',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Existencia(L)</strong>',
                dataIndex: 'existencia_litros',
                tooltip: 'Existencia(L)',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Existencia Imp.</strong>',
                dataIndex: 'existencia_importe',
                tooltip: 'Existencia Importe',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Chapa</strong>',
                dataIndex: 'chapa',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Combustible</strong>',
                dataIndex: 'combustible',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Centro Costo</strong>',
                dataIndex: 'centro_costo',
                align: 'center',
                flex: 1
            }
        ],

        tbar: {
            id: 'tbar_reporte_control_combustible_Deposito',
            height: 36,
            items: [mes_anno, cmbSearch,/* btnSearch, btnClearSearch,*/ '->']
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('store_reporte_control_combustible_Deposito'),
            displayInfo: true,

        }
    });

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

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'panel_id_store_reporte_control_combustible_Deposito',
        title: 'Control de combustible por depósito',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree,grid]
    });
    App.render(_panel);
});