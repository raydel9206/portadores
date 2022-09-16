/**
 * Created by rherrerag on 03/02/2017.
 */

Ext.onReady(function () {

    var periodo = App.PerformSyncServerRequest(Routing.generate('getCurrentPeriodo'),{});
    var store_consumo_mensual_vehiculos = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_consumo_mensual_vehiculos',
        fields: [
            {name: 'matricula'},
            {name: 'tipo_combustible'},
            {name: 'kms_recorridos_mn', type: 'float'},
            {name: 'cmb_consumido_mn', type: 'float'},
            {name: 'comb_consumido_norma_mn', type: 'float'},
            {name: 'cmb_abastecido_mn', type: 'float'},

            {name: 'kms_recorridos_cuc', type: 'float'},
            {name: 'cmb_consumido_cuc', type: 'float'},
            {name: 'comb_consumido_norma_mn', type: 'float'},
            {name: 'cmb_abastecido_cuc', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadConsumoMensualVehiculos'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                Ext.getCmp('id_grid_consumo_mensual_vehiculos').getSelectionModel().deselectAll();
                if(cmbMeses.getValue() == null)
                    cmbMeses.setValue(periodo.mes);
                if(checkbox_acumulado.getValue()){
                    operation.setParams({
                        acumulado: true,
                        mes: cmbMeses.getValue()
                    });
                }else{
                    operation.setParams({
                        mes: cmbMeses.getValue()
                    });
                }
            }
        }
    });

    var btnSearch = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        tooltip: 'Buscar',
        iconCls: 'fa fa-search fa-1_4',
        handler: function () {
            Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore().currentPage = 1;
            Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore().load();
            Ext.getCmp('grid_totales_MN').getStore().load();
            Ext.getCmp('grid_totales_CUC').getStore().load();
        }
    });

    var store_totales_MN = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_totales_MN',
        fields: [
            {name: 'tipo'},
            {name: 'portador'},
            {name: 'orden'},
            {name: 'comb', type: 'float'},
            {name: 'kms', type: 'float'},
            {name: 'abast', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadTotalesMN'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        groupField: 'portador',
        groupDir: 'DESC',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if(checkbox_acumulado.getValue()){
                    operation.setParams({
                        accion: true,
                        acumulado: true,
                        mes: cmbMeses.getValue()
                    });
                }else{
                    operation.setParams({
                        accion: true,
                        mes: cmbMeses.getValue()
                    });
                }
            }
        }
    });

    var store_totales_CUC = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_totales_CUC',
        fields: [
            {name: 'tipo'},
            {name: 'comb', type: 'float'},
            {name: 'kms', type: 'float'},
            {name: 'abast', type: 'float'},
        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadTotalesCUC'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: false,
        groupField: 'portador',
        groupDir: 'DESC',
        listeners: {
            beforeload: function (This, operation, eOpts) {
                if(checkbox_acumulado.getValue()){
                    operation.setParams({
                        accion: true,
                        acumulado: true,
                        mes: cmbMeses.getValue()
                    });
                }else{
                    operation.setParams({
                        accion: true,
                        mes: cmbMeses.getValue()
                    });
                }
            }
        }
    });

    var cmbMeses = Ext.create('Ext.form.ComboBox', {
        labelWidth: 140,
        id: 'cmb_meses_consumo_mensual',
        store: Ext.create('Ext.data.JsonStore', {
            fields: ['mes', 'numero'],
            data: [
                {name: 'Enero', numero: 1},
                {name: 'Febrero', numero: 2},
                {name: 'Marzo', numero: 3},
                {name: 'Abril', numero: 4},
                {name: 'Mayo', numero: 5},
                {name: 'Junio', numero: 6},
                {name: 'Julio', numero: 7},
                {name: 'Agosto', numero: 8},
                {name: 'Septiembre', numero: 9},
                {name: 'Octubre', numero: 10},
                {name: 'Noviembre', numero: 11},
                {name: 'Diciembre', numero: 12}
            ]
        }),
        displayField: 'name',
        valueField: 'numero',
        queryMode: 'local',
        forceSelection: true,
        emptyText: 'Mes...',
        editable: false,
        afterLabelTextTpl: [
            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
        ],
        allowBlank: false,
        // listeners: {
        //     change: function(){
        //         if(Ext.getCmp('cmb_meses_resumen_eficiencia').getValue() != null && Ext.getCmp('number_field_year').getValue() != ''){
        //             Ext.getCmp('chech_acumulado_resumen_eficiencia').setDisabled(false);
        //         }
        //     }
        // }
    });

    var btnPrint = Ext.create('Ext.button.MyButton', {
        id: 'btn_prin_consumo_mensual_vehiculos',
        text: 'Imprimir',
        //disabled: true,
        iconCls: 'fa fa-print',
        handler: function (This, e) {
            App.ShowWaitMsg();
            var store = Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore();
            if (store.getCount() !== 0) {
                var acumulado = checkbox_acumulado.getValue();
                var accion = true;
                var exp = true;
                var obj = {};
                obj.accion = accion;
                obj.exp = exp;
                obj.mes = cmbMeses.getValue();
                obj.acumulado = acumulado;

                var _result = App.PerformSyncServerRequest(Routing.generate('printConsumoMensualVehiculos'), obj);
                App.HideWaitMsg();

                if (_result.success) {
                    var newWindow = window.open('', '', 'width=1500, height=700'),
                        document = newWindow.document.open();
                    document.write(_result.html);
                    document.close();
                    newWindow.print();
                }
            }
            else
                App.InfoMessage('Información', "No Existen Datos Para Imprimir", 'danger');
            App.HideWaitMsg();
        }
    });

    var btnExport = Ext.create('Ext.button.MyButton', {
        id: '_btn_consumo_mensual_vehiculos_export',
        text: 'Exportar',
        // disabled: true,
        iconCls: 'fa fa-share-square-o',
        handler: function (This, e) {
            App.ShowWaitMsg();
            var store = Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore();
            var send = [];
            var acumulado = checkbox_acumulado.getValue();
            var accion = true;
            var exp = true;

            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            var obj = {};
            obj.store = Ext.encode(send);
            obj.accion = accion;
            obj.exp = exp;
            obj.mes = cmbMeses.getValue();

            obj.acumulado = acumulado;
            var _result = App.PerformSyncServerRequest(Routing.generate('printConsumoMensualVehiculos'), obj);
            App.HideWaitMsg();
            if (_result.success) {
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent(_result.html));
            }
        }
    });

    var checkbox_acumulado = Ext.create('Ext.form.field.Checkbox', {
        id: 'chech_acumulado_resumen_eficiencia',
        boxLabel: 'Acumulado',
        disabled: false,
        listeners: {
            change: function () {
                Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore().currentPage = 1;
                Ext.getCmp('id_grid_consumo_mensual_vehiculos').getStore().load();
                Ext.getCmp('grid_totales_MN').getStore().load();
                Ext.getCmp('grid_totales_CUC').getStore().load();
                Ext.getCmp('btn_prin_resumen_eficiencia').setDisabled(false);
                Ext.getCmp('_btn_resumen_eficiencia_export').setDisabled(false);
            }
        }
    });

    var grid_consumo_mensual_vehiculos = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_consumo_mensual_vehiculos',
        store: store_consumo_mensual_vehiculos,
        width: App.GetDesktopWidth(),
        height: App.GetDesktopHeigth() - 340,
        features: [{
            ftype: 'summary',
            dock: 'bottom'
        }],
        columns: [
            {header: '<strong>No.</strong>', xtype: 'rownumberer', id: 'numero', width: 40},
            {
                header: '<strong>Matr&iacute;cula</strong>', dataIndex: 'matricula', flex: 0.3,
                summaryType: 'count',
                summaryRenderer: function () {
                    return '<strong>TOTALES</strong>';
                }
            },
            {header: '<strong>Combustible</strong>', dataIndex: 'tipo_combustible', flex: 0.5},
            {
                header: 'CONSUMO MN',
                columns: [
                    {
                        header: '<strong>Kms Recorridos</strong>', dataIndex: 'kms_recorridos_mn', flex: 0.9,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }, {
                        header: '<strong>Combustible<br>Consumido</strong>', dataIndex: 'cmb_consumido_mn', flex: 0.9,
                        summaryType: 'sum',
                        //align: 'center',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }, {
                        header: '<strong>Combustible<br>Consumido x<br> norma</strong>',
                        dataIndex: 'comb_consumido_norma_mn',
                        flex: 0.9,
                        summaryType: 'sum',
                        //align: 'center',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }, {
                        header: '<strong>Abastecido</strong>', dataIndex: 'cmb_abastecido_mn', flex: 0.9,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }
                ]
            }, {
                header: 'CONSUMO CUC',
                columns: [
                    {
                        header: '<strong>Kms Recorridos</strong>', dataIndex: 'kms_recorridos_cuc', flex: 0.9,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        header: '<strong>Combustible<br>Consumido</strong>', dataIndex: 'cmb_consumido_cuc', flex: 0.9,
                        summaryType: 'sum',
                        //align: 'center',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        header: '<strong>Combustible<br>Consumido x<br> norma</strong>',
                        dataIndex: 'comb_consumido_norma_cuc',
                        flex: 0.9,
                        summaryType: 'sum',
                        //align: 'center',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    },
                    {
                        header: '<strong>Abastecido</strong>', dataIndex: 'cmb_abastecido_cuc', flex: 0.9,
                        summaryType: 'sum',
                        summaryRenderer: function (value, summaryData, dataIndex) {
                            return "<strong>" + Ext.util.Format.round(value, 2) + "</strong>";
                        },
                        field: {
                            xtype: 'numberfield'
                        }
                    }
                ]
            }
        ],
        tbar: {
            id: 'consumo_mensual_vehiculos_tbar',
            height: 36,
            items: [cmbMeses, checkbox_acumulado, btnSearch, btnPrint, '-', btnExport]
        }
    });

    var grid_totales_MN = Ext.create('Ext.grid.Panel', {
        id: 'grid_totales_MN',
        width: '50%',
        features: [{
            groupHeaderTpl: ' ',
            ftype: 'groupingsummary'
        }, {
            ftype: 'summary',
            dock: 'bottom'
        }],
        store: store_totales_MN,
        title: 'MN',
        titleAlign: 'center',
        height: '100%',
        margin: '5 5 5 5',
        border: true,
        frame: true,
        columns: [
            {
                header: '<strong>TIPO</strong>', dataIndex: 'tipo', flex: 0.5,
                summaryRenderer: function (value, summaryData, dataIndex) {
                    if (summaryData.kms_mn > 20000) {
                        return '<strong>TOTAL</strong>'
                    } else {
                        return '<strong>SUBTOTAL</strong>'
                    }
                }
            },
            {
                header: '<strong>KMS</strong>', dataIndex: 'kms', flex: 0.5,
                summaryType: 'sum',
                id: 'kms_mn',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            },
            {
                header: '<strong>COMB</strong>', dataIndex: 'comb', flex: 0.5,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            },
            {
                header: '<strong>ABAST</strong>', dataIndex: 'abast', flex: 0.5,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            }
        ]
    });

    var grid_totales_CUC = Ext.create('Ext.grid.Panel', {
        id: 'grid_totales_CUC',
        width: '50%',
        store: store_totales_CUC,
        title: 'CUC',
        features: [{
            groupHeaderTpl: ' ',
            ftype: 'groupingsummary'
        }, {
            ftype: 'summary',
            dock: 'bottom'
        }],
        titleAlign: 'center',
        height: '100%',
        margin: '5 5 5 5',
        border: true,
        frame: true,
        columns: [
            {
                header: '<strong>TIPO</strong>', dataIndex: 'tipo', flex: 0.5,
                summaryRenderer: function (value, summaryData, dataIndex) {
                    if (summaryData.kms_cuc > 60000) {
                        return '<strong>TOTAL</strong>'
                    } else {
                        return '<strong>SUBTOTAL</strong>'
                    }
                }
            },
            {
                header: '<strong>KMS</strong>', dataIndex: 'kms', flex: 0.5,
                summaryType: 'sum',
                id: 'kms_cuc',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            },
            {
                header: '<strong>COMB</strong>', dataIndex: 'comb', flex: 0.5,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            },
            {
                header: '<strong>ABAST</strong>', dataIndex: 'abast', flex: 0.5,
                summaryType: 'sum',
                summaryRenderer: function (value) {
                    return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.number(value, '0.00'));
                }
            },
        ]
    });

    var panel_extra = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_extra',
        width: App.GetDesktopWidth(),
        layout: 'hbox',
        height: 250,
        border: true,
        frame: true,
        items: [grid_totales_MN, grid_totales_CUC]
    });

    var panel_consumo_mensual_vehiculos = Ext.create('Ext.panel.Panel', {
        id: 'id_panel_consumo_mensual_vehiculos',
        title: 'Consumo Mensual por Vehículos ',
        width: App.GetDesktopWidth(),
        height: App.GetDesktopHeigth() - 75,
        border: true,
        frame: true,
        layout: 'vbox',
        items: [grid_consumo_mensual_vehiculos, panel_extra]
    });

    App.RenderMainPanel(panel_consumo_mensual_vehiculos);
});