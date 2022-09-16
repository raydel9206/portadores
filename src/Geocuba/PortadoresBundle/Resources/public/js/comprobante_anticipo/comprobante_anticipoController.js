Ext.onReady(function(){

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
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
        rootVisible: false,
        collapsible: true,
        collapsed: false,
        header: {style: {backgroundColor: 'white', borderBottom: '1px solid #c1c1c1 !important'}},
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
                grid_comp_anticipo.enable();
                grid_comp_anticipo.focus();
                panetree.collapse();
            },
            expand: function (This, record, tr, rowIndex, e, eOpts) {
                grid_comp_anticipo.focus();
            }
        }
    });

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

                let assignGridPromise = new Promise((resolve, reject) => {
                    let i = 0;
                    while(!Ext.getCmp('id_grid_comp_anticipo') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_comp_anticipo'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let monedaStore = Ext.create('Ext.data.JsonStore', {
        storeId: 'monedaStore',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    let store = Ext.create('Ext.data.JsonStore', {
        storeId: 'store',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'nro_vale'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/comp_anticipo/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        groupField: 'nro_vale',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                grid_comp_anticipo.getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    moneda: monedaCombo.getValue(),
                    mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                    anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                });
            }
        }
    });

    let monedaCombo = Ext.create('Ext.form.ComboBox', {
        id: 'moneda_combo',
        width: 155,
        store: monedaStore,
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        emptyText: 'Moneda...',
        listeners: {
            select: function (This, newValue, oldValue, eOpts) {
                grid_comp_anticipo.getStore().load();
            }
        }
    });

    let _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'modelo50723_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        handler: function (This, e) {
            // var summary = Ext.getCmp('grid_modelo5073').getView().normalView.features[0].summaryRecord.data;
            // var send = [];
            // Ext.Array.each(summary, function (valor) {
            //     send.push(valor);
            // });
            var obj ={};
            // obj.summary = Ext.encode(send);
            // obj.mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
            // obj.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            // obj.unidadid =Ext.getCmp('arbolunidades').getSelection()[0].data.id;
            // obj.siglas_unidad =Ext.getCmp('arbolunidades').getSelection()[0].data.siglas;
            // obj.accion = true;
            //
            App.request('POST', App.buildURL('/portadores/comp_anticipo/export'), obj, null, null,
                function (response) { // success_callback
                    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                }
            );

            // var mes = Ext.getCmp('mes_anno').getValue().getMonth()+1;
            // var anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            // window.open(App.buildURL("/portadores/modelo5073/export") + "?view_id=" + App.route + "&unidadid=" + Ext.getCmp('arbolunidades').getSelection()[0].data.id + "&anno=" + anno + "&mes=" + mes + "&accion=true");
        }
    });


    var grid_comp_anticipo = Ext.create('Ext.grid.Panel',{
        id: 'id_grid_comp_anticipo',
        disabled: true,
        region: 'center',
        store: store,
        features: [{
            groupHeaderTpl: 'Anticipo: {name} ' + ' ({rows.length})',
            ftype: 'groupingsummary'
        }, {
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: [
            { text: '<strong>Cuenta</strong>',
                dataIndex: 'cuenta',
                flex: 1
            },{ text: '<strong>Débito</strong>',
                dataIndex: 'debito',
                flex: 1,
                summaryType: 'sum',
                formatter: "number('0.00')",
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                },
            },{ text: '<strong>Crédito</strong>',
                dataIndex: 'credito',
                flex: 1,
                summaryType: 'sum',
                formatter: "number('0.00')",
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                },
            }
        ],
        tbar: {
            id: 'tbar_comp',
            height: 36,
            items: [mes_anno, '-',monedaCombo , '-',_btn_Export]
        },
    });
    var _panel_comp_anticipo = Ext.create('Ext.panel.Panel',{
        id: 'id_panel_comp_anticipo',
        title: 'Comprobante de Operaciones - Anticipos',
        frame : true,
        closable:true,
        layout: 'border',
        items:[panetree,grid_comp_anticipo]
    });
    App.render(_panel_comp_anticipo);
});