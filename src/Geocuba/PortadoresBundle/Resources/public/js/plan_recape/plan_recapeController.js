/**
 * Created by kireny on 06/07/2017.
 */

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

    var store_plan_recape = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_plan_recape',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'fecha'},
            {name: 'unidad_id'},
            {name: 'unidad'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_recape/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_plan_recape').getSelectionModel().deselectAll();
                operation.setParams({
                    // nombre: find_button.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            }
        }
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '{name} ' + ' ({rows.length})',
        hideGroupedHeader: true,
        startCollapsed: false,
        ftype: 'grouping'
    });

    var store_vehiculos_recape = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_vehiculos_recape',
        fields: [
            {name: 'id'},
            {name: 'mes'},
            {name: 'vehiculo_id'},
            {name: 'matricula'},
            {name: 'marca'},
            {name: 'medidas'},
            {name: 'fecha_rotacion'},
            {name: 'cant_neumaticos'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_recape/loadVehiculos'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        groupField: 'mes',
        listeners: {
            beforeload: function (This, operation) {
                var selected = Ext.getCmp('id_grid_plan_recape').getSelectionModel().getLastSelected();
                if (selected != undefined) {
                    operation.setParams({'id_recape': selected.data.id})
                }
                Ext.getCmp('id_grid_vehiculos_recape').getSelectionModel().deselectAll();
            }
        }
    });

    //TODO barra de scroll para las unidades
    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border:true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        collapseDirection: 'left',
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
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
                if(Ext.getCmp('plan_recape_btn_add'))
                Ext.getCmp('plan_recape_btn_add').enable();
                Ext.getCmp('id_grid_plan_recape').enable();
                Ext.getCmp('id_grid_plan_recape').getStore().loadPage(1);
                if(Ext.getStore('id_store_vehiculo_unidad'))
                    Ext.getStore('id_store_vehiculo_unidad').load();
            }
        }


    });

    var grid_plan_recape = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_plan_recape',
        width: '30%',
        height: '100%',
        region: 'center',
        store: store_plan_recape,
        features: [groupingFeature],
        columns: [
            {text: '<strong>Nombre del Plan</strong>', dataIndex: 'nombre', flex: 1}
        ],
        tbar: {
            id: 'plan_recape_tbar',
            height: 36,
            items: []
        },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_plan_recape'),
            displayInfo: false,
        },
        listeners: {
            selectionchange: function (This, selected) {
                if (Ext.isEmpty(selected)){
                    // grid_vehiculos_recape.collapse();
                    store_vehiculos_recape.removeAll();
                } else {
                    if (grid_vehiculos_recape.getCollapsed()) {
                        grid_vehiculos_recape.on('expand', function () {
                            store_vehiculos_recape.loadPage(1);
                        }, this, {single: true});

                        grid_vehiculos_recape.expand();
                    } else {
                        store_vehiculos_recape.loadPage(1);
                    }
                }
                if(Ext.getCmp('plan_recape_btn_mod'))
                Ext.getCmp('plan_recape_btn_mod').setDisabled(selected.length == 0);
                if(Ext.getCmp('plan_recape_btn_del'))
                Ext.getCmp('plan_recape_btn_del').setDisabled(selected.length == 0);

            }
        }
    });

    var grid_vehiculos_recape = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_vehiculos_recape',
        title: 'Vehículos del Plan de Recape',
        region: 'east',
        // collapsible: false,
        // collapsed: false,
        border:true,
        width: '45%',
        height: '100%',
        store: store_vehiculos_recape,
        columns: [
            {text: '<strong>Vehículo</strong>', dataIndex: 'matricula', flex: 0.3},
            {text: '<strong>Cant Neumáticos</strong>', dataIndex: 'cant_neumaticos', flex: 0.3},
            {text: '<strong>Marca Neumáticos</strong>', dataIndex: 'marca', flex: 0.3},
            {text: '<strong>Fecha Rotación</strong>', dataIndex: 'fecha_rotacion', flex: 0.3}
        ],
        plugins: [{
            rowBodyTpl: new Ext.XTemplate(
                "<table align='left' width='80%' style='margin-top: 10px; margin-bottom: 10px; background: #f0f0f0;'>" +
                "<tr>" +
                "<td style='padding-right: 20px; padding-top: 10px; padding-left: 10px;'><p><b>Medidas de los Neumáticos:</b> {medidas}</p></td>" +
                "</tr>" +
                "</table>"
            ),
            ptype: 'rowexpander'
        }],
        // tbar: {
        //     id: 'vehiculos_recape_tbar',
        //     height: 36,
        //     items: []
        // },
        bbar: {
            xtype: 'pagingtoolbar',
            pageSize: 25,
            store: Ext.getStore('id_store_vehiculos_recape'),
            displayInfo: true,
        },
        listeners: {
            expand: function (p, eOpts) {
                Ext.getCmp('id_grid_vehiculos_recape').getStore().loadPage(1);
            }
        }
    });

    var panel_plan_recape = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_plan_recape',
        title: 'Planes de Recape',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_plan_recape, grid_vehiculos_recape]
    });

    App.render(panel_plan_recape);

});