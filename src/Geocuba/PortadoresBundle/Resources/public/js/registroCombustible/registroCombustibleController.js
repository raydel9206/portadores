/**
 * Created by pfcadenas on 08/2016.
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
                    while(!Ext.getCmp('id_grid_registro_combustible') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_registro_combustible'));
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

    var _store_vehiculos = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_vehiculo',
        fields: [
            { name: 'id'},
            { name: 'nmarca_vehiculoid'},
            { name: 'nestado_tecnicoid'},
            { name: 'ndenominacion_vehiculoid'},
            { name: 'nunidadid'},
            { name: 'nchoferid'},
            { name: 'ntipo_combustibleid'},
            { name: 'matricula'},
            { name: 'norma'},
            { name: 'nro_inventario'},
            { name: 'nro_serie_carreceria'},
            { name: 'nro_serie_motor'},
            { name: 'color'},
            { name: 'nroOrden'},
            { name: 'nro_circulacion'},
            { name: 'fecha_expiracion_circulacion'},
            { name: 'anno_fabricacion'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        sorters: [{
            property: 'nroOrden',
            direction: 'ASC'
        }],
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_registro_combustible').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                });
            }
        }

    });

    var _store_planificacion = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_planificacion',
        fields: [
            { name: 'id'},
            { name: 'fecha_planificacion'},
            { name: 'monedaid'},
            { name: 'monedanombre'},
            { name: 'recibido'},
            //{ name: 'saldo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/registro_combustible/loadPlanificacion'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false
    });

    var _store_analisis = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_analisis',
        fields: [
            { name: 'id'},
            { name: 'semana'},
            { name: 'numerosemana', type:'int'},
            { name: 'conceptoid'},
            { name: 'conceptonombre'},
            { name: 'combustible'},
            { name: 'lubricante'},
            { name: 'km'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/registro_combustible/loadAnalisis'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'numerosemana',
            direction: 'ASC'
        },{
            property: 'conceptoid',
            direction: 'ASC'
        }],
        groupField: 'numerosemana',
        autoLoad: false
    });

    var store_registro_combustible = Ext.create('Ext.data.JsonStore',{
        storeId: 'id_store_registro_combustible',
        fields: [
            { name: 'id'},
            { name: 'fecha_registro'},
            { name: 'fecha_planif'},
            { name: 'vehiculoid'},
            { name: 'vehiculonorma'},
            { name: 'vehiculochapa'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/registro_combustible/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_registro_combustible').getSelectionModel().deselectAll();
                operation.setParams({
                    vehiculo: textSearch.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                });
            }
        }
    });

    var textSearch = Ext.create('Ext.form.field.ComboBox',{
        width: 120,
        emptyText:'Matrícula...',
        id:'buscar_registro_combustible',
        store: _store_vehiculos,
        name: 'buscar_registro_combustible',
        displayField: 'matricula',
        valueField: 'id',
        queryMode: 'local',
        forceSelection: true,
        enableKeyEvents: true,
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode === 13) {
                    Ext.getCmp('id_grid_registro_combustible').getStore().currentPage = 1;
                    Ext.getCmp('id_grid_registro_combustible').getStore().load();
                }
            }
        }
    });
    var btnSearch = Ext.create('Ext.button.MyButton',{
        width : 25,
        height : 25,
        tooltip:'Buscar',
        iconCls: 'fa fa-search text-primary',
        handler: function(){
            Ext.getCmp('id_grid_registro_combustible').getStore().currentPage = 1;
            Ext.getCmp('id_grid_registro_combustible').getStore().load()
        }
    });
    var btnClearSearch = Ext.create('Ext.button.MyButton',{
        width : 25,
        height : 25,
        tooltip:'Limpiar',
        iconCls: 'fa fa-eraser text-primary',
        handler: function(){
            textSearch.reset();
            Ext.getCmp('id_grid_registro_combustible').getStore().currentPage = 1;
            Ext.getCmp('id_grid_registro_combustible').getStore().load();
        }
    });

    var grid_registro_combustible = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_registro_combustible',
        store: store_registro_combustible,
        region:'center',
        width: '60%',
        disabled: true,
        columns: [


            { text: '<strong>Matrícula del vehículo</strong>',
                dataIndex: 'vehiculochapa',
                filter: 'string',
                flex: 2
            }
        ],
        tbar: {
            id: 'registro_combustible_tbar',
            height: 36,
            items: [ mes_anno,textSearch, btnSearch, btnClearSearch, '-' ]
        },
        // bbar: {
        //     xtype: 'pagingtoolbar',
        //     //pageSize: 25,
        //     store: Ext.getStore('id_store_registro_combustible'),
        //     displayInfo: true,
        //     plugins: new Ext.ux.ProgressBarPager()
        // },
        plugins: ['gridfilters'],
        features: [
            {
                ftype:'grouping',
                startCollapsed:true,
                groupHeaderTpl: [
                    '<div>{name:this.formatName}</div>',
                    {
                        formatName: function(name) {
                            return Ext.String.trim(name);
                        }
                    }
                ]
            }
        ],
        listeners: {
            'selectionchange': {
                fn: function(View, selections, options) {

                    if (Ext.getCmp('registro_combustible_btn_mod') !== undefined)
                        Ext.getCmp('registro_combustible_btn_mod').setDisabled(selections.length === 0);
                    if (Ext.getCmp('registro_combustible_btn_del') !== undefined)
                        Ext.getCmp('registro_combustible_btn_del').setDisabled(selections.length === 0);
                    if (Ext.getCmp('registro_combustible_btn_print') !== undefined)
                        Ext.getCmp('registro_combustible_btn_print').setDisabled(selections.length === 0);

                    if (Ext.getCmp('planificacion_btn_add') !== undefined)
                        Ext.getCmp('planificacion_btn_add').setDisabled(selections.length === 0);
                    if (Ext.getCmp('planificacion_btn_mod') !== undefined)
                        Ext.getCmp('planificacion_btn_mod').setDisabled(selections.length === 0);
                    if (Ext.getCmp('planificacion_btn_del') !== undefined)
                        Ext.getCmp('planificacion_btn_del').setDisabled(selections.length === 0);

                    if (Ext.getCmp('analisis_btn_add') !== undefined)
                        Ext.getCmp('analisis_btn_add').setDisabled(selections.length === 0);

                    if (selections.length === 0) {
                        _panel_east.collapse();
                        grid_planificacion.getStore().removeAll();
                        grid_analisis.getStore().removeAll();
                    }
                    else {
                        _panel_east.expand();
                        grid_planificacion.getStore().load(
                            {
                                params:{id:selections[0].data.id}
                            }
                        );
                        grid_analisis.getStore().load(
                            {
                                params:{id:selections[0].data.id}
                            }
                        );
                    }
                },
                scope: this
            }
        }
    });

    var grid_planificacion = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_planificacion',
        store: _store_planificacion,
        region:'north',
        height : (App.container.getHeight() - 140)/2,
        columns: [
            { text: '<strong>Fecha</strong>',
                dataIndex: 'fecha_planificacion',
                filter: 'string',
                flex: 2
            },
            { text: '<strong>Recibido</strong>',
                dataIndex: 'recibido',
                filter: 'string',
                flex: 2
            },
            //{ text: '<strong>Saldo</strong>',
            //    dataIndex: 'saldo',
            //    filter: 'string',
            //    flex: 2
            //},
            { text: '<strong>Moneda</strong>',
                dataIndex: 'monedanombre',
                filter: 'string',
                flex: 2
            }
        ],
        tbar: {
            id: 'planificacion_tbar',
            height: 36,
            items: [  ]
        },
        plugins: ['gridfilters'],
        listeners: {
            'selectionchange': {
                fn: function(View, selections, options) {
                    if(selections.length === 0){
                        DisablePlanificacion();
                    }
                    else
                    {
                        EnablePlanificacion();
                    }
                },
                scope: this
            }
        }
    });

    var grid_analisis = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_analisis',
        store: _store_analisis,
        region:'center',
        height : (App.container.getHeight() - 140)/2,
        columns: [
            { text: '<strong>Concepto</strong>',
                dataIndex: 'conceptonombre',
                filter: 'string',
                flex: 2
            },
            //{ text: '<strong>Semana</strong>',
            //    dataIndex: 'semana',
            //    filter: 'string',
            //    flex: 2
            //},
            { text: '<strong>Comb.</strong>',
                dataIndex: 'combustible',
                filter: 'string',
                flex: 2
            },
            { text: '<strong>Lub.</strong>',
                dataIndex: 'lubricante',
                filter: 'string',
                flex: 2
            },
            { text: '<strong>Km(Mh).</strong>',
                dataIndex: 'km',
                filter: 'string',
                flex: 2
            }
        ],
        tbar: {
            id: 'analisis_tbar',
            height: 36,
            items: [  ]
        },
        plugins: ['gridfilters'],
        features: [
            {
                ftype:'grouping',
                startCollapsed:true,
                groupHeaderTpl: [
                    '<div>Semana: {rows:this.formatNameO}</div>',
                    {
                        formatNameO: function(name, children) {
                            return name[0].data.numerosemana + ' del ' + name[0].data.semana  ;
                        }
                    }
                ]
            }
        ],
        listeners: {
            'selectionchange': {
                fn: function(View, selections, options) {

                },
                scope: this
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
                grid_registro_combustible.enable();
                grid_registro_combustible.getStore().loadPage(1);
                _store_vehiculos.load();
                // if(Ext.getStore('id_store_persona_chofer'))
                //     Ext.getStore('id_store_persona_chofer').load();
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                _panel_east.collapse();
            }
        }
    });

    EnablePlanificacion = function(){
        Ext.getCmp('planificacion_btn_mod').enable();
        Ext.getCmp('planificacion_btn_del').enable();
    };

    DisablePlanificacion = function(){
        Ext.getCmp('planificacion_btn_mod').disable();
        Ext.getCmp('planificacion_btn_del').disable();
    };

    var _panel_east = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_east',
        title: 'Análisis del consumo',
        region: 'east',
        width: '40%',
        collapsible:true,
        collapsed:true,
        height : App.container.getHeight() - 75,
        border : true,
        layout: 'border',
        items:[grid_analisis]
    });

    var _panel_registro_combustible = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_registro_combustible',
        title: 'Registro de combustible y lubricante por fuente de abastecimiento',
        // width : App.container.getHeight(),
        // height : App.GetDesktopHeigth() - 75,
        frame : true,
        layout: 'border',
        padding: '2 0 0',
        items:[panetree, grid_registro_combustible,_panel_east]
    });
    App.render(_panel_registro_combustible);
});