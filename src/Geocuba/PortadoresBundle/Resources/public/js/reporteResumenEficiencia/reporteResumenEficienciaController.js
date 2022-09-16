/**
 * Created by orlando on 03/02/2017.
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
                    while(!Ext.getCmp('id_grid_resumen_eficiencia') && i < 5){
                        setTimeout(() => { i++; }, 1000);
                    }
                    resolve(Ext.getCmp('id_grid_resumen_eficiencia'));
                });
                assignGridPromise.then((grid) => {
                    me.grid = grid;
                });
            }
        }
    });

    let _storec = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_Combustible_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true,
        listeners: {
            load: function (store,records) {
                store.insert(0,[{
                    id: 'null',
                    nombre: 'Todos'
                }])
            }
        }
    });

    let tipos_combustible = Ext.create('Ext.form.ComboBox', {
        store: _storec,
        width: 150,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'id_tipos_combustible',
        emptyText: 'Combustible...',
        listeners: {
            select: function (This, record, eOpts) {
                grid_resumen_eficiencia.getStore().load();
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

    var store_resumen_eficiencia = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_resumen_eficiencia',
        fields: [
            {name: 'marca'},
            {name: 'matricula'},
            {name: 'nro_orden'},

            {name: 'kms_salir'},
            {name: 'kms_llegar'},
            {name: 'kms_trabajado'},

            {name: 'comb_salir'},
            {name: 'comb_abastecido'},
            {name: 'comb_llegar'},
            {name: 'comb_consumido'},

            {name: 'norma_plan'},
            {name: 'norma_real'},

            {name: 'plan_motorrecursos'},
            {name: 'adicionales'},
            {name: 'existencia'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/resumen_eficiencia/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'nro_orden',
            direction: 'ASC'
        }],
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                if(Ext.getCmp('btn_prin_resumen_eficiencia'))
                    Ext.getCmp('btn_prin_resumen_eficiencia').setDisabled(true);
                if(Ext.getCmp('_btn_resumen_eficiencia_export'))
                    Ext.getCmp('_btn_resumen_eficiencia_export').setDisabled(true);
                Ext.getCmp('id_grid_resumen_eficiencia').getSelectionModel().deselectAll();
                operation.setParams({
                    unidadid:Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth()+1,
                    anno: mes_anno.getValue().getFullYear(),
                    acumulado: checkbox_acumulado.getValue(),
                    tipoCombustible: tipos_combustible.getValue()
                });
            },
            load: function (This, records, successful, eOpts) {
                if(Ext.getCmp('btn_prin_resumen_eficiencia'))
                    Ext.getCmp('btn_prin_resumen_eficiencia').setDisabled(records.length == 0);
                if(Ext.getCmp('_btn_resumen_eficiencia_export'))
                    Ext.getCmp('_btn_resumen_eficiencia_export').setDisabled(records.length == 0);

            }
        }
    });

    var checkbox_acumulado = Ext.create('Ext.form.field.Checkbox', {
        id: 'chech_acumulado_resumen_eficiencia',
        boxLabel: 'Acumulado',
        disabled: true,
        listeners: {
            change: function() {
                Ext.getCmp('id_grid_resumen_eficiencia').getStore().currentPage = 1;
                Ext.getCmp('id_grid_resumen_eficiencia').getStore().load();
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
                grid_resumen_eficiencia.enable();
                grid_resumen_eficiencia.getStore().load();
            }
        }
    });

    var grid_resumen_eficiencia = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_resumen_eficiencia',
        store: store_resumen_eficiencia,
        region: 'center',
        width: '75%',
        height: '100%',
        columnWidth:50,
        disabled: true,
        columns: [
            {text: '<strong></strong>', xtype: 'rownumberer', id: 'numero'},
            {text: '<strong>Marca</strong>', dataIndex: 'marca', filter: 'string', align: 'center', locked: true, width: 120},
            {text: '<strong>Matr&iacute;cula</strong>', dataIndex: 'matricula', filter: 'string',align: 'center', locked: true, width: 100},
            {
                text: 'De la salida',
                columns: [
                    {text: '<strong>Kms/Mth</strong>', dataIndex: 'kms_salir', align: 'center', formatter: "number('0')" },
                    {text: '<strong>Comb.</strong>', dataIndex: 'comb_salir', align: 'center', formatter: "number('0')"}
                ]
            },
            {
                text: 'Abastecidos',
                columns: [
                    {text: '<strong>Comb.</strong>', dataIndex: 'comb_abastecido', align: 'center', formatter: "number('0')"}
                ]
            },
            {
                text: 'De la llegada',
                columns: [
                    {text: '<strong>Kms/Mth</strong>', dataIndex: 'kms_llegar', align: 'center', formatter: "number('0')"},
                    {text: '<strong>Comb.</strong>', dataIndex: 'comb_llegar', align: 'center', formatter: "number('0')"}
                ]
            },
            {
                text: 'Eficiencia Real',
                columns: [
                    {text: '<strong>Kms/Mth Trab.</strong>', dataIndex: 'kms_trabajado', align: 'center', formatter: "number('0')"},
                    {text: '<strong>Comb. Consumido</strong>', dataIndex: 'comb_consumido', align: 'center', formatter: "number('0')"},
                    {text: '<strong>Norma Plan</strong>', dataIndex: 'norma_plan', align: 'center'},
                    {text: '<strong>Norma Real</strong>', dataIndex: 'norma_real', align: 'center',
                        renderer: function (val2, met, record, a, b, c, d) {
                            if (val2) {
                                return Ext.util.Format.number(val2, '0.00');
                            }
                            return '';
                        }
                    }
                ]
            },
            {
                text: 'Motorrecurso/MotoHoras',
                columns: [
                    {text: '<strong>Plan Mes</strong>', dataIndex: 'plan_motorrecursos', align: 'center'},
                    {text: '<strong>Adicionales</strong>', dataIndex: 'adicionales', align: 'center'},
                    {text: '<strong>Existencia</strong>', dataIndex: 'existencia', align: 'center'},
                ]
            }
        ],
        tbar: {
            id: 'resumen_eficiencia_tbar',
            height: 36,
            items: [mes_anno, '-',checkbox_acumulado, tipos_combustible]
        },
        plugins: 'gridfilters',
        listeners: {
            selectionchange: function (This, selected, e) {
                //Ext.getCmp('resumen_eficiencia_btn_mod').setDisabled(selected.length == 0);
                //Ext.getCmp('resumen_eficiencia_btn_del').setDisabled(selected.length == 0);
            }
        }
    });

    var panel_resumen_eficiencia = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_resumen_eficiencia',
        title: 'Resumen de la Eficiencia del Transporte',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_resumen_eficiencia]
    });

    App.render(panel_resumen_eficiencia);
});