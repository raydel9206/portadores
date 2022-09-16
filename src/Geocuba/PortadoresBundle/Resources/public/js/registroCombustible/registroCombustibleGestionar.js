/**
 * Created by pfcadenas  08/2016.
 */


let semanaActual = 0;
Ext.onReady(function () {

    function round(value, exp) {
        if (typeof exp === 'undefined' || +exp === 0) return Math.round(value);
        value = +value;
        exp = +exp;
        if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) return NaN;    // Shift
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));    // Shift back
        value = value.toString().split('e');
        return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
    }

// var _store_vehiculos = Ext.create('Ext.data.JsonStore', {
//     storeId: 'id_store_vehiculo',
//     fields: [
//         {name: 'id'},
//         {name: 'nmarca_vehiculoid'},
//         {name: 'nestado_tecnicoid'},
//         {name: 'ndenominacion_vehiculoid'},
//         {name: 'nunidadid'},
//         {name: 'nchoferid'},
//         {name: 'ntipo_combustibleid'},
//         {name: 'matricula'},
//         {name: 'norma'},
//         {name: 'nro_inventario'},
//         {name: 'nro_serie_carreceria'},
//         {name: 'nro_serie_motor'},
//         {name: 'color'},
//         {name: 'nro_circulacion'},
//         {name: 'fecha_expiracion_circulacion'},
//         {name: 'anno_fabricacion'}
//     ],
//     proxy: {
//         type: 'ajax',
//         url: App.buildURL('/portadores/vehiculo/load'),
//         reader: {
//             rootProperty: 'rows'
//         }
//     },
//     pageSize: 1000,
//     autoLoad: true
// });

    var _store_moneda = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_moneda',
        fields: [
            {name: 'id'},
            {name: 'nombre'},
            {name: 'unica'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/moneda/loadMoneda'),
            reader: {
                rootProperty: 'rows'
            }
        },
        autoLoad: true
    });

    Ext.define('Portadores.registro_combustible.Window', {
        extend: 'Ext.window.Window',
        width: 280,
        //height: 160,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Fecha de registro',
                            format: 'm/Y',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'fecha_registro',
                            id: 'fecha_registro',
                            listeners: {
                                afterrender: function (This) {
                                    var date = new Date();

                                    let dias = App.getDaysInMonth(App.selected_year, App.selected_month);
                                    let anno = App.selected_year;
                                    let min = new Date(App.selected_month + '/' + 1 + '/' + anno);
                                    let max = new Date(App.selected_month + '/' + dias + '/' + anno);

                                    This.setMinValue(min);
                                    This.setMaxValue(max);
                                    This.setValue(date);
                                }
                            }
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    Ext.define('Portadores.planificacion.Window', {
        extend: 'Ext.window.Window',
        width: 300,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {
            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    bodyPadding: 10,
                    layout: {
                        type: 'vbox',
                        align: 'stretch'
                    },
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelAlign: 'top',
                        allowBlank: false
                    },
                    items: [
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Fecha del recibo',
                            format: 'd/m/Y',
                            startDay: 1,
                            labelWidth: 110,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            name: 'fecha_planificacion',
                            id: 'fecha_planificacion',
                            allowBlank: false,
                            listeners: {
                                afterrender: function (This) {

                                    var date = new Date(Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.fecha_planif);
                                    var primerDia = new Date(date.getFullYear(), date.getMonth(), 1);
                                    var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);

                                    This.setMinValue(primerDia);
                                    This.setMaxValue(ultimoDia);
                                }
                            }
                        }, {
                            xtype: 'combobox',
                            fieldLabel: 'Moneda',
                            store: _store_moneda,
                            emptyText: 'Seleccione la moneda...',
                            name: 'monedaid',
                            id: 'monedaid',
                            displayField: 'nombre',
                            valueField: 'id',
                            queryMode: 'local',
                            forceSelection: true,
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }, {
                            xtype: 'numberfield',
                            fieldLabel: 'Recibido',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ],
                            labelWidth: 110,
                            decimalSeparator: '.',
                            value: 0,
                            name: 'recibido',
                            id: 'recibido',
                            minValue: 1
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var numero_semana = 6;

    function fn_actualizar_existenciareal_combustible(_grid, n) {

        if (n >= numero_semana)
            return;

        _grid.store.data.items[2].data['' + (3 * n + 1)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 1)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 1)]) - parseFloat(_grid.store.data.items[3].data['' + (3 * n + 1)]), 2).toFixed(2);
        _grid.store.data.items[2].data['' + (3 * n + 2)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 2)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 2)]) - parseFloat(_grid.store.data.items[3].data['' + (3 * n + 2)]), 2).toFixed(2);
        _grid.store.data.items[2].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * n + 3)]) - parseFloat(_grid.store.data.items[0].data['' + (3 * n + 3)]), 2).toFixed(2);
        _grid.store.data.items[1].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[2].data['' + (3 * n + 1)]) * 100 / Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma, 2).toFixed(2);

        fn_actualizar_existenciainicial_combustible(_grid, n + 1);

    }

    function fn_actualizar_existenciafinal_combustible(_grid, n) {

        if (n >= numero_semana)
            return;

        _grid.store.data.items[3].data['' + (3 * n + 1)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 1)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 1)]) - parseFloat(_grid.store.data.items[2].data['' + (3 * n + 1)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * n + 2)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 2)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 2)]) - parseFloat(_grid.store.data.items[2].data['' + (3 * n + 2)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 3)]) + parseFloat(_grid.store.data.items[2].data['' + (3 * n + 3)]), 2).toFixed(2);

        fn_actualizar_existenciainicial_combustible(_grid, n + 1);

    }

    function fn_actualizar_iniciales(_grid, n) {

        if (n >= numero_semana)
            return;

        _grid.store.data.items[3].data['' + (3 * n + 1)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 1)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 1)]) - parseFloat(_grid.store.data.items[2].data['' + (3 * n + 1)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * n + 2)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 2)]) + parseFloat(_grid.store.data.items[1].data['' + (3 * n + 2)]) - parseFloat(_grid.store.data.items[2].data['' + (3 * n + 2)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[0].data['' + (3 * n + 3)]) + parseFloat(_grid.store.data.items[2].data['' + (3 * n + 3)]), 2).toFixed(2);

        fn_actualizar_finales(_grid, n + 1);

    }

    function fn_actualizar_finales(_grid, n) {

        //let a = _grid.getStore().getModifiedRecords()[0].modified;

        if (n >= numero_semana)
            return;

        _grid.store.data.items[0].data['' + (3 * n + 1)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 1)]), 2).toFixed(2);
        _grid.store.data.items[0].data['' + (3 * n + 2)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 2)]), 2).toFixed(2);
        _grid.store.data.items[0].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 3)]), 2).toFixed(2);

        fn_actualizar_iniciales(_grid, n);
    }

    function fn_actualizar_existenciainicial_combustible(_grid, n) {

        if (n >= numero_semana)
            return;

        if (_grid.store.data.items[2].data['' + (3 * (n - 1) + 3)] !== '0.00') {
            _grid.store.data.items[0].data['' + (3 * n + 1)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 1)]), 2).toFixed(2);
            _grid.store.data.items[0].data['' + (3 * n + 2)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 2)]), 2).toFixed(2);
            _grid.store.data.items[0].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * (n - 1) + 3)]), 2).toFixed(2);
        }

        fn_actualizar_existenciafinal_combustible(_grid, n);
    }


    function fn_actualizar_totales(_grid) {

        var j = 0;
        _grid.store.data.items[0].data['' + (3 * (numero_semana) + 1)] = round(parseFloat(_grid.store.data.items[0].data['' + (j * 3 + 1)]), 2).toFixed(2);
        _grid.store.data.items[0].data['' + (3 * (numero_semana) + 2)] = round(parseFloat(_grid.store.data.items[0].data['' + (j * 3 + 2)]), 2).toFixed(2);
        _grid.store.data.items[0].data['' + (3 * (numero_semana) + 3)] = round(parseFloat(_grid.store.data.items[0].data['' + (j * 3 + 3)]), 2).toFixed(2);


        var totalComb = 0;
        var totalLub = 0;
        var totalKm = 0;
        for (j = 0; j < numero_semana; j++) {
            totalComb += round(parseFloat(_grid.store.data.items[1].data['' + (3 * (j) + 1)]), 2);
            totalLub += round(parseFloat(_grid.store.data.items[1].data['' + (3 * (j) + 2)]), 2);
            totalKm += round(parseFloat(_grid.store.data.items[1].data['' + (3 * (j) + 3)]), 2);
        }
        _grid.store.data.items[1].data['' + (3 * (numero_semana) + 1)] = totalComb.toFixed(2);
        _grid.store.data.items[1].data['' + (3 * (numero_semana) + 2)] = totalLub.toFixed(2);
        _grid.store.data.items[1].data['' + (3 * (numero_semana) + 3)] = totalKm.toFixed(2);

        totalComb = 0;
        totalLub = 0;
        totalKm = 0;
        for (j = 0; j < numero_semana; j++) {
            totalComb += round(parseFloat(_grid.store.data.items[2].data['' + (3 * (j) + 1)]), 2);
            totalLub += round(parseFloat(_grid.store.data.items[2].data['' + (3 * (j) + 2)]), 2);
            totalKm += round(parseFloat(_grid.store.data.items[2].data['' + (3 * (j) + 3)]), 2);
        }
        _grid.store.data.items[2].data['' + (3 * (numero_semana) + 1)] = totalComb.toFixed(2);
        _grid.store.data.items[2].data['' + (3 * (numero_semana) + 2)] = totalLub.toFixed(2);
        _grid.store.data.items[2].data['' + (3 * (numero_semana) + 3)] = totalKm.toFixed(2);


        j = numero_semana - 1;
        while (j > 0 && _grid.store.data.items[3].data['' + j * 3 + 1] === 0) j--;
        _grid.store.data.items[3].data['' + (3 * (numero_semana) + 1)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * j + 1)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * (numero_semana) + 2)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * j + 2)]), 2).toFixed(2);
        _grid.store.data.items[3].data['' + (3 * (numero_semana) + 3)] = round(parseFloat(_grid.store.data.items[3].data['' + (3 * j + 3)]), 2).toFixed(2);

        totalComb = 0;
        totalLub = 0;
        totalKm = 0;
        for (j = 0; j < numero_semana; j++) {
            totalComb += round(parseFloat(_grid.store.data.items[4].data['' + (3 * (j) + 1)]), 2);
            totalLub += round(parseFloat(_grid.store.data.items[4].data['' + (3 * (j) + 2)]), 2);
            totalKm += round(parseFloat(_grid.store.data.items[4].data['' + (3 * (j) + 3)]), 2);
        }
        _grid.store.data.items[4].data['' + (3 * (numero_semana) + 1)] = totalComb.toFixed(2);
        _grid.store.data.items[4].data['' + (3 * (numero_semana) + 2)] = totalLub.toFixed(2);
        _grid.store.data.items[4].data['' + (3 * (numero_semana) + 3)] = totalKm.toFixed(2);
    }

    Ext.define('Portadores.analisis.Window', {
        extend: 'Ext.window.Window',
        width: 750,
        height: 295,
        modal: true,
        plain: true,
        resizable: false,
        initComponent: function () {

            var cols = [];
            var fields = [];
            var semanas = [];

            fields.push({name: '0'});
            cols.push({
                xtype: 'gridcolumn',
                dataIndex: '0',
                width: 250,
                text: '<b>Conceptos</b>'
            });

            var date = new Date(Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.fecha_planif),
                day = date.getDay() || 7,
                i = -1,
                di = 0,
                df = 0;


            var ultimo = new Date(date.getYear(), date.getMonth() + 1, 0);
            var last = ultimo.getDate();
            while (df < last) {
                if (i === -1) {
                    i = 0;
                    di = 1;
                    df = di + 7 - day;
                }
                else {
                    di = df + 1;
                    df = di + 6;

                    if (di + 6 > last) {
                        df = last;
                    }
                    i += 1;
                }

                semanas.push({id: i, semana: di + '-' + df});
                fields.push({name: '' + (i * 3 + 1)}, {name: '' + (i * 3 + 2)}, {name: '' + (i * 3 + 3)});
                cols.push({
                    xtype: 'gridcolumn',
                    text: '<b>Semana del: ' + di + ' al ' + df + '</b>',
                    columns: [{
                        xtype: 'gridcolumn',
                        hidden: i !== 0,
                        text: '<b>Comb.</b>',
                        allowBlank: false,
                        border: true,
                        dataIndex: '' + (i * 3 + 1),
                        width: 80,
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            value: 0,
                            name: '' + (i * 3 + 1),
                            allowBlank: false,
                            minValue: 0
                        },
                        renderer: function (value, metaData, record, rowIndex, colIndex) {
                            if (rowIndex === 3 || rowIndex === 1) {
                                return '<span style="margin: 0; padding: 0; width: 100%; color:' + "#73b51e" + ';">' + value + '</span>';
                            } else {
                                return value;
                            }
                        }
                    },
                        {
                            xtype: 'gridcolumn',
                            hidden: i !== 0,
                            text: '<b>Lub.</b>',
                            allowBlank: false,
                            border: true,
                            dataIndex: '' + (i * 3 + 2),
                            width: 80,
                            editor: {
                                xtype: 'numberfield',
                                decimalSeparator: '.',
                                value: 0,
                                name: '' + (i * 3 + 2),
                                allowBlank: false,
                                minValue: 0
                            },
                            renderer: function (value, metaData, record, rowIndex, colIndex) {
                                if (rowIndex === 3 || rowIndex === 1) {
                                    return '<span style="margin: 0; padding: 0; width: 100%; color:' + "#73b51e" + ';">' + value + '</span>';
                                } else {
                                    return value;
                                }
                            }
                        },
                        {
                            xtype: 'gridcolumn',
                            hidden: i !== 0,
                            text: '<b>Km(Mh)</b>',
                            allowBlank: false,
                            border: true,
                            dataIndex: '' + (i * 3 + 3),
                            width: 80,
                            editor: {
                                xtype: 'numberfield',
                                decimalSeparator: '.',
                                value: 0,
                                name: '' + (i * 3 + 3),
                                allowBlank: false,
                                minValue: 0
                            },
                            renderer: function (value, metaData, record, rowIndex, colIndex) {
                                if (rowIndex === 3) {
                                    return '<span style="margin: 0; padding: 0; width: 100%; color:' + "#73b51e" + ';">' + value + '</span>';
                                } else {
                                    return value;
                                }
                            }
                        }]
                });
            }

            i += 1;
            fields.push({name: '' + (i * 3 + 1)}, {name: '' + (i * 3 + 2)}, {name: '' + (i * 3 + 3)});
            cols.push({
                xtype: 'gridcolumn',
                text: '<b>Total</b>',
                columns: [{
                    xtype: 'gridcolumn',
                    text: '<b>Comb.</b>',
                    allowBlank: false,
                    border: true,
                    dataIndex: '' + (i * 3 + 1),
                    width: 80
                    //editor: {
                    //    xtype: 'numberfield',
                    //    decimalSeparator: '.',
                    //    value: 0,
                    //    name: '' + (i * 3 + 1),
                    //    allowBlank: false
                    //}
                },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>Lub.</b>',
                        allowBlank: false,
                        border: true,
                        dataIndex: '' + (i * 3 + 2),
                        width: 80
                        //editor: {
                        //    xtype: 'numberfield',
                        //    decimalSeparator: '.',
                        //    value: 0,
                        //    name: '' + (i * 3 + 2),
                        //    allowBlank: false
                        //}
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<b>Km(Mh)</b>',
                        allowBlank: false,
                        border: true,
                        dataIndex: '' + (i * 3 + 3),
                        width: 80
                        //editor: {
                        //    xtype: 'numberfield',
                        //    decimalSeparator: '.',
                        //    value: 0,
                        //    name: '' + (i * 3 + 3),
                        //    allowBlank: false
                        //}
                    }]
            });

            numero_semana = i;

            this.items = [
                {
                    xtype: 'form',
                    frame: true,
                    width: '100%',
                    height: '100%',
                    defaultType: 'textfield',
                    bodyPadding: 5,
                    items: [{
                        xtype: 'combobox',
                        fieldLabel: 'Semana',
                        margin: '0 0 0 550',
                        store: Ext.create('Ext.data.JsonStore', {
                            fields: [
                                {name: 'id'},
                                {name: 'semana'}
                            ],
                            data: semanas
                        }),
                        width: 180,
                        labelWidth: 60,
                        name: 'semanaid',
                        id: 'semanaid',
                        displayField: 'semana',
                        valueField: 'id',
                        value: 0,
                        queryMode: 'local',
                        forceSelection: true,
                        allowBlank: false,
                        listeners: {
                            change: function (This, newValue, oldValue, eOpts) {
                                semanaActual = newValue;
                                Ext.getCmp('id_grid_editar_analisis').columns[1 + 3 * oldValue].hide();
                                Ext.getCmp('id_grid_editar_analisis').columns[2 + 3 * oldValue].hide();
                                Ext.getCmp('id_grid_editar_analisis').columns[3 + 3 * oldValue].hide();

                                Ext.getCmp('id_grid_editar_analisis').columns[1 + 3 * newValue].show();
                                Ext.getCmp('id_grid_editar_analisis').columns[2 + 3 * newValue].show();
                                Ext.getCmp('id_grid_editar_analisis').columns[3 + 3 * newValue].show();
                            }
                        }
                    },
                        {
                            xtype: 'gridpanel',
                            //title: '<div align="center">Analisis</div>',
                            id: 'id_grid_editar_analisis',
                            margin: '0 0 0 0',
                            border: true,
                            height: 200,
                            selType: 'cellmodel',
                            columnLines: true,
                            plugins: [
                                Ext.create('Ext.grid.plugin.CellEditing', {
                                    clicksToEdit: 1,
                                    listeners: {
                                        beforeedit: function (This, e, eOpts) {
                                            //if (Ext.getCmp('').getSelectionModel().getLastSelected().data.coeficiente)
                                            //return true;
                                            if ((e.rowIdx === 4) || (e.rowIdx === 2 && e.colIdx % 3 === 3) || /*(e.rowIdx == 0 && e.colIdx > 3) ||*/ isNaN(e.value)) {
                                                return false;
                                            }
                                        },
                                        edit: {
                                            fn: function (editor, e, eOpts) {
                                                // if (Ext.getCmp('').getSelectionModel().getLastSelected().data.coeficiente)
                                                //return true;

                                                var _grid = Ext.getCmp('id_grid_editar_analisis'),
                                                    n = Ext.getCmp('semanaid').getValue();
                                                //n = parseInt((e.colIdx - 1) / 3);

                                                if (e.rowIdx === 0 /*&& (e.colIdx == 1 || e.colIdx == 3 )*/) {
                                                    fn_actualizar_existenciafinal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 1 && e.colIdx % 3 === 1) {
                                                    //_grid.store.data.items[1].data['' + (3 * n + 2)] = round( e.value * 0.015, 2).toFixed(2);
                                                    fn_actualizar_existenciafinal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 2 && e.colIdx % 3 === 1) {
                                                    _grid.store.data.items[1].data['' + (3 * n + 3)] = round(e.value * 100 / Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma, 2).toFixed(2);
                                                    //_grid.store.data.items[2].data['' + (3 * n + 2)] = round( e.value * 0.015, 2).toFixed(2);
                                                    _grid.store.data.items[4].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[2].data['' + (3 * n + 3)]) - e.value * 100 / Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma, 2).toFixed(2);
                                                    _grid.store.data.items[4].data['' + (3 * n + 1)] = round((_grid.store.data.items[4].data['' + (3 * n + 3)] * Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma / 100) * -1, 2).toFixed(2);
                                                    //fn_actualizar_existenciafinal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 2 && e.colIdx % 3 === 2) {
                                                    //fn_actualizar_existenciafinal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 2 && e.colIdx % 3 === 0) {
                                                    _grid.store.data.items[4].data['' + (3 * n + 3)] = round(e.value - parseFloat(_grid.store.data.items[1].data['' + (3 * n + 3)]), 2).toFixed(2);
                                                    _grid.store.data.items[4].data['' + (3 * n + 1)] = round((parseFloat(_grid.store.data.items[4].data['' + (3 * n + 3)]) * Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma / 100) * -1, 2).toFixed(2);
                                                    //fn_actualizar_existenciafinal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 3 && (e.colIdx % 3 === 1 || e.colIdx % 3 === 2)) {
                                                    fn_actualizar_existenciareal_combustible(_grid, n);
                                                }
                                                if (e.rowIdx === 3 && e.colIdx % 3 === 0) {
                                                    _grid.store.data.items[2].data['' + (3 * n + 3)] = round(e.value - parseFloat(_grid.store.data.items[0].data['' + (3 * n + 3)]), 2).toFixed(2);
                                                    _grid.store.data.items[4].data['' + (3 * n + 3)] = round(parseFloat(_grid.store.data.items[2].data['' + (3 * n + 3)]) - parseFloat(_grid.store.data.items[1].data['' + (3 * n + 3)]), 2).toFixed(2);
                                                    _grid.store.data.items[4].data['' + (3 * n + 1)] = round((parseFloat(_grid.store.data.items[4].data['' + (3 * n + 3)]) * Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.vehiculonorma / 100) * -1, 2).toFixed(2);
                                                    fn_actualizar_existenciareal_combustible(_grid, n);
                                                }

                                                fn_actualizar_totales(_grid);

                                                _grid.getView().refresh();
                                            }
                                        }
                                    }

                                })
                            ],
                            store: Ext.create('Ext.data.JsonStore', {
                                storeId: 'id_store_analisis_edit',
                                fields: fields,
                                //data: datas,
                                proxy: {
                                    type: 'ajax',
                                    url: App.buildURL('/portadores/registro_combustible/loadAnalisisData'),
                                    extraParams: {
                                        registroid: Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id
                                    },
                                    reader: {
                                        rootProperty: 'rows'
                                    }
                                },
                                sorters: [{
                                    property: 'numerosemana',
                                    direction: 'ASC'
                                }, {
                                    property: 'conceptoid',
                                    direction: 'ASC'
                                }],
                                groupField: 'semana',
                                autoLoad: true
                            }),
                            columns: cols,
                            sortableColumns: false
                        }
                    ]
                }
            ];

            this.callParent();
        }
    });

    var _btnAdd = Ext.create('Ext.button.MyButton', {
        id: 'registro_combustible_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square text-primary',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.registro_combustible.Window', {
                title: 'Adicionar registro combustible',
                id: 'window_registro_combustible_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_registro_combustible_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                App.request('POST', '/portadores/registro_combustible/add', form.getValues(), null, null, function (response) {
                                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                        semanaActual = 0;
                                        form.reset();
                                        window.close();
                                        Ext.getCmp('id_grid_registro_combustible').getStore().load();
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            semanaActual = 0;
                            Ext.getCmp('window_registro_combustible_id').close()
                        }
                    }
                ]
            }).show();
        }
    });
    // var _btnAddOld = Ext.create('Ext.button.MyButton', {
    //     id: 'registro_combustible_btn_add',
    //     text: 'Adicionar',
    //     iconCls: 'fa fa-plus-square-o fa-1_4',
    //     width: 100,
    //     handler: function (This, e) {
    //         Ext.create('Portadores.registro_combustible.Window', {
    //             title: 'Adicionar registro combustible',
    //
    //             id: 'window_registro_combustible_id',
    //             buttons: [
    //                 {
    //                     text: 'Aceptar',
    //                     width: 70,
    //                     handler: function () {
    //                         var window = Ext.getCmp('window_registro_combustible_id');
    //                         var form = window.down('form').getForm();
    //                         if (form.isValid()) {
    //                             App.ShowWaitMsg();
    //                             //window.hide();
    //                             var _result = App.PerformSyncServerRequest(Routing.generate('addRegistroCombustible'), form.getValues());
    //                             App.HideWaitMsg();
    //                             if (_result.success) {
    //                                 form.reset();
    //                                 //window.close();
    //                                 Ext.getCmp('id_grid_registro_combustible').getStore().load();
    //                             }
    //                             App.InfoMessage('Información', _result.message, _result.cls);
    //                         }
    //                     }
    //                 },
    //                 {
    //                     text: 'Cancelar',
    //                     width: 70,
    //                     handler: function () {
    //                         Ext.getCmp('window_registro_combustible_id').close()
    //                     }
    //                 }
    //             ]
    //         }).show();
    //     }
    // });

    // var _btnMod = Ext.create('Ext.button.MyButton', {
    //     id: 'registro_combustible_btn_mod',
    //     text: 'Modificar',
    //     iconCls: 'fa fa-pencil-square-o fa-1_4',
    //     disabled: true,
    //     width: 100,
    //     handler: function (This, e) {
    //         var selection = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected();
    //         var window = Ext.create('Portadores.registro_combustible.Window', {
    //             title: 'Modificar registro combustible',
    //             id: 'window_registro_combustible_id',
    //             buttons: [
    //                 {
    //                     text: 'Aceptar',
    //                     width: 70,
    //                     handler: function () {
    //                         var form = window.down('form').getForm();
    //                         if (form.isValid()) {
    //                             App.ShowWaitMsg();
    //                             //window.hide();
    //                             var obj = form.getValues();
    //                             obj.id = selection.data.id;
    //                             var _result = App.PerformSyncServerRequest(Routing.generate('modRegistroCombustible'), obj);
    //                             App.HideWaitMsg();
    //                             if (_result.success) {
    //                                 window.close();
    //                                 Ext.getCmp('id_grid_registro_combustible').getStore().load();
    //                                 Ext.getCmp('id_grid_registro_combustible').getSelectionModel().deselectAll();
    //                             }
    //                             App.InfoMessage('Información', _result.message, _result.cls);
    //                         }
    //                     }
    //                 },
    //                 {
    //                     text: 'Cancelar',
    //                     width: 70,
    //                     handler: function () {
    //                         Ext.getCmp('window_registro_combustible_id').close();
    //                     }
    //                 }
    //             ]
    //         });
    //         window.show();
    //         window.down('form').loadRecord(selection);
    //     }
    // });
    //
    var _btn_Del = Ext.create('Ext.button.MyButton', {
        id: 'registro_combustible_btn_del',
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            let selection = Ext.getCmp('id_grid_registro_combustible').getSelection();
            Ext.Msg.show({
                title: '¿Eliminar Portador?',
                message: Ext.String.format('¿Está seguro que desea eliminar el registro del vehiculo <span class="font-italic font-weight-bold">{0}</span>?', selection[0].get('vehiculochapa')),
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        App.request('DELETE', App.buildURL('/portadores/registro_combustible/del'), {id: selection[0].get('id')}, null, null, function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getCmp('id_grid_registro_combustible').getStore().reload();
                            }
                        });
                    }
                }
            });

        }
    });

    var _btn_Print = Ext.create('Ext.button.MyButton', {
        id: 'registro_combustible_btn_print',
        text: 'Imprimir',
        disabled: true,
        iconCls: 'fa fa-print text-primary',
        handler: function (This, e) {
            // App.ShowWaitMsg();
            var store = Ext.getCmp('id_grid_registro_combustible').getStore();
            var send = [];
            Ext.Array.each(store.data.items, function (valor) {
                send.push(valor.data);
            });
            //
            var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;

            App.request('POST', '/portadores/registro_combustible/print', {id: registroid}, null, null, function (response) {
                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                    var newWindow = window.open('', '', 'width=1024, height=768'),
                        document = newWindow.document.open();
                    document.write(response.html);
                    document.close();
                    newWindow.print();
                }
            });
            // var _result = App.PerformSyncServerRequest(Routing.generate('printRegistroCombustible'), {
            //     id: registroid
            // });
            // App.HideWaitMsg();
            // if (_result.success) {
            //     var newWindow = window.open('', '', 'width=1024, height=768'),
            //         document = newWindow.document.open();
            //
            //     document.write(_result.html);
            //     document.close();
            //     newWindow.print();
            // }
        }
    });

    var _tbar = Ext.getCmp('registro_combustible_tbar');
    _tbar.add(_btnAdd);
    _tbar.add('-');
    // _tbar.add(_btnMod);
    // _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.add('->');
    _tbar.add(_btn_Print);
    _tbar.setHeight(36);


    var _btnAddPlanificacion = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_btn_add',
        text: 'Adicionar',
        iconCls: 'fa fa-plus-square',
        width: 100,
        handler: function (This, e) {
            Ext.create('Portadores.planificacion.Window', {
                title: 'Adicionar planificacion del combustible',

                id: 'window_planificacion_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            var window = Ext.getCmp('window_planificacion_id');
                            var form = window.down('form').getForm();
                            if (form.isValid()) {
                                // App.ShowWaitMsg();
                                // //window.hide();
                                //
                                // var obj = form.getValues();
                                // obj.registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                                // var _result = App.PerformSyncServerRequest(Routing.generate('addPlanificacion'), obj);
                                // App.HideWaitMsg();
                                // if (_result.success) {
                                //     form.reset();
                                //     //window.close();
                                //     var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                                //     Ext.getCmp('id_grid_planificacion').getStore().load({
                                //         params: {id: registroid}
                                //     });
                                //
                                //     Ext.getCmp('id_grid_analisis').getStore().load({
                                //         params: {id: registroid}
                                //     });
                                // }
                                // App.InfoMessage('Información', _result.message, _result.cls);
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_planificacion_id').close()
                        }
                    }
                ]
            }).show();
            Ext.getCmp('fecha_planificacion').enable();
        }
    });

    var _btnModPlanificacion = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_btn_mod',
        text: 'Modificar',
        iconCls: 'fa fa-pencil-square',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var selection = Ext.getCmp('id_grid_planificacion').getSelectionModel().getLastSelected();
            var window = Ext.create('Portadores.planificacion.Window', {
                title: 'Modificar planificación',
                id: 'window_planificacion_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            // var form = window.down('form').getForm();
                            // if (form.isValid()) {
                            //     App.ShowWaitMsg();
                            //     //window.hide();
                            //     var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                            //     var obj = form.getValues();
                            //     obj.id = selection.data.id;
                            //     obj.registroid = registroid;
                            //     var _result = App.PerformSyncServerRequest(Routing.generate('modPlanificacion'), obj);
                            //     App.HideWaitMsg();
                            //     if (_result.success) {
                            //         window.close();
                            //         Ext.getCmp('id_grid_planificacion').getStore().load({
                            //             params: {id: registroid}
                            //         });
                            //         Ext.getCmp('id_grid_planificacion').getSelectionModel().deselectAll();
                            //
                            //         Ext.getCmp('id_grid_analisis').getStore().load({
                            //             params: {id: registroid}
                            //         });
                            //     }
                            //     App.InfoMessage('Información', _result.message, _result.cls);
                            // }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_planificacion_id').close();
                        }
                    }
                ]
            });
            window.show();
            window.down('form').loadRecord(selection);
            Ext.getCmp('fecha_planificacion').disable();
        }
    });

    var _btn_DelPlanificacion = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_btn_del',
        text: 'Eliminar',
        iconCls: 'fa fa-minus-square',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            // App.ConfirmMessage(function () {
            //     var selection = Ext.getCmp('id_grid_planificacion').getSelectionModel().getLastSelected();
            //     App.ShowWaitMsg();
            //     var _result = App.PerformSyncServerRequest(Routing.generate('delPlanificacion'), {id: selection.data.id});
            //     App.HideWaitMsg();
            //     App.InfoMessage('Información', _result.message, _result.cls);
            //
            //     var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
            //     Ext.getCmp('id_grid_planificacion').getStore().load({
            //         params: {id: registroid}
            //     });
            //     Ext.getCmp('id_grid_planificacion').getSelectionModel().deselectAll();
            //
            //     Ext.getCmp('id_grid_analisis').getStore().load({
            //         params: {id: registroid}
            //     });
            // }, "Está seguro que desea eliminar la planificación del combustible?");

        }
    });

    var _tbarPlanificaion = Ext.getCmp('planificacion_tbar');
    _tbarPlanificaion.add(_btnAddPlanificacion);
    _tbarPlanificaion.add('-');
    _tbarPlanificaion.add(_btnModPlanificacion);
    _tbarPlanificaion.add('-');
    _tbarPlanificaion.add(_btn_DelPlanificacion);
    _tbarPlanificaion.setHeight(36);


    var _btnAddAnalisis = Ext.create('Ext.button.MyButton', {
        id: 'analisis_btn_add',
        text: 'Gestionar',
        iconCls: 'fa fa-plus-square text-primary',
        disabled: true,
        width: 100,
        handler: function (This, e) {
            var window = Ext.create('Portadores.analisis.Window', {
                title: 'Adicionar análisis del combustible',

                id: 'window_analisis_id',
                buttons: [
                    {
                        text: 'Recalcular',
                        width: 80,
                        handler: function () {
                            App.mask();

                            fn_actualizar_existenciafinal_combustible(Ext.getCmp('id_grid_editar_analisis'), 0);
                            fn_actualizar_totales(Ext.getCmp('id_grid_editar_analisis'));
                            Ext.getCmp('id_grid_editar_analisis').getView().refresh();

                            App.unmask();
                            App.showAlert('Acción realizada con éxito.', 'success');
                        }
                    }, {
                        text: 'Iniciales',
                        width: 80,
                        handler: function () {
                            App.mask();

                            fn_actualizar_iniciales(Ext.getCmp('id_grid_editar_analisis'), 0);
                            Ext.getCmp('id_grid_editar_analisis').getView().refresh();

                            App.unmask();

                            App.showAlert('Acción realizada con éxito.', 'success');
                        }
                    }, '->',
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            // App.ShowWaitMsg();
                            var _store = Ext.getCmp('id_grid_editar_analisis').getStore();
                            var obj = {};
                            obj.registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                            var send = [];
                            Ext.Array.each(_store.data.items, function (valor) {
                                send.push(valor.data);
                            });
                            obj.store = Ext.encode(send);
                            App.request('POST', '/portadores/registro_combustible/addAnalisis', obj, null, null, function (response) {
                                if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                    window.close();
                                    semanaActual = 0;
                                    var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                                    Ext.getCmp('id_grid_analisis').getStore().load({
                                        params: {id: registroid}
                                    });
                                }
                            })
                            // var _result = App.PerformSyncServerRequest(Routing.generate('addAnalisis'), obj);
                            // App.HideWaitMsg();
                            // if (_result.success) {
                            //     window.close();
                            //     var registroid = Ext.getCmp('id_grid_registro_combustible').getSelectionModel().getLastSelected().data.id;
                            //     Ext.getCmp('id_grid_analisis').getStore().load({
                            //         params: {id: registroid}
                            //     });
                            // }
                            // App.InfoMessage('Información', _result.message, _result.cls);
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            semanaActual = 0;
                            Ext.getCmp('window_analisis_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    var _tbarAnalisis = Ext.getCmp('analisis_tbar');
    _tbarAnalisis.add(_btnAddAnalisis);
    _tbarAnalisis.setHeight(36);
});
