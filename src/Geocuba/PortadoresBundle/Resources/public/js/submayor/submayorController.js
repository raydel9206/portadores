/**
 * Created by javier on 20/05/2016.
 */

Ext.onReady(function(){
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
                    while (!Ext.getCmp('id_grid_submayor') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_submayor'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
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

    var store_submayor = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_submayor',
        fields: [
            { name: 'id'},
            { name: 'noValeAnticipo'},
            { name: 'fechaVale'},
            { name: 'gfecha'},
            { name: 'matricula'},
            { name: 'ntarjeta'},
            { name: 'choferNombre'},
            { name: 'actividad'},
            { name: 'tipoCombustible'},
            { name: 'cantLitros', type:'float'},
            { name: 'centro_costo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/submayor/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        groupField:'gfecha',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_submayor').getSelectionModel().deselectAll();
                operation.setParams({
                    noValeAnticipo: textSearch.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear()
                });
            }
        }
    });

    var textSearch = Ext.create('Ext.form.field.Text',{
        width: 200,
        emptyText:'Vale a buscar...',
        id:'buscar_submayor',
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    Ext.getCmp('id_grid_submayor').getStore().currentPage = 1;
                    Ext.getCmp('id_grid_submayor').getStore().load();
                }
            }
        }
    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width : 25,
        height : 25,
        tooltip:'Buscar',
        iconCls: 'fas fa-search text-primary',
        handler: function(){
            Ext.getCmp('id_grid_submayor').getStore().currentPage = 1;
            Ext.getCmp('id_grid_submayor').getStore().load();
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton',{
        width : 25,
        height : 25,
        tooltip:'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function(){
            textSearch.reset();
            Ext.getCmp('id_grid_submayor').getStore().currentPage = 1;
            Ext.getCmp('id_grid_submayor').getStore().load();
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
                grid_submayor.enable();
                grid_submayor.getStore().load();
            }
        }


    });

    var grid_submayor = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_submayor',
        store: store_submayor,
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        columns: [
            { text: '<strong>Vale</strong>', dataIndex: 'noValeAnticipo', filter: 'string', flex: 1},
            { text: '<strong>Fecha</strong>', dataIndex: 'fechaVale', filter: 'string', width: 100},
            { text: '<strong>Chofer</strong>', dataIndex: 'choferNombre', filter: 'string', flex: 1, summaryType: function(){return '<strong>TOTAL</strong> '}},
            { text: '<strong>Chapa</strong>', dataIndex: 'matricula', filter: 'string', width: 80},
            { text: '<strong>Centro Costo</strong>', dataIndex: 'centro_costo', filter: 'string', flex: 1},
            { text: '<strong>Dep&oacute;sito</strong>', dataIndex: 'ntarjeta', filter: 'string', flex: 1},
            { text: '<strong>Tipo de Combustible</strong>', dataIndex: 'tipoCombustible', filter: 'string', flex: 1},
            {
                text: '<strong>Cantidad</strong>',
                dataIndex: 'cantLitros',
                filter: 'string',
                width: 80,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.Number.toFixed(value,2));
                }
            },
            { text:'<strong>Observaciones</strong>', dataIndex:'actividad', filter:'string', flex:1,
                renderer:function (val) {
                    if (val == 'Cancelado' || val=='Cancelado/Tránsito') {
                        return '<strong style="color:' + "red" + ';">' + val + '</strong>';
                    }
                    return val;
                }
            }
        ],

        features: [{
            ftype: 'groupingsummary',
            groupHeaderTpl: '<b>{name}</b> ({rows.length} Anticipo{[values.rows.length > 1 ? "s" : ""]})',
            hideGroupedHeader: true,
            startCollapsed: true,
            id: 'restaurantGrouping'
        }],

        tbar: {
            id: 'submayor_tbar',
            height: 36,
            items: [ mes_anno, textSearch, btnSearch, btnClearSearch]
        },
        plugins: 'gridfilters',
        listeners: {
            headerclick:function(This,columnIndex){
                store_submayor.setGroupField(columnIndex.dataIndex);
            },
            selectionchange: function(This, selected, e){
            }
        }
    });



    var panel_submayor = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_submayor',
        title: 'Submayor de Vales de Anticipo y Liquidación de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items:[panetree, grid_submayor]
    });


    App.render(panel_submayor);



});