
Ext.onReady(function () {
    Ext.define('desglddsdsoseG', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'dia', type: 'date', dateFormat: 'D,j'},
            {name: 'plan_pico', type: 'number'},
            {name: 'plan_diario', type: 'number'}


        ]
    });

    Ext.define('desglose', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nromes', type: 'int'},
            {name: 'mes', type: 'string'},
            {name: 'cant_dias', type: 'int'}
        ]
    });
    let meses = Ext.create('Ext.data.Store', {
        model: 'desglose',
        id: 'id_store_meses',
        data: [
            {id: '1', nromes: 1, mes: 'Enero', cant_dias: 31},
            {id: '2', nromes: 2, mes: 'Febrero', cant_dias: 28},
            {id: '3', nromes: 3, mes: 'Marzo', cant_dias: 31},
            {id: '4', nromes: 4, mes: 'Abril', cant_dias: 31},
            {id: '5', nromes: 5, mes: 'Mayo', cant_dias: 31},
            {id: '6', nromes: 6, mes: 'Junio', cant_dias: 31},
            {id: '7', nromes: 7, mes: 'Julio', cant_dias: 31},
            {id: '8', nromes: 8, mes: 'Agosto', cant_dias: 31},
            {id: '9', nromes: 9, mes: 'Septiembre', cant_dias: 31},
            {id: '10', nromes: 10, mes: 'Octubre', cant_dias: 31},
            {id: '11', nromes: 11, mes: 'Noviembre', cant_dias: 30},
            {id: '12', nromes: 12, mes: 'Diciembre', cant_dias: 31}
        ]
    });
    Ext.Date.patterns = {
        ISO8601Long: "Y-m-d H:i:s",
        ISO8601Short: "Y-m-d",
        ShortDate: "n/j/Y",
        LongDate: "l, F d, Y",
        FullDateTime: "l, F d, Y g:i:s A",
        MonthDay: "F d",
        ShortTime: "g:i A",
        LongTime: "g:i:s A",
        SortableDateTime: "Y-m-d\\TH:i:s",
        UniversalSortableDateTime: "Y-m-d H:i:sO",
        YearMonth: "F, Y"
    };

    let _btn_Ver = Ext.create('Ext.button.MyButton', {
        id: 'desglose_btn_ver',
        text: 'Desglose Plan',
        iconCls: 'fa fa-th fa-1_4',
        // disabled: true,
        width: 130,
        handler: function (This, e) {

            Ext.create('Ext.window.Window', {
                id: 'desglosemeses_id',
                title: 'Desglose por meses y dias',
                height: 405,
                width: 220,
                layout: 'fit',
                items: [{
                    xtype: 'gridpanel',
                    id: 'grid_mesess',
                    frame: true,
                    width: 300,
                    height: 200,
                    store: meses,
                    enableColumnHide: true,
                    columns: [
                        {
                            text: 'Meses',
                            dataIndex: 'mes',
                            flex: 1

                        }
                    ]


                }],

                buttons: [
                    {
                        text: 'Desglosar Mes',
                        width: 120,
                        handler: function () {
                            let window = Ext.getCmp('desglosemeses_id');
                            let selection = Ext.getCmp('grid_mesess').getSelectionModel().getLastSelected();
                            //console.log(selection)
                            let mes = selection.data.nromes;
                            let mes1 = selection.data.mes;
                            //window.close();
                            let result_store_mes = App.PerformSyncServerRequest(Routing.generate('loadDesgloseMesCDA001'), mes);
                            let _result = App.PerformSyncServerRequest(Routing.generate('getCurrentPeriodo'), {});
                            //console.log(mes)
                            let objids = {};
                            objids.nromes = selection.data.nromes;
                            let result_mes = App.PerformSyncServerRequest(Routing.generate('loaddesglosemesDesgloseElectricidad'), objids);
                            if (result_mes.rows.length == 0) {
                                if (selection) {
                                    Ext.create('Ext.window.Window', {
                                        title: 'Desglose ' + 'de ' + selection.data.mes1,
                                        plain: true,
                                        resizable: true,
                                        modal: true,
                                        id: 'ventana_desgloseautomatico',
                                        layout: 'fit',


                                        items: [

                                            {
                                                xtype: 'fieldset',
                                                name: 'container1',
                                                layout: {
                                                    type: 'hbox'
                                                },
                                                items: [

                                                    {
                                                        xtype: 'fieldcontainer',
                                                        name: 'container2',
                                                        fieldLabel: '   ',
                                                        labelAlign: 'top',
                                                        labelSeparator: ' ',
                                                        layout: {
                                                            type: 'vbox'
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'displayfield',
                                                                name: 'dias_habiles',
                                                                //columnWidth: 0.25,
                                                                id: 'dias_habiles',
                                                                margin: '4 4 4 4',
                                                                //width : 200,
                                                                bodyPadding: 10,
                                                                fieldLabel: ' Días Hábiles',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                //afterLabelTextTpl: [
                                                                //    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                //],
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'displayfield',
                                                                name: 'Sabados',
                                                                //columnWidth: 0.25,
                                                                id: 'Sabados',
                                                                margin: '4 4 4 4',
                                                                //width : 200,
                                                                bodyPadding: 10,
                                                                fieldLabel: 'Sabados',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                //afterLabelTextTpl: [
                                                                //    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                //],
                                                                allowBlank: false
                                                            }
                                                            , {
                                                                xtype: 'displayfield',
                                                                name: 'Domingos',
                                                                //columnWidth: 0.25,
                                                                id: 'Domingos',
                                                                margin: '4 4 4 4',
                                                                //width : 200,
                                                                bodyPadding: 10,
                                                                fieldLabel: 'Domingos',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                //afterLabelTextTpl: [
                                                                //    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                //],
                                                                allowBlank: false
                                                            }
                                                            //{

                                                        ]
                                                    },

                                                    {
                                                        xtype: 'fieldcontainer',
                                                        name: 'container3',
                                                        fieldLabel: 'Plan Diario',
                                                        labelAlign: 'top',
                                                        layout: {
                                                            type: 'vbox'
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'diashabiles_diario',
                                                                //columnWidth: 0.25,
                                                                id: 'diashabiles_diario',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: 'Días Hábiles ',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Sabados_diario',
                                                                //columnWidth: 0.25,
                                                                id: 'Sabados_diario',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: 'Sábados',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Domingos_diario',
                                                                //columnWidth: 0.25,
                                                                id: 'Domingos_diario',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: 'Domingos',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }

                                                        ]
                                                    },

                                                    {
                                                        xtype: 'fieldcontainer',
                                                        name: 'container4',
                                                        fieldLabel: 'Plan Pico',
                                                        labelAlign: 'top',
                                                        layout: {
                                                            type: 'vbox'
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'diashabiles_pico',
                                                                //columnWidth: 0.25,
                                                                id: 'diashabiles_pico',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: ' ',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Sabados_pico',
                                                                //columnWidth: 0.25,
                                                                id: 'Sabados_pico',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: 'Sábados',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                name: 'Domingos_pico',
                                                                //columnWidth: 0.25,
                                                                id: 'Domingos_pico',
                                                                margin: '4 4 4 4',
                                                                width: 100,
                                                                //fieldLabel: 'Sábados',
                                                                //labelWidth: 130,
                                                                //hide:true,
                                                                afterLabelTextTpl: [
                                                                    '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                                                                ],
                                                                allowBlank: false
                                                            }

                                                        ]
                                                    }


                                                ]


                                            }


                                        ],
                                        buttons: [
                                            {
                                                text: 'Aceptar',
                                                width: 100,
                                                handler: function () {
                                                    let mes = selection.data.nromes;
                                                    let mes1 = selection.data.mes;
                                                    //console.log(mes)


                                                    //console.log(result_store_mes)
                                                    let dias_diario = parseInt(Ext.getCmp('diashabiles_diario').getValue());

                                                    let sabados_diario = parseInt(Ext.getCmp('Sabados_diario').getValue());

                                                    let domingos_diario = parseInt(Ext.getCmp('Domingos_diario').getValue());

                                                    let dias_pico = parseInt(Ext.getCmp('diashabiles_pico').getValue());
                                                    let Sabados_pico = parseInt(Ext.getCmp('Sabados_pico').getValue());
                                                    let domingos_pico = parseInt(Ext.getCmp('Domingos_pico').getValue());

                                                    let sumaplandiario = dias_diario + sabados_diario + domingos_diario;
                                                    let sumaplanpico = dias_pico + Sabados_pico + domingos_pico;

                                                    //if(sumaplandiario<1000 || sumaplanpico<100)
                                                    //{


                                                    Ext.create('Ext.window.Window', {
                                                        id: 'desglose_id',
                                                        title: 'Desglose ' + 'de ' + selection.data.mes1,
                                                        height: 500,
                                                        width: 300,
                                                        layout: 'fit',
                                                        items: [{
                                                            xtype: 'gridpanel',
                                                            id: 'grid_eneroaa',
                                                            frame: true,
                                                            width: 200,
                                                            height: 200,
                                                            store: Ext.create('Ext.data.Store', {
                                                                model: 'desgloseG'
                                                            }),
                                                            enableColumnHide: true,
                                                            columns: [
                                                                {
                                                                    text: 'Día',
                                                                    //flex:1 ,
                                                                    width: 100,
                                                                    sortable: true,

                                                                    dataIndex: 'dia',
                                                                    xtype: 'datecolumn',
                                                                    format: 'l,j'
                                                                    //groupable: false

                                                                },
                                                                {
                                                                    text: 'Plan Diario',
                                                                    //flex:1 ,
                                                                    width: 100,
                                                                    sortable: true,
                                                                    dataIndex: 'plan_diario'
                                                                    //groupable: false,

                                                                }, {
                                                                    text: 'Plan del Pico',
                                                                    //flex:1,
                                                                    width: 100,
                                                                    sortable: true,
                                                                    dataIndex: 'plan_pico'
                                                                    //groupable: false,

                                                                }
                                                            ],
                                                            listeners: {

                                                                afterrender: function (This, eOpts) {

                                                                    for (i = 1; i < selection.data.cant_dias + 1; i++) {
                                                                        let nromes = selection.data.nromes;
                                                                        //console.log(nromes)


                                                                        let fecha = new Date(_result.anno, nromes - 1);
                                                                        //console.log(fecha)

                                                                        let fecha1 = new Date(fecha.getFullYear(), fecha.getMonth(), i);

                                                                        //let fecha_format  = Ext.util.Format.date(fecha1,'D,j');


                                                                        Ext.getCmp('grid_eneroaa').getStore().add({
                                                                            dia: fecha1,
                                                                            plan_pico: 0,
                                                                            plan_diario: 0
                                                                        });
                                                                        //Ext.getCmp('grid_eneroaa').getView().refresh();
                                                                        let selectiongrid = Ext.getCmp('grid_eneroaa').getStore();


                                                                    }
                                                                    let cantdom = 0;
                                                                    let cantsab = 0;
                                                                    let canthabiles = 0;
                                                                    selectiongrid.each(function (r) {

                                                                        let dia = Ext.util.Format.date(r.get('dia'), 'D,j');
                                                                        //dia.substring()
                                                                        let letra = dia.substring(0, 3);
                                                                        let letracompelta = dia.substring(0, 6);
                                                                        //console.log(dia);
                                                                        //console.log(letra);
                                                                        //console.log(r);

                                                                        if (letra == 'Dom') {
                                                                            //let var_recor = selectiongrid.findRecord('dia', reco);
                                                                            cantdom = cantdom + 1;


//console.log(var_recor.data.plan_diario)
                                                                        } else if (letra == 'Sáb') {
                                                                            cantsab = cantsab + 1;

                                                                        } else {
                                                                            canthabiles = canthabiles + 1;

                                                                        }

                                                                    }, this);

                                                                    //console.log(cantdom)
                                                                    //console.log(cantsab)
                                                                    //console.log(canthabiles)


                                                                    selectiongrid.each(function (r) {

                                                                        let dia = Ext.util.Format.date(r.get('dia'), 'D,j');
                                                                        //dia.substring()
                                                                        let letra = dia.substring(0, 3);
                                                                        let letracompelta = dia.substring(0, 6);
                                                                        //console.log(dia);
                                                                        //console.log(letra);
                                                                        //console.log(r);
                                                                        if (letra == 'Dom') {
                                                                            //let var_recor = selectiongrid.findRecord('dia', reco);
                                                                            r.data.plan_diario = Ext.util.Format.round(domingos_diario / cantdom, 2);
                                                                            r.data.plan_pico = Ext.util.Format.round(domingos_pico / cantdom, 2);
                                                                            Ext.getCmp('grid_eneroaa').getView().refresh();
//console.log(var_recor.data.plan_diario)
                                                                        } else if (letra == 'Sáb') {
                                                                            r.data.plan_diario = Ext.util.Format.round(sabados_diario / cantsab, 2);
                                                                            r.data.plan_pico = Ext.util.Format.round(Sabados_pico / cantsab, 2);
                                                                            Ext.getCmp('grid_eneroaa').getView().refresh();

                                                                        } else {
                                                                            r.data.plan_diario = Ext.util.Format.round(dias_diario / canthabiles, 2);
                                                                            r.data.plan_pico = Ext.util.Format.round(dias_pico / canthabiles, 2);
                                                                            Ext.getCmp('grid_eneroaa').getView().refresh();
                                                                        }

                                                                    }, this);

                                                                }

                                                            },
                                                            selType: 'cellmodel',
                                                            plugins: [
                                                                Ext.create('Ext.grid.plugin.CellEditing', {
                                                                    clicksToEdit: 1,
                                                                    listeners: {
                                                                        edit: function (editor, e, eOpts) {

                                                                            let posiicon = e.rowIdx;

                                                                            let plan = Ext.getCmp('grid_eneroaa').getStore().getAt(posiicon).get('plan_diario');

                                                                            if (e.value > plan) {

                                                                                App.InfoMessage('Información', 'El valor del Pico no debe ser mayor que el real', 'warning');
                                                                                //e.cancel();
                                                                                Ext.getCmp('grid_eneroaa').getStore().getAt(posiicon).set('plan_diario', 0);
                                                                                Ext.getCmp('grid_eneroaa').getStore().getAt(posiicon).set('plan_pico', 0);
                                                                            }
                                                                            //console.log(plan)


                                                                        }
                                                                    }
                                                                })
                                                            ]


                                                        }],

                                                        buttons: [
                                                            {
                                                                text: 'Aceptar',
                                                                //iconCls: 'fa fa-plus-square-o fa-1_4',
                                                                width: 70,
                                                                handler: function () {

                                                                    let selectiongrid = Ext.getCmp('grid_eneroaa').getStore();

                                                                    // console.log(selectiongrid.getNewRecords().data)

                                                                    let values = [];
                                                                    selectiongrid.each(function (r) {
                                                                        values = values.concat({
                                                                            dia: r.get('dia'),
                                                                            plan_pico: r.get('plan_pico'),
                                                                            plan_diario: r.get('plan_diario'),
                                                                            nromes: selection.data.nromes

                                                                        });
                                                                    }, this);

                                                                    let completo = true;
                                                                    for (i = 0; i < values.length; i++) {

                                                                        if (values[i]['plan_pico'] == 0 || values[i]['plan_diario'] == 0) {
                                                                            completo = false;
                                                                            break;
                                                                        }

                                                                    }
                                                                    if (completo) {
                                                                        let _result = App.PerformSyncServerRequest(Routing.generate('desgloselectricidadDesgloseElectricidad'), {datos: JSON.stringify(values)});
                                                                        if (_result) {
                                                                            let windowadd = Ext.getCmp('desglose_id');
                                                                            let windowaaa = Ext.getCmp('ventana_desgloseautomatico');
                                                                            windowadd.close();
                                                                            windowaaa.close();
                                                                            Ext.getCmp('grid_meses').getStore().load();


                                                                        }
                                                                    } else
                                                                        App.InfoMessage('Información', 'Por favor revice le faltan Datos por completar', 'warning')
                                                                    //console.log(values.length)
                                                                    //console.log(values[0].plan_pico)

                                                                }
                                                            },

                                                            {
                                                                text: 'Cancelar',
                                                                width: 70,
                                                                handler: function () {
                                                                    Ext.getCmp('desglose_id').close()
                                                                }
                                                            }
                                                        ]// Let's put an empty grid in just to illustrate fit layout

                                                    }).show();


                                                    //}
                                                    //else
                                                    //App.InfoMessage('Información','Por favor revise  su desglose  esta por encima del plan mensual','warning');


                                                }


                                            },
                                            {
                                                text: 'Cancelar',
                                                width: 100,
                                                handler: function () {

                                                    Ext.getCmp('ventana_desgloseautomatico').close();

                                                }
                                            }

                                        ]


                                    }).show();

                                } else
                                    App.InfoMessage('Información', 'Seleccione el mes a Desglosar', 'warning')


                            } else
                                App.InfoMessage('Información', 'Este mes ya ha sido desglosado', 'warning')


                            // console.log(selectiongrid.getNewRecords().data)


                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('desglosemeses_id').close()
                        }
                    }
                ]// Let's put an empty grid in just to illustrate fit layout

            }).show();

        }


    });

    let _btnMod = Ext.create('Ext.button.MyButton', {
        id: 'desglosemod_btn_ver',
        text: 'Modificar Plan',
        iconCls: 'fa fa-th fa-1_4',
        // disabled: true,
        width: 120,
        handler: function (This, e) {
            let _result = App.PerformSyncServerRequest(Routing.generate('getCurrentPeriodo'), {});

            Ext.create('Ext.window.Window', {
                id: 'desglosemesesmod_id',
                title: 'Modificar Desglose ',
                height: 405,
                width: 195,
                layout: 'fit',
                items: [{
                    xtype: 'gridpanel',
                    id: 'grid_mesessmod',
                    frame: true,
                    width: 400,
                    height: 200,
                    store: meses,
                    enableColumnHide: true,
                    columns: [
                        {
                            text: 'Meses',
                            dataIndex: 'mes'

                        }
                    ]

                }],

                buttons: [
                    {
                        text: 'Modificar Mes',
                        //iconCls: 'fa fa-plus-square-o fa-1_4',
                        width: 100,
                        handler: function () {
                            let window = Ext.getCmp('desglosemesesmod_id');
                            let selection = Ext.getCmp('grid_mesessmod').getSelectionModel().getLastSelected();
                            let mes_select = selection.data.nromes;
                            let mes1_select = selection.data.mes;
                            //console.log(_result.mes)


                            window.close();
                            let objids = {};
                            objids.nromes = selection.data.nromes;
                            let result = App.PerformSyncServerRequest(Routing.generate('loaddesglosemesDesgloseElectricidad'), objids);
//console.log(result)
                            if (result.rows.length != 0) {

                                if (mes_select >= _result.mes) {


                                    Ext.create('Ext.window.Window', {
                                        id: 'desglosemod_id',
                                        title: 'Modificar Desglose ' + 'de ' + selection.data.mes1,
                                        height: 500,
                                        width: 550,
                                        layout: 'fit',
                                        items: [{
                                            xtype: 'gridpanel',
                                            id: 'grid_eneroaamod',
                                            frame: true,
                                            width: 400,
                                            height: 200,
                                            store: Ext.create('Ext.data.Store', {
                                                model: 'desgloseG'
                                            }),
                                            enableColumnHide: true,
                                            columns: [
                                                {
                                                    text: 'Día',
                                                    flex: 1,
                                                    width: 100,
                                                    xtype: 'datecolumn',
                                                    format: 'l,j',
                                                    //sortable: true,
                                                    dataIndex: 'fecha_desglose'
                                                    //groupable: false,


                                                },
                                                {
                                                    text: 'Plan Diario',
                                                    id: 'pland',
                                                    flex: 1,
                                                    width: 100,
                                                    sortable: true,
                                                    dataIndex: 'plan_diario',
                                                    groupable: false,
                                                    xtype: 'numbercolumn',
                                                    editor: Ext.create('Ext.form.field.Number', {
                                                        //decimalSeparator: '.',
                                                        minValue: 0
                                                    })
                                                    //renderer: function (val2, met, record, a, b, c, d) {
                                                    //
                                                    //    let fechaactua=new Date();
                                                    //    let dia=Ext.Date.format(fechaactua,'j');
                                                    //     let ddd=  new Date(record.get('fecha_desglose'));
                                                    //    let fe=Ext.Date.format(ddd,'j')
                                                    //    console.log(fe)
                                                    //    if(fe<dia)
                                                    //    {
                                                    //        Ext.getCmp('grid_eneroaamod').getStore().getAt(0).setDisabled(true)
                                                    //        //Ext.getCmp('grid_eneroaamod').getColumnModel().setEditable(colnum,false);
                                                    //        //Ext.getCmp('pland').disabled();
                                                    //        //met.style = 'font-style:italic !important;font-weight: bold;background: #F22222;';
                                                    //  return val2;
                                                    //
                                                    //    }else
                                                    //    return val2;
                                                    //
                                                    //}

                                                }, {
                                                    text: 'Plan Pico',
                                                    flex: 1,
                                                    width: 100,
                                                    sortable: true,
                                                    dataIndex: 'plan_pico',
                                                    xtype: 'numbercolumn',
                                                    groupable: false,
                                                    editor: Ext.create('Ext.form.field.Number', {
                                                        //decimalSeparator: '.',
                                                        minValue: 0
                                                    })
                                                }
                                            ],

                                            listeners: {

                                                afterrender: function (This, eOpts) {
                                                    /* for(i=1;i<result.rows.length+1;i++){

                                                     //console.log(result.rows[i].dia)
                                                     // Ext.getCmp('grid_eneroaamod').getStore().add({dia:i, plan_pico:result.rows[i].plan_pico,plan_diario:'resul'});

                                                     }*/
                                                    Ext.getCmp('grid_eneroaamod').getStore().loadData(result.rows);
                                                    let selectiongrid = Ext.getCmp('grid_eneroaamod').getStore();

                                                    // console.log(selectiongrid.getNewRecords().data)

                                                    let values = [];
                                                    selectiongrid.each(function (r) {
                                                        let fecha = r.get('fecha_desglose');
                                                        let reusl = fecha.split(",");

                                                        //console.log(reusl[1])
                                                        let fechaactua = new Date();
                                                        let dia = Ext.Date.format(fechaactua, 'j');
                                                        //console.log(dia)
                                                        if (reusl[1] < dia) {
                                                            //r.setDisabled();

                                                        }

                                                    }, this);

                                                }

                                            },
                                            selType: 'cellmodel',
                                            plugins: [
                                                Ext.create('Ext.grid.plugin.CellEditing', {
                                                    clicksToEdit: 1,
                                                    listeners: {
                                                        edit: function (editor, e, eOpts) {

                                                            let posiicon = e.rowIdx;

                                                            let plan = Ext.getCmp('grid_eneroaamod').getStore().getAt(posiicon).get('plan_diario');

                                                            if (e.value > plan) {

                                                                App.InfoMessage('Información', 'El valor del Pico no debe ser mayor que el real', 'warning');
                                                                //e.cancel();
                                                                Ext.getCmp('grid_eneroaamod').getStore().getAt(posiicon).set('plan_diario', 0);
                                                                Ext.getCmp('grid_eneroaamod').getStore().getAt(posiicon).set('plan_pico', 0);
                                                            }
                                                            //console.log(plan)


                                                        }
                                                    }
                                                })
                                            ]


                                        }],

                                        buttons: [
                                            {
                                                text: 'Aceptar',
                                                //iconCls: 'fa fa-plus-square-o fa-1_4',
                                                width: 70,
                                                handler: function () {

                                                    let selectiongridmod = Ext.getCmp('grid_eneroaamod').getStore();

                                                    //console.log(selectiongridmod.getData())

                                                    let values1 = [];
                                                    selectiongridmod.each(function (r) {
                                                        values1 = values1.concat({
                                                            id: r.get('id'),
                                                            dia: r.get('dia'),
                                                            plan_pico: r.get('plan_pico'),
                                                            plan_diario: r.get('plan_diario'),
                                                            nromes: selection.data.nromes
                                                        });
                                                    }, this);
//datosmod:JSON.stringify(values1)
                                                    let completo = true;
                                                    for (i = 0; i < values1.length; i++) {

                                                        if (values1[i]['plan_pico'] == 0 || values1[i]['plan_diario'] == 0) {
                                                            completo = false;
                                                            break;
                                                        }
                                                    }
                                                    if (completo) {
                                                        let _result1 = App.PerformSyncServerRequest(Routing.generate('desgloselectricidadModDesgloseElectricidad'), {datosmod: JSON.stringify(values1)});

                                                        if (_result1) {
                                                            let windowmod = Ext.getCmp('desglosemod_id');
                                                            windowmod.close();
                                                            Ext.getCmp('grid_meses').getStore().load();
                                                        }
                                                    } else
                                                        App.InfoMessage('Información', 'Por favor revice le faltan Datos por completar', 'warning')


                                                }
                                            },

                                            {
                                                text: 'Cancelar',
                                                width: 70,
                                                handler: function () {
                                                    Ext.getCmp('desglosemod_id').close()
                                                }
                                            }
                                        ]// Let's put an empty grid in just to illustrate fit layout

                                    }).show();


                                } else
                                    App.InfoMessage('Información', 'No puede Modificar meses anteriores ', 'warning');


                            } else
                                App.InfoMessage('Información', "El mes " + selection.data.mes + " no ha sido desglosado", 'danger');


                        }
                    },

                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('desglosemesesmod_id').close()
                        }
                    }
                ]// Let's put an empty grid in just to illustrate fit layout

            }).show();
        }
    });

    let _tbar = Ext.getCmp('desglose_tbar');
});