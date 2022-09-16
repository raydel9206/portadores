Ext.onReady(function () {

    let gridAnexos3 = Ext.getCmp('gridAnexos3');
    let mes_anno = Ext.getCmp('mes_anno');

//     let storePersonas = Ext.create('Ext.data.JsonStore', {
//         storeId: 'id_store_persona',
//         fields: [
//             {name: 'id'},
//             {name: 'nombre'}
//         ],
//         sorters: 'nombreunidadid',
//         proxy: {
//             type: 'ajax',
//             url: App.buildURL('/portadores/persona/load'),
//             reader: {
//                 rootProperty: 'rows'
//             }
//         },
//         autoLoad: false,
//         listeners: {
//             beforeload: function (This, operation) {
//                 operation.setParams({
//                     unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
//                 });
//             }
//         }
//     });
//
//     Ext.define('Portadores.anexos3.Window', {
//         extend: 'Ext.window.Window',
//         bodyPadding: '10',
//         modal: true,
//         resizable: false,
//         items: [{
//             xtype: 'form',
//             defaults: {
//                 afterLabelTextTpl: [
//                     '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
//                 ],
//                 labelWidth: 85,
//                 labelAlign: 'right',
//                 allowBlank: false,
//                 xtype: 'combobox',
//                 margin: '5 0 5 0'
//             },
//             layout: {
//                 type: 'vbox',
//                 align: 'stretch'
//             },
//             width: 300,
//             items: [
//                 {
//                     fieldLabel: 'Area',
//                     id: 'combo_area',
//                     store: Ext.create('Ext.data.JsonStore', {
//                         storeId: 'storeAreaId',
//                         fields: [
//                             {name: 'id'},
//                             {name: 'nombre'}
//                         ],
//                         proxy: {
//                             type: 'ajax',
//                             url: App.buildURL('/portadores/area/loadArea'),
//                             reader: {
//                                 rootProperty: 'rows'
//                             }
//                         },
//                         autoLoad: false,
//                         listeners: {
//                             beforeload: function (This, operation) {
//                                 operation.setParams({
//                                     unidadid: Ext.getCmp('arbolunidades').getSelection()[0].data.id
//                                 });
//                             }
//                         }
//
//                     }),
//                     valueField: 'id',
//                     displayField: 'nombre',
//                     queryMode: 'local',
//                     name: 'area_id',
//                     allowBlank: false,
//                     editable: false
//                 },
//                 {
//                     fieldLabel: 'Operario',
//                     id: 'combo_operario',
//                     store: storePersonas,
//                     valueField: 'id',
//                     displayField: 'nombre',
//                     queryMode: 'local',
//                     name: 'operario_id',
//                     allowBlank: false,
//                     editable: false
//                 },
//                 {
//                     fieldLabel: 'Responsable',
//                     id: 'combo_responsable',
//                     store: storePersonas,
//                     valueField: 'id',
//                     displayField: 'nombre',
//                     queryMode: 'local',
//                     name: 'responsable_id',
//                     allowBlank: false,
//                     editable: false
//                 }
//             ]
//         }]
//     });
//
    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/anexo3gee/${action === 'print' ? 'export' : action}`);

        // let extraData = gridAnexos3.getStore().getProxy().getReader().rawData.extra_data;

        // let loadExtraData = () => {
        //     App.mask();
        //     let personasPromise = new Promise((resolve, reject) => {
        //         storePersonas.load({
        //             callback: function () {
        //                 resolve();
        //             }
        //         });
        //     });
        //
        //     let areasPromise = new Promise((resolve, reject) => {
        //         Ext.getCmp('combo_area').getStore().load({
        //             callback: function () {
        //                 resolve();
        //             }
        //         });
        //     });
        //
        //     Promise.all([personasPromise, areasPromise]).then(() => {
        //         if (!(extraData instanceof Array)) {
        //             const {area_id, operario_id, responsable_id} = extraData;
        //             Ext.getCmp('combo_area').setValue(area_id);
        //             Ext.getCmp('combo_operario').setValue(operario_id);
        //             Ext.getCmp('combo_responsable').setValue(responsable_id);
        //         }
        //         App.unmask();
        //     });
        // };

        if (action === 'generate') {
            // let winform = Ext.create('Portadores.anexos3.Window', {
            //     title: 'Generar análisis',
            //     buttons: [
            //         {
            //             text: 'Aceptar',
            //             width: 70,
            //             handler: function () {
            //                 let form = winform.down('form').getForm();
            //                 if (form.isValid()) {
            //                     let params = form.getValues();
            //                     params.equipo_id = selection.data.id;
            //                     params.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
            //                     params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();
            //
            //                     let makeRequest = () => App.request('POST', url, params, null, null, response => {
            //                         if (response && response.hasOwnProperty('success') && response.success) {
            //                             gridAnexos3.getStore().load();
            //                             winform.close();
            //                         }
            //                     });
            //
            //                     if (gridAnexos3.getStore().getData().length) {
            //                         Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea Generar el Análisis nuevamente? Todo cambio realizado se perderá', function (btn) {
            //                             if (btn === 'yes') {
            //                                 makeRequest();
            //                             }
            //                         });
            //                     } else makeRequest()
            //                 }
            //             }
            //         },
            //         {
            //             text: 'Cancelar',
            //             width: 70,
            //             handler: function () {
            //                 winform.close()
            //             }
            //         }
            //     ],
            //     listeners: {
            //         afterrender: function () {
            //             loadExtraData();
            //         }
            //     }
            // }).show();
            let params = {
                mes:  Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                anno:  Ext.getCmp('mes_anno').getValue().getFullYear(),
                quincena: Ext.getCmp('quincena_combo').getValue()
            };

            App.request('POST', url, params, null, null, response => {
                if (response && response.hasOwnProperty('success') && response.success) {
                    gridAnexos3.getStore().load();
                    // winform.close();
                }
            });
        }
        else if (action === 'extra_data') {
            let winform = Ext.create('Portadores.anexos3.Window', {
                title: 'Actualizar datos extra del análisis',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.anexo_id = extraData.anexo_id;

                                App.request('PUT', url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridAnexos3.getStore().load();
                                        winform.close();
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            winform.close()
                        }
                    }
                ],
                listeners: {
                    afterrender: function () {
                        loadExtraData();
                    }
                }
            }).show();
        }
        else if (action === 'save') {
            Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea guardar los cambios?', function (btn) {
                if (btn === 'yes') {
                    let data = gridAnexos3.getStore().getUpdatedRecords().map(record => {
                        let modified = {};
                        Object.keys(record.modified).forEach(key => modified[key] = record.data[key]);

                        return {
                            id: record.id,
                            ...modified
                        };
                    });

                    App.request('PUT', url, {data: Ext.encode(data)}, null, null, response => {
                        if (response && response.hasOwnProperty('success') && response.success) {
                            gridAnexos3.getStore().load();
                            Ext.getCmp('btn_save').disable();
                        }
                    });
                }
            });
        }
        else if (action === 'export' || action === 'print') {

            let { items } = gridAnexos3.getStore().getData();

            let records = items.map(record => ({...record.data}));

            let params = {
                records: Ext.encode(records),
                mes: mes_anno.getValue().getMonth(),
                anno: mes_anno.getValue().getFullYear(),
                quincena: Ext.getCmp('quincena_combo').getValue()
            };

            App.request('GET', url, params, null, null, response => {
                if (response && response.hasOwnProperty('success') && response.success) {

                    if (action === 'print') {
                        let newWindow = window.open('', '', 'width=800, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        document.close();
                        newWindow.print();
                    }
                    else
                        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(response.html));
                    // console.log(response);
                }
            });
        }
    };

    let _btnGenerate = Ext.create('Ext.button.MyButton', {
        text: 'Generar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler.bind(this, 'generate')
    });
    let _btnExport = Ext.create('Ext.button.MyButton', {
        id: 'btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-excel text-primary',
        disabled: true,
        handler: action_handler.bind(this, 'export')
    });

//
//     let _btnSave = Ext.create('Ext.button.MyButton', {
//         id: 'btn_save',
//         disabled: true,
//         text: 'Guardar',
//         iconCls: 'fas fa-save text-primary',
//         width: 100,
//         handler: action_handler.bind(this, 'save')
//     });
//
//     let _btnExtraData = Ext.create('Ext.button.MyButton', {
//         id: 'btn_extra_data',
//         text: 'Otros Datos',
//         disabled: true,
//         iconCls: 'fas fa-bars text-primary',
//         width: 100,
//         handler: action_handler.bind(this, 'extra_data')
//     });
//
//     // let _btnExport = Ext.create('Ext.button.MyButton', {
//     //     id: 'btn_export',
//     //     text: 'Exportar',
//     //     iconCls: 'fas fa-file-excel text-primary',
//     //     disabled: true,
//     //     handler: action_handler.bind(this, 'export')
//     // });
//
//     let _btnPrint = Ext.create('Ext.button.MyButton', {
//         id: 'btn_print',
//         text: 'Imprimir',
//         disabled: true,
//         iconCls: 'fas fa-print text-primary',
//         handler: action_handler.bind(this, 'print')
//     });
//
    let _tbarAnexo3 = Ext.getCmp('gridAnexo3Tbar');
//     _tbarAnexo3.add(_btnExtraData);
//     _tbarAnexo3.add('-');
    _tbarAnexo3.add(_btnGenerate);
    _tbarAnexo3.add('->');
    _tbarAnexo3.add(_btnExport);
//     _tbarAnexo3.add('-');
//     _tbarAnexo3.add(_btnSave);
//     _tbarAnexo3.add('->');
//     _tbarAnexo3.add(_btnPrint);
});
