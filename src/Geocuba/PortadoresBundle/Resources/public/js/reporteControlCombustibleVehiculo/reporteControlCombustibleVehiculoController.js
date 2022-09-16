/**
 * Created by pfcadenas on 1/09/2016.
 */

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
                    while (!Ext.getCmp('grid_reporte_control_combustible_Vehiculo') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('grid_reporte_control_combustible_Vehiculo'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    var store_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta_reporte_control_combustible_Vehiculo',
        fields: [
            {name: 'chapa'}
        ],

        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'chapa',
            direction: 'ASC',
        }],
        autoLoad: false,
        limit: 10000

    });

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        store: store_vehiculo,
        id: 'id_comboSearch',
        width: 100,
        displayField: 'matricula',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'No. Chapa...',
        selectOnFocus: true,
        editable: true,
        allowBlank: false,
        listeners: {
            change: function (This, newValue) {

                Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().currentPage = 1;
                Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().load();

                if (Ext.getCmp('reporte_control_combustible_Vehiculo_btn_print'))
                    Ext.getCmp('reporte_control_combustible_Vehiculo_btn_print').setDisabled(newValue === null)
                if (Ext.getCmp('reporte_control_combustible_Vehiculo_btn_export'))
                    Ext.getCmp('reporte_control_combustible_Vehiculo_btn_export').setDisabled(newValue === null)


            }
        }

    });

    /*var btnSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        iconCls: 'fa fa-search fa-1_4',
        handler: function () {
            Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().currentPage = 1;
            Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().load();
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        iconCls: 'fa fa-eraser fa-1_4',
        handler: function () {
            cmbSearch.setValue('');
            Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().currentPage = 1;
            Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getStore().load();
        }
    });*/

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

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_reporte_control_combustible_Vehiculo',
        width: '100%',
        //sortableColumns: false,
        disabled: true,
        region: 'center',
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'store_reporte_control_combustible_Vehiculo',
            fields: [
                {name: 'vale'},
                {name: 'fecha'},
                {name: 'comb_abastecido', type: 'float'},
                {name: 'entrada'},
                {name: 'existencia'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/controlCombustibleVehiculo/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    Ext.getCmp('grid_reporte_control_combustible_Vehiculo').getSelectionModel().deselectAll();
                    operation.setParams({
                        id: cmbSearch.getValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                        mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                        anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                    });
                }
            }
        }),
        features: [{
            ftype: 'summary',
            //dock: 'bottom'
        }],
        columns: [
            {
                text: '<strong>Vale</strong>',
                dataIndex: 'vale',
                align: 'center',
                filter: 'string',
                flex: 1,
                summaryRenderer: function (value) {
                    return '<strong>Total</strong>';
                }
            }, {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                align: 'center',
                //formatter: 'date("d/m/Y")',
                flex: 1
            }, {
                text: '<strong>Comb. Abastecido(L)</strong>',
                dataIndex: 'comb_abastecido',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                },
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Entrada</strong>',
                dataIndex: 'entrada',
                align: 'center',
                flex: 1
            }, {
                text: '<strong>Existencia(L)</strong>',
                dataIndex: 'existencia',
                align: 'center',
                flex: 1
            }
        ],

        tbar: {
            id: 'tbar_reporte_control_combustible_Vehiculo',
            height: 36,
            items: [mes_anno, cmbSearch, /*btnSearch, btnClearSearch,*/ '->']
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'panel_id_store_reporte_control_combustible_Vehiculo',
        title: 'Control de combustible por veh&iacute;culo',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid]
    });
    App.render(_panel);
});