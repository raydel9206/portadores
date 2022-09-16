/**
 * Created by yosley on 06/01/2016.
 */
Ext.onReady(function () {

    var anno = Ext.create('Ext.form.NumberField', {
        id: 'fieldAnnoId',
        fieldLabel: 'Año',
        labelWidth: 30,
        width: 100,
        value: App.selected_year,
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                if (Ext.getCmp('select_portadorid').getValue() !== null)
                    grid_cda.getStore().load();
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
        autoLoad: true,
        listeners: {
            load: function (This, records, successful, eOpts) {
                Ext.getCmp('moneda_combo').setValue(This.getData().items[0]);
            }
        }
    });

    let monedaCombo = Ext.create('Ext.form.ComboBox', {
        id: 'moneda_combo',
        width: 100,
        store: monedaStore,
        displayField: 'nombre',
        valueField: 'id',
        queryMode: 'local',
        emptyText: 'Moneda...',
        listeners: {
            change: function (This, newValue, oldValue, eOpts) {
                if (Ext.getCmp('select_portadorid').getValue() !== null)
                    grid_cda.getStore().load();
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
        border: true,
        id: 'arbolunidades',
        hideHeaders: true,
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
                if (record) {
                    Ext.getCmp('id_grid_cda001').setDisabled(false);
                    if (Ext.getCmp('select_portadorid').getValue()) {
                        Ext.getCmp('id_grid_cda001').getStore().load();
                    }
                }
            }
        }


    });

    Ext.define('Cda001', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id'},
            {name: 'actividadid'},
            {name: 'actividad_nombre'},
            {name: 'codigo_mep_act', type: 'number'},
            {name: 'codigo_gae_act', type: 'number'},

            {name: 'portadorid'},
            {name: 'unidadid'},

            {name: 'real_nivel_act', type: 'number'},
            {name: 'real_consumo', type: 'number'},
            {name: 'real_indice', type: 'number'},

            {name: 'acumulado_nivel_act', type: 'number'},
            {name: 'acumulado_consumo', type: 'number'},
            {name: 'acumulado_indice', type: 'number'},

            {name: 'estimado_nivel_act', type: 'number'},
            {name: 'estimado_consumo', type: 'number'},
            {name: 'estimado_indice', type: 'number'},

            {name: 'propuesta_plan_nivel_act', type: 'number'},
            {name: 'propuesta_plan_consumo', type: 'number'},
            {name: 'propuesta_plan_indice', type: 'number'},

            {name: 'plan_final_nivel_act', type: 'number'},
            {name: 'plan_final_consumo', type: 'number'},
            {name: 'plan_final_indice', type: 'number'},

            {name: 'total_desglose_nivel_act', type: 'number'},
            {name: 'total_desglose_consumo', type: 'number'},
            {name: 'total_desglose_indice', type: 'number'},

            {name: 'enero_nivel_act', type: 'number'},
            {name: 'enero_consumo', type: 'number'},
            {name: 'enero_indice', type: 'number'},

            {name: 'febrero_nivel_act', type: 'number'},
            {name: 'febrero_consumo', type: 'number'},
            {name: 'febrero_indice', type: 'number'},

            {name: 'marzo_nivel_act', type: 'number'},
            {name: 'marzo_consumo', type: 'number'},
            {name: 'marzo_indice', type: 'number'},

            {name: 'abril_nivel_act', type: 'number'},
            {name: 'abril_consumo', type: 'number'},
            {name: 'abril_indice', type: 'number'},

            {name: 'mayo_nivel_act', type: 'number'},
            {name: 'mayo_consumo', type: 'number'},
            {name: 'mayo_indice', type: 'number'},

            {name: 'junio_nivel_act', type: 'number'},
            {name: 'junio_consumo', type: 'number'},
            {name: 'junio_indice', type: 'number'},

            {name: 'julio_nivel_act', type: 'number'},
            {name: 'julio_consumo', type: 'number'},
            {name: 'julio_indice', type: 'number'},

            {name: 'agosto_nivel_act', type: 'number'},
            {name: 'agosto_consumo', type: 'number'},
            {name: 'agosto_indice', type: 'number'},

            {name: 'septiembre_nivel_act', type: 'number'},
            {name: 'septiembre_consumo', type: 'number'},
            {name: 'septiembre_indice', type: 'number'},

            {name: 'octubre_nivel_act', type: 'number'},
            {name: 'octubre_consumo', type: 'number'},
            {name: 'octubre_indice', type: 'number'},

            {name: 'noviembre_nivel_act', type: 'number'},
            {name: 'noviembre_consumo', type: 'number'},
            {name: 'noviembre_indice', type: 'number'},

            {name: 'diciembre_nivel_act', type: 'number'},
            {name: 'diciembre_consumo', type: 'number'},
            {name: 'diciembre_indice', type: 'number'}
        ]
    });

    var store_actividad = Ext.create('Ext.data.Store', {
        storeId: 'actividadStoreid',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'codigomep'},
            {name: 'codigogae'},
            {name: 'administrativa'},
            {name: 'um_actividad'},
            {name: 'um_actividad_nombre'},
            {name: 'inversion'},
            {name: 'trafico'},
            {name: 'id_portador'},
            {name: 'portadornombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/actividad/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 10000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    portadorid: Ext.getCmp('select_portadorid').getValue(),
                    portadorName: Ext.getCmp('select_portadorid').getRawValue()
                });
            }
        }
    });

    var _storeportadores = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_portadores',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/portador/load'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true,

    });

    var selec_portador = Ext.create('Ext.form.ComboBox', {
        fieldLabel: 'Portador',
        labelAlign: 'left',
        width: 190,
        labelWidth: 50,
        // labelPad: 10,
        // readOnly: true,
        store: _storeportadores,
        queryMode: 'local',
        displayField: 'nombre',
        valueField: 'id',
        id: 'select_portadorid',
        listeners: {
            select: function (combo, record, eOpts) {
                if (Ext.getCmp('id_menu_button'))
                    Ext.getCmp('id_grid_cda001').getStore().load();
                store_actividad.load();
            }
        }
    });

    var grid_cda = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_cda001',
        region: 'center',
        disabled: true,
        columnWidth: '50',
        columnLines: true,
        viewConfig: {
            stripeRows: true
        },
        store: Ext.create('Ext.data.Store', {
            model: 'Cda001',
            proxy: {
                type: 'ajax',
                url: App.buildURL('/portadores/cda001/load'),
                reader: {
                    rootProperty: 'rows'
                }
            },
            storeId: 'id_store_cda001',
            listeners: {
                beforeload: function (This, operation, eOpts) {
                    operation.setParams({
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        portadorid: Ext.getCmp('select_portadorid').getValue(),
                        anno: Ext.getCmp('fieldAnnoId').getValue(),
                        moneda: Ext.getCmp('moneda_combo').getValue(),
                        portadorName: Ext.getCmp('select_portadorid').getRawValue()
                    });
                },
                load: function (This, records, succesful, eOpts) {
                    Ext.getCmp('adicionar_actividad').setDisabled(false);
                    if (succesful) {
                        if (records.length === 0) {
                            App.showAlert('No existen datos para el CDA 001 en el año ' + Ext.getCmp('fieldAnnoId').getValue() + ' para el portador ' +
                                Ext.getStore('id_store_portadores').findRecord('id', Ext.getCmp('select_portadorid').getValue()).data.nombre, 'warning');
                        }
                        if (Ext.getStore('id_store_cda001').data.items.length > 0) {
                            Ext.getCmp('real').setText('<strong>Real </strong> - ' + Ext.getStore('id_store_cda001').getAt(0).get('anno_real'));
                            Ext.getCmp('acumulado').setText('<strong>Acumulado </strong> - ' + Ext.getStore('id_store_cda001').getAt(0).get('anno_acumulado'));
                            Ext.getCmp('estimado').setText('<strong>Estimado </strong> - ' + Ext.getStore('id_store_cda001').getAt(0).get('anno_acumulado'));
                            Ext.getCmp('propuesta').setText('<strong>Propuesta de Plan </strong> - ' + Ext.getStore('id_store_cda001').getAt(0).get('anno_propuesta'));
                            Ext.getCmp('final').setText('<strong>Plan Final </strong> - ' + Ext.getStore('id_store_cda001').getAt(0).get('anno_propuesta'));
                            Ext.getCmp('id_panel_cda001').setTitle('CDA001' + ' de ' + Ext.getStore('id_store_cda001').getAt(0).get('portador_nombre'));
                        }
                        else {
                            Ext.getCmp('real').setText('<strong>Real</strong>');
                            Ext.getCmp('acumulado').setText('<strong>Acumulado</strong>');
                            Ext.getCmp('estimado').setText('<strong>Estimado</strong>');
                            Ext.getCmp('propuesta').setText('<strong>Propuesta de Plan</strong>');
                            Ext.getCmp('final').setText('<strong>Plan Final</strong>');
                            Ext.getCmp('id_panel_cda001').setTitle('CDA001');
                        }
                    }
                },

            }
        }),
        plugins: {
            ptype: 'cellediting',
            clicksToEdit: 2,
            listeners: {
                edit: function (editor, e, eOpts) {
                    if (Ext.getCmp('guardar_cda001').isDisabled()) {
                        Ext.getCmp('guardar_cda001').setDisabled(false);
                    }
                    var nivelActi_desglose = e.record.get('enero_nivel_act') + e.record.get('febrero_nivel_act') + e.record.get('marzo_nivel_act') + e.record.get('abril_nivel_act') + e.record.get('mayo_nivel_act') +
                        e.record.get('junio_nivel_act') + e.record.get('julio_nivel_act') + e.record.get('agosto_nivel_act') + e.record.get('septiembre_nivel_act') + e.record.get('octubre_nivel_act') +
                        e.record.get('noviembre_nivel_act') + e.record.get('diciembre_nivel_act');

                    var consumo_desglose = e.record.get('enero_consumo') + e.record.get('febrero_consumo') + e.record.get('marzo_consumo') + e.record.get('abril_consumo') + e.record.get('mayo_consumo') +
                        e.record.get('junio_consumo') + e.record.get('julio_consumo') + e.record.get('agosto_consumo') + e.record.get('septiembre_consumo') + e.record.get('octubre_consumo') +
                        e.record.get('noviembre_consumo') + e.record.get('diciembre_consumo');

                    // var Nivel_activ = consumo_desglose / e.record.get('indice_planf');
                    // var ajuste = nivelActi_desglose - Nivel_activ;
                    // Ext.getCmp('ajusteid').expand(true);
                    // Ext.getCmp('ajustevalorid').setValue(Ext.util.Format.round(ajuste, 3));

                    if (e.colIdx === 19 || e.colIdx === 22 || e.colIdx === 25 || e.colIdx === 28 || e.colIdx === 31 || e.colIdx === 34 || e.colIdx === 37 || e.colIdx === 40 || e.colIdx === 43 || e.colIdx === 46 || e.colIdx === 49 || e.colIdx === 52 || e.colIdx === 55) {
                        e.record.data.nivelactiv_desglose = nivelActi_desglose;
                    } else {
                        e.record.data.consumo_desglose = consumo_desglose;
                    }

                    e.record.data.indice_desglose = Ext.util.Format.round(consumo_desglose / nivelActi_desglose, 4);

                    if (e.record.data.actividad_nombre !== '') {

                        var store = Ext.getStore('actividadStoreid').findRecord('nombre', e.record.data.actividad_nombre);
                        e.record.data.codigo_mep_act = store.data.codigomep;
                        e.record.data.codigo_gae_act = store.data.codigogae;
                        e.record.data.actividad_um = store.data.um_actividad_nombre;
                        e.record.data.actividadid = store.data.id;
                    }

                    if (e.colIdx === 10 || e.colIdx === 11) {

                        e.record.data.propuesta_plan_indice = e.record.get('propuesta_plan_nivel_act') === 0 ? 0 : e.record.get('propuesta_plan_consumo') / e.record.get('propuesta_plan_nivel_act')

                    }

                    if (e.colIdx === 13 || e.colIdx === 14) {

                        e.record.data.plan_final_indice = e.record.get('plan_final_nivel_act') === 0 ? 0 : e.record.get('plan_final_consumo') / e.record.get('plan_final_nivel_act')

                    }

                    Ext.getCmp('id_grid_cda001').getView().refresh();

                }
            }
        },
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: [
            {
                text: '<strong>Código</strong>',
                id: 'ccodigo_mep_act',
                dataIndex: 'codigo_mep_act',
                width: 99, align: 'center',
                // locked: true
            },
            {
                text: '<strong>Actividad</strong>',
                dataIndex: 'actividad_nombre',
                width: 270, align: 'center',
                // locked: true,
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('actividad_administrativa') === true) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #40FF00;';
                    }
                    return val2;
                },
                editor: Ext.create('Ext.form.field.ComboBox', {
                    typeAhead: true,
                    id: 'combo_act',
                    triggerAction: 'all',
                    queryMode: 'local',
                    displayField: 'nombre',
                    value: 'id',
                    store: store_actividad,
                })
            },
            {
                text: '<strong>UM. N.ACT</strong>',
                dataIndex: 'actividad_um',
                width: 150, align: 'center',
                filter: 'string',
            },
            {
                text: '<strong>Real </strong> ',
                id: 'real',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'real_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'real_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        dataIndex: 'real_indice',
                        formatter: "number('0.0000')",
                    }
                ]
            },
            {
                text: '<strong>Acumulado</strong> ',
                id: 'acumulado',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'acumulado_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        editor: {
                            xtype: 'numberfield'
                        }
                    },

                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'acumulado_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        dataIndex: 'acumulado_indice',
                        formatter: "number('0.0000')"
                    }
                ],

            },
            {
                text: '<strong>Estimado</strong> ',
                id: 'estimado',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'estimado_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },

                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'estimado_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'estimado_indice',
                        width: 100, align: 'right',
                        formatter: "number('0.0000')"
                    }
                ]
            },
            {
                text: '<strong>Propuesta De Plan</strong> ',
                id: 'propuesta',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'propuesta_plan_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },

                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'propuesta_plan_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'propuesta_plan_indice',
                        width: 100, align: 'right',
                        formatter: "number('0.0000')"
                    }
                ]
            },
            {
                text: '<strong>Plan Final </strong> ',
                id: 'final',
                tpl: '{30}',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'plan_final_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },

                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'plan_final_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        editor: {
                            xtype: 'numberfield'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'plan_final_indice',
                        width: 100, align: 'right',
                        formatter: "number('0.0000')"
                    }
                ]
            },
            {
                text: '<strong>Total Desglose</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'total_desglose_nivel_act',
                        width: 100, align: 'right',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        id: 'id_nivel_activ_desglose',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelActi = record.get('enero_nivel_act') + record.get('febrero_nivel_act') + record.get('marzo_nivel_act') + record.get('abril_nivel_act') + record.get('mayo_nivel_act') +
                                record.get('junio_nivel_act') + record.get('julio_nivel_act') + record.get('agosto_nivel_act') + record.get('septiembre_nivel_act') + record.get('octubre_nivel_act') +
                                record.get('noviembre_nivel_act') + record.get('diciembre_nivel_act');

                            if (Ext.util.Format.round(nivelActi) !== Ext.util.Format.round(record.get('plan_final_nivel_act'))) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                            }
                            return Ext.util.Format.number(nivelActi, '0');
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'total_desglose_consumo',
                        width: 100, align: 'right',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0'));
                        },
                        renderer: function (val2, met, record, a, b, c, d) {
                            var consumo = record.get('enero_consumo') + record.get('febrero_consumo') + record.get('marzo_consumo') + record.get('abril_consumo') + record.get('mayo_consumo') +
                                record.get('junio_consumo') + record.get('julio_consumo') + record.get('agosto_consumo') + record.get('septiembre_consumo') + record.get('octubre_consumo') +
                                record.get('noviembre_consumo') + record.get('diciembre_consumo');

                            if (Ext.util.Format.round(consumo) !== Ext.util.Format.round(record.get('plan_final_consumo'))) {
                                met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                            }
                            return Ext.util.Format.number(consumo, '0');
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        dataIndex: 'total_desglose_indice',
                        width: 100, align: 'right',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('enero_nivel_act') + record.get('febrero_nivel_act') + record.get('marzo_nivel_act') + record.get('abril_nivel_act') + record.get('mayo_nivel_act') +
                                record.get('junio_nivel_act') + record.get('julio_nivel_act') + record.get('agosto_nivel_act') + record.get('septiembre_nivel_act') + record.get('octubre_nivel_act') +
                                record.get('noviembre_nivel_act') + record.get('diciembre_nivel_act');
                            var consumo = record.get('enero_consumo') + record.get('febrero_consumo') + record.get('marzo_consumo') + record.get('abril_consumo') + record.get('mayo_consumo') +
                                record.get('junio_consumo') + record.get('julio_consumo') + record.get('agosto_consumo') + record.get('septiembre_consumo') + record.get('octubre_consumo') +
                                record.get('noviembre_consumo') + record.get('diciembre_consumo');

                            if (nivelactv !== 0) {
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            } else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Enero</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'enero_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield'
                        },
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'enero_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        dataIndex: 'enero_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('enero_nivel_act');
                            var consumo = record.get('enero_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Febrero</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'febrero_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },

                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'febrero_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        dataIndex: 'febrero_indice',
                        type: 'number',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('febrero_nivel_act');
                            var consumo = record.get('febrero_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Marzo</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'marzo_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'marzo_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'marzo_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('marzo_nivel_act');
                            var consumo = record.get('marzo_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Abril</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'abril_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'abril_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'abril_indice'
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('abril_nivel_act');
                            var consumo = record.get('abril_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Mayo</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'mayo_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'mayo_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Mayo_indice'
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('mayo_nivel_act');
                            var consumo = record.get('mayo_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Junio</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'junio_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'junio_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'junio_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('junio_nivel_act');
                            var consumo = record.get('junio_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Julio</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'julio_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'julio_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Julio_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('julio_nivel_act');
                            var consumo = record.get('julio_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Agosto</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'agosto_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'agosto_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Agosto_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('agosto_nivel_act');
                            var consumo = record.get('agosto_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Septiembre</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'septiembre_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'septiembre_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Septiembre_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('septiembre_nivel_act');
                            var consumo = record.get('septiembre_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Octubre</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'octubre_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'octubre_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Octubre_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('octubre_nivel_act');
                            var consumo = record.get('octubre_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Noviembre</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'noviembre_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'noviembre_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Noviembre_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('noviembre_nivel_act');
                            var consumo = record.get('noviembre_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Diciembre</strong>',
                columns: [
                    {
                        text: '<strong>Nivel Activ</strong>',
                        dataIndex: 'diciembre_nivel_act',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Consumo</strong>',
                        dataIndex: 'diciembre_consumo',
                        width: 100, align: 'right',
                        formatter: "number('0')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.000'));
                        },
                        editor: {
                            xtype: 'numberfield',
                            allowBlank: false
                        }
                    },
                    {
                        text: '<strong>Índice</strong>',
                        width: 100, align: 'right',
                        //dataIndex: 'Diciembre_indice',
                        renderer: function (val2, met, record, a, b, c, d) {
                            var nivelactv = record.get('diciembre_nivel_act');
                            var consumo = record.get('diciembre_consumo');
                            if (nivelactv !== 0)
                                return Ext.util.Format.number(consumo / nivelactv, '0.0000');
                            else
                                return Ext.util.Format.number(0, '0.0000');
                        }
                    }
                ]
            },
            {
                text: '<strong>Código Gae</strong>',
                id: 'codigo_gae_act',
                dataIndex: 'codigo_gae_act',
                width: 130, align: 'center',
                filter: 'string',
                hidden: true,
            }
        ],
        // enableLocking: true,
        tbar: {
            id: 'cda001_Electricidad_tbar',
            height: 36,
            items: [selec_portador, anno, monedaCombo]
        },
        listeners: {
            selectionchange: function (This, selected, eF) {
                if (Ext.getCmp('eliminar_actividad')) {
                    Ext.getCmp('eliminar_actividad').setDisabled(selected.length === 0);
                }
            }
        }
    });

    // var panelDerecho = Ext.create('Ext.panel.Panel', {
    //     title: 'Ajuste Nivel Actividad CDA001',
    //     region: 'east',
    //     id: 'ajusteid',
    //     collapsible: true,
    //     collapsed: true,
    //     width: 250,
    //     layout: 'vbox',
    //     bodyPadding: 5,
    //     items: [{
    //         xtype: 'textfield',
    //         labelAlign: 'top',
    //         fieldLabel: 'Ajuste NA',
    //         id: 'ajustevalorid',
    //         labelWidth: 60,
    //         fieldStyle: 'font-weight: bold; color: #FF0000;'
    //     }
    //     ]
    // });

    var _panel_cda001 = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_cda001',
        title: 'CDA001',
        frame:true,
        closable:true,
        layout: 'border',
        padding: '2 0 0',
        items: [panetree, grid_cda]
    });

    App.render(_panel_cda001);
});


