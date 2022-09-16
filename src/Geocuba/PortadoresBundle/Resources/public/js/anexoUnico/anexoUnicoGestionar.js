Ext.onReady(function () {

    Ext.define('CombustibleKilometros', {
        extend: 'Ext.data.Model',
        fields: [
            // {name: 'fecha', type: 'date', convert: (v) => {
            //     console.log(v);
            //     console.log(Ext.Date.format(v,'d/m/Y'));
            //     return Ext.Date.format(v,'d/m/Y');
            // }},
            {name: 'fecha', type: 'date', dateFormat: 'd/m/Y'},
            {name: 'nro_tarjeta', type: 'string'},
            {name: 'kilometraje', type: 'number'},
            {name: 'comb_abast', type: 'number'},
            {name: 'comb_est_tanke', type: 'number'}
        ]
    });

    var store_vehiculo = Ext.create('Ext.data.JsonStore', {
        storeId: 'vehiculo_store',
        fields: [
            {name: 'id'},
            {name: 'matricula'},
            {name: 'norma'},
            {name: 'nmarca_vehiculo'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/vehiculo/loadCombo'),
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
        }
    });

    var store_persona = Ext.create('Ext.data.JsonStore', {
        storeId: 'person_store',
        fields: [{name: 'id'}, {name: 'nombre'}],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/persona/loadCombo'),
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
        }
    });

    Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_tipo_mantenimiento',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'kilometros'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/anexo_unico/loadTipoMantenimientoBy'),
            reader: {
                rootProperty: 'rows'
            }
        },
        pageSize: 1000,
        autoLoad: false,
        listeners: {
            load: function (This, operation, eOpts) {
                if (Ext.getCmp('kilometraje_mantenimiento_id').getValue()) {
                    var kilometros = Ext.getCmp('kilometraje_mantenimiento_id').getValue();
                    let id = Ext.getCmp('combo_tipo_mantenimiento_id_id').getValue();
                    let mantenimiento = This.findRecord('id', id);
                    Ext.getCmp('variacion').setValue(kilometros / mantenimiento.data.kilometros);
                }

            }
        }
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
        // height: 600,
        height: 450,
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
                        height: 365,
                        items: [
                            // {
                            //     xtype: 'container',
                            //     width: '100%',
                            //     items: [
                            //         {
                            //             xtype: 'container',
                            //             width: '100%',
                            //             margin: '5 0 0 0',
                            //             //flex: 1,
                            //             layout: {
                            //                 type: 'hbox',
                            //                 align: 'stretch'
                            //             },
                            //             padding: 5,
                            //             items: [
                            //                 {
                            //                     title: 'Vehículos',
                            //                     columnWidth: 0.65,
                            //                     xtype: 'grid',
                            //                     id: 'id_grid_vehiculos_anexo',
                            //                     scrollable: true,
                            //                     width: 300,
                            //                     height: 350,
                            //                     store: store_vehiculo,
                            //                     tbar: {
                            //                         id: 'anexo_unicoGestionar_tbar',
                            //                         height: 36,
                            //                         items: [Ext.create('Ext.form.field.Text', {
                            //                             id: 'find_button_vehiculo',
                            //                             emptyText: 'Buscar por Matrícula...',
                            //                             width: 250,
                            //                             enableKeyEvents: true,
                            //                             listeners: {
                            //                                 keyup: function (This, e, eOpts) {
                            //                                     console.log('a');
                            //                                     store_vehiculo.filterBy(function (record) {
                            //                                         return record.data.matricula.search(This.value) !== -1;
                            //                                     }, this);
                            //                                 },
                            //                                 change: function (field, newValue, oldValue, eOpt) {
                            //                                     field.getTrigger('clear').setVisible(newValue);
                            //                                 },
                            //                             },
                            //                             triggers: {
                            //                                 clear: {
                            //                                     cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                            //                                     hidden: true,
                            //                                     handler: function () {
                            //                                         this.setValue(null);
                            //                                         this.updateLayout();
                            //
                            //                                         Ext.getCmp('id_grid_vehiculos_anexo').getStore().clearFilter();
                            //                                         this.setMarked(false);
                            //                                     }
                            //                                 }
                            //                             },
                            //
                            //                             setMarked: function (marked) {
                            //                                 var el = this.getEl(),
                            //                                     id = '#' + this.getId();
                            //
                            //                                 this.marked = marked;
                            //
                            //                                 if (marked) {
                            //                                     el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                            //                                     el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                            //                                     el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
                            //                                 } else {
                            //                                     el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                            //                                     el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                            //                                     el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
                            //                                 }
                            //                             }
                            //                         })]
                            //                     },
                            //                     columns: [{
                            //                         text: 'Matricula', dataIndex: 'matricula', width: '40%'
                            //                     }, {
                            //                         text: 'Marca', dataIndex: 'nmarca_vehiculo', width: '60%'
                            //                     }],
                            //                     listeners: {
                            //                         rowclick: function (This, record, tr, rowIndex, e, eOpts) {
                            //                             This.up('form').getForm().reset();
                            //                             Ext.getCmp('norma_plan_id').setValue(record.data.norma);
                            //                             // Ext.getStore('id_store_tipo_mantenimiento').load();
                            //
                            //                             if (record.data.odometro == false) {
                            //                                 Ext.getCmp('kilometraje_mes_anterior_id').disable();
                            //                                 Ext.getCmp('kilometraje_id').disable();
                            //                             }
                            //
                            //                             App.request('GET', App.buildURL('/portadores/anexo_unico/getLastAnexoVehiculo'), {idvehiculo: record.data.id}, null, null,
                            //                                 function (response) {
                            //                                     if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            //                                         // console.log(response)
                            //                                         // console.log(response.rows.kilometrajeCierreMes)
                            //                                         Ext.getCmp('kilometraje_mes_anterior_id').setValue(response.rows.kilometrajeCierreMes);
                            //                                         Ext.getCmp('combustible_estimado_tanque_id').setValue(response.rows.combustibleEstimadoTanqueCierre);
                            //                                         tipo_manteniento = response.rows.tipo_mantenimiento;
                            //                                         Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(response.rows.kilometraje_proximo_Mantenimiento);
                            //                                     } else {
                            //                                         Ext.getCmp('kilometraje_mes_anterior_id').setValue(0);
                            //                                         Ext.getCmp('combustible_estimado_tanque_id').setValue(0);
                            //                                         tipo_manteniento = null;
                            //                                         Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(0);
                            //                                     }
                            //                                 }, null, null, true
                            //                             );
                            //                         },
                            //                         render: function (This, eOpts) {
                            //                             console.log(Ext.getCmp('id_grid_anexo_unico').getSelection().length != 0)
                            //                             if (Ext.getCmp('id_grid_anexo_unico').getSelection().length != 0) {
                            //                                 var selection = Ext.getCmp('id_grid_anexo_unico').getSelection()[0];
                            //
                            //                                 Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().select(store_vehiculo.find('id', selection.data.vehiculo));
                            //
                            //                             }
                            //
                            //                         }
                            //                     }
                            //
                            //                 },
                            //                 {
                            //                     xtype: 'container',
                            //                     layout: 'vbox',
                            //                     width: '100%',
                            //                     items: [
                            //                         {
                            //                             xtype: 'container',
                            //                             layout: 'hbox',
                            //                             items: [
                            //                                 {
                            //                                     xtype: 'datefield',
                            //                                     name: 'fecha_anexo',
                            //                                     margin: '10 10 10 10',
                            //                                     id: 'fecha_anexo',
                            //                                     fieldLabel: 'Fecha del Anexo Único',
                            //                                     labelAlign: 'top',
                            //                                     allowBlank: false,
                            //                                     afterLabelTextTpl: [
                            //                                         '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            //                                     ],
                            //                                 },
                            //                                 {
                            //                                     xtype: 'combobox',
                            //                                     name: 'persona',
                            //                                     id: 'personaid',
                            //                                     margin: '10 10 10 10',
                            //                                     fieldLabel: 'Habilitado por',
                            //                                     labelWidth: 90,
                            //                                     labelAlign: 'top',
                            //                                     displayField: 'nombre',
                            //                                     valueField: 'id',
                            //                                     typeAhead: true,
                            //                                     forceSelection: true,
                            //                                     triggerAction: 'all',
                            //                                     queryMode: 'local',
                            //                                     editable: true,
                            //                                     emptyText: 'Seleccione la persona',
                            //                                     selectOnFocus: true,
                            //                                     allowBlank: false,
                            //                                     store: store_persona
                            //                                 }, //ya
                            //                             ]
                            //                         },
                            //                         {
                            //                             xtype: 'fieldset',
                            //                             title: 'Datos cierre mes anterior verificado',
                            //                             columnWidth: .5,
                            //                             margin: '10 10 10 10',
                            //                             layout: 'fit',
                            //                             defaults: {
                            //                                 margin: '0 5 5 0',
                            //                                 allowBlank: false,
                            //                                 decimalSeparator: '.'
                            //                             },
                            //                             items: [
                            //                                 {
                            //                                     xtype: 'numberfield',
                            //                                     name: 'kilometraje_mes_anterior',
                            //                                     id: 'kilometraje_mes_anterior_id',
                            //                                     fieldLabel: 'Kilometraje',
                            //                                     minValue: 0,
                            //                                     value: 0,
                            //                                     listeners: {
                            //                                         change: function (This, newValue) {
                            //                                             var total_km = Ext.getCmp('kilometraje_id').getValue() - newValue;
                            //                                             if (total_km >= 0) {
                            //                                                 Ext.getCmp('km_total_recorrido_id').setValue(total_km);
                            //
                            //                                                 var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                            //                                                 if (newValue != 0 && total_comb != 0) {
                            //                                                     Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                            //                                                     var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                            //                                                     var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                            //                                                     Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                            //                                                     Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                            //                                                 }
                            //                                             }
                            //                                         }
                            //                                     } //ya
                            //                                 },
                            //                                 {
                            //                                     xtype: 'numberfield',
                            //                                     name: 'combustible_estimado_tanque',
                            //                                     id: 'combustible_estimado_tanque_id',
                            //                                     fieldLabel: 'Combustible estimado tanque',
                            //                                     minValue: 0,
                            //                                     value: 0,
                            //                                     listeners: {
                            //                                         change: function (This, newValue) {
                            //
                            //                                             var cant_comb_abastec = 0;
                            //                                             Ext.Array.each(Ext.getStore('combustibleKilometrosStoreId').data.items, function (value) {
                            //                                                 cant_comb_abastec += value.data.comb_abast;
                            //                                             })
                            //
                            //                                             var total_comb = Ext.getCmp('combustible_estimado_tanque_id').getValue() + cant_comb_abastec - Ext.getCmp('comb_estimado_tanke_id').getValue();
                            //                                             Ext.getCmp('comb_total_consumido_id').setValue(total_comb);
                            //
                            //                                             if (newValue != 0 && total_comb != 0) {
                            //                                                 Ext.getCmp('indice_real_id').setValue(total_comb !== null ? Ext.getCmp('km_total_recorrido_id').getValue() / total_comb : 0);
                            //                                                 var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                            //                                                 // var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                            //                                                 Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                            //                                                 // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                            //                                             }
                            //                                         }
                            //                                     } //ya
                            //                                 }
                            //                             ]
                            //                         },
                            //                         {
                            //                             xtype: 'fieldset',
                            //                             title: 'Próximo mantenimiento verificado',
                            //                             columnWidth: .7,
                            //                             margin: '50 10 10 10',
                            //                             layout: 'fit',
                            //                             defaults: {
                            //                                 margin: '0 5 5 0',
                            //                                 allowBlank: false,
                            //                                 decimalSeparator: '.',
                            //                                 labelWidth: 150
                            //                             },
                            //                             items: [
                            //                                 {
                            //                                     xtype: 'numberfield',
                            //                                     name: 'kilometraje_proximo_mantenimiento',
                            //                                     id: 'kilometraje_proximo_mantenimiento_id',
                            //                                     fieldLabel: 'Kilometraje próximo mantenimiento',
                            //                                     minValue: 0,
                            //                                     value: 0 //ya
                            //                                 },
                            //                                 {
                            //                                     xtype: 'numberfield',
                            //                                     name: 'norma_plan',
                            //                                     id: 'norma_plan_id',
                            //                                     fieldLabel: 'Índice de consumo plan (Km/l)',
                            //                                     minValue: 0,
                            //                                     value: 0 //ya
                            //                                 }
                            //                             ]
                            //                         },
                            //                     ]
                            //                 }
                            //             ]
                            //         },
                            //         {
                            //             xtype: 'panel',
                            //             title: 'Combustible y kilómetros',
                            //             width: '100%',
                            //             height: 380,
                            //             bodyPadding: 10,
                            //             closable: false,
                            //             layout: 'fit',
                            //             defaults: {
                            //                 margin: '0 5 0 0'
                            //             },
                            //             items: [
                            //                 {
                            //                     xtype: 'gridpanel',
                            //                     id: 'gridCombustibleKilometrosId',
                            //                     height: 350,
                            //                     store: Ext.create('Ext.data.Store', {
                            //                         id: 'combustibleKilometrosStoreId',
                            //                         model: 'CombustibleKilometros',
                            //                         proxy: {
                            //                             type: 'ajax',
                            //                             url: App.buildURL('/portadores/anexo_unico/loadCombKilometros'),
                            //                             reader: {
                            //                                 rootProperty: 'grid'
                            //                             }
                            //                         },
                            //                         autoLoad: false
                            //                     }),
                            //                     forceFit: true,
                            //                     enableColumnHide: false,
                            //                     plugins: {
                            //                         ptype: 'rowediting',
                            //                         clicksToEdit: 1,
                            //
                            //                     },
                            //                     columns: [
                            //                         {
                            //                             text: '<strong>Fecha</strong>',
                            //                             flex: 15,
                            //                             dataIndex: 'fecha',
                            //                             editor: {
                            //                                 xtype: 'datefield',
                            //
                            //                                 // Ext.util.Format.date(field, 'd/m/Y H:i:s');
                            //                             },
                            //                             // formatter: 'date("d/m/Y")',
                            //                             renderer: function (value) {
                            //                                 console.log(value)
                            //                                 return Ext.Date.format(value, 'd/m/Y')
                            //                                 // return value
                            //
                            //                             },
                            //                         },
                            //                         {
                            //                             text: '<strong>Nro tarjeta</strong>',
                            //                             flex: 15,
                            //                             dataIndex: 'nro_tarjeta',
                            //                             editor: Ext.create('Ext.form.field.ComboBox', {
                            //                                 typeAhead: true,
                            //                                 triggerAction: 'all',
                            //                                 displayField: 'nro_tarjeta',
                            //                                 store: Ext.create('Ext.data.Store', {
                            //                                     storeId: 'tarjetasStoreId',
                            //                                     fields: [
                            //                                         {name: 'id'},
                            //                                         {name: 'nro_tarjeta'}
                            //                                     ],
                            //                                     proxy: {
                            //                                         type: 'ajax',
                            //                                         url: App.buildURL('/portadores/tarjeta/loadTarjeta'),
                            //                                         reader: {
                            //                                             rootProperty: 'rows'
                            //                                         }
                            //                                     },
                            //                                     pageSize: 1000,
                            //                                     autoLoad: true,
                            //                                     listeners: {
                            //                                         beforeload: function (This, operation) {
                            //                                             operation.setParams({
                            //                                                 nunidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                            //                                             })
                            //                                         }
                            //                                     }
                            //                                 })
                            //                             })
                            //                         },
                            //                         {
                            //                             text: '<strong>Kilometraje</strong>',
                            //                             flex: 15,
                            //                             dataIndex: 'kilometraje',
                            //                             editor: Ext.create('Ext.form.field.Number', {
                            //                                 decimalSeparator: '.',
                            //                                 minValue: 0
                            //                             })
                            //                         },
                            //                         {
                            //                             text: '<strong>Comb. abastecido</strong>',
                            //                             flex: 22.5,
                            //                             dataIndex: 'comb_abast',
                            //                             editor: Ext.create('Ext.form.field.Number', {
                            //                                 decimalSeparator: '.',
                            //                                 minValue: 0
                            //                             })
                            //                         },
                            //                         {
                            //                             text: '<strong>Comb. estim. en tanque</strong>',
                            //                             flex: 22.5,
                            //                             dataIndex: 'comb_est_tanke',
                            //                             editor: Ext.create('Ext.form.field.Number', {
                            //                                 decimalSeparator: '.',
                            //                                 minValue: 0
                            //                             })
                            //                         }
                            //                     ],
                            //                     tbar: [
                            //                         {
                            //                             text: 'Adicionar',
                            //                             iconCls: 'fa fa-plus-square-o fa-1_4',
                            //                             handler: function () {
                            //                                 Ext.getCmp('gridCombustibleKilometrosId').getStore().add({
                            //                                     id: '',
                            //                                     fecha: new Date(),
                            //                                     nro_tarjeta: '',
                            //                                     kilometraje: 0,
                            //                                     comb_abast: 0,
                            //                                     comb_est_tanke: 0
                            //                                 });
                            //                             }
                            //                         },
                            //                         '-',
                            //                         {
                            //                             text: 'Eliminar',
                            //                             iconCls: 'fa fa-minus-square-o fa-1_4',
                            //                             handler: function () {
                            //                                 var record = Ext.getCmp('gridCombustibleKilometrosId').getSelectionModel().getLastSelected();
                            //                                 Ext.getCmp('gridCombustibleKilometrosId').getStore().remove(record);
                            //                             }
                            //                         }
                            //                     ], //ya
                            //                     // listeners: {
                            //                     //     beforerender: function (This) {
                            //                     //
                            //                     //     }
                            //                     // }
                            //                 }
                            //             ]
                            //         },
                            //
                            //     ]
                            // },
                            // {
                            //     xtype: 'fieldset',
                            //     title: 'Resumen',
                            //     width: '100%',
                            //     height: 440,
                            //     // height: '100%z',
                            //     // margin: '10 10 10 10',
                            //     layout: 'column', // arrange fieldsets side by side
                            //     // layout: 'vbox',
                            //     items: [
                            //         {
                            //             xtype: 'container',
                            //             width: '100%',
                            //             // layout: 'hbox',
                            //             layout: 'column',
                            //             // columnWidth: 0.5,
                            //             items: [
                            //                 {
                            //                     xtype: 'fieldset',
                            //                     title: 'Datos del cierre del mes',
                            //                     columnWidth: 0.65,
                            //                     // margin: '10 20 20 5',
                            //                     layout: 'fit',
                            //                     defaults: {
                            //                         margin: '0 5 5 0',
                            //                         allowBlank: false,
                            //                         decimalSeparator: '.',
                            //                         labelWidth: 130
                            //                     },
                            //                     items: [
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'kilometraje',
                            //                             id: 'kilometraje_id',
                            //                             fieldLabel: 'Kilometraje',
                            //                             minValue: 0,
                            //                             value: 0,
                            //                             listeners: {
                            //                                 change: function (This, newValue) {
                            //                                     let total_km = newValue - Ext.getCmp('kilometraje_mes_anterior_id').getValue();
                            //                                     Ext.getCmp('km_total_recorrido_id').setValue(total_km);
                            //                                     Ext.getStore('id_store_tipo_mantenimiento').load({
                            //                                         params: {
                            //                                             id: Ext.getCmp('id_grid_vehiculos_anexo').getSelection()[0].data.id,
                            //                                             kilometraje: Ext.getCmp('kilometraje_id').getValue(),
                            //                                         }
                            //                                     });
                            //
                            //
                            //                                     // var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                            //                                     // var cant_comb = total_km / Ext.getCmp('norma_plan_id').getValue();
                            //                                     let total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                            //                                     if (newValue != 0 && total_comb != 0) {
                            //                                         Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                            //                                         var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue());
                            //                                         // var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                            //                                         Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                            //                                         // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                            //                                     }
                            //
                            //                                 }
                            //                             } //ya
                            //                         },
                            //
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'comb_estimado_tanke',
                            //                             id: 'comb_estimado_tanke_id',
                            //                             fieldLabel: 'Comb. estimado en tanque',
                            //                             minValue: 0,
                            //                             value: 0,
                            //                             listeners: {
                            //                                 change: function (This, newValue, oldValue) {
                            //                                     var store_abastecidos = Ext.getStore('combustibleKilometrosStoreId');
                            //                                     total_abastecidos = 0;
                            //                                     store_abastecidos.each(function (record) {
                            //                                         total_abastecidos += record.data.comb_abast;
                            //                                     });
                            //
                            //                                     //Lo abastecido segun economia
                            //                                     var cant_comb_abastec = total_abastecidos;
                            //
                            //                                     var total_km = Ext.getCmp('km_total_recorrido_id').getValue();
                            //                                     //TOTAL DE COMBUSTIBLE CONSUMIDO  = COMBUS QUE INICIA  + LO abastecido segun economia -COMB QUE QUEDO EN TANQUE
                            //                                     var total_comb = Ext.getCmp('combustible_estimado_tanque_id').getValue() + cant_comb_abastec - Ext.getCmp('comb_estimado_tanke_id').getValue();
                            //                                     Ext.getCmp('comb_total_consumido_id').setValue(total_comb);
                            //
                            //                                     if (newValue != 0 && total_km != 0) {
                            //                                         Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                            //                                         var porciento_real = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue());
                            //                                         // var porciento_real_ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                            //                                         Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento_real);
                            //                                         // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento_real_);
                            //                                     }
                            //                                 }
                            //                             } //ya
                            //                         }
                            //                     ]
                            //                 },
                            //                 {
                            //                     xtype: 'fieldset',
                            //                     columnWidth: 0.35,
                            //                     layout: 'fit',
                            //                     title: 'Totales',
                            //                     margin: '0 0 0 10',
                            //                     defaults: {
                            //                         margin: '0 5 5 0',
                            //                         allowBlank: false,
                            //                         decimalSeparator: '.',
                            //                         labelWidth: 125,
                            //                         // labelAlign:'top'
                            //                     },
                            //                     items: [
                            //                         // {
                            //                         //
                            //                         //     xtype: 'numberfield',
                            //                         //     name: 'combustible_total_abastecido',
                            //                         //     id: 'abastecido_id',
                            //                         //     // readOnly: true,
                            //                         //     fieldLabel: 'Comb. abastecido',
                            //                         //     minValue: 0,
                            //                         //     value: 0
                            //                         // },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'km_total_recorrido',
                            //                             id: 'km_total_recorrido_id',
                            //                             fieldLabel: 'Km total recorrido', //ya
                            //                             // readOnly: true
                            //                         },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'combustible_total_consumido',
                            //                             id: 'comb_total_consumido_id',
                            //                             fieldLabel: 'Comb. total consumido',
                            //                             readOnly: false //ya
                            //                         }
                            //                     ]
                            //                 },
                            //             ]
                            //         },
                            //         {
                            //             xtype: 'container',
                            //             width: '100%',
                            //             layout: 'column',
                            //             items: [
                            //                 {
                            //                     xtype: 'fieldset',
                            //                     title: 'Índice de Consumo Real (Km/l) ',
                            //                     columnWidth: 0.65,
                            //                     margin: '10 20 20 5',
                            //                     layout: 'fit',
                            //                     defaults: {
                            //                         margin: '5 5 5 0',
                            //                         allowBlank: false,
                            //                         decimalSeparator: '.',
                            //                         labelWidth: 100
                            //                     },
                            //                     items: [
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'indice_real',
                            //                             id: 'indice_real_id',
                            //                             fieldLabel: 'Real',
                            //                             readOnly: true //ya
                            //                         },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'por_ciento_indice_real_plan',
                            //                             id: 'por_ciento_indice_real_plan_id',
                            //                             fieldLabel: '% Real/Plan',
                            //                             readOnly: true //ya
                            //                         },
                            //                         // {
                            //                         //     xtype: 'numberfield',
                            //                         //     name: 'indice_real_plan',
                            //                         //     id: 'por_ciento_indice_real_plan_id__',
                            //                         //     fieldLabel: '         ',
                            //                         //     readOnly: true
                            //                         // }
                            //                     ]
                            //                 },
                            //                 {
                            //                     xtype: 'fieldset',
                            //                     title: 'Mantenimiento',
                            //                     columnWidth: 0.35,
                            //                     margin: '10 0 0 10',
                            //                     defaults: {
                            //                         margin: '0 5 5 0',
                            //                         allowBlank: false,
                            //                         decimalSeparator: '.',
                            //                         labelWidth: 110
                            //                     },
                            //                     items: [
                            //                         {
                            //                             xtype: 'combobox',
                            //                             name: 'tipo_mantenimiento_id',
                            //                             id: 'combo_tipo_mantenimiento_id_id',
                            //                             fieldLabel: 'Tipo de Mantenimiento',
                            //                             store: Ext.getStore('id_store_tipo_mantenimiento'),
                            //                             displayField: 'nombre',
                            //                             valueField: 'id',
                            //                             // typeAhead: true,
                            //                             queryMode: 'local',
                            //                             forceSelection: false,
                            //                             // triggerAction: 'all',
                            //                             allowBlank: true,
                            //                             emptyText: 'Seleccione mantenimiento...',
                            //                             // selectOnFocus: true,
                            //                             editable: true,
                            //                             listeners: {
                            //                                 select: function (combo, records, eOpts) {
                            //
                            //                                     Ext.getCmp('kilometraje_mantenimiento_id').setValue(records.data.kilometros);
                            //                                     var kilometros_planf = Ext.getCmp('kilometros_plannif').getValue();
                            //
                            //                                     var km_proximo_mant = Ext.getCmp('kilometraje_mantenimiento_id').getValue() + kilometros_planf;
                            //                                     Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(km_proximo_mant);
                            //                                     // if (records) {
                            //                                     //     Ext.getCmp('kilometros_plannif').setValue(response..kilometros);
                            //                                     //     App.request('GET', App.buildURL('/portadores/anexo_unico/loadTipoMantenimientoBy'), {id: Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id}, null, null,
                            //                                     //         function (response) {
                            //                                     //             if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                            //                                     //                 if (response.total != 0) {
                            //                                     //                     Ext.getCmp('kilometraje_mantenimiento_id').setReadOnly(false);
                            //                                     //
                            //                                     //                 } else {
                            //                                     //                     App.showAlert('El vehículo seleccionado no tiene asociado Mantenimientos', 'warning');
                            //                                     //                 }
                            //                                     //
                            //                                     //             }
                            //                                     //         }, null, null, true
                            //                                     //     );
                            //                                     //
                            //                                     //     // var datos_vehiculos_mantenimientos = App.PerformSyncServerRequest(Routing.generate('loadTipoMantenimientoByAnexoUnico'), {id: Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id});
                            //                                     //     // console.log(datos_vehiculos_mantenimientos)
                            //                                     //     // if (datos_vehiculos_mantenimientos) {
                            //                                     //     //     Ext.getCmp('kilometraje_mantenimiento_id').setReadOnly(false);
                            //                                     //     //
                            //                                     //     //     if (datos_vehiculos_mantenimientos.total != 0) {
                            //                                     //     //         Ext.getCmp('kilometros_plannif').setValue(datos_vehiculos_mantenimientos.rows.kilometros);
                            //                                     //     //
                            //                                     //     //
                            //                                     //     //     } else {
                            //                                     //     //         App.InfoMessage('Información', 'El vehiculo Seleccionado no tiene asociado Mantenimientos', 'warning');
                            //                                     //     //     }
                            //                                     //     // }
                            //                                     // }
                            //                                 },
                            //
                            //                             } //ya
                            //                         },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'kilometraje_mantenimiento',
                            //                             id: 'kilometraje_mantenimiento_id',
                            //                             fieldLabel: 'Kilometraje',
                            //                             minValue: 0,
                            //                             value: 0,
                            //                             // readOnly: true,
                            //                             listeners: {
                            //                                 change: function (This, newValue) {
                            //                                     console.log(Ext.getStore('id_store_tipo_mantenimiento').data.length)
                            //                                     if (Ext.getStore('id_store_tipo_mantenimiento').getData().length > 0) {
                            //
                            //                                         // if (This.isDisabled())
                            //                                         // This.setDisabled(false);
                            //
                            //                                         var kilometros_planf = Ext.getCmp('kilometros_plannif').getValue();
                            //                                         let id = Ext.getCmp('combo_tipo_mantenimiento_id_id').getValue();
                            //                                         let mantenimiento = Ext.getStore('id_store_tipo_mantenimiento').findRecord('id', id);
                            //                                         Ext.getCmp('variacion').setValue(newValue / mantenimiento.data.kilometros);
                            //                                         if (kilometros_planf) {
                            //                                             var km_proximo_mant = newValue + kilometros_planf;
                            //                                             Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(km_proximo_mant);
                            //                                         }
                            //                                     }
                            //                                 }
                            //                             } //ya
                            //                         },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             name: 'variacion',
                            //                             id: 'variacion',
                            //                             fieldLabel: 'Variación',
                            //                             readOnly: true //ya
                            //                         },
                            //                         {
                            //                             xtype: 'numberfield',
                            //                             id: 'kilometros_plannif',
                            //                             name: 'kilometros_plannif',
                            //                             hidden: true,
                            //                             value: 0 //ya
                            //
                            //                         }
                            //                     ]
                            //                 }
                            //             ]
                            //         },
                            //         {
                            //             xtype: 'container',
                            //             width: '100%',
                            //             items: [
                            //                 {
                            //                     xtype: 'textareafield',
                            //                     name: 'observaciones',
                            //                     id: 'observaciones_id',
                            //                     grow: false,
                            //                     fieldLabel: 'Observaciones',
                            //                     labelAlign: 'top',
                            //                     width: '95%',
                            //                     margin: '0 0 0 5', //ya
                            //                 }
                            //             ]
                            //         }
                            //     ]
                            // },


                            /** AQUIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII */
                            {
                                xtype: 'tabpanel',
                                width: '100%',
                                height: 380,
                                activeTab: 0,
                                border: true,
                                // autoDestroy: false,
                                items: [
                                    {
                                        xtype: 'panel',
                                        title: 'Datos generales',
                                        bodyPadding: 10,
                                        closable: false,
                                        width: '100%',
                                        height: 380,
                                        layout: 'column',
                                        defaults: {
                                            margin: '0 5 0 0'
                                        },
                                        items: [
                                            {
                                                xtype: 'fieldcontainer',
                                                layout: 'hbox',
                                                columnWidth: 1,
                                                defaults: {
                                                    margin: '5 20 5 20'
                                                },
                                                items: [

                                                    {
                                                        xtype: 'combo',
                                                        name: 'vehiculo',
                                                        id: 'vehiculoid',
                                                        // columnWidth: .40,
                                                        fieldLabel: 'Matrícula',
                                                        labelWidth: 60,
                                                        width: 110,
                                                        labelAlign: 'top',
                                                        displayField: 'matricula',
                                                        valueField: 'id',
                                                        typeAhead: true,
                                                        queryMode: 'local',
                                                        editable: true,
                                                        forceSelection: true,
                                                        triggerAction: 'all',
                                                        emptyText: 'Seleccione...',
                                                        selectOnFocus: true,
                                                        allowBlank: false,
                                                        store: store_vehiculo,
                                                        listeners: {
                                                            select: function (combo, record) {
                                                                combo.up('form').getForm().reset();
                                                                combo.select(record);
                                                                Ext.getCmp('norma_plan_id').setValue(record.data.norma);
                                                                // Ext.getStore('id_store_tipo_mantenimiento').load();

                                                                if (record.data.odometro == false) {
                                                                    Ext.getCmp('kilometraje_mes_anterior_id').disable();
                                                                    Ext.getCmp('kilometraje_id').disable();
                                                                }

                                                                App.request('GET', App.buildURL('/portadores/anexo_unico/getLastAnexoVehiculo'), {idvehiculo: record.data.id}, null, null,
                                                                    function (response) {
                                                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                            // console.log(response)
                                                                            // console.log(response.rows.kilometrajeCierreMes)
                                                                            Ext.getCmp('kilometraje_mes_anterior_id').setValue(response.rows.kilometrajeCierreMes);
                                                                            Ext.getCmp('combustible_estimado_tanque_id').setValue(response.rows.combustibleEstimadoTanqueCierre);
                                                                            tipo_manteniento = response.rows.tipo_mantenimiento;
                                                                            Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(response.rows.kilometraje_proximo_Mantenimiento);
                                                                        } else {
                                                                            Ext.getCmp('kilometraje_mes_anterior_id').setValue(0);
                                                                            Ext.getCmp('combustible_estimado_tanque_id').setValue(0);
                                                                            tipo_manteniento = null;
                                                                            Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(0);
                                                                        }
                                                                    }, null, null, true
                                                                );
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'datefield',
                                                        name: 'fecha_anexo',
                                                        // margin: '10 10 10 10',
                                                        id: 'fecha_anexo',
                                                        fieldLabel: 'Fecha del Anexo Único',
                                                        labelAlign: 'top',
                                                        allowBlank: false,
                                                        afterLabelTextTpl: [
                                                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                        ],
                                                    },
                                                    {
                                                        xtype: 'combobox',
                                                        name: 'persona',
                                                        id: 'personaid',
                                                        fieldLabel: 'Habilitado por',
                                                        // columnWidth: .60,
                                                        labelWidth: 85,
                                                        width: 250,
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
                                                        store: store_persona
                                                    },
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Datos cierre mes anterior verificado',
                                                columnWidth: .5,
                                                margin: '10 5 0 0',
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
                                                        value: 0,
                                                        listeners: {
                                                            change: function (This, newValue) {
                                                                var total_km = Ext.getCmp('kilometraje_id').getValue() - newValue;
                                                                if (total_km >= 0) {
                                                                    Ext.getCmp('km_total_recorrido_id').setValue(total_km);

                                                                    var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                                                                    if (newValue != 0 && total_comb != 0) {
                                                                        Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                                                                        var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                                                                        var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                        Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                                                                        Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'combustible_estimado_tanque',
                                                        id: 'combustible_estimado_tanque_id',
                                                        fieldLabel: 'Combustible estimado tanque',
                                                        minValue: 0,
                                                        value: 0,
                                                        listeners: {
                                                            change: function (This, newValue) {

                                                                var cant_comb_abastec = 0;
                                                                Ext.Array.each(Ext.getStore('combustibleKilometrosStoreId').data.items, function (value) {
                                                                    cant_comb_abastec += value.data.comb_abast;
                                                                })

                                                                var total_comb = Ext.getCmp('combustible_estimado_tanque_id').getValue() + cant_comb_abastec - Ext.getCmp('comb_estimado_tanke_id').getValue();
                                                                Ext.getCmp('comb_total_consumido_id').setValue(total_comb);

                                                                if (newValue != 0 && total_comb != 0) {
                                                                    Ext.getCmp('indice_real_id').setValue(total_comb !== null ? Ext.getCmp('km_total_recorrido_id').getValue() / total_comb : 0);
                                                                    var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100;
                                                                    // var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                                                                    // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
                                                                }
                                                            }
                                                        }
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Próximo mantenimiento verificado',
                                                columnWidth: .5,
                                                margin: '10 0 0 0',
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
                                                        fieldLabel: 'Kilometraje próximo mantenimiento',
                                                        minValue: 0,
                                                        value: 0
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'norma_plan',
                                                        id: 'norma_plan_id',
                                                        fieldLabel: 'Índice de consumo plan (Km/l)',
                                                        minValue: 0,
                                                        value: 0,
                                                        readOnly:true,
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'textareafield',
                                                name: 'observaciones',
                                                id: 'observaciones_id',
                                                grow: false,
                                                fieldLabel: 'Observaciones',
                                                labelAlign: 'top',
                                                margin: '0',
                                                height: '40%',
                                                columnWidth: 1
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'panel',
                                        title: 'Combustible y kilómetros',
                                        width: '100%',
                                        height: 380,
                                        bodyPadding: 10,
                                        closable: false,
                                        layout: 'fit',
                                        defaults: {
                                            margin: '0 5 0 0'
                                        },
                                        items: [
                                            {
                                                xtype: 'gridpanel',
                                                id: 'gridCombustibleKilometrosId',
                                                height: 340,
                                                store: Ext.create('Ext.data.Store', {
                                                    id: 'combustibleKilometrosStoreId',
                                                    model: 'CombustibleKilometros',
                                                    proxy: {
                                                        type: 'ajax',
                                                        url: App.buildURL('/portadores/anexo_unico/loadCombKilometros'),
                                                        reader: {
                                                            rootProperty: 'rows'
                                                        }
                                                    },
                                                    autoLoad: false
                                                }),
                                                forceFit: true,
                                                enableColumnHide: false,
                                                plugins: {
                                                    ptype: 'rowediting',
                                                    clicksToEdit: 1,

                                                },
                                                columns: [
                                                    {
                                                        text: '<strong>Fecha</strong>',
                                                        flex: 1,
                                                        dataIndex: 'fecha',
                                                        editor: {
                                                            xtype: 'datefield',

                                                            // Ext.util.Format.date(field, 'd/m/Y H:i:s');
                                                        },
                                                        // formatter: 'date("d/m/Y")',
                                                        renderer: function (value) {
                                                            console.log(value)
                                                            return Ext.Date.format(value, 'd/m/Y')
                                                            // return value

                                                        },
                                                    },
                                                    {
                                                        text: '<strong>Nro tarjeta</strong>',
                                                        flex: 1,
                                                        dataIndex: 'nro_tarjeta',
                                                        editor: Ext.create('Ext.form.field.ComboBox', {
                                                            typeAhead: true,
                                                            triggerAction: 'all',
                                                            displayField: 'nro_tarjeta',
                                                            queryMode: 'local',
                                                            store: Ext.create('Ext.data.Store', {
                                                                storeId: 'tarjetasStoreId',
                                                                fields: [
                                                                    {name: 'id'},
                                                                    {name: 'nro_tarjeta'}
                                                                ],
                                                                proxy: {
                                                                    type: 'ajax',
                                                                    url: App.buildURL('/portadores/tarjeta/loadTarjeta'),
                                                                    reader: {
                                                                        rootProperty: 'rows'
                                                                    }
                                                                },
                                                                pageSize: 1000,
                                                                autoLoad: true,
                                                                listeners: {
                                                                    beforeload: function (This, operation) {
                                                                        operation.setParams({
                                                                            nunidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                                                        })
                                                                    }
                                                                }
                                                            })
                                                        })
                                                    },
                                                    {
                                                        text: '<strong>Kilometraje</strong>',
                                                        flex: 1,
                                                        dataIndex: 'kilometraje',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            decimalSeparator: '.',
                                                            minValue: 0
                                                        })
                                                    },
                                                    {
                                                        text: '<strong>Comb. abastecido</strong>',
                                                        flex: 1,
                                                        dataIndex: 'comb_abast',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            decimalSeparator: '.',
                                                            minValue: 0
                                                        })
                                                    },
                                                    {
                                                        text: '<strong>Comb. estimado <br>en tanque</strong>',
                                                        flex: 1,
                                                        dataIndex: 'comb_est_tanke',
                                                        editor: Ext.create('Ext.form.field.Number', {
                                                            decimalSeparator: '.',
                                                            minValue: 0
                                                        })
                                                    }
                                                ],
                                                bbar: {
                                                    xtype: 'pagingtoolbar'
                                                },
                                                tbar: [
                                                    {
                                                        text: 'Adicionar',
                                                        iconCls: 'fas fa-plus-square text-primary',
                                                        handler: function () {
                                                            Ext.getCmp('gridCombustibleKilometrosId').getStore().add({
                                                                id: '',
                                                                fecha: new Date(),
                                                                nro_tarjeta: '',
                                                                kilometraje: 0,
                                                                comb_abast: 0,
                                                                comb_est_tanke: 0
                                                            });
                                                        }
                                                    },
                                                    '-',
                                                    {
                                                        text: 'Eliminar',
                                                        iconCls: 'fas fa-minus-square text-primary',
                                                        handler: function () {
                                                            var record = Ext.getCmp('gridCombustibleKilometrosId').getSelectionModel().getLastSelected();
                                                            Ext.getCmp('gridCombustibleKilometrosId').getStore().remove(record);
                                                        }
                                                    }
                                                ],
                                                // listeners: {
                                                //     beforerender: function (This) {
                                                //
                                                //     }
                                                // }
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'panel',
                                        title: 'Resumen',
                                        bodyPadding: 10,
                                        closable: false,
                                        width: '100%',
                                        height: 380,
                                        layout: 'column',
                                        defaults: {
                                            margin: '0 5 0 0'
                                        },
                                        items: [
                                            {
                                                xtype: 'fieldset',
                                                title: 'Datos del cierre del mes',
                                                columnWidth: .5,
                                                margin: '10 5 0 0',
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
                                                        name: 'kilometraje',
                                                        id: 'kilometraje_id',
                                                        fieldLabel: 'Kilometraje',
                                                        minValue: 0,
                                                        value: 0,
                                                        listeners: {
                                                            change: function (This, newValue) {
                                                                let total_km = newValue - Ext.getCmp('kilometraje_mes_anterior_id').getValue();
                                                                Ext.getCmp('km_total_recorrido_id').setValue(total_km);
                                                                Ext.getStore('id_store_tipo_mantenimiento').load({
                                                                    params: {
                                                                        id: Ext.getCmp('vehiculoid').getValue(),
                                                                        kilometraje: Ext.getCmp('kilometraje_id').getValue(),
                                                                    }
                                                                });


                                                                // var total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                                                                // var cant_comb = total_km / Ext.getCmp('norma_plan_id').getValue();
                                                                let total_comb = Ext.getCmp('comb_total_consumido_id').getValue();
                                                                if (newValue != 0 && total_comb != 0) {
                                                                    Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                                                                    var porciento = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue());
                                                                    // var porciento__ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento);
                                                                    // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento__);
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
                                                                var store_abastecidos = Ext.getStore('combustibleKilometrosStoreId');
                                                                total_abastecidos = 0;
                                                                store_abastecidos.each(function (record) {
                                                                    total_abastecidos += record.data.comb_abast;
                                                                });

                                                                //Lo abastecido segun economia
                                                                var cant_comb_abastec = total_abastecidos;

                                                                var total_km = Ext.getCmp('km_total_recorrido_id').getValue();
                                                                //TOTAL DE COMBUSTIBLE CONSUMIDO  = COMBUS QUE INICIA  + LO abastecido segun economia -COMB QUE QUEDO EN TANQUE
                                                                var total_comb = Ext.getCmp('combustible_estimado_tanque_id').getValue() + cant_comb_abastec - Ext.getCmp('comb_estimado_tanke_id').getValue();
                                                                Ext.getCmp('comb_total_consumido_id').setValue(total_comb);

                                                                if (newValue != 0 && total_km != 0) {
                                                                    Ext.getCmp('indice_real_id').setValue(total_comb !== null ? total_km / total_comb : 0);
                                                                    var porciento_real = (Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue());
                                                                    // var porciento_real_ = 100 - ((Ext.getCmp('indice_real_id').getValue() / Ext.getCmp('norma_plan_id').getValue()) * 100);
                                                                    Ext.getCmp('por_ciento_indice_real_plan_id').setValue(porciento_real);
                                                                    // Ext.getCmp('por_ciento_indice_real_plan_id__').setValue(porciento_real_);
                                                                }
                                                            }
                                                        }
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Totales del mes',
                                                layout: 'fit',
                                                columnWidth: .5,
                                                margin: '10 5 0 0',
                                                defaults: {
                                                    margin: '0 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 150
                                                },
                                                items: [
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
                                                        readOnly: true
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Índice de Consumo Real (Km/l) ',
                                                columnWidth: .5,
                                                margin: '20 0 0 0',
                                                layout: 'fit',
                                                defaults: {
                                                    margin: '5 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 150
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
                                                    },
                                                ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: 'Mantenimiento',
                                                columnWidth: .49,
                                                margin: '-88 0 0 5',
                                                defaults: {
                                                    margin: '0 5 5 0',
                                                    allowBlank: false,
                                                    decimalSeparator: '.',
                                                    labelWidth: 125
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

                                                                Ext.getCmp('kilometraje_mantenimiento_id').setValue(records.data.kilometros);
                                                                var kilometros_planf = Ext.getCmp('kilometros_plannif').getValue();

                                                                var km_proximo_mant = Ext.getCmp('kilometraje_mantenimiento_id').getValue() + kilometros_planf;
                                                                Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(km_proximo_mant);
                                                                // if (records) {
                                                                //     Ext.getCmp('kilometros_plannif').setValue(response..kilometros);
                                                                //     App.request('GET', App.buildURL('/portadores/anexo_unico/loadTipoMantenimientoBy'), {id: Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id}, null, null,
                                                                //         function (response) {
                                                                //             if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                                                //                 if (response.total != 0) {
                                                                //                     Ext.getCmp('kilometraje_mantenimiento_id').setReadOnly(false);
                                                                //
                                                                //                 } else {
                                                                //                     App.showAlert('El vehículo seleccionado no tiene asociado Mantenimientos', 'warning');
                                                                //                 }
                                                                //
                                                                //             }
                                                                //         }, null, null, true
                                                                //     );
                                                                //
                                                                //     // var datos_vehiculos_mantenimientos = App.PerformSyncServerRequest(Routing.generate('loadTipoMantenimientoByAnexoUnico'), {id: Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id});
                                                                //     // console.log(datos_vehiculos_mantenimientos)
                                                                //     // if (datos_vehiculos_mantenimientos) {
                                                                //     //     Ext.getCmp('kilometraje_mantenimiento_id').setReadOnly(false);
                                                                //     //
                                                                //     //     if (datos_vehiculos_mantenimientos.total != 0) {
                                                                //     //         Ext.getCmp('kilometros_plannif').setValue(datos_vehiculos_mantenimientos.rows.kilometros);
                                                                //     //
                                                                //     //
                                                                //     //     } else {
                                                                //     //         App.InfoMessage('Información', 'El vehiculo Seleccionado no tiene asociado Mantenimientos', 'warning');
                                                                //     //     }
                                                                //     // }
                                                                // }
                                                            },

                                                        }
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'kilometraje_mantenimiento',
                                                        id: 'kilometraje_mantenimiento_id',
                                                        fieldLabel: 'Kilometraje',
                                                        minValue: 0,
                                                        value: 0,
                                                        // readOnly: true,
                                                        listeners: {
                                                            change: function (This, newValue) {
                                                                console.log(Ext.getStore('id_store_tipo_mantenimiento').data.length)
                                                                if (Ext.getStore('id_store_tipo_mantenimiento').getData().length > 0) {
                                                                    // if (This.isDisabled())
                                                                    // This.setDisabled(false);
                                                                    var kilometros_planf = Ext.getCmp('kilometros_plannif').getValue();
                                                                    let id = Ext.getCmp('combo_tipo_mantenimiento_id_id').getValue();
                                                                    let mantenimiento = Ext.getStore('id_store_tipo_mantenimiento').findRecord('id', id);
                                                                    Ext.getCmp('variacion').setValue(newValue / mantenimiento.data.kilometros);
                                                                    if (kilometros_planf) {
                                                                        var km_proximo_mant = newValue + kilometros_planf;
                                                                        Ext.getCmp('kilometraje_proximo_mantenimiento_id').setValue(km_proximo_mant);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        name: 'variacion',
                                                        id: 'variacion',
                                                        fieldLabel: 'Variación',
                                                        readOnly: true,
                                                        allowBlank: true,
                                                    },
                                                    {
                                                        xtype: 'numberfield',
                                                        id: 'kilometros_plannif',
                                                        name: 'kilometros_plannif',
                                                        hidden: true,
                                                        value: 0

                                                    }
                                                ]
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
            },
            destroy: function () {
                store_vehiculo.clearFilter();
            }

        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_add',
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        // disabled: true,
        handler: function (This, e) {

            Ext.create('Portadores.anexo_unico.Window', {
                title: 'Adicionar Anexo Único',
                id: 'window_anexo_unico_id',
                listeners: {
                    afterrender: function (This) {

                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_anexo_unico_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                // obj.vehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;

                                var store = Ext.getStore('combustibleKilometrosStoreId');
                                var arr = new Array();

                                for (var i = 0; i < store.data.items.length; i++) {
                                    var comb_kilometros = new Object();
                                    comb_kilometros.fecha = Ext.Date.format(store.data.items[i].data.fecha, 'd/m/Y');
                                    comb_kilometros.nro_tarjeta = store.data.items[i].data.nro_tarjeta;
                                    comb_kilometros.kilometraje = store.data.items[i].data.kilometraje;
                                    comb_kilometros.comb_abast = store.data.items[i].data.comb_abast;
                                    comb_kilometros.comb_est_tanke = store.data.items[i].data.comb_est_tanke;
                                    arr.push(comb_kilometros);
                                }
                                obj.comb_kilometros = Ext.encode(arr);

                                App.request('POST', App.buildURL('/portadores/anexo_unico/add'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_anexo_unico').getStore().load({
                                                params: {
                                                    nunidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                                }
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

                                // App.ShowWaitMsg();
                                // var obj = form.getValues();
                                // obj.vehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;
                                //
                                // // var store = Ext.getStore('combustibleKilometrosStoreId');
                                // // var arr = new Array();
                                // // for(var i = 0; i < store.data.items.length; i++){
                                // //     var comb_kilometros = new Object();
                                // //     comb_kilometros.fecha = Ext.Date.format(store.data.items[i].data.fecha, 'd/m/Y');
                                // //     comb_kilometros.nro_tarjeta = store.data.items[i].data.nro_tarjeta;
                                // //     comb_kilometros.kilometraje = store.data.items[i].data.kilometraje;
                                // //     comb_kilometros.comb_abast = store.data.items[i].data.comb_abast;
                                // //     comb_kilometros.comb_est_tanke = store.data.items[i].data.comb_est_tanke;
                                // //     arr.push(comb_kilometros);
                                // // }
                                // // obj.comb_kilometros = Ext.encode(arr);
                                //
                                //
                                // var _result = App.PerformSyncServerRequest(Routing.generate('addAnexoUnico'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                //     App.InfoMessage('Información', _result.message, _result.cls);
                                // }
                                // else {
                                //     // form.markInvalid(_result.message);
                                //     App.InfoMessage('Información', _result.message, _result.cls);
                                // }
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
        iconCls: 'fas fa-edit text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();

            // var AppConstant = Practice.utilities.AppConstants;
            // console.log(AppConstant.mes);
            var window = Ext.create('Portadores.anexo_unico.Window', {
                title: 'Modificar el Anexo Único',
                id: 'window_anexo_unico_id',
                listeners: {
                    afterrender: function () {
                        // App.request(App.buildURL('/portadores/anexo_unico/loadCombKilometros'), {anexoid: selection.data.id},null,null,
                        //                         //     function (_result) {
                        //                         //         Ext.getCmp('gridCombustibleKilometrosId').getStore().loadData(_result.grid)
                        //                         //     });
                        // console.log('afterrender');
                        // Ext.getCmp('id_grid_vehiculos_anexo').setListeners(
                        //     {
                        //         render: function (This, eOpts) {
                        //             console.log('render')
                        //             var selection = Ext.getCmp('id_grid_anexo_unico').getSelection()[0];
                        //
                        //             Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().select(store_vehiculo.find('id', selection.data.vehiculo));
                        //         }
                        //     });

                    }
                },
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {

                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                var obj = form.getValues();
                                obj.id = selection.data.id;
                                var store = Ext.getCmp('gridCombustibleKilometrosId').getStore();
                                var send = [];
                                Ext.Array.each(store.data.items,function(valor){
                                    send.push(valor.data);
                                });
                                obj.store = Ext.encode(send);

                                App.request('POST', App.buildURL('/portadores/anexo_unico/mod'), obj, null, null,
                                    function (response) { // success_callback
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            window.close();
                                            Ext.getCmp('id_grid_anexo_unico').getStore().load({
                                                params: {
                                                    nunidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
                                                }
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

                                // App.ShowWaitMsg();
                                // var obj = form.getValues();
                                // obj.id = selection.data.id;
                                // obj.vehiculo = Ext.getCmp('id_grid_vehiculos_anexo').getSelectionModel().getLastSelected().data.id;
                                // console.log(obj)
                                // // var store = Ext.getCmp('gridCombustibleKilometrosId').getStore();
                                // // var send = [];
                                // // Ext.Array.each(store.data.items, function (valor) {
                                // //     send.push(valor.data);
                                // // });
                                // // obj.store = Ext.encode(send);
                                // var _result = App.PerformSyncServerRequest(Routing.generate('modAnexoUnico'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     window.close();
                                //     var obj = {};
                                //     Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                //     // obj.mes = AppConstant.mes;
                                //     // obj.nunidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;
                                //     // var resul = App.PerformSyncServerRequest(Routing.generate('loadAnexoUnico'), obj);
                                //     //
                                //     // Ext.getCmp('id_grid_anexo_unico').getStore().loadData(resul.rows);
                                //
                                //     // Ext.getCmp('id_grid_anexo_unico').getStore().load();
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
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
            Ext.getStore('combustibleKilometrosStoreId').load({
                params: {
                    anexoid: Ext.getCmp('id_grid_anexo_unico').getSelection()[0].data.id
                }
            })
            window.show();
            window.down('form').loadRecord(selection);


        }
    });

    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_anexo_unico').getSelectionModel().getLastSelected();
            Ext.Msg.show({
                title: '¿Eliminar Anexo Único?',
                message: '¿Está seguro que desea eliminar el anexo único seleccionado?',
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/anexo_unico/del'), {id: selection.data.id}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_anexo_unico').getStore().load();
                            }
                        });
                    }
                }
            });
        }
    });

    var _print = Ext.create('Ext.button.MyButton', {
        id: 'anexo_unico_btn_print',
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


