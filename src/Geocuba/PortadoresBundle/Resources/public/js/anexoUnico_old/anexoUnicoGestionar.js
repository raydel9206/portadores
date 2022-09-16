/**
 * Created by yosley on 13/10/2017.
 */
Ext.onReady(function () {
    var textSearch_ = Ext.create('Ext.form.field.Text', {
        width: 200,
        id: 'buscar_vehiculo_anexo_unico_',
        emptyText: 'Buscar por matricula',
        listeners: {
            keydown: function (This, e) {
                if (e.keyCode == 13) {
                    //  Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
                    Ext.getCmp('id_grid_vehiculos_anexo').getStore().load({
                        params: {
                            matricula: textSearch.getValue()
                        }
                    });


                }
            }
        }
    });
    var btnSearch_ = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,

        tooltip: 'Buscar',
        iconCls: 'fas fa-search text-primary',
        handler: function () {
            // Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
            Ext.getCmp('id_grid_vehiculos_anexo').getStore().load({
                params: {
                    matricula: textSearch.getValue()
                }
            });

        }
    });
    var btnClearSearch_ = Ext.create('Ext.button.MyButton', {
        width: 30,
        height: 28,
        tooltip: 'Limpiar',
        iconCls: 'fas fa-eraser text-primary',
        handler: function () {
            Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
            textSearch.reset();
        }
    });
    Ext.define('CombustibleKilometros', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'fecha', type: 'date'},
            {name: 'nro_tarjeta', type: 'string'},
            {name: 'kilometraje', type: 'number'},
            {name: 'comb_abast', type: 'number'},
            {name: 'comb_est_tanke', type: 'number'}
        ]
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'nombre'}
        ],
        proxy: {
            type: 'ajax',
            url: Routing.generate('loadTipoMantenimiento'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: true,
    });


    Ext.define('MyApp.view.Main', {
        extend: 'Ext.tab.Panel',
        plugins: 'responsive',
        responsiveConfig: {
            landscape: {
                tabPosition: 'left'
            },
            portrait: {
                tabPosition: 'top'
            }
        },
        items: [
            {title: 'Foo'},
            {title: 'Bar'}
        ]
    });
    Ext.define('Portadores.anexo_unico.Window', {
        extend: 'Ext.window.Window',
        width: 700,
        height: 600,
        modal: true,
        plain: true,
        resizable: false,
        scrollable: true,
        initComponent: function () {
            var me = this;
            Ext.applyIf(me, {
                items: [
                    {
                        xtype: 'form',
                        layout: 'vbox',
                        items: [
                            {
                                xtype: 'container',
                                width: '100%',
                                items: [
                                    {
                                        xtype: 'container',
                                        width: '100%',
                                        margin: '5 0 0 0',
                                        //flex: 1,
                                        layout: {
                                            type: 'hbox',
                                            align: 'stretch'
                                        },
                                        padding: 5,
                                        items: [
                                            {
                                                title: 'Vehiculos',
                                                columnWidth: 0.65,
                                                xtype: 'grid',
                                                id: 'id_grid_vehiculos_anexo',
                                                scrollable: true,
                                                width: 300,
                                                height: 350,
                                                store: Ext.create('Ext.data.JsonStore', {
                                                    fields: [
                                                        {name: 'id'},
                                                        {name: 'matricula'},
                                                        {name: 'norma'},
                                                        {name: 'nmarca_vehiculo'}
                                                    ],
                                                    proxy: {
                                                        type: 'ajax',
                                                        url: Routing.generate('loadVehiculo'),
                                                        reader: {
                                                            rootProperty: 'rows'
                                                        }
                                                    },
                                                    pageSize: 1000,
                                                    autoLoad: true
                                                }),
                                                tbar: {
                                                    id: 'anexo_unicoGestionar_tbar',
                                                    height: 36,
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            width: 200,
                                                            id: 'buscar_vehiculo_anexo_unico_',
                                                            emptyText: 'Buscar por matricula',
                                                            listeners: {
                                                                keydown: function (This, e) {
                                                                    if (e.keyCode == 13) {
                                                                        //  Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
                                                                        Ext.getCmp('id_grid_vehiculos_anexo').getStore().load({
                                                                            params: {
                                                                                matricula: Ext.getCmp('buscar_vehiculo_anexo_unico_').getValue()
                                                                            }
                                                                        });


                                                                    }
                                                                }
                                                            }

                                                        },
                                                        {
                                                            xtype: 'button',
                                                            width: 30,
                                                            height: 28,
                                                            tooltip: 'Buscar',
                                                            iconCls: 'fas fa-search text-primary',
                                                            handler: function () {
                                                                // Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
                                                                Ext.getCmp('id_grid_vehiculos_anexo').getStore().load({
                                                                    params: {
                                                                        matricula: Ext.getCmp('buscar_vehiculo_anexo_unico_').getValue()
                                                                    }
                                                                });

                                                            }
                                                        },
                                                        {
                                                            xtype: 'button',
                                                            width: 30,
                                                            height: 28,
                                                            tooltip: 'Limpiar',
                                                            iconCls: 'fas fa-eraser text-primary',
                                                            handler: function () {
                                                                Ext.getCmp('id_grid_vehiculos_anexo').getStore().load();
                                                                Ext.getCmp('buscar_vehiculo_anexo_unico_').reset();
                                                            }
                                                        }
                                                    ]
                                                },
                                                columns: [{
                                                    text: 'Marca', dataIndex: 'nmarca_vehiculo', flex: 0.5
                                                }, {
                                                    text: 'Matricula', dataIndex: 'matricula'
                                                }],

                                                listeners: {
                                                    rowdblclick: function (This, record, tr, rowIndex, e, eOpts) {
                                                        Ext.getCmp('norma_plan_id').setValue(record.data.norma);


                                                        var datos_vehiculos = App.PerformSyncServerRequest(Routing.generate('getDatosVehiculoAnexoUnico'), {idvehiculo: record.data.id});


                                                        if (datos_vehiculos != null) {
                                                            console.log(datos_vehiculos.id)
                                                            Ext.getCmp('personaid').setValue(datos_vehiculos.datos.id);


                                                        } else {
                                                            App.InfoMessage('Información', 'El vehiculo Seleccionado no tiene asociado una tarjeta o persona', 'warning');
                                                        }


                                                    },
                                                    render: function (This, eOpts) {
                                                        var selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();
                                                        if (selection) {
                                                            Ext.getCmp('id_grid_vehiculos_anexo').getStore().on('load', function (store, records, options) {
                                                                Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().select(store.find('id', selection.data.vehiculo));
                                                            });
                                                        }


                                                    }
                                                }

                                            },
                                            {
                                                xtype: 'container',
                                                layout: 'vbox',
                                                width: '100%',
                                                items: [
                                                    {
                                                        xtype: 'container',
                                                        layout: 'hbox',
                                                        items: [
                                                            {
                                                                xtype: 'datefield',
                                                                name: 'fecha_anexo',
                                                                margin: '10 10 10 10',
                                                                id: 'fecha_anexo',
                                                                fieldLabel: 'Fecha del Anexo Único',
                                                                labelAlign: 'top',
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                listeners: {
                                                                    select: function (field, value, eOpts) {

                                                                        console.log(value)
                                                                        var mes = (Ext.Date.format(value, 'n'));
                                                                        var anno = (Ext.Date.format(value, 'Y'));
                                                                        console.log(mes)

                                                                        if (Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected()) {
                                                                            var obj = {};
                                                                            obj.idvehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;
                                                                            obj.mes = mes;
                                                                            obj.anno = anno;
                                                                            var comb_abaste = App.PerformSyncServerRequest(Routing.generate('getCombAbastecidoAnexoUnico'), obj);
                                                                            if (comb_abaste.total_litros == 0) {
                                                                                App.InfoMessage('Información', 'El vehiculo Seleccionado no tiene combustible abastecido en el mes seleccionado', 'warning');
                                                                            } else {
                                                                                Ext.getCmp('abastecido_id').setValue(comb_abaste.total_litros);
                                                                            }


                                                                            function CallBack(resonse) {
                                                                                if (resonse.success) {

                                                                                    console.log(resonse)
                                                                                    Ext.getCmp('kilometraje_mes_anterior_id').setValue(resonse.rows.kilometrajeCierreMes);
                                                                                    Ext.getCmp('combustible_estimado_tanque_id').setValue(resonse.rows.combustibleEstimadoTanqueCierre);
                                                                                    Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(resonse.rows.kilometraje_proximo_Mantenimiento);
                                                                                } else {
                                                                                    Ext.getCmp('kilometraje_mes_anterior_id').setValue(0);
                                                                                    Ext.getCmp('combustible_estimado_tanque_id').setValue(0);
                                                                                    Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(0);

                                                                                }
                                                                            }

                                                                            App.PerformServerRequest(Routing.generate('getLastAnexoVehiculo'), obj, CallBack);

                                                                        } else {
                                                                            App.InfoMessage('Información', 'Seleccione el Vehiculo', 'warning');
                                                                            Ext.getCmp('fecha_anexo').setValue(null);
                                                                        }
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'combobox',
                                                                name: 'persona',
                                                                id: 'personaid',
                                                                margin: '10 10 10 10',
                                                                fieldLabel: 'Habilitado por',
                                                                labelWidth: 90,
                                                                labelAlign: 'top',
                                                                displayField: 'nombre',
                                                                valueField: 'id',
                                                                typeAhead: true,
                                                                forceSelection: true,
                                                                triggerAction: 'all',
                                                                queryMode: 'local',
                                                                editable: true,
                                                                emptyText: 'Seleccione la persona',
                                                                selectOnFocus: true,
                                                                allowBlank: false,
                                                                store: Ext.create('Ext.data.JsonStore', {
                                                                    fields: [{name: 'id'}, {name: 'nombre'}],
                                                                    proxy: {
                                                                        type: 'ajax',
                                                                        url: Routing.generate('loadPersona'),
                                                                        reader: {
                                                                            rootProperty: 'rows'
                                                                        }
                                                                    },
                                                                    pageSize: 1000,
                                                                    autoLoad: true
                                                                })
                                                            },
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'fieldset',
                                                        title: 'Datos cierre mes anterior verificado',
                                                        columnWidth: .5,
                                                        margin: '10 10 10 10',
                                                        layout: 'fit',
                                                        defaults: {
                                                            margin: '0 5 5 0',
                                                            allowBlank: false,
                                                            decimalSeparator: '.'
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'numberfield',
                                                                name: 'kilometraje_mes_anterior',
                                                                id: 'kilometraje_mes_anterior_id',
                                                                fieldLabel: 'Kilometraje',
                                                                minValue: 0,
                                                                value: 0
                                                            },
                                                            {
                                                                xtype: 'numberfield',
                                                                name: 'combustible_estimado_tanque',
                                                                id: 'combustible_estimado_tanque_id',
                                                                fieldLabel: 'Combustible estimado tanque',
                                                                minValue: 0,
                                                                value: 0
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'fieldset',
                                                        title: 'Próximo mantenimiento verificado',
                                                        columnWidth: .7,
                                                        margin: '50 10 10 10',
                                                        layout: 'fit',
                                                        defaults: {
                                                            margin: '0 5 5 0',
                                                            allowBlank: false,
                                                            decimalSeparator: '.',
                                                            labelWidth: 150
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'numberfield',
                                                                name: 'kilometraje_proximo_mantenimiento',
                                                                id: 'kilometraje_proximo_mantenimiento_id',
                                                                fieldLabel: 'Cant Kilometros próximo mantenimiento',
                                                                minValue: 0,
                                                                value: 0
                                                            },
                                                            {
                                                                xtype: 'numberfield',
                                                                name: 'norma_plan',
                                                                id: 'norma_plan_id',
                                                                fieldLabel: 'Índice de consumo plan (Km/l)',
                                                                minValue: 0,
                                                                value: 0
                                                            }
                                                        ]
                                                    },
                                                ]
                                            }
                                        ]
                                    },
                                    /*{
                                     xtype: 'panel',
                                     title: 'Combustible y kilómetros',
                                     width: '100%',
                                     height: 380,
                                     bodyPadding: 10,
                                     closable: false,
                                     layout: 'fit',
                                     defaults:{
                                     margin: '0 5 0 0'
                                     },
                                     items: [
                                     {
                                     xtype: 'gridpanel',
                                     id: 'gridCombustibleKilometrosId',
                                     height: 350,
                                     store: Ext.create('Ext.data.Store',{
                                     storeId: 'combustibleKilometrosStoreId',
                                     model: 'CombustibleKilometros'
                                     }),
                                     forceFit: true,
                                     enableColumnHide: false,
                                     plugins: {
                                     ptype: 'cellediting',
                                     clicksToEdit: 1
                                     },
                                     columns:[
                                     {text: '<strong>Fecha</strong>', flex: 15,dataIndex: 'fecha', editor: 'datefield', formatter: 'date("d/m/Y")'},
                                     {text: '<strong>Nro tarjeta</strong>', flex: 15,dataIndex: 'nro_tarjeta', editor: Ext.create('Ext.form.field.ComboBox', {
                                     typeAhead: true,
                                     triggerAction: 'all',
                                     displayField: 'nro_tarjeta',
                                     store: Ext.create('Ext.data.Store',{
                                     storeId: 'tarjetasStoreId',
                                     fields:[
                                     {name: 'id'},
                                     {name: 'nro_tarjeta'}
                                     ],
                                     proxy: {
                                     type: 'ajax',
                                     url: Routing.generate('loadTarjeta'),
                                     reader: {
                                     rootProperty: 'rows'
                                     }
                                     },
                                     pageSize: 1000,
                                     autoLoad: true
                                     })
                                     })
                                     },
                                     {text: '<strong>Kilometraje</strong>', flex: 15,dataIndex: 'kilometraje', editor: Ext.create('Ext.form.field.Number',{
                                     decimalSeparator: '.',
                                     minValue: 0
                                     })
                                     },
                                     {text: '<strong>Comb. abastecido</strong>', flex: 22.5, dataIndex: 'comb_abast', editor: Ext.create('Ext.form.field.Number',{
                                     decimalSeparator: '.',
                                     minValue: 0
                                     })
                                     },
                                     {text: '<strong>Comb. estim. en tanque</strong>', flex: 22.5,dataIndex: 'comb_est_tanke', editor: Ext.create('Ext.form.field.Number',{
                                     decimalSeparator: '.',
                                     minValue: 0
                                     })
                                     }
                                     ],
                                     tbar:[
                                     { text: 'Adicionar', iconCls: 'fa fa-plus-square-o fa-1_4', handler: function(){
                                     Ext.getCmp('gridCombustibleKilometrosId').getStore().add({id:'', fecha: new Date(), nro_tarjeta: '', kilometraje: 0, comb_abast: 0, comb_est_tanke: 0});
                                     }},
                                     '-',
                                     { text: 'Eliminar', iconCls: 'fa fa-minus-square-o fa-1_4', handler: function(){
                                     var record = Ext.getCmp('gridCombustibleKilometrosId').getSelectionModel().getLastSelected();
                                     Ext.getCmp('gridCombustibleKilometrosId').getStore().remove(record);
                                     }}
                                     ]
                                     }
                                     ]
                                     },*/

                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: 'Resumen',
                                width: '100%',
                                height: 440,
                                // height: '100%z',
                                // margin: '10 10 10 10',
                                layout: 'column', // arrange fieldsets side by side
                                // layout: 'vbox',
                                items: [
                                    {
                                        xtype: 'container',
                                        width: '100%',
                                        // layout: 'hbox',
                                        layout: 'column',
                                        // columnWidth: 0.5,
                                        items: [
                                            {
                                                xtype: 'fieldset',
                                                title: 'Datos del cierre del mes',
                                                columnWidth: 0.65,
                                                // margin: '10 20 20 5',
                                                layout: 'fit',
                                                defaults: {
                                                    margin: '0 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 130
                                                },
                                                items: [
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'kilometraje',
                                                        id: 'kilometraje_id',
                                                        fieldLabel: 'Kilometraje',
                                                        minValue: 0,
                                                        value: 0,
                                                        listeners: {
                                                            change: function (This, newValue) {
                                                                var total_km = newValue - Ext.getCmp('kilometraje_mes_anterior_id').getValue();
                                                                Ext.getCmp('km_total_recorrido_id').setValue(total_km);

                                                                // var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                                                                // var cant_comb = total_km / Ext.getCmp('norma_plan_id').getValue();
                                                                var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                                                                if (newValue != 0 && total_comb != 0) {
                                                                    Ext.getCmp('indice_real_id').setValue((total_km / total_comb));
                                                                    var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                                                                    var porciento__ = 100-((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                                                                }

                                                            }
                                                        }
                                                    },

                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'comb_estimado_tanke',
                                                        id: 'comb_estimado_tanke_id',
                                                        fieldLabel: 'Comb. estimado en tanque',
                                                        minValue: 0,
                                                        value: 0,
                                                        listeners: {
                                                            change: function (This, newValue, oldValue) {
                                                                // var store_abastecidos = Ext.getStore('combustibleKilometrosStoreId');
                                                                // total_abastecidos = 0;
                                                                // store_abastecidos.each(function(record){
                                                                //    total_abastecidos += record.data.comb_abast;
                                                                // });
                                                                //
                                                                //
                                                                //
                                                                //Lo abastecido segun economia
                                                                var cant_comb_abastec = Ext.getCmp('abastecido_id').getValue();

                                                                var total_km = Ext.getCmp('km_total_recorrido_id').getValue();
                                                                //TOTAL DE COMBUSTIBLE CONSUMIDO  = COMBUS QUE INICIA  + LO abastecido segun economia -COMB QUE QUEDO EN TANQUE
                                                                var total_comb = Ext.getCmp('combustible_estimado_tanque_id').getValue() + cant_comb_abastec - Ext.getCmp('comb_estimado_tanke_id').getValue();
                                                                Ext.getCmp('comb_total_consumido_id').setValue(total_comb);

                                                                if (newValue != 0 && total_km != 0) {
                                                                    Ext.getCmp('indice_real_id').setValue((total_km / total_comb));
                                                                    var porciento_real = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                                                                    var porciento_real_ = 100-((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento_real);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento_real_);
                                                                }
                                                            }
                                                        }
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                columnWidth: 0.35,
                                                layout: 'fit',
                                                title: 'Totales',
                                                margin: '0 0 0 10',
                                                defaults: {
                                                    margin: '0 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 125,
                                                    // labelAlign:'top'
                                                },
                                                items: [
                                                    {

                                                        xtype: 'numberfield',
                                                        name: 'combustible_total_abastecido',
                                                        id: 'abastecido_id',
                                                        readOnly: true,
                                                        fieldLabel: 'Comb. abastecido',
                                                        minValue: 0,
                                                        value: 0
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'km_total_recorrido',
                                                        id: 'km_total_recorrido_id',
                                                        fieldLabel: 'Km total recorrido',
                                                        readOnly: true
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'combustible_total_consumido',
                                                        id: 'comb_total_consumido_id',
                                                        fieldLabel: 'Comb. total consumido',
                                                        readOnly: false
                                                    }
                                                ]
                                            },
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        width: '100%',
                                        layout: 'column',
                                        items: [
                                            {
                                                xtype: 'fieldset',
                                                title: 'Índice de Consumo Real (Km/l) ',
                                                columnWidth: 0.65,
                                                margin: '10 20 20 5',
                                                layout: 'fit',
                                                defaults: {
                                                    margin: '5 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 100
                                                },
                                                items: [
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'indice_real',
                                                        id: 'indice_real_id',
                                                        fieldLabel: 'Real',
                                                        readOnly: true
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'por_ciento_indice_real_plan',
                                                        id: 'por_ciento_indice_real_plan_id',
                                                        fieldLabel: '% Real/Plan',
                                                        readOnly: true
                                                    }, {
                                                        xtype: 'numberfield',
                                                        name: 'indice_real_plan',
                                                        id: 'por_ciento_indice_real_plan_id__',
                                                        fieldLabel: '         ',
                                                        readOnly: true
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Mantenimiento',
                                                columnWidth: 0.35,
                                                margin: '10 0 0 10',
                                                defaults: {
                                                    margin: '0 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 110
                                                },
                                                items: [
                                                    {
                                                        xtype: 'combobox',
                                                        name: 'tipo_mantenimiento_id',
                                                        id: 'combo_tipo_mantenimiento_id_id',
                                                        fieldLabel: 'Tipo de Mantenimiento',
                                                        store: Ext.getStore('id_store_tipo_mantenimiento'),
                                                        displayField: 'nombre',
                                                        valueField: 'id',
                                                        // typeAhead: true,
                                                        queryMode: 'local',
                                                        forceSelection: false,
                                                        // triggerAction: 'all',
                                                        allowBlank: true,
                                                        emptyText: 'Seleccione mantenimiento...',
                                                        // selectOnFocus: true,
                                                        editable: true,
                                                        listeners: {
                                                            select: function (combo, records, eOpts) {
                                                                if (records) {
                                                                    var datos_vehiculos_mantenimientos = App.PerformSyncServerRequest(Routing.generate('loadTipoMantenimientoByAnexoUnico'), {id: Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id});
                                                                    console.log(datos_vehiculos_mantenimientos)
                                                                    if (datos_vehiculos_mantenimientos) {
                                                                        Ext.getCmp('kilometraje_mantenimiento_id').setReadOnly(false);

                                                                        if (datos_vehiculos_mantenimientos.total != 0) {
                                                                            Ext.getCmp('kilometros_plannif').setValue(datos_vehiculos_mantenimientos.rows.kilometros);


                                                                        } else {
                                                                            App.InfoMessage('Información', 'El vehiculo Seleccionado no tiene asociado Mantenimientos', 'warning');
                                                                        }
                                                                    }
                                                                }


                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'kilometraje_mantenimiento',
                                                        id: 'kilometraje_mantenimiento_id',
                                                        fieldLabel: 'Cant Kilometros',
                                                        minValue: 0,
                                                        value: 0,
                                                        readOnly: true,
                                                        listeners: {
                                                            change: function (This, newValue) {
                                                                var kilometros_planf = Ext.getCmp('kilometros_plannif').getValue();

                                                                if (kilometros_planf) {
                                                                    var km_proximo_mant = newValue + kilometros_planf;
                                                                    Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(km_proximo_mant);
                                                                }
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'numberfield',
                                                        id: 'kilometros_plannif',
                                                        name:'kilometros_plannif',
                                                        hidden: true,
                                                        value:0

                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        width: '100%',
                                        items: [
                                            {
                                                xtype: 'textareafield',
                                                name: 'observaciones',
                                                id: 'observaciones_id',
                                                grow: false,
                                                fieldLabel: 'Observaciones',
                                                labelAlign: 'top',
                                                width: '95%',
                                                margin: '0 0 0 5',
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            });

            this.callParent();
        },
        listeners: {
            afterrender: function () {
                $('#observaciones_id-inputEl').css('min-height', '48px');
            }
        }
    });
    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        handler: function (This, e) {

            Ext.create('Portadores.anexo_unico.Window', {
                title: 'Adicionar Anexo Único',
                id: 'window_anexo_unico_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_anexo_unico_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var obj = form.getValues();
                                obj.vehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;

                                // var store = Ext.getStore('combustibleKilometrosStoreId');
                                // var arr = new Array();
                                // for(var i = 0; i < store.data.items.length; i++){
                                //     var comb_kilometros = new Object();
                                //     comb_kilometros.fecha = Ext.Date.format(store.data.items[i].data.fecha, 'd/m/Y');
                                //     comb_kilometros.nro_tarjeta = store.data.items[i].data.nro_tarjeta;
                                //     comb_kilometros.kilometraje = store.data.items[i].data.kilometraje;
                                //     comb_kilometros.comb_abast = store.data.items[i].data.comb_abast;
                                //     comb_kilometros.comb_est_tanke = store.data.items[i].data.comb_est_tanke;
                                //     arr.push(comb_kilometros);
                                // }
                                // obj.comb_kilometros = Ext.encode(arr);

                                var _result = App.PerformSyncServerRequest(Routing.generate('addAnexoUnico'), obj);
                                App.HideWaitMsg();
                                if (_result.success) {
                                    window.close();
                                    Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                    App.InfoMessage('Información', _result.message, _result.cls);
                                }
                                else {
                                    // form.markInvalid(_result.message);
                                    App.InfoMessage('Información', _result.message, _result.cls);
                                }
                            } else {
                                console.log('no valido')
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_anexo_unico_id').close();
                        }
                    }
                ]
            }).show();
        }
    });
    var _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();
            console.log(selection)
            // var AppConstant = Practice.utilities.AppConstants;
            // console.log(AppConstant.mes);
            var window = Ext.create('Portadores.anexo_unico.Window', {
                title: 'Modificar el Anexo Unico',
                id: 'window_anexo_unico_id',
                listeners: {
                    // afterrender: function () {
                    //     var _result = App.PerformSyncServerRequest(Routing.generate('loadAnexoUnicoCombKilometros'), {anexoid: selection.data.id});
                    //     Ext.getCmp('gridCombustibleKilometrosId').getStore().loadData(_result.grid)
                    // }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {

                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.ShowWaitMsg();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                obj.vehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;
                                console.log(obj)
                                // var store = Ext.getCmp('gridCombustibleKilometrosId').getStore();
                                // var send = [];
                                // Ext.Array.each(store.data.items, function (valor) {
                                //     send.push(valor.data);
                                // });
                                // obj.store = Ext.encode(send);
                                var _result = App.PerformSyncServerRequest(Routing.generate('modAnexoUnico'), obj);
                                App.HideWaitMsg();
                                if (_result.success) {
                                    window.close();
                                    var obj = {};
                                    Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                    // obj.mes = AppConstant.mes;
                                    // obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                    // var resul = App.PerformSyncServerRequest(Routing.generate('loadAnexoUnico'), obj);
                                    //
                                    // Ext.getCmp('id_grid_anexo_unico').getStore().loadData(resul.rows);

                                    // Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                }
                                App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_anexo_unico_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);


        }
    });
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_del',
        text: 'Eliminar',
        iconCls: 'fa fa-minus-square-o fa-1_4',
        disabled: true,
        width: 100,
        handler: function (This, e) {

            App.ConfirmMessage(function () {
                var selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();
                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('delAnexoUnico'), {id: selection.data.id});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);

                Ext.getCmp('id_grid_anexo_unico').getStore().load();
                Ext.getCmp('id_grid_anexo_unico').getView().refresh();

            }, "Está seguro que desea eliminar el Anexo Unico selecionado?");

        }
    });
    var _print = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_del',
        text: 'Imprimir',
        iconCls: 'fas fa-print text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            App.ConfirmMessage(function () {
                var selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();
                App.ShowWaitMsg();
                var _result = App.PerformSyncServerRequest(Routing.generate('delAnexoUnico'), {id: selection.data.id});
                App.HideWaitMsg();
                App.InfoMessage('Información', _result.message, _result.cls);
                Ext.getCmp('id_grid_anexo_unico').getStore().load();
                Ext.getCmp('id_grid_abastecidos').getStore().removeAll();
                // Ext.getCmp('id_panel_info_anexo_unico').collapse();
            }, "Está seguro que desea eliminar el Anexo?");

        }
    });

    var _tbar = Ext.getCmp('anexo_unico_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.setHeight(36);
});


