/**
 * Created by yosley on 06/10/2015.
 */


Ext.onReady(function () {
    let action = '';
    let store_desglose = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_cheque_desglose',
        fields: [
            {name: 'id'},
            {name: 'cheque'},
            {name: 'monto',type:'float'},
            {name: 'litros',type:'float'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/chequeFincimex/loadChequeDesglose'),
            reader: {
                rootProperty: 'rows'
            }
        },
        sorters: [{
            property: 'tipo_combustible',
            direction: 'ASC'
        }],
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams({
                    chequeid: Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected().data.id,
                });
            }
        }
    });

    let store_tipocombustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_combustible',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'precio'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('portadores/tipocombustible/loadCombo'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    let store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda_tarjeta',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true
    });

    var edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 1,
        listeners: {
            beforeedit: function (This, e, eOpts) {
                console.log(e.grid.store.data.items[e.rowIdx].data.tipo_combustible_id);
                if ((e.grid.store.data.items[e.rowIdx].data.tipo_combustible_id == '' || e.grid.store.data.items[e.rowIdx].data.tipo_combustible_id == '--Seleccione--') && e.colIdx > 0) {
                    return false;
                }
            },
            edit: function (This, e, eOpts) {
                if (e.colIdx === 0 && edit) {
                    e.record.data.tipo_combustible_id = e.value;
                    var record = store_tipocombustible.findRecord('id', e.value);
                    if (record) {
                        e.record.data.tipo_combustible = record.data.codigo;
                        if (e.record.data['monto'])
                            e.record.data['litros'] = e.record.data['monto'] / record.data.precio;
                    }
                    e.record.commit();
                }
                if (e.colIdx === 1 && edit) {
                    var record = store_tipocombustible.findRecord('id', e.grid.store.data.items[e.rowIdx].data.tipo_combustible_id);
                    e.record.data['litros'] = e.value / record.data.precio;
                    e.record.commit();
                }
                if (e.colIdx === 2 && edit) {
                    var record = store_tipocombustible.findRecord('id', e.grid.store.data.items[e.rowIdx].data.tipo_combustible_id);
                    e.record.data['monto'] = e.value * record.data.precio;
                    e.record.commit();
                }

                Ext.getCmp('grid_desglose').getView().refresh();
            }
        }
    });

    Ext.define('Portadores.chequefincimex.Window', {
        extend: 'Ext.window.Window',
        width: 350,
        modal: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    // frame: true,
                    defaultType: 'textfield',
                    bodyPadding: 10,
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'container',
                            width: '98%',
                            defaultType: 'textfield',
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            fieldDefaults: {
                                msgTarget: 'side',
                                allowBlank: false
                            },
                            items: [
                                {
                                    title: 'Datos',
                                    xtype: 'fieldset',
                                    layout: {
                                        type: 'vbox',
                                        align: 'left'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'No. Cheque',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            // value: '########',
                                            maskRe: /^[0-9]$/,
                                            labelWidth: 120,
                                            name: 'no_cheque'
                                        },
                                        {
                                            xtype: 'datefield',
                                            name: 'fecha_registro',
                                            id: 'fecha_registro',
                                            fieldLabel: 'Fecha',
                                            value: new Date(),
                                            editable: false,
                                            labelWidth: 120,
                                            margin: '5 0',
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                afterrender: function (This) {
                                                    var mes = App.selected_month;
                                                    var dias = App.getDaysInMonth(App.selected_year, mes);
                                                    var anno = App.selected_year;
                                                    var min = new Date(mes + '/' + 1 + '/' + anno);
                                                    var max = new Date(App.selected_month+ '/' + dias + '/' + anno);
                                                    // This.setMinValue(min);
                                                    This.setMaxValue(max);
                                                }
                                            },
                                        },
                                        {
                                            xtype: 'combobox',
                                            name: 'moneda_id',
                                            id: 'moneda_id',
                                            fieldLabel: 'Moneda',
                                            labelWidth: 120,
                                            margin: '5 0',
                                            store: store_moneda,
                                            displayField: 'nombre',
                                            valueField: 'id',
                                            typeAhead: true,
                                            queryMode: 'local',
                                            forceSelection: true,
                                            triggerAction: 'all',
                                            emptyText: 'Moneda...',
                                            selectOnFocus: true,
                                            editable: true,
                                            afterLabelTextTpl: [
                                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                            ],
                                            listeners: {
                                                select: function (combo, record, eOpts) {
                                                    Ext.getCmp('container_montos').setTitle('Montos en ' + record.data.nombre);
                                                    Ext.getCmp('reload_button').enable();

                                                    if (action === 'add'){
                                                        let obj = {
                                                            monedaid: record.data.id,
                                                            nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                                        };
                                                        App.request('GET', App.buildURL('/portadores/chequeFincimex/loadLastSolicitudCompra'), obj, null, null,
                                                            function (response) {
                                                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                    Ext.getStore('id_store_cheque_desglose').setData(response.rows);
                                                                }
                                                            }, null, null, true);
                                                    }
                                                }
                                            }
                                        }
                                    ]
                                },
                                {
                                    title: 'Montos',
                                    id: 'container_montos',
                                    xtype: 'fieldset',
                                    defaultType: 'textfield',
                                    layout: {
                                        type: 'vbox'
                                    },
                                    items: [
                                        {
                                            xtype: 'gridpanel',
                                            id: 'grid_desglose',
                                            height: 200,
                                            width: '100%',
                                            store: Ext.getStore('id_store_cheque_desglose'),
                                            plugins: [edit],
                                            border: true,
                                            features: [{
                                                ftype: 'summary',
                                                dock: 'bottom'
                                            }],
                                            // viewConfig: {
                                            //     getRowClass: function (record, rowIndex, rowParams, store) {
                                            //         if (!record.get('historial')) return 'row-error';
                                            //     }
                                            // },
                                            columns: [

                                                {
                                                    text: '<strong>Combustible</strong>',
                                                    dataIndex: 'tipo_combustible',
                                                    flex: .4,
                                                    align: 'center',
                                                    editor: {
                                                        xtype: 'combobox',
                                                        typeAhead: true,
                                                        id: 'combo_act',
                                                        triggerAction: 'all',
                                                        queryMode: 'local',
                                                        displayField: 'codigo',
                                                        valueField: 'id',
                                                        store: store_tipocombustible,
                                                        listeners: {
                                                            beforeselect: function (This, record, index, eOpts) {
                                                                if (Ext.getStore('id_store_cheque_desglose').findRecord('tipo_combustible_id', record.data.id)) {
                                                                    return false;
                                                                }

                                                            },
                                                            select: function (This, record) {

                                                            }
                                                        }
                                                    },
                                                    summaryType: 'sum',
                                                    summaryRenderer: function (value, summaryData, dataIndex) {
                                                        return `<strong>Total</strong>`;
                                                    }

                                                },
                                                {
                                                    text: '<strong>Monto</strong>',
                                                    dataIndex: 'monto',
                                                    flex: .3,
                                                    align: 'right',
                                                    formatter: "number('0.00')",
                                                    editor: {
                                                        xtype: 'numberfield',
                                                        // decimalSeparator: '.',
                                                        hideTrigger: true
                                                    },
                                                    renderer: function (val2, met, record, a, b, c, d) {
                                                        return Ext.util.Format.round(val2, 2);
                                                    },
                                                    summaryType: 'sum',
                                                    summaryRenderer: function (value, summaryData, dataIndex) {
                                                        return `<bold>${Ext.util.Format.number(Ext.util.Format.round(value, 2),'0.00')}</bold>`;
                                                        // return Ext.util.Format.round(value, 2);
                                                    }
                                                },
                                                {
                                                    text: '<strong>Litros</strong>',
                                                    dataIndex: 'litros',
                                                    flex: .3,
                                                    align: 'right',
                                                    formatter: "number('0.00')",
                                                    editor: {
                                                        xtype: 'numberfield',
                                                        // decimalSeparator: '.',
                                                        hideTrigger: true
                                                    },
                                                    renderer: function (val2, met, record, a, b, c, d) {
                                                        return Ext.util.Format.round(val2, 2);
                                                    },
                                                    summaryType: 'sum',
                                                    summaryRenderer: function (value, summaryData, dataIndex) {
                                                        return `<bold>${Ext.util.Format.number(Ext.util.Format.round(value, 2),'0.00')}</bold>`;
                                                    }
                                                }
                                            ],
                                            tbar: [{
                                                xtype: 'button',
                                                text: 'Recargar',
                                                id: 'reload_button',
                                                disabled: true,
                                                iconCls: 'fas fa-sync text-primary',
                                                handler: function () {
                                                    Ext.MessageBox.confirm('Confirmaci&oacute;n', 'Esta acción eliminará el desglose realizado. <br> ¿Desea continuar?', function (btn) {
                                                        if (btn === 'yes') {
                                                            Ext.getStore('id_store_cheque_desglose').removeAll();
                                                            let obj = {
                                                                monedaid: Ext.getCmp('moneda_id').getValue(),
                                                                nunidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                                                            };
                                                            App.request('GET', App.buildURL('/portadores/chequeFincimex/loadLastSolicitudCompra'), obj, null, null,
                                                                function (response) {
                                                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                        Ext.getStore('id_store_cheque_desglose').setData(response.rows);
                                                                    }
                                                                }, null, null, true);
                                                        }
                                                    });
                                                }
                                            },'->', {
                                                xtype: 'button',
                                                text: 'Adicionar',
                                                iconCls: 'fas fa-plus-square text-primary',
                                                handler: function () {
                                                    Ext.getCmp('grid_desglose').getStore().add({
                                                        cheque: '',
                                                        tipo_combustible: '--Seleccione--',
                                                        tipo_combustible_id: '',
                                                        monto: 0,
                                                        litros: 0,
                                                    });

                                                }
                                            }, {
                                                xtype: 'button',
                                                text: 'Eliminar',
                                                iconCls: 'fas fa-minus-square text-primary',
                                                handler: function () {
                                                    let select = Ext.getCmp('grid_desglose').getSelectionModel().getLastSelected();
                                                    Ext.getStore('id_store_cheque_desglose').remove(select);
                                                }
                                            }],
                                            listeners: {
                                                // rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                                                //     // if(record.data.historial)
                                                //     saldo_inicial = Ext.Number.correctFloat(Ext.Number.from(saldo_inicial, 1) + Ext.Number.from(record.data.importe, 1));
                                                //     Ext.getCmp('grid_liq').getStore().remove(record);
                                                //     Ext.getCmp('nro_vale').reset();
                                                //     Ext.getCmp('cant_litros').reset();
                                                //     Ext.getCmp('importe').reset();
                                                //     Ext.getCmp('importe_final').reset();
                                                //     Ext.getCmp('fecha_vale').reset();
                                                //     Ext.getCmp('hora_vale').reset();
                                                //     // Ext.getCmp('nsubactividadid').reset();
                                                //     Ext.getCmp('nfamilia').reset();
                                                //     Ext.getCmp('nservicentroid').reset();
                                                //     // Ext.getCmp('ncentrocostoid').reset();
                                                //     Ext.getCmp('liquidacionid').reset();
                                                //
                                                //     Ext.getCmp('liquidacionid').setValue(record.data.liquidacionid);
                                                //     Ext.getCmp('hora_vale').setValue(record.data.hora_vale);
                                                //     Ext.getCmp('fecha_vale').setValue(record.data.fecha_vale);
                                                //     Ext.getCmp('nro_vale').setValue(record.data.nro_vale);
                                                //     if (record.data.ncentrocostoid)
                                                //         Ext.getCmp('ncentrocostoid').setValue(record.data.ncentrocostoid);
                                                //     if (record.data.nsubactividadid)
                                                //         Ext.getCmp('nsubactividadid').setValue(record.data.nsubactividadid);
                                                //     if (record.data.nservicentroid)
                                                //         Ext.getCmp('nservicentroid').setValue(record.data.nservicentroid);
                                                //     if (record.data.nfamilia)
                                                //         Ext.getCmp('nfamilia').setValue(record.data.nfamilia);
                                                //     if (record.data.importe) {
                                                //         Ext.getCmp('importe').setValue(record.data.importe);
                                                //         if (record.data.importe_final)
                                                //             Ext.getCmp('importe_inicial').setValue(Ext.Number.correctFloat(Ext.Number.from(record.data.importe_final) + Ext.Number.from(record.data.importe)));
                                                //         else
                                                //             Ext.getCmp('importe_inicial').setValue(Ext.Number.from(record.data.importe));
                                                //     }
                                                //     if (record.data.cant_litros)
                                                //         Ext.getCmp('cant_litros').setValue(record.data.cant_litros);
                                                // }
                                            }
                                        }
                                        // {
                                        //     fieldLabel: 'Gasolina',
                                        //     afterLabelTextTpl: [
                                        //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        //     ],
                                        //     labelWidth: 80,
                                        //     width: 200,
                                        //     value: 0,
                                        //     maskRe: /^[0-9.]$/,
                                        //     name: 'monto_gasolina',
                                        //     id: 'cheque_monto_gasolina',
                                        //     listeners: {
                                        //         change: function (This, value) {
                                        //             let value2 = Ext.getCmp('cheque_monto_diesel').getValue();
                                        //             // This.setValue(App.round(value,2));
                                        //             Ext.getCmp('cheque_monto_total').setValue(Number(value) + Number(value2));
                                        //
                                        //         }
                                        //     }
                                        // },
                                        // {
                                        //     fieldLabel: 'Diesel',
                                        //     afterLabelTextTpl: [
                                        //         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                        //     ],
                                        //     labelWidth: 80,
                                        //     width: 200,
                                        //     value: 0,
                                        //     margin: '5 0',
                                        //     maskRe: /^[0-9.]$/,
                                        //     name: 'monto_diesel',
                                        //     id: 'cheque_monto_diesel',
                                        //     listeners: {
                                        //         change: function (This, value) {
                                        //             let value2 = Ext.getCmp('cheque_monto_gasolina').getValue();
                                        //             // This.setValue(App.round(value,2));
                                        //             Ext.getCmp('cheque_monto_total').setValue(Number(value) + Number(value2));
                                        //
                                        //         }
                                        //     }
                                        // },
                                        // {
                                        //     fieldLabel: 'Total',
                                        //     editable: false,
                                        //     labelAlign: 'left',
                                        //     allowBlank: true,
                                        //     margin: '5 0',
                                        //     labelWidth: 80,
                                        //     width: 200,
                                        //     value: 0,
                                        //     maskRe: /^[0-9]$/,
                                        //     name: 'monto_total',
                                        //     id: 'cheque_monto_total',
                                        //     listeners: {
                                        //         change: function (This, newValue, oldValue, eOpts) {
                                        //             Ext.getCmp('cheque_monto_total').setValue(App.round(newValue, 2));
                                        //         }
                                        //     }
                                        // }
                                    ]
                                },

                            ]
                        },
                        // {
                        //     title: 'Solicitudes de Compra',
                        //     id: 'distribuciones_fieldset',
                        //     xtype: 'fieldset',
                        //     width: '50%',
                        //     margin: '0 10 10',
                        //     items: [
                        //         {
                        //             xtype: 'grid',
                        //             id: 'id_grid_distribucion',
                        //             frame: true,
                        //             selModel: {
                        //                 mode: 'MULTI'
                        //             },
                        //             columnLines: true,
                        //             store: Ext.create('Ext.data.JsonStore', {
                        //                 id: 'store_distribucion',
                        //                 fields: [
                        //                     {name: 'id'},
                        //                     {name: 'denominacion'},
                        //                     {name: 'fecha'},
                        //                     {name: 'tipo_combustible_id'},
                        //                     {name: 'tipo_combustible'},
                        //                     {name: 'precio'},
                        //                     {name: 'portador'},
                        //                     {name: 'cantidad'}
                        //                 ],
                        //                 proxy: {
                        //                     type: 'ajax',
                        //                     url: App.buildURL('/portadores/distribucion/loadSinCheque'),
                        //                     reader: {
                        //                         rootProperty: 'rows'
                        //                     }
                        //                 },
                        //                 pageSize: 1000,
                        //                 autoLoad: true,
                        //                 listeners: {
                        //                     beforeload: function (This, operation, eOpts) {
                        //                         Ext.getCmp('distribuciones_fieldset').mask('Cargando...');
                        //                         Ext.getCmp('id_grid_distribucion').getSelectionModel().deselectAll();
                        //                         operation.setParams({
                        //                             unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
                        //                             aprobada: true
                        //                         });
                        //                     },
                        //                     load: function (This, records, successful, eOpts) {
                        //                         Ext.getCmp('distribuciones_fieldset').unmask();
                        //                     }
                        //                 }
                        //             }),
                        //             columns: [
                        //                 {
                        //                     text: '<strong>Tipo Combustible</strong>',
                        //                     dataIndex: 'tipo_combustible',
                        //                     flex: .6,
                        //                     align: 'center'
                        //                 },
                        //                 {
                        //                     text: '<strong>Fecha</strong>',
                        //                     dataIndex: 'fecha',
                        //                     flex: .4,
                        //                     align: 'center'
                        //                 },
                        //                 {
                        //                     text: '<strong>Cant(L)</strong>',
                        //                     dataIndex: 'cantidad',
                        //                     flex: .3,
                        //                     align: 'center'
                        //                 }
                        //             ],
                        //             tbar: {
                        //                 id: 'distribucion_combustible_tbar',
                        //                 height: 30,
                        //                 padding: 2,
                        //                 items: [{
                        //                     id: 'distribucion_btn_import',
                        //                     text: 'Importar',
                        //                     disabled: true,
                        //                     glyph: 0xf191,
                        //                     width: 90,
                        //                     height: 24,
                        //                     handler: function (This, e) {
                        //                         var diesel = Ext.getCmp('cheque_monto_diesel');
                        //                         var gasolina = Ext.getCmp('cheque_monto_gasolina');
                        //                         var store = Ext.getStore('store_distribucion');
                        //
                        //                         var seleccion = Ext.getCmp('id_grid_distribucion').getSelection();
                        //                         seleccion.forEach(function (seleccion) {
                        //                             distribuciones.push(seleccion.data.id);
                        //                             if (seleccion.data.portador === 'DIESEL') {
                        //                                 diesel.setValue(App.round(diesel.getValue(), 2) + App.round(seleccion.data.cantidad * seleccion.data.precio, 2));
                        //                                 store.remove(seleccion);
                        //                             }
                        //                             else {
                        //                                 gasolina.setValue(App.round(gasolina.getValue(), 2) + App.round(seleccion.data.cantidad * seleccion.data.precio, 2));
                        //                                 store.remove(seleccion);
                        //                             }
                        //
                        //                         });
                        //
                        //                     }
                        //                 },
                        //                     {
                        //                         id: 'distribucion_btn_deshacer',
                        //                         text: 'Deshacer',
                        //                         tooltip: 'Deshacer las distribuciones importadas',
                        //                         iconCls: 'fas fa-undo-alt text-primary',
                        //                         width: 90,
                        //                         height: 24,
                        //                         handler: function (This, e) {
                        //                             let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();
                        //                             Ext.getCmp('cheque_monto_diesel').setValue(0);
                        //                             Ext.getCmp('cheque_monto_gasolina').setValue(0);
                        //
                        //                             if (selection)
                        //                                 Deshacer(selection.data.id);
                        //
                        //                             Ext.getCmp('id_grid_distribucion').getStore().load();
                        //                             distribuciones_old = distribuciones;
                        //                             LimpiarDistribuciones(distribuciones);
                        //
                        //
                        //                         }
                        //                     }
                        //
                        //                 ]
                        //             },
                        //             listeners: {
                        //                 selectionchange: function (This, selected, e) {
                        //                     if (Ext.getCmp('distribucion_btn_import') != undefined)
                        //                         Ext.getCmp('distribucion_btn_import').setDisabled(selected.length == 0);
                        //                 }
                        //             }
                        //         }
                        //
                        //     ]
                        // }


                    ]
                }
            ];

            this.callParent();
        },
        listeners: {
            beforeclose: function () {
                Ext.getStore('id_store_cheque_desglose').removeAll();
            }
        }


    });

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'chequefincimex_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            action = 'add';
            Ext.create('Portadores.chequefincimex.Window', {
                title: 'Adicionar cheque',
                id: 'window_chequefincimex_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let window1 = Ext.getCmp('window_chequefincimex_id');
                            let form = window1.down('form').getForm();
                            if (form.isValid()) {
                                window1.hide();
                                var obj = form.getValues();
                                var send = [];
                                Ext.Array.each(Ext.getStore('id_store_cheque_desglose').data.items, function (valor) {
                                    send.push(valor.data);
                                });
                                obj.store = Ext.encode(send);
                                obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                App.request('POST', App.buildURL('/portadores/chequeFincimex/addChequeFincimex'), obj, null, null,
                                    function (response) {
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            Ext.getCmp('id_grid_chequefincimex').getStore().loadPage(1);
                                            if (response.html != undefined) {
                                                var newWindow = window.open('', 'center', 'width=1024, height=600'),
                                                    document = newWindow.document.open();
                                                document.write(response.html);
                                                document.close();
                                                newWindow.print();
                                            }
                                            window1.close();
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window1.down('form').getForm().markInvalid(response.errors);
                                            }
                                            window1.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        window1.show();
                                    }, null, true);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_chequefincimex_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'chequefincimex_btn_mod',
        text: 'Modificar',
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            action = 'upd';
            let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();

            let window = Ext.create('Portadores.chequefincimex.Window', {
                title: 'Modificar cheque',
                id: 'window_chequefincimex_id',
                listeners: {
                    afterrender: function (This, operation, eOpts) {
                        Ext.getCmp('grid_desglose').getStore().load();
                    },
                    beforeclose: function (This, operation, eOpts) {
                        store_desglose.removeAll();
                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                let obj = form.getValues();
                                obj.id = selection.data.id;
                                obj.distribuciones = distribuciones;
                                App.request('POST', App.buildURL('/portadores/chequeFincimex/modChequeFincimex'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            LimpiarDistribuciones(distribuciones_old);
                                            Ext.getStore('id_store_chequefincimex').loadPage(1);
                                            window.removeListener('beforeload', function (This, eOpts) {
                                                callbackFuntion(This, eOpts);
                                            });
                                        } else {
                                            if (response && response.hasOwnProperty('errors') && response.errors) {
                                                window.down('form').getForm().markInvalid(response.errors);
                                            }
                                            window.show();
                                        }
                                    },
                                    function (response) { // failure_callback
                                        window.show();
                                    }
                                );
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_chequefincimex_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
        }
    });

    let _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'chequefincimex_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            action = 'delete';
            let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar cheque de FINCIMEX?',
                message: Ext.String.format('¿Está seguro que desea eliminar el cheque de FINCIMEX<span class="font-italic font-weight-bold">{0}</span>?', selection.data.nombre),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/chequeFincimex/delChequeFincimex'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_chequefincimex').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    let _btn_Depositar = Ext.create('Ext.button.MyButton', {
        id: 'chequefincimex_btn_depositar',
        text: 'Depositar',
        iconCls: 'fas fa-hand-holding-usd text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {


            let form_depositar = Ext.create('Ext.form.Panel', {
                bodyPadding: 10,
                id: 'form_depositar_id',
                defaults: {
                    anchor: '100%'
                },

                // The fields
                defaultType: 'datefield',
                items: [{
                    fieldLabel: 'Fecha Deposito',
                    name: 'fecha_deposito',
                    allowBlank: false,
                    // value: new Date(),
                    listeners: {
                        afterrender: function (This) {
                            let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();
                            var dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                            var anno = App.selected_year;
                            var min = selection.data.fecha_registro;
                            var max = new Date(App.selected_month + '/' + dias + '/' + anno);
                            This.setMinValue(min);
                            This.setMaxValue(max);
                        }
                    },
                    format: 'd/m/Y',
                }],

                // Reset and Submit buttons
                buttons: [{
                    text: 'Aceptar',
                    formBind: true, //only enabled once the form is valid
                    handler: function () {
                        Ext.getCmp('depositar_id').hide();
                        let form = Ext.getCmp('form_depositar_id');
                        let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();

                        let obj = form.getValues();
                        obj.cheque_id = selection.data.id;
                        App.request('POST', App.buildURL('/portadores/chequeFincimex/depositarChequeFincimex'), obj, null, null,
                            function (response) { // success_callback
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    Ext.getCmp('depositar_id').close();
                                    Ext.getStore('id_store_chequefincimex').loadPage(1);
                                }
                            },
                            function (response) { // failure_callback
                                Ext.getCmp('depositar_id').close();
                            }
                        );

                    }
                }, {
                    text: 'Cerrar',

                    handler: function () {
                        Ext.getCmp('depositar_id').close();
                    }
                }
                ]
            });

            Ext.create('Ext.window.Window', {
                title: 'Depositar Cheque',
                height: 120,
                width: 250,
                layout: 'fit',
                id: 'depositar_id',
                items: [form_depositar]
            }).show();

        }
    });

    let _btn_Imprimir = Ext.create('Ext.button.MyButton', {
        id: '_btn_cheques_export',
        text: 'Imprimir',
        // disabled: true,
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {
            let store = Ext.getCmp('id_grid_chequefincimex').getStore();
            let send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });

            App.request('GET', App.buildURL('/portadores/chequeFincimex/print'), {store: Ext.encode(send)}, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        var newWindow = window.open('', '', 'width=1200, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        document.close();
                        newWindow.print();
                    }

                }, null, null, true
            );

        }
    });

    let _btn_Comprobante = Ext.create('Ext.button.MyButton', {
        id: '_btn_cheques_comprobante',
        text: 'Comprobante',
        disabled: true,
        iconCls: 'fas fa-receipt text-primary',
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_chequefincimex').getSelectionModel().getLastSelected();

            App.request('GET', App.buildURL('/portadores/chequeFincimex/comprobante'), {cheque_id: selection.data.id}, null, null,
                function (response) { // success_callback
                    var newWindow = window.open('', '', 'width=1200, height=700'),
                        document = newWindow.document.open();
                    document.write(response.html);
                    document.close();
                    newWindow.print();
                }, null, null, true
            );

        }
    });

    let Saldo = Ext.create('Ext.button.MyButton', {
        id: 'saldocuenta_id',
        text: 'Saldos',
        iconCls: 'fas fa-file-invoice-dollar text-primary',
        // disabled: true,
        width: 100,
        handler: function (This, e) {
            let unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
            App.request('GET', App.buildURL('/portadores/chequeFincimex/load_saldoChequeFincimex'), {unidad: unidadid}, null, null,
                function (response) { // success_callback
                    if (response.rows.length == 0) { // success_callback but check if exists errors
                        App.showAlert('No existen Montos', 'warning');
                    } else {
                        Ext.override(Ext.window.Toast, {
                            slideInDuration: 2500,
                            slideBackDuration: 10000,
                            hideDuration: 600,
                            autoCloseDelay: 6000,
                        });

                        // response.rows.forEach(function (fila) {
                        //
                        //     console.log(fila);
                        //
                        // })

                        Ext.create('Ext.window.Window', {
                            title: 'Saldo Disponible Fincimex',
                            height: 165,
                            width: 430,
                            modal: true,
                            resizable: false,
                            layout: 'fit',
                            items: [
                                {
                                    xtype: 'grid',
                                    id: 'id_grid_saldo',
                                    disableSelection: true,
                                    store: Ext.create('Ext.data.JsonStore', {
                                        id: 'store_saldo',
                                        fields: [
                                            {name: 'moneda'},
                                            {name: 'saldo_diesel'},
                                            {name: 'saldo_gasolina'},
                                        ],
                                    }),
                                    columns: [
                                        {
                                            text: '<strong>Tipo Combustible</strong>',
                                            dataIndex: 'tipo_combustible',
                                            flex: 3,
                                            align: 'center'
                                        },
                                        {
                                            text: '<strong>CUP</strong>',
                                            dataIndex: 'saldo_1',
                                            flex: 1,
                                            formatter: "number('0.00')",
                                            align: 'right'
                                        },
                                        {
                                            text: '<strong>CUC</strong>',
                                            dataIndex: 'saldo_2',
                                            flex: 1,
                                            formatter: "number('0.00')",
                                            align: 'right'
                                        },
                                        {
                                            text: '<strong>Total</strong>',
                                            dataIndex: 'total',
                                            flex: 1,
                                            formatter: "number('0.00')",
                                            align: 'right'
                                        },

                                    ]

                                },
                            ],
                        }).show();

                        Ext.getCmp('id_grid_saldo').getStore().loadData(response.rows);

                    }

                }, null, null, true
            );

        }

    });

    let _tbar = Ext.getCmp('chequefincimex_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.add('-');
    _tbar.add(_btn_Depositar);
    _tbar.add('->');
    _tbar.add(_btn_Comprobante);
    _tbar.add('-');
    _tbar.add(_btn_Imprimir);
    _tbar.add('-');
    _tbar.add(Saldo);
    _tbar.setHeight(36);


    // let _tbar_grid = Ext.getCmp('distribucion_combustible_tbar');
    // _tbar_grid.add(_btnUpd);
    // _tbar_grid.add('-');
    // _tbar.add(_btnMod);


});

LimpiarDistribuciones = function (distribuciones) {
    Ext.Array.each(distribuciones, function () {
        distribuciones.pop();
    });
}

Deshacer = function (id) {
    App.request('GET', App.buildURL('/portadores/chequeFincimex/deshacer'), {cheque_id: id}, null, null, null, null, null, true, false);
}

RestaurarDistribuciones = function (distribuciones_old2, id) {
    App.request('GET', App.buildURL('/portadores/chequeFincimex/restaurarDistribuciones'), {
            cheque_id: id,
            distribuciones: distribuciones_old2
        }, null, null,
        null, null, null, true, false);
}

