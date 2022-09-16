/**
 * Created by yosley on 10/04/2017.
 */

Ext.onReady(function () {

    let storeAutolectura = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_autolecturaprepago',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'serviciosid', type: 'string'},
            {name: 'nombreserviciosid', type: 'string'},
            {name: 'lectura_pico', type: 'number'},
            {name: 'lectura5pm', type: 'number'},
            {name: 'perdidaT_dia', type: 'number'},
            {name: 'lectura_dia', type: 'number'},
            {name: 'consumo_total_mad', type: 'number'},
            {name: 'consumo_total_dia', type: 'number'},
            {name: 'fecha_autolectura', type: 'date'},
            {name: 'consumo_total_real', type: 'number'},
            {name: 'consumo_total_plan', type: 'number'},
            {name: 'consumo_total_porciento', type: 'number'},
            {name: 'consumo_pico_real', type: 'number'},
            {name: 'plan_pico', type: 'number'},
            {name: 'plan_diario', type: 'number'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/autolectura_prepago/getautolecturasbyservicios'),
            reader: {
                type: 'json',
                rootProperty: 'rows'
            }
        },
        listeners: {
            load: function (This, records, successful, operation) {
                if (records.length === 0) {
                    App.showAlert('No existen autolecturas para el servicio seleccionado', 'danger', 3500);
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

    let paneltree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'paneltree',
        // border: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        // header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'},},
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
                storeAutolectura.removeAll();
                gridprepago_este.getStore().load();
                gridprepago_este.focus();
                paneltree.collapse();
            }
        }
    });

    let gridprepago_center = Ext.create('Ext.grid.Panel', {
        id: 'id_gridprepago',
        region: 'center',
        flex: 1,
        columnLines: true,
        viewConfig: {
            stripeRows: true
        },
        border: '10 5 3 10',
        style: {
            borderStyle: 'solid'
        },
        tbar: {
            id: 'autolectura_tbar',
            height: 36
        },
        store: storeAutolectura,
        columns: [

            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha_autolectura',
                flex: 1,
                align: 'center',

                xtype: 'datecolumn',
                format: 'D,j M, Y '
            },
            {
                text: '<strong>Lecturas</strong>',
                dataIndex: 'lectura_dia',
                flex: 1,
                align: 'center'
            },
            {
                text: '<strong>Consumo Total</strong>',
                columns: [
                    {
                        text: '<strong>Plan</strong>',
                        dataIndex: 'plan_diario',
                        formatter: "number('0.00')",
                        width: 100,
                        align: 'center'
                    },
                    {
                        text: '<strong>Real</strong>',
                        dataIndex: 'consumo_total_real',
                        width: 100,
                        align: 'center',
                        renderer: function (val2, met, record, a, b, c, d) {
                            if (record.get('consumo_total_real') > record.get('plan_diario')) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                                return Ext.util.Format.round((record.get('consumo_total_real')), 2);
                            } else
                                return Ext.util.Format.round((record.get('consumo_total_real')), 2);
                        }
                    },
                    {
                        text: '<strong>%</strong>',
                        dataIndex: 'consumo_total_porciento',
                        width: 100,
                        align: 'center',
                        renderer: function (value, metaData, record) {
                            if (value === null ||  isNaN(value) || value === 0) {
                                return 0 ;
                            }
                            return Ext.util.Format.round((record.get('consumo_total_real') * 100 / record.get('plan_diario')), 2);
                        }
                    }
                ]
            }, {
                text: '<strong>Perdida diaria</strong>',
                name: 'perdidaT_dia',
                dataIndex: 'perdidaT_dia',
                flex: 1,
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('autolectura_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() === 'button' && selected.length === 0)
                    }
                );
            }
        }
    });

    let gridprepago_este = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_prepagoautolecturaservicios',
        region: 'west',
        width: 280,
        title: 'Servicios',
        // frame: true,
        columnLines: true,
        collapsed: false,
        hideHeaders: true,
        style: {
            borderStyle: 'solid'
        },
        store: Ext.create('Ext.data.JsonStore', {
            storeId: 'id_store_autolecturaservicios',
            fields: [
                {name: 'id'},
                {name: 'nombre_servicio'},
                {name: 'codigo_cliente'},
                {name: 'factor_metrocontador'},
                {name: 'MaximaDemandaContratada'},
                {name: 'control'},
                {name: 'ruta'},
                {name: 'folio'},
                {name: 'direccion'},
                {name: 'factor_combustible'},
                {name: 'indice_consumo'},
                {name: 'consumo_prom_anno'},
                {name: 'consumo_prom_plan'},
                {name: 'consumo_prom_real'},
                {name: 'capac_banco_transf'},
                {name: 'tipo_servicio'},
                {name: 'turno_trabajo'},
                {name: 'nunidadid'},
                {name: 'nombreunidadid'},
                {name: 'provicianid'},
                {name: 'nombreprovicianid'},
                {name: 'tarifaid'},
                {name: 'nombretarifaid'},
                {name: 'nactividadid'},
                {name: 'nombrenactividadid'},
                {name: 'num_nilvel_actividadid'},
                {name: 'nombreum_nilvel_actividadid'},
                {name: 'servicio_mayor'},
                {name: 'servicio_prepago'}
            ],
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/servicio/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            autoLoad: false,
            filters: [{
                property: 'servicio_prepago',
                value: /true/
            }],
            listeners: {
                beforeload: function (This, operation) {
                    operation.setParams({
                        unidadid: (Ext.getCmp('paneltree').getSelectionModel().getLastSelected() !== undefined) ? Ext.getCmp('paneltree').getSelectionModel().getLastSelected().data.id : null,
                    });
                }
            }
        }),
        columns: [
            {
                dataIndex: 'nombre_servicio',
                text: 'Nombre',
                width: 280,
                align: 'center'
            }
        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                if (selected.length !== 0) {
                    storeAutolectura.load({params: {idservicios: selected[0].id}});
                }
                Ext.getCmp('autolectura_tbar').items.each(
                    function (item, index, length) {
                        item.setDisabled(item.getXType() === 'button' && selected.length === 0)
                    }
                );
            }
        }
    });

    let grid_prepagoacumulados = Ext.create('Ext.panel.Panel', {
        id: 'id_gridprepagoacumulados',
        region: 'east',
        hidden: true,
        columnLines: true,
        title: 'Acumulados kwhats',
        height: 100,
        width: 200,
        // frame: true,
        collapsible: true,
        items: [
            {
                xtype: 'fieldcontainer',
                flex: 1,
                layout: 'vbox',
                border: true,
                // margin: '10 10 10 10',
                collapsible: false,
                labelAlign: 'right',


                items: [{
                    xtype: 'fieldcontainer',
                    flex: 1,
                    width: 230,

                    layout: 'anchor',
                    border: false,
                    margin: '10 10 10 10',
                    collapsible: false,
                    labelAlign: 'right',

                    items: [
                        {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Plan</strong>',
                            id: 'id_plan',
                            name: 'id_plan'
                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Real</strong>',
                            id: 'id_real',
                            name: 'id_real'
                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>Diferencia</strong>',
                            id: 'id_diferencia',
                            name: 'id_diferencia'
                        }, {
                            xtype: 'displayfield',
                            fieldLabel: '<strong>% Plan-Real</strong>',
                            id: 'id_plan_real',
                            name: 'id_plan_real'
                        }]


                }
                ]
            }
        ]
    });

    let _panel_autolectura = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_autolectura',
        padding: '2 0 0',
        title: 'Autolecturas Prepago',
        border: true,
        frame: true,
        layout: 'border',
        items: [paneltree, gridprepago_este, gridprepago_center, grid_prepagoacumulados]
    });

    App.render(_panel_autolectura);

});