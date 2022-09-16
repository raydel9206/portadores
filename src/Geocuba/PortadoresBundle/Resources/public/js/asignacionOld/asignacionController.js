/**
 * Created by kireny on 5/11/15.
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
                    while(!Ext.getCmp('id_grid_asignacion') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_asignacion'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let tipo_combustible = Ext.create('Ext.form.ComboBox', {
        id: 'nTipoCombustibleId',
        name: 'ntipo_combustibleid',
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
        typeAhead: true,
        queryMode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        selectOnFocus: true,
        editable: true,
        listeners: {
            select: function (This, newValue, oldValue, eOpts) {
                grid.getStore().load();
            }
        }
    });

    let btnClearSearch = Ext.create('Ext.button.MyButton', {
        width: 25,
        height: 25,
        tooltip: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            tipo_combustible.reset();
            mes_anno.reset();
            grid.getStore().load();
        }
    });

    var store = Ext.create('Ext.data.JsonStore', {
        storeId: 'storeAsignacionId',
        fields: [
            {name: 'id'},
            {name: 'denominacion'},
            {name: 'fecha'},
            {name: 'tipo_combustible'},
            {name: 'cantidad'},
            {name: 'modificable'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/asignacion/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        pageSize: 1000,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                grid.getSelectionModel().deselectAll();
                operation.setParams({
                    tipo_combustibleid: tipo_combustible.getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth()+1,
                    anno: mes_anno.getValue().getFullYear(),
                });
            }
        }

    });

    var store_disponible = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_disponible',
        fields: [
            {name: 'id'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible'},
            {name: 'disponible'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_disponible/loadDisponible'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
            load: function (){
                Ext.getCmp('planificacion_combustible_btn_mod').setDisabled(true);
                Ext.getCmp('asignacion_combustible_desglose_btn_back').setDisabled(true);
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
        // collapseDirection: 'left',
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
                grid.enable();
                grid.getStore().loadPage(1);
                grid.focus();
                grid_disponible.getStore().load();
                panetree.collapse();
                grid_disponible.setDisabled(false);
                grid_disponible.expand();
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid.focus();
                grid_disponible.collapse();
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_asignacion',
        region: 'center',
        width: '50%',
        disabled: true,
        store: store,
        columns: [
            {

                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                filter: 'string',
                flex: .5
            },
            // {
            //     text: '<strong>Denominación</strong>',
            //     dataIndex: 'denominacion',
            //     filter: 'string',
            //     flex: .8
            // },
            {
                text: '<strong>Tipo de Combustible</strong>',
                dataIndex: 'tipo_combustible',
                filter: 'string',
                flex: .8
            },
            {
                text: '<strong>Cantidad</strong>',
                dataIndex: 'cantidad',
                filter: 'string',
                flex: .5
            },
            {
                text: '<strong>Para Mes</strong>',
                dataIndex: 'paraMes',
                filter: 'string',
                flex: .5
            },


        ],
        tbar: {
            id: 'Area_tbar',
            height: 36,
            items: [mes_anno,tipo_combustible,btnClearSearch, '-']
        },
        // bbar: {
        //     xtype: 'pagingtoolbar',
        //     pageSize: 25,
        //     store: Ext.getStore('storeAsignacionId'),
        //     displayInfo: true,
        //     // plugins: new Ext.ux.ProgressBarPager()
        // },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                if(selected.length != 0){
                    if (Ext.getCmp('asignacion_btn_mod'))
                        Ext.getCmp('asignacion_btn_mod').setDisabled(!selected[0].data.modificable);
                    if (Ext.getCmp('asignacion_btn_del'))
                        Ext.getCmp('asignacion_btn_del').setDisabled(!selected[0].data.modificable);
                }
            },
        }
    });

    var grid_disponible = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_disponible',
        region:'east',
        width:'25%',
        collapsible: true,
        collapsed: true,
        columnLines : true,
        disabled: true,
        border: true,
        title: 'Plan Disponible FINCIMEX',
        store: store_disponible,
        columns: [
            {text: '<strong>Tipo Combustible</strong>', dataIndex: 'tipo_combustible',  flex: 1},
            {text: '<strong>Plan Disponible</strong>', dataIndex: 'disponible', flex: .7, editor: 'numberfield',},
        ],
        tbar: {
            id: 'cdt_tbar',
            height: 36,
            items:[
                {
                    id: 'planificacion_combustible_btn_mod',
                    text: 'Guardar',
                    iconCls: 'fas fa-save text-primary',
                    disabled: true,
                    handler: function (This, e) {
                        var unidadid = panetree.getSelectionModel().getLastSelected().data.id;
                        var store = grid_disponible.getStore();
                        var send = [];
                        Ext.Array.each(store.data.items, function (valor) {
                            send.push(valor.data);
                        });
                        var store_send = Ext.encode(send);
                        Ext.Msg.show({
                            title: '¿Guardar Cambios?',
                            message: '¿Está seguro que desea guardar los cambios realizados?.',
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.Msg.QUESTION,
                            fn: function (btn) {
                                if (btn === 'yes') {
                                    App.request('POST', App.buildURL('/portadores/plan_disponible/modDisponible'), {unidadid: unidadid, store: store_send}, null, null, function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            This.setStyle('borderColor', '#d8d8d8');
                                            This.disable();
                                            Ext.getCmp('asignacion_combustible_desglose_btn_back').setDisabled(true);
                                            grid_disponible.getStore().load();
                                            grid.getStore().load();
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                {
                    id: 'asignacion_combustible_desglose_btn_back',
                    text: 'Deshacer',
                    iconCls: 'fas fas fa-undo-alt text-primary',
                    disabled: true,
                    width: 100,
                    handler: function (This, e) {
                        This.setDisabled(true);
                        Ext.getCmp('planificacion_combustible_btn_mod').setDisabled(true);
                        grid_disponible.getStore().reload();
                    }
                },
                {
                    id: 'asignacion_combustible_desglose_btn_act',
                    text: 'Actualizar',
                    iconCls: 'fas fas fa-sync-alt text-primary',
                    disabled: true,
                    width: 100,
                    handler: function (This, e) {
                        grid_disponible.getStore().reload();
                    }
                }
            ]

        },
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 2,
            listeners: {
                // beforeedit: function (This, e, eOpts) {
                //     if(Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected().data.aprobada)
                //         return false;
                // },
                edit: function (This, e, eOpts) {
                    Ext.getCmp('planificacion_combustible_btn_mod').setDisabled(false);
                    Ext.getCmp('asignacion_combustible_desglose_btn_back').setDisabled(false);

                    var unidadid = panetree.getSelectionModel().getLastSelected().data.id;
                    var selection = grid_disponible.getSelectionModel().getLastSelected();
                }
            }
        },
        listeners: {
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid.focus();
                panetree.collapse();
                // store_disponible.load();

            }
        }


    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'manage_Area_panel_id',
        title: 'Asignaciones de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid, grid_disponible]
    });
    App.render(_panel);
});