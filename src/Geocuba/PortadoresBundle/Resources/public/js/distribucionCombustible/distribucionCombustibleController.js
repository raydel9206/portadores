/**
 * Created by pfcadenas on 11/11/2016.
 */

Ext.onReady(function () {
    let disposicion = 0;

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
                    while (!Ext.getCmp('id_grid_distribucion') && i < 5) {
                        setTimeout(() => {
                            i++;
                        }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_distribucion'));
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

    var panetree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: tree_store,
        region: 'west',
        width: 280,
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
                grid_distribucion.enable();
                grid_distribucion.getSelectionModel().deselectAll();
                grid_distribucion.getStore().loadPage(1);
                if (Ext.getStore('id_store_vehiculo'))
                    Ext.getStore('id_store_vehiculo').load();
            }
        }
    });

    var store_distribucion_combustible_desglose = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_distribucion_combustible_desglose',
        fields: [
            {name: 'id'},
            {name: 'vehiculoid'},
            {name: 'vehiculo'},
            {name: 'vehiculo_denominacion'},
            {name: 'vehiculo_marca'},
            {name: 'vehiculo_norma'},
            {name: 'personaid'},
            {name: 'monto_asignado'},
            {name: 'cantidad', type: 'float'},
            {name: 'monto', type: 'float'},
            {name: 'persona'},
            {name: 'cambustible_asignado', type: 'float'},
            {name: 'preciocombustible'},
            {name: 'monto_asignado', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/distribucion/loadDesglose'),
            reader: {
                rootProperty: 'rows'
            }
        },
        groupField: 'vehiculo_denominacion',
        autoLoad: false,
        pageSize: 1000,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                grid_desglose.getSelectionModel().deselectAll();
                var seleccion = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                if (seleccion !== null) {
                    operation.setParams({
                        distribucion: seleccion.data.id,
                        tipoCombustible: seleccion.data.tipo_combustible_id,
                        unidadid: seleccion.data.unidadid
                    });
                }
            },
            load: function (This, records, successful, eOpts) {
                var seleccion = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();

                // if(!seleccion.data.aprobada){
                var datos = grid_desglose.getStore().data.items;
                var suma = 0;

                for (var i = 0; i < datos.length; i++) {
                    suma += datos[i].data.cambustible_asignado;
                }
                Ext.getCmp('disposicion').setValue(Ext.util.Format.round(disposicion - suma, 0));
                var selection_dist = grid_distribucion.getSelectionModel().getLastSelected();
                selection_dist.data.cantidad = suma;
                grid_distribucion.getView().refresh();
                // }else{
                //     Ext.getCmp('disposicion').setValue('-----');
                // }
            }
        }
    });

    var grid_distribucion = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_distribucion',
        region: 'center',
        width: '28%',
        padding: '0 1',
        disabled: true,
        store: Ext.create('Ext.data.JsonStore', {
            fields: [
                {name: 'id'},
                {name: 'denominacion'},
                {name: 'fecha'},
                {name: 'tipo_combustible_id'},
                {name: 'tipo_combustible'},
                {name: 'cantidad'},
                {name: 'aprobada'},
                {name: 'cheque'},
                {name: 'unidadid'}
            ],
            groupField: 'tipo_combustible',
            sorters: 'tipo_combustible',
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/distribucion/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            pageSize: 1000,
            autoLoad: false,
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    grid_distribucion.getSelectionModel().deselectAll();
                    operation.setParams({
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        mes: mes_anno.getValue().getMonth() + 1,
                        anno: mes_anno.getValue().getFullYear(),
                    });
                }
            }
        }),
        features: [{
            ftype: 'grouping',
            groupHeaderTpl: '<strong>{name}</strong>',
            hideGroupedHeader: true,
            startCollapsed: false
        }],
        columns: [
            {
                text: '<strong>Denominación</strong>',
                dataIndex: 'denominacion',
                flex: .8,
                align: 'center'
            },
            {
                text: '<strong>Fecha</strong>',
                dataIndex: 'fecha',
                flex: .5,
                align: 'center'
            },
            {
                text: '<strong>Cant(L)</strong>',
                dataIndex: 'cantidad',
                flex: .4,
                align: 'center'
            },
            {
                text: '<strong>Aprob.</strong>',
                dataIndex: 'aprobada',
                flex: .4,
                align: 'center',
                renderer: function (value) {
                    if (value)
                        return "<div class='badge-false'>Si</div>";
                    else
                        return "<div class='badge-true'>No</div>";
                }
            }
        ],
        tbar: {
            id: 'distribucion_combustible_tbar',
            height: 36,
            items: [mes_anno/*,tipo_combustible*/]
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('distribucion_combustible_btn_add'))
                    Ext.getCmp('distribucion_combustible_btn_add').setDisabled(false);

                if (Ext.getCmp('distribucion_combustible_btn_print'))
                    Ext.getCmp('distribucion_combustible_btn_print').setDisabled(selected.length === 0);

                if (selected.length > 0) {
                    if (Ext.getCmp('distribucion_combustible_btn_mod'))
                        Ext.getCmp('distribucion_combustible_btn_mod').setDisabled(selected[0].data.aprobada);
                    if (Ext.getCmp('distribucion_combustible_btn_del'))
                        Ext.getCmp('distribucion_combustible_btn_del').setDisabled(selected[0].data.aprobada);
                    if (Ext.getCmp('distribucion_combustible_btn_aprobar'))
                        Ext.getCmp('distribucion_combustible_btn_aprobar').setDisabled(selected[0].data.aprobada);
                    if (Ext.getCmp('distribucion_combustible_desglose_btn_des'))
                        Ext.getCmp('distribucion_combustible_desglose_btn_des').setDisabled(selected[0].data.aprobada);

                    if (selected[0].data.aprobada) {
                        if (Ext.getCmp('distribucion_combustible_btn_desaprobar'))
                            Ext.getCmp('distribucion_combustible_btn_desaprobar').setDisabled(selected[0].data.cheque);
                    }
                    else {
                        if (Ext.getCmp('distribucion_combustible_btn_desaprobar'))
                            Ext.getCmp('distribucion_combustible_btn_desaprobar').setDisabled(true);
                    }
                } else {
                    if (Ext.getCmp('distribucion_combustible_btn_mod'))
                        Ext.getCmp('distribucion_combustible_btn_mod').setDisabled(true);
                    if (Ext.getCmp('distribucion_combustible_btn_del'))
                        Ext.getCmp('distribucion_combustible_btn_del').setDisabled(true);
                    if (Ext.getCmp('distribucion_combustible_btn_aprobar'))
                        Ext.getCmp('distribucion_combustible_btn_aprobar').setDisabled(true);
                    if (Ext.getCmp('distribucion_combustible_desglose_btn_des'))
                        Ext.getCmp('distribucion_combustible_desglose_btn_des').setDisabled(true);
                    if (Ext.getCmp('distribucion_combustible_btn_desaprobar'))
                        Ext.getCmp('distribucion_combustible_btn_desaprobar').setDisabled(true);
                }

                grid_desglose.setDisabled(selected.length === 0);
                if (selected.length > 0) {
                    var obj = {};
                    obj.tipo_combustibleid = selected[0].data.tipo_combustible_id;
                    obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;



                    App.request('GET', App.buildURL('/portadores/plan_disponible/loadDisponibleTC'), obj, null, null,
                        function (response) {
                            disposicion = response.disponible;
                        }, null, null, true, false
                    );
                    grid_desglose.getStore().load();
                }
            }
        }
    });

    var grid_desglose = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_desglose',
        title: 'Desglose de Combustible',
        width: '47%',
        region: 'east',
        border: true,
        disabled: true,
        store: store_distribucion_combustible_desglose,
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        },
            {
                ftype: 'grouping',
                groupHeaderTpl: [
                    '<b>{name:this.formatName}</b>',
                    {
                        formatName: function (name) {
                            return Ext.String.trim(name);
                        }
                    }
                ]
            }],
        columnLines: true,
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 2,
            listeners: {
                beforeedit: function (This, e, eOpts) {
                    if (Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected().data.aprobada)
                        return false;
                },
                edit: function (This, e, eOpts) {
                    var selection_dist = Ext.getCmp('id_grid_distribucion').getSelectionModel().getLastSelected();
                    var selection = grid_desglose.getSelectionModel().getLastSelected();

                    if (selection_dist.data.aprobada) {
                        selection.data.combustible_asignado = selection.data.inicial;
                        grid_desglose.getView().refresh();
                        return;
                    }

                    if (store_distribucion_combustible_desglose.isFiltered()) {
                        store_distribucion_combustible_desglose.clearFilter();
                    }

                    var inicial = selection.data.inicial;
                    var datos = grid_desglose.getStore().data.items;
                    var suma = 0;
                    for (var i = 0; i < datos.length; i++) {
                        suma += datos[i].data.cambustible_asignado;
                    }

                    if (e.originalValue === e.value) {
                        store_distribucion_combustible_desglose.filterBy(function (record) {
                            return record.data.vehiculo.search(Ext.getCmp('find_button_vehiculo').getValue()) !== -1;
                        }, this);
                        return false;
                    }

                    if (Ext.getCmp('distribucion_combustible_desglose_btn_des')) {
                        if (suma > 0)
                            Ext.getCmp('distribucion_combustible_desglose_btn_des').setDisabled(false);
                    }

                    if (Ext.getCmp('distribucion_combustible_desglose_btn_act'))
                        Ext.getCmp('distribucion_combustible_desglose_btn_act').setDisabled(selection_dist.data.aprobada);
                    if (Ext.getCmp('distribucion_combustible_desglose_btn_back'))
                        Ext.getCmp('distribucion_combustible_desglose_btn_back').setDisabled(selection_dist.data.aprobada);
                    if (!selection_dist.data.aprobada)
                        Ext.getCmp('distribucion_combustible_desglose_btn_act').setStyle('borderColor', 'red');

                    var disponible = disposicion - suma;
                    e.record.data['kms'] = Ext.util.Format.round(e.record.data['cambustible_asignado'] * e.record.data['vehiculo_norma'], 2);
                    cantidad = inicial - e.record.data['cambustible_asignado'];

                    if (cantidad < 0) {
                        cant = cantidad * (-1);
                        e.record.data['incremento'] = cant;
                        e.record.data['reduccion'] = 0;
                    } else {
                        e.record.data['reduccion'] = cantidad;
                        e.record.data['incremento'] = 0;
                    }

                    Ext.getCmp('disposicion').setValue(disponible);
                    selection_dist.data.cantidad = suma;
                    grid_desglose.getView().refresh();
                    grid_distribucion.getView().refresh();
                    store_distribucion_combustible_desglose.filterBy(function (record) {
                        return record.data.vehiculo.search(Ext.getCmp('find_button_vehiculo').getValue()) !== -1;
                    }, this);
                    Calcular_Desglose(grid_desglose.getStore().data.items)
                }
            }
        },

        columns: [
            {
                dataIndex: 'vehiculo',
                width: 80,
                text: '<b>Matrícula</b>',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('paralizado') === false) {
                        met.style = 'font-style:italic !important;font-weight: bold;color: red;';
                    }
                    return val2;
                }
            },
            // {
            //     dataIndex: 'persona',
            //     width: 170,
            //     text: '<b>Persona Autorizada</b>'
            // },123123123
            {
                dataIndex: 'cambustible_asignado',
                width: 90,
                formatter: "number('0.00')",
                align: 'right',
                name: 'cambustible_asignado',
                text: '<b>Asign(L)</b>',
                editor: 'numberfield',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            }, {
                dataIndex: 'monto_asignado',
                width: 90,
                formatter: "number('0.00')",
                align: 'right',
                name: 'monto_asignado',
                text: '<b>Asign($)</b>',
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            }, {
                dataIndex: 'vehiculo_norma',
                width: 65,
                formatter: "number('0.00')",
                align: 'right',
                name: 'vehiculo_norma',
                text: '<b>Km/L</b>'
            }, {
                dataIndex: 'kms',
                width: 80,
                formatter: "number('0.00')",
                align: 'right',
                name: 'kms',
                text: '<b>Kms</b>'
            },
            // {
            //     dataIndex: 'cantidad',
            //     width: 85,
            //     formatter: "number('0.00')",
            //     align: 'right',
            //     name: 'cantidad',
            //     text: '<b>Carga(L)</b>',
            //     summaryType: 'sum',
            //     summaryRenderer: function (value) {
            //         return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
            //     }
            // },
            // {
            //     dataIndex: 'monto',
            //     width: 85,
            //     formatter: "number('0.00')",
            //     align: 'right',
            //     name: 'monto',
            //     text: '<b>Carga($)</b>',
            //     summaryType: 'sum',
            //     summaryRenderer: function (value) {
            //         return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
            //     }
            // },
            {
                dataIndex: 'incremento',
                width: 115,
                formatter: "number('0.00')",
                align: 'right',
                name: 'incremento',
                text: '<b>Incremento(L)</b>'
            },
            {
                dataIndex: 'reduccion',
                width: 110,
                formatter: "number('0.00')",
                align: 'right',
                name: 'reduccion',
                text: '<b>Reducci&oacute;n(L)</b>'
            }
        ],
        tbar: {
            id: 'grid_desglose_tbar',
            height: 36,
            items: [Ext.create('Ext.form.field.Text', {
                id: 'find_button_vehiculo',
                emptyText: 'Matrícula...',
                width: 90,
                enableKeyEvents: true,
                listeners: {
                    keyup: function (This, e, eOpts) {
                        store_distribucion_combustible_desglose.filterBy(function (record) {
                            return record.data.vehiculo.search(This.value) !== -1;
                        }, this);
                    },
                    change: function (field, newValue, oldValue, eOpt) {
                        field.getTrigger('clear').setVisible(newValue);
                    },
                },
                triggers: {
                    clear: {
                        cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                        hidden: true,
                        handler: function () {
                            this.setValue(null);
                            this.updateLayout();

                            store_distribucion_combustible_desglose.clearFilter();
                            this.setMarked(false);
                        }
                    }
                },

                setMarked: function (marked) {
                    var el = this.getEl(),
                        id = '#' + this.getId();

                    this.marked = marked;

                    if (marked) {
                        el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                        el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                        el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
                    } else {
                        el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                        el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                        el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
                    }
                }
            }), '-']
        },
        listeners: {
            selectionchange: function (This, selected, e) {
                if (Ext.getCmp('distribucion_combustible_desglose_btn_mod') !== undefined)
                    Ext.getCmp('distribucion_combustible_desglose_btn_mod').setDisabled(selected.length === 0);
            }
        }
    });

    var _panel = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_distribucion_combustible',
        title: 'Distribución de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '1 0 0',
        items: [panetree, grid_distribucion, grid_desglose],
        // tbar: {
        //     id: 'distribucion_combustible_tbar',
        //     height: 36,
        //     items: []
        // }
    });
    App.render(_panel);
});

Calcular_Desglose = function (grid) {
//Here I do the sum between columns//
    for (var i = 0; i < grid.length; i++) {
        grid[i].data.kms = App.round(grid[i].data.cambustible_asignado * grid[i].data.vehiculo_norma, 2);
        grid[i].data.monto_asignado = App.round(grid[i].data.cambustible_asignado * grid[i].data.preciocombustible, 2);
    }
    Ext.getCmp('id_grid_desglose').getView().refresh();
};


