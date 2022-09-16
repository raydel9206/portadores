/**
 * Created by orlando on 09/01/2017.
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
                    while (!Ext.getCmp('id_grid_combustible_caja') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_combustible_caja'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    var store_tarjeta = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tarjeta',
        fields: [
            {name: 'nro_tarjeta'},
            {name: 'id'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tarjeta/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{property: 'nro_tarjeta', direction: 'ASC'}],
        listeners: {
            beforeload: function (store, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                });
            }
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

    var store_combustible_caja = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_combustible_caja',
        fields: [
            {name: 'fecha_entrada'},
            {name: 'nro_factura'},
            {name: 'entrada_importe'},
            {name: 'fecha_salida'},
            {name: 'no_anticipo'},
            {name: 'nro_vale'},
            {name: 'centro_costo'},
            {name: 'chapa'},
            {name: 'cantidad'},
            {name: 'importe_inicial'},
            {name: 'salida_importe'},
            {name: 'importe_final'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/libro_caja/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_combustible_caja').getSelectionModel().deselectAll();
                operation.setParams({
                    nro_tarjeta: cmbSearch.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                });
            }
        }
    });

    var cmbSearch = Ext.create('Ext.form.ComboBox', {
        labelWidth: 140,
        store: store_tarjeta,
        id:'nro_tarjeta',
        displayField: 'nro_tarjeta',
        valueField: 'nro_tarjeta',
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

                Ext.getCmp('id_grid_combustible_caja').getStore().currentPage = 1;
                Ext.getCmp('id_grid_combustible_caja').getStore().load();

                Ext.getCmp('reporte_control_combustible_Deposito_btn_print').setDisabled(!newValue);
                Ext.getCmp('reporte_control_combustible_Deposito_btn_export').setDisabled(!newValue);
            }
        }
    });


    var panetree = Ext.create('Ext.tree.Panel', {
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
            select: function (This, record, tr, rowIndex, e, eOpts) {
                grid_combustible_caja.enable();
                store_tarjeta.load();
            }
        }


    });

    var grid_combustible_caja = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_combustible_caja',
        store: store_combustible_caja,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        columns: [
            {text: '<strong>Fecha</strong>', dataIndex: 'fecha_entrada', filter: 'string', width: 150, align: 'center'},
            {text: '<strong>No. Fact</strong>', dataIndex: 'nro_factura', filter: 'string', width: 80, align: 'center'},
            {
                text: '<strong>Importe</strong>',
                dataIndex: 'entrada_importe',
                filter: 'string',
                width: 80,
                align: 'center'
            },
            {text: '<strong>Firma</strong>', filter: 'string', width: 80, align: 'center'},
            {text: '<strong>Fecha</strong>', dataIndex: 'fecha_salida', filter: 'string', width: 150, align: 'center'},
            {
                text: '<strong>No. Anticipo</strong>',
                dataIndex: 'no_anticipo',
                filter: 'string',
                width: 120,
                align: 'center'
            },
            {
                text: '<strong>No. Vale <br>Serv.</strong>',
                dataIndex: 'nro_vale',
                filter: 'string',
                width: 80,
                align: 'center'
            },
            {text: '<strong>C/C</strong>', dataIndex: 'centro_costo', filter: 'string', width: 120, align: 'center'},
            {text: '<strong>Chapa</strong>', dataIndex: 'chapa', filter: 'string', width: 80, align: 'center'},
            {text: '<strong>Cantidad</strong>', dataIndex: 'cantidad', filter: 'string', width: 80, align: 'center'},
            {
                text: '<strong>Importe <br> Inic</strong>',
                dataIndex: 'importe_inicial',
                filter: 'string',
                width: 80,
                align: 'center'
            },
            {
                text: '<strong>Importe <br>Abast.</strong>',
                dataIndex: 'salida_importe',
                filter: 'string',
                width: 80,
                align: 'center'
            },
            {
                text: '<strong>Importe <br>Final.</strong>',
                dataIndex: 'importe_final',
                filter: 'string',
                width: 80,
                align: 'center'
            },
        ],
        tbar: {
            id: 'combustible_caja_tbar',
            height: 36,
            items: [mes_anno, cmbSearch]
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_combustible_caja'),
            displayInfo: true,
        },
        plugins: 'gridfilters'
    });

    var panel_combustible_caja = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_combustible_caja',
        title: 'Libro Combustible en Caja',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_combustible_caja]
    });

    App.render(panel_combustible_caja);
});