/**
 * Created by Yosley on 11/07/2017.
 */

Ext.onReady(function () {

    let mes_anno = Ext.create('Ext.form.field.Month', {
        id: 'mes_anno',
        extend: 'Ext.form.field.Date',
        alias: 'widget.monthfield',
        width: 90,
        format: 'm/Y',
        requires: ['Ext.picker.Month'],
        alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
        value: new Date(App.selected_month + '/1/' + App.selected_year),
        selectMonth: null,
        grid: null,
        triggers: {
            clear: {
                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                hidden: true,
                handler: function () {
                    this.reset();
                    this.updateLayout();
                    grid.getStore().load();
                    grid_2.getStore().load();
                    grid_3.getStore().load();
                }
            }
        },
        createPicker: function () {
            let me = this,
                format = Ext.String.format,
                pickerConfig;

            pickerConfig = {
                pickerField: me,
                ownerCmp: me,
                renderTo: document.body,
                floating: true,
                hidden: true,
                focusOnShow: true,
                minDate: me.minValue,
                maxDate: me.maxValue,
                disabledDatesRE: me.disabledDatesRE,
                disabledDatesText: me.disabledDatesText,
                disabledDays: me.disabledDays,
                disabledDaysText: me.disabledDaysText,
                format: me.format,
                showToday: me.showToday,
                startDay: me.startDay,
                minText: format(me.minText, me.formatDate(me.minValue)),
                maxText: format(me.maxText, me.formatDate(me.maxValue)),
                listeners: {
                    select: {scope: me, fn: me.onSelect},
                    monthdblclick: {scope: me, fn: me.onOKClick},
                    yeardblclick: {scope: me, fn: me.onOKClick},
                    OkClick: {scope: me, fn: me.onOKClick},
                    CancelClick: {scope: me, fn: me.onCancelClick}
                },
                keyNavConfig: {
                    esc: function () {
                        me.collapse();
                    }
                }
            };

            if (Ext.isChrome) {
                me.originalCollapse = me.collapse;
                pickerConfig.listeners.boxready = {
                    fn: function () {
                        this.picker.el.on({
                            mousedown: function () {
                                this.collapse = Ext.emptyFn;
                            },
                            mouseup: function () {
                                this.collapse = this.originalCollapse;
                            },
                            scope: this
                        });
                    },
                    scope: me,
                    single: true
                }
            }

            return Ext.create('Ext.picker.Month', pickerConfig);
        },
        onCancelClick: function () {
            var me = this;
            me.selectMonth = null;
            me.collapse();
        },
        onOKClick: function () {
            // let me = this;
            // if (me.selectMonth) {
            //     me.setValue(me.selectMonth);
            //     me.fireEvent('select', me, me.selectMonth);
            // }
            // me.collapse();
            // me.grid.getSelectionModel().deselectAll();
            // me.grid.getStore().load();
            var me = this;
            if (me.selectMonth) {
                me.setValue(me.selectMonth);
                me.fireEvent('select', me, me.selectMonth);
            }
            else {
                me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                me.setValue(me.selectMonth);
                me.fireEvent('select', me, me.selectMonth);
            }
            me.collapse();
            grid.getStore().load();
            grid_2.getStore().load();
            grid_3.getStore().load();
        },
        onSelect: function (m, d) {
            var me = this;
            me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
        }
    });
//--------------------------------------------------------------------------
//     let mes_anno = Ext.create('Ext.form.field.Date', {
//         xtype: 'datefield',
//         format: 'm, Y',
//         id: 'mes_anno',
//         // fieldLabel: 'Date',
//         width: 115,
//         emptyText: 'Periodo',
//         value: new Date(App.selected_month + '/1/' + App.selected_year),
//         // labelWidth: 30,
//         triggers: {
//             clear: {
//                 cls: Ext.baseCSSPrefix + 'form-clear-trigger',
//                 hidden: true,
//                 handler: function () {
//                     this.reset();
//                     this.updateLayout();
//                     grid.getStore().load();
//                     grid_2.getStore().load();
//                     grid_3.getStore().load();
//                 }
//             }
//         },
//         createPicker: function () {
//             var me = this,
//                 format = Ext.String.format;
//             return Ext.create('Ext.picker.Month', {
//                 pickerField: me,
//                 ownerCt: me.ownerCt,
//                 renderTo: document.body,
//                 floating: true,
//                 hidden: true,
//                 focusOnShow: true,
//                 minDate: me.minValue,
//                 maxDate: me.maxValue,
//                 disabledDatesRE: me.disabledDatesRE,
//                 disabledDatesText: me.disabledDatesText,
//                 disabledDays: me.disabledDays,
//                 disabledDaysText: me.disabledDaysText,
//                 format: me.format,
//                 showToday: me.showToday,
//                 startDay: me.startDay,
//                 minText: format(me.minText, me.formatDate(me.minValue)),
//                 maxText: format(me.maxText, me.formatDate(me.maxValue)),
//                 listeners: {
//                     select: {
//                         scope: me,
//                         fn: me.onSelect
//                     },
//                     monthdblclick: {
//                         scope: me,
//                         fn: me.onOKClick
//                     },
//                     yeardblclick: {
//                         scope: me,
//                         fn: me.onOKClick
//                     },
//                     OkClick: {
//                         scope: me,
//                         fn: me.onOKClick
//                     },
//                     CancelClick: {
//                         scope: me,
//                         fn: me.onCancelClick
//                     }
//                 },
//                 keyNavConfig: {
//                     esc: function () {
//                         me.collapse();
//                     }
//                 }
//             });
//         },
//         onCancelClick: function () {
//             var me = this;
//             me.selectMonth = null;
//             me.collapse();
//         },
//         onOKClick: function (m, d) {
//             var me = this;
//             me.getTrigger('clear').setVisible(true);
//             if (me.selectMonth) {
//                 me.setValue(me.selectMonth);
//                 me.fireEvent('select', me, me.selectMonth);
//             }
//             else {
//                 me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
//                 me.setValue(me.selectMonth);
//                 me.fireEvent('select', me, me.selectMonth);
//             }
//             me.collapse();
//             grid.getStore().load();
//             grid_2.getStore().load();
//             grid_3.getStore().load();
//         },
//         onSelect: function (m, d) {
//             var me = this;
//             me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
//         }
//     });
//
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
            load: function (store, records) {
                store.insert(0, [{
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
                Ext.getCmp('grid_equipo_equipo').getStore().load();
                Ext.getCmp('grid_equipo_equipo_acumulado').getStore().load();
                Ext.getCmp('grid_equipo_equipo_resumen').getStore().load();
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
        frame: true,
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
                Ext.getCmp('equipo_equipo_panel_id').enable();
                // Ext.getCmp('grid_equipo_equipo').getStore().load();
                // Ext.getCmp('grid_equipo_equipo_acumulado').getStore().load();
                // Ext.getCmp('grid_equipo_equipo_resumen').getStore().load();
                if (Ext.getCmp('equipo_equipo_btn_print'))
                    Ext.getCmp('equipo_equipo_btn_print').setDisabled(false);
                if (Ext.getCmp('equipo_equipo_btn_export'))
                    Ext.getCmp('equipo_equipo_btn_export').setDisabled(false);
            }
        }
    });

    var store_equipo_equipo = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_equipo_equipoId',
        fields: [
            {name: 'matricula'},
            {name: 'marca'},
            {name: 'modelo'},
            // {name: 'actividad_nombre'},
            {name: 'unidadid'},
            {name: 'unidad_nombre'},
            {name: 'tipo_combustible'},
            {name: 'nivel_actividad_real', type: 'number'},
            {name: 'consumo_real', type: 'number'},
            {name: 'indice_consumo_fabricante', type: 'number'},
            {name: 'indice_real', type: 'number'},
            {name: 'indice_plan', type: 'number'},
            {name: 'comb_debio_consumir', type: 'number'},
            {name: 'diferencia_consumo', type: 'number'},
            {name: 'desviacion_indice_normado', type: 'number'},
            {name: 'desviacion_indice_normado_abs', type: 'number'},

        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/analisis_equipo_equipo/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        autoLoad: false,
        pageSize: 1000,
        groupField: 'tipo_combustible',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    tipoCombustible: tipos_combustible.getValue(),
                });
            },
            load: function (This, records, successful, eOpts) {
                // console.log(records)
                if (records.length == 0) {
                    App.showAlert('No existen datos para mostrar el Análisis Equipo a Equipo para el mes de' + ' ' + App.getMonthName(mes_anno.getValue().getMonth() + 1) + ' ' + mes_anno.getValue().getFullYear(), 'warning');
                }
            }
        }
    });

    var store_equipo_equipo_acumulado = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_equipo_equipoacumuladoId',
        fields: [
            {name: 'unidad_nombre'},
            {name: 'descripcion'},
            {name: 'matricula'},
            {name: 'nivel_actividad_real', type: 'number'},
            {name: 'consumo_real', type: 'number'},
            {name: 'indice_real', type: 'number'},
            {name: 'indice_plan', type: 'number'},
            {name: 'comb_debio_consumir', type: 'number'},
            {name: 'diferencia_consumo', type: 'number'},
            {name: 'desviacion_indice_normado', type: 'number'},
            {name: 'desviacion_indice_normado_abs', type: 'number'},
            {name: 'tipo_combustible'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/analisis_equipo_equipo/loadAcumulado'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        autoLoad: false,
        pageSize: 1000,
        groupField: 'tipo_combustible',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                    mes: mes_anno.getValue().getMonth() + 1,
                    anno: mes_anno.getValue().getFullYear(),
                    tipoCombustible: tipos_combustible.getValue(),
                });
            },

        }
    });

    // var store_equipo_equipo_resumen = Ext.create('Ext.data.JsonStore', {
    //     storeId: 'store_equipo_equipo_resumenId',
    //     fields: [
    //
    //         {name: 'marca'},
    //         {name: 'modelo'},
    //         {name: 'descripcion'},
    //         {name: 'matricula'},
    //
    //         {name: 'indice_plan', type: 'number'},
    //         {name: 'abs_1'},
    //         {name: 'abs_2'},
    //         {name: 'abs_3'},
    //         {name: 'abs_4'},
    //         {name: 'abs_5'},
    //         {name: 'abs_6'},
    //         {name: 'abs_7'},
    //         {name: 'abs_8'},
    //         {name: 'abs_9'},
    //         {name: 'abs_10'},
    //         {name: 'abs_11'},
    //         {name: 'abs_12'},
    //         {name: 'tipo_combustible_id'},
    //         {name: 'tipo_combustible'},
    //         {name: 'actividad_nombre'},
    //         {name: 'unidadid'},
    //         {name: 'unidad_nombre'},
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         url: App.buildURL('/portadores/analisis_equipo_equipo/loadResumen'),
    //         reader: {
    //             rootProperty: 'rows'
    //             // sortRoot: 'id'
    //         }
    //     },
    //     autoLoad: false,
    //     pageSize: 1000,
    //     groupField: 'tipo_combustible',
    //     listeners: {
    //         beforeload: function (This, operation, eOpts) {
    //             operation.setParams({
    //                 unidad: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
    //                 mes: mes_anno.getValue().getMonth() + 1,
    //                 anno: mes_anno.getValue().getFullYear(),
    //                 tipoCombustible: tipos_combustible.getValue(),
    //             });
    //         }
    //
    //     }
    // });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_equipo_equipo',
        store: store_equipo_equipo,
        rowLines: true,
        // features: [{
        //     groupHeaderTpl: '{name} ' + '({rows.length})',
        //     ftype: 'groupingsummary'
        // }],
        // hideCollapseTool: true,
        title: 'ANALISIS DE LOS CONSUMOS EQUIPO A EQUIPO',
        columns: [
            {header: '<strong>No.</strong>', align: 'center', xtype: 'rownumberer', width: 45},
            {
                text: '<strong>Descripción </br>y tipo de </br> vehículo</strong>',
                dataIndex: 'descripcion',
                width: 200, align: 'center'
            },
            {
                text: '<strong>Chapa</strong>',
                dataIndex: 'matricula',
                width: 100,
                align: 'center',
                filter: 'string'
            },
            {
                text: '<strong>Índice de</br>Consumo</br>por datos </br>de fábrica <br>(km/L)</strong>',
                dataIndex: 'indice_consumo_fabricante',
                filter: 'string',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>Nivel de</br>Actividad.</br>Real</br>(km)</strong>',
                dataIndex: 'nivel_actividad_real',
                filter: 'string',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>Consumo.</br>Real</br>(L)</strong>',
                dataIndex: 'consumo_real',
                filter: 'string',
                width: 100,
                align: 'center',
                formatter: "number('0')",
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>Combustible</br>que Debió</br>Consumir (L) </strong>',
                dataIndex: 'comb_debio_consumir',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad_real') == 0) {
                        return '';
                    }
                    return Ext.util.Format.round(val2, 2);
                },
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>ĺndice </br>consumo</br>real (UM)/L</strong>',
                dataIndex: 'indice_real',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2)
                        return Ext.util.Format.number(val2, '0.00');

                    return '';
                }
            },
            {
                text: '<strong>ĺndice. </br>Cons. Normado.</br>(UM)/L</strong>',
                dataIndex: 'indice_plan',
                filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>Diferencias </br>en </br>Consumo</br>(L)</strong>',
                dataIndex: 'diferencia_consumo',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad_real') == 0) {
                        return '';
                    }
                    return Ext.util.Format.round(val2, 2);
                }
            },
            {
                text: '<strong>% </br>Desviación </br>del indice</br>normado</strong>',
                dataIndex: 'desviacion_indice_normado',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad_real') == 0) {
                        return '';
                    }
                    return Ext.util.Format.round(val2, 2);
                }
            },
            {
                text: '<strong>Desv. </br>Abs</strong>',
                dataIndex: 'desviacion_indice_normado_abs',
                filter: 'string',
                width: 100,
                align: 'center',
                // formatter: "number('0.00')",
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    if (val2 < 5) {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    return '0.00';
                }
            }
        ],
        // enableLocking: true,
        // width: '75%',
        // height: '100%',
        listeners: {
            selectionchange: function (This, selected, e) {
                Ext.getCmp('equipo_equipo_tbar').items.each(
                    function (item, index, length) {
                        if (index != 0)
                            item.setDisabled(item.getXType() == 'button' && selected.length == 0)
                    }
                );
            }
        }
    });

    var grid_2 = Ext.create('Ext.grid.Panel', {
        id: 'grid_equipo_equipo_resumen',
        // store: store_equipo_equipo_resumen,
        // features: [{
        //     groupHeaderTpl: '{name} ' + '({rows.length})',
        //     ftype: 'groupingsummary'
        // }],
        defaults: {
            bodyPadding: 10
        },

        title: 'ANALISIS DE LOS CONSUMOS EQUIPO A EQUIPO RESUMEN',
        columns: [
            {header: '<strong>No.</strong>', align: 'center', xtype: 'rownumberer', width: 45},
            {
                text: '<strong>Datos del Equipo</strong>',
                // flex: 0.5,
                columns: [
                    {
                        text: '<strong>Descripción y tipo de </br> vehículo</strong>',
                        dataIndex: 'descripcion',
                        width: 200, align: 'center'
                    },
                    {
                        text: '<strong>Chapa</strong>',
                        dataIndex: 'matricula',
                        width: 80, align: 'center',
                        filter: 'string'
                    },
                ]
            },
            {
                text: '<strong>ĺndice </br>Cons Normado</br>(UM)/lts</strong>',
                dataIndex: 'indice_plan',
                filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>Desv.ABS <br> Enero</strong>',
                dataIndex: 'abs_1',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Febrero</strong>',
                dataIndex: 'abs_2',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Marzo</strong>',
                dataIndex: 'abs_3',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else if (val2 < 5)
                        return Ext.util.Format.number(val2, '0.00');
                    else
                        return '';
                }
            }, {
                text: '<strong>Desv.ABS <br> Abril</strong>',
                dataIndex: 'abs_4',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Mayo</strong>',
                dataIndex: 'abs_5',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Junio</strong>',
                dataIndex: 'abs_6',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Julio</strong>',
                dataIndex: 'abs_7',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }

                }
            }, {
                text: '<strong>Desv.ABS <br> Agosto</strong>',
                dataIndex: 'abs_8',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Septiembre</strong>',
                dataIndex: 'abs_9',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Octubre</strong>',
                dataIndex: 'abs_10',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Noviembre</strong>',
                dataIndex: 'abs_11',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }, {
                text: '<strong>Desv.ABS <br> Diciembre</strong>',
                dataIndex: 'abs_12',
                filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (val2 >= 5) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return Ext.util.Format.number(val2, '0.00');
                    }
                    else {
                        return Ext.util.Format.number(val2, '0.00');
                    }
                }
            }
        ],
        // width: '75%',
        // height: '100%'
    });

    var grid_3 = Ext.create('Ext.grid.Panel', {
        id: 'grid_equipo_equipo_acumulado',
        store: store_equipo_equipo_acumulado,
        columnLines: true,
        defaults: {
            bodyPadding: 10
        },

        title: 'ANALISIS DE LOS CONSUMOS EQUIPO A EQUIPO ACUMULADO',
        columns: [
            {header: '<strong>No.</strong>', align: 'center', xtype: 'rownumberer', width: 45},
            {
                text: '<strong>Datos del Equipo</strong>',
                // flex: .5,
                columns: [
                    {
                        text: '<strong>Descripción y tipo de </br> vehículo</strong>',
                        dataIndex: 'descripcion',
                        width: 200,
                        align: 'center'
                    },
                    {
                        text: '<strong>Chapa</strong>',
                        dataIndex: 'matricula',
                        width: 100,
                        align: 'center',
                        filter: 'string'
                    },
                ]
            },
            {
                text: '<strong>Nivel de</br>Actividad.</br>Real (UM)</strong>',
                dataIndex: 'nivel_actividad_real',
                // filter: 'float',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>Consumo</br>Real</br>(lts)</strong>',
                dataIndex: 'consumo_real',
                // filter: 'string',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>Combustible</br>que Debió</br>Consumir (Lts) </strong>',
                dataIndex: 'comb_debio_consumir',
                // filter: 'string',
                width: 100,
                align: 'center',
                summaryType: 'sum',
                summaryRenderer: function (value, summaryData, dataIndex) {
                    return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                }
            },
            {
                text: '<strong>ĺndice </br>consumo</br>real (UM)/lts</strong>',
                dataIndex: 'indice_real',
                // filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad_real') == 0) {
                        return '';
                    }
                    return val2;
                }

            },
            {
                text: '<strong>ĺndice </br>Cons Normado</br>(UM)/lts</strong>',
                dataIndex: 'indice_plan',
                // filter: 'string',
                width: 100,
                align: 'center'
            },
            {
                text: '<strong>Diferencias en </br>Consumo</br>(litros)</strong>',
                dataIndex: 'diferencia_consumo',
                // filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad') == 0) {
                        return '';
                    }
                    return val2;
                }
            },
            {
                text: '<strong>% Desviación </br>del indice</br>normado</strong>',
                dataIndex: 'desviacion_indice_normado',
                // filter: 'string',
                width: 100,
                align: 'center',
                renderer: function (val2, met, record, a, b, c, d) {
                    if (record.get('nivel_actividad_real') == 0) {
                        return '';
                    }
                    return Ext.util.Format.round(val2, 2);
                }
            },
            {
                text: '<strong>Desv.ABS</strong>',
                dataIndex: 'desviacion_indice_normado_abs', /*filter: 'string',*/
                width: 100,
                align: 'center',
                formatter: "number('0.00')",
                renderer: function (val2, met, record, a, b, c, d) {
                    var desv_abs = record.get('desv_indice_abs');
                    if (record.get('desv_indice_abs') >= 3) {
                        met.style = 'font-style:italic !important;font-weight: bold;background: #FF0100;';
                        return desv_abs;
                    }
                    return desv_abs;
                }
            }
        ],

    });

    var _panel_derecho = Ext.create('Ext.panel.Panel', {
        id: 'equipo_equipo_panel_id',
        region: 'center',
        flex: 1,
        disabled: true,
        collapse: true,
        layout: {
            type: 'accordion',
            titleCollapse: true,
            animate: true,
            activeOnTop: true
        },
        items: [grid, grid_3],
        tbar: {
            id: 'equipo_equipo_tbar',
            height: 36,
            items: [mes_anno, tipos_combustible]
        },

    });

    var _panel = Ext.create('Ext.panel.Panel', {
        title: 'Análisis de los Consumos Equipo a Equipo',
        frame: true,
        closable: true,
        layout: {
            type: 'hbox',       // Arrange child items vertically
            align: 'stretch',    // Each takes up full width
            padding: 2
        },
        items: [panetree, _panel_derecho]
    });
    App.render(_panel);
})

