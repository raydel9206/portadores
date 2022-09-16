/**
 * Created by yosley on 14/07/2017.
 */

Ext.onReady(function () {

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_info_primaria_5073Id',
        fields: [
            { name: 'id_tarjeta'},
            { name: 'nro_tarjeta'},
            { name: 'matricula'},
            { name: 'nombre_persona'},
            { name: 'saldo_inicial',type:'number'},
            { name: 'saldo_inicial_cant',type:'number'},
            { name: 'existencia_importe',type:'number'},
            { name: 'existencia_cantidad',type:'number'},
            { name: 'entrada_importe',type:'number'},
            { name: 'entrada_cantidad',type:'number'},
            { name: 'salida_importe',type:'number'},
            { name: 'salida_cantidad',type:'number'},
            { name: 'tipoComb'},
            { name: 'saldo_final_cant',type:'number'},
            { name: 'importe_total',type:'number'},
            { name: 'importe_total_cant',type:'number'},
            { name: 'ultima_recarga',type:'number'},
            { name: 'ultima_recarga_cant',type:'number'},
            { name: 'cliente_gastos_corrientes',type:'string'},
            { name: 'comprobracion',type:'number'},


        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadInfoPrimaria5073'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        groupField:'tipoComb',
        groupDir:'DESC',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('grid_info_primaria_5073').getSelectionModel().deselectAll();
                operation.setParams({
                    nunidadid:Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes:''
                });
            }
        }
    });

    var store_elec = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_info_primaria_5073_elecId',
        fields: [
            {name: 'com_asignado'},
            {name: 'com_disp_fincimex'},
            {name: 'comb_disp_cuenta1'},
            {name: 'no_cliente'},
            {name: 'comb_entr_constructor'},
            {name: 'anno'},
            {name: 'mes'},
            {name: 'unidad_id'},
            {name: 'consumo_electricidad'},

        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadInfoPrimaria5073Elec'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('grid_info_primaria_5073_1').getSelectionModel().deselectAll();
                operation.setParams({
                    nunidadid:Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes:''
                });
            }
        },
        autoLoad: false
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '{name} ',
        hideGroupedHeader: true,
        startCollapsed: false,
        align: 'center',
        ftype: 'grouping'
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });var cellEditing1 = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        //store: store,
        id: 'arbolunidades',
        hideHeaders: true,
        width: 280,
        rootVisible: false,
        frame: true,
        collapsible: true,
        collapsed: false,
        region:'west' ,
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
            'afterrender': {
                fn: function (This, eOpts) {
                    this.loadTree(panetree);
                }, scope: this
            },
            rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                console.log(record.id)

                Ext.getCmp('pane_id_5073').setTitle('INFORMACIÓN MODELO 5073-08 '+' '+' -----'+record.data.nombre);

                Ext.getCmp('arbolunidades').collapse();
                if (record) {
                    Ext.getCmp('grid_info_primaria_5073').getStore().load();
                    Ext.getCmp('grid_info_primaria_5073_1').getStore().load();
                    console.log(Ext.getCmp('grid_info_primaria_5073').getStore().collect())

                }

            },
        }



    });
    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_info_primaria_5073',
        store: store,
        height: 420,
        // flex:1,
        width:820,
        //frame:true,
       plugins: cellEditing1,
        columns: [
            {
                text: '<strong>No. Tarjeta</strong>', dataIndex: 'nro_tarjeta', filter: 'string', width:150,
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return '<strong>TOTAL</strong>';
                }
            },
            {
                text: '<strong>Matrícula </br> del Vehículo</strong>', dataIndex: 'matricula', filter: 'string',  width:100,
            },
            {
                text: '<strong>Combustible </br> Disponible </br> Inicial mes</strong>', dataIndex: 'saldo_inicial_cant',   width:100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.Number.correctFloat( value);
                }
            },
            {
                text: '<strong>Compras </br> en el Mes</strong>', dataIndex: 'entrada_cantidad', filter: 'string',   width:100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.Number.correctFloat( value);
                }
            },
            {
                text: '<strong>Consumo</strong>', dataIndex: 'salida_cantidad', filter: 'string',   width:100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.Number.correctFloat( value);
                }
            },
            {
                text: '<strong>Combustible </br>Disponible </br> Final mes</strong>', dataIndex: 'saldo_final_cant', filter: 'string',  width:100,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.Number.correctFloat( value);
                }
            },
            {
                text: '<strong>Comprobación</strong>',   width:100,dataIndex:'comprobracion',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.Number.correctFloat( value);
                },
                renderer: function (val2, met, record, a, b, c, d) {

                    if (record.get('saldo_final_cant')!=val2) {

                        return '<div class="label-danger">' + val2 + '</div>';

                    }else

                    {
                        return val2;
                    }
                },
               // renderer: function (value, metaData, record, rowIdx, colIdx, store, view) {
               //      return Ext.util.Format.round((record.get('saldo_inicial') + record.get('entrada_importe')-record.get('salida_importe')), 2);
               //  },
               // summaryType: function (records, values) {
               //      var i = 0,
               //          length = records.length,
               //          total_tm = 0,
               //          record;
               //      for (; i < length; ++i) {
               //          record = records[i];
               //          total_tm += record.get('saldo_inicial') + record.get('entrada_importe')-record.get('salida_importe');
               //      }
               //      return "<strong>" + total_tm + "</strong>";
               //  }
            },
            {
                text: '<strong>Carga </br>Próximo </br> Mes</strong>', dataIndex: 'ultima_recarga_cant', filter: 'string',   width:100,
            },
            {
                text: '<strong>Número de </br>cliente Gastos </br>corrientes</strong>', dataIndex: 'cliente_gastos_corrientes', filter: 'string',   width:100,
                field: {
                    xtype: 'numberfield'
                }
            },

        ],
       features: [{
            ftype: 'groupingsummary',
            groupHeaderTpl: '<b>{name}</b> ({rows.length})',
            hideGroupedHeader: true,
            startCollapsed: false,
            id: 'idGrouping',
        },
            {
                ftype: 'summary',
                dock: 'bottom',
            }

        ],
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('pane_id_5073_tbar').items.each(
                    function (item, index, length) {
                        if (index != 0)
                            item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });

    var grid_electricidad = Ext.create('Ext.grid.Panel', {
        id: 'grid_info_primaria_5073_1',
        store: store_elec,
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        //frame:true,
        height: 420,
       width:500,
     //   flex:1,
        plugins: cellEditing,
        columns: [
            {
                text: '<strong>Combustible </br>Asignado al </br>Polo</strong>', dataIndex: 'com_asignado', filter: 'string',  width:100,
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                },
                field: {
                    xtype: 'numberfield'
                }
            },
            {
                text: '<strong>Combustible </br>Disponible </br>Fincimex</strong>', dataIndex: 'com_disp_fincimex', filter: 'string', width:100,
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                },
                field: {
                    xtype: 'numberfield'
                }
            },
            {
                text: '<strong>Combustible </br>Disponible </br>Cuenta 1</strong>', dataIndex: 'comb_disp_cuenta1', filter: 'string', width:100,
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                },
                field: {
                    xtype: 'numberfield'
                }
            },
            {
                text: '<strong>No Cliente</strong>', dataIndex: 'no_cliente', filter: 'string', width:100,
                field: {
                    xtype: 'textfield'
                }
            },
            {
                text: '<strong>Combustible </br>entregado al </br> constructor</strong>', dataIndex: 'comb_entr_constructor', filter: 'string',  width:100,
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                },
                field: {
                    xtype: 'numberfield'
                }
            },
            {
                text: '<strong>Consumo  </br> Electricidad</strong>', dataIndex: 'consumo_electricidad', filter: 'string',  width:100,
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                },
                field: {
                    xtype: 'numberfield'
                }
            },
        ],

    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'info_primaria_5073_panel_id',
        title: 'Información Primaria 5073',
        width: App.GetDesktopWidth(),
        //height: App.GetDesktopHeigth()-95,
        // border: true,
        // frame: true,
        layout: 'hbox',
        items: [panetree,
            {
                xtype:'panel',
                layout: 'hbox',
                id:'pane_id_5073',
                 region: 'center',
                // height: 500,
                // title:'sdfdfdf',
                //flex:1,
                tbar: {
                    id: 'pane_id_5073_tbar',
                    height: 36,
                },
                items:[
                    grid,{ xtype:'splitter'},grid_electricidad],
                bbar: {
                    id: 'info_5073_bbar',
                    height: 36,
                    items:[
                        {
                            xtype: 'button',
                            text: 'Enero',
                            id:'1',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(1, This);
                                }
                            }
                        }, {
                            xtype: 'button',
                            text: 'Febrero',
                            id:'2',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(2, This);
                                }
                            }
                        }, {
                            xtype: 'button',
                            text: 'Marzo',
                            id:'3',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(3, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Abril',
                            id:'4',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(4, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Mayo',
                            id:'5',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(5, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Junio',
                            id:'6',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(6, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Julio',
                            id:'7',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(7, This);
                                }
                            }
                        }, {
                            xtype: 'button',
                            text: 'Agosto',
                            id:'8',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(8, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Septiembre',
                            id:'9',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(9, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Octubre',
                            id:'10',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(10, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Noviembre',
                            id:'11',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(11, This);
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Diciembre',
                            id:'12',
                            listeners: {
                                click: function (This) {
                                    loadInfo5073(12, This);
                                }
                            }
                        }
                    ]

                },
            },
          ]
    });

    App.RenderMainPanel(_panel);
});

this.loadTree = function (pTree) {
    App.ShowWaitMsg();
    var result = App.PerformSyncServerRequest(Routing.generate('loadlocalUnidad'));
    App.HideWaitMsg();
    // console.log(result.tree)
    if (result.success) {

        //var root = Ext.getCmp('_func_grid_id').getRootNode();
        //root.removeAll(false);
        //root.appendChild(_result.tree);

        var root = pTree.getRootNode();
        console.log(result.tree)
        root.removeAll(true);
        root.appendChild(result.tree);
        pTree.expandNode(root.child());
        // var group1 = root.childNodes[0];
        // group1.set('checked', null);
        this.doChild(root.childNodes);


    }
}
function doChild(children) {
    // console.log(children)
    if (typeof children == 'undefined' || children === null || children.length <= 0)
        return;
    // Ext.each(myItems, function(eachItem){
    //     //Do whatever you want to do with eachItem
    // }, this);
    // children.each(function(child){
    //     child.set('checked',null);
    //     doChild(child);
    // });

    for (var i = 0; i < children.length; i++) {
        children[i].set('checked', null);
        doChild(children[i].childNodes)   // <= recursivity
    }
}

function loadInfo5073(pmes, btn) {
    mes = pmes;
    var _bbar = Ext.getCmp('info_5073_bbar');
    _bbar.items.each(function (element) {
        element.setStyle({
            background: '#F5F5F5'
        });
    });
    btn.setStyle({
        background: '#C1DDF1'
    });

    var obj={};
    obj.mes=mes;
    obj.nunidadid=  Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
    var resul = App.PerformSyncServerRequest(Routing.generate('loadInfoPrimaria5073'), obj);
    if(resul.total==0)
    {
        Ext.getCmp('grid_info_primaria_5073').getStore().loadData(resul.rows);
        Ext.getCmp('grid_info_primaria_5073').getView().refresh();
        App.InfoMessage('Informacion','No Existen datos para el mes seleccionado','warning');
    }else
    {
        Ext.getCmp('grid_info_primaria_5073').getStore().loadData(resul.rows);
        Ext.getCmp('grid_info_primaria_5073').getView().refresh();
    }

    var resul_ = App.PerformSyncServerRequest(Routing.generate('loadInfoPrimaria5073Elec'), obj);

    if(resul_.total==0)
    {
        Ext.getCmp('grid_info_primaria_5073_1').getStore().loadData(resul_.rows);
        Ext.getCmp('grid_info_primaria_5073_1').getView().refresh();
        App.InfoMessage('Informacion','No Existen datos para el mes seleccionado','warning');
    }else
    {
        Ext.getCmp('grid_info_primaria_5073_1').getStore().loadData(resul_.rows);
        Ext.getCmp('grid_info_primaria_5073_1').getView().refresh();
    }


}