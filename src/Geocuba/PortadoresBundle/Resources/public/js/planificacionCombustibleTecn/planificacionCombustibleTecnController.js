let mesesArray = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

Ext.onReady(function () {
    const treeStore = Ext.create('Ext.data.TreeStore', {
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
    const panelTree = Ext.create('Ext.tree.Panel', {
        title: 'Unidades',
        store: treeStore,
        region: 'west',
        width: 280,
        id: 'arbolunidades',
        hideHeaders: true,
        border: true,
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
            select: function () {
                grid.enable();
                store_planificacion_combustible.load();
            }
        }
    });

    let store_planificacion_combustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_planificacion_combustible',
        fields: [
            {name: 'id'},
            {name: 'anno'},
            {name: 'aprobada'},
            {name: 'unidad_id'},
            {name: 'tipo_combustible_id'},
            {name: 'tipo_combustible_recirculacion_id'},

            {name: 'equipo_tecnologico_id'},
            {name: 'equipo_tecnologico_descripcion'},
            {name: 'equipo_tecnologico_nro_inventario'},
            {name: 'equipo_tecnologico_norma'},
            {name: 'equipo_tecnologico_norma_recirculacion'},

            {name: 'combustible_ene', type: 'float'},
            {name: 'combustible_feb', type: 'float'},
            {name: 'combustible_mar', type: 'float'},
            {name: 'combustible_abr', type: 'float'},
            {name: 'combustible_may', type: 'float'},
            {name: 'combustible_jun', type: 'float'},
            {name: 'combustible_jul', type: 'float'},
            {name: 'combustible_ago', type: 'float'},
            {name: 'combustible_sep', type: 'float'},
            {name: 'combustible_oct', type: 'float'},
            {name: 'combustible_nov', type: 'float'},
            {name: 'combustible_dic', type: 'float'},
            {name: 'combustible_total', type: 'float'},

            {name: 'nivel_actividad_ene', type: 'float'},
            {name: 'nivel_actividad_feb', type: 'float'},
            {name: 'nivel_actividad_mar', type: 'float'},
            {name: 'nivel_actividad_abr', type: 'float'},
            {name: 'nivel_actividad_may', type: 'float'},
            {name: 'nivel_actividad_jun', type: 'float'},
            {name: 'nivel_actividad_jul', type: 'float'},
            {name: 'nivel_actividad_ago', type: 'float'},
            {name: 'nivel_actividad_sep', type: 'float'},
            {name: 'nivel_actividad_oct', type: 'float'},
            {name: 'nivel_actividad_nov', type: 'float'},
            {name: 'nivel_actividad_dic', type: 'float'},
            {name: 'nivel_actividad_total', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/planificacion_combustible_tecn/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        // groupField: ['actividad_nombre'],
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation) {
                Ext.getCmp('grid_planificacion_combustible').getSelectionModel().deselectAll();
                operation.setParams({
                    ...operation.getParams(),
                    tipo_combustibleid: Ext.getCmp('nTipoCombustibleId').getValue() == null ? '' : Ext.getCmp('nTipoCombustibleId').getValue(),
                    anno: Ext.getCmp('fieldAnnoId').getValue(),
                    unidad_id: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
            load: function (This, records) {
                if (parseInt(App.current_year) === parseInt(App.selected_year)) {
                    Ext.getCmp('fieldMesId').setValue(Ext.getCmp('fieldMesId').getStore().findRecord('nombre', App.getMonthName(App.selected_month)));
                } else {
                    Ext.getCmp('fieldMesId').setValue(Ext.getCmp('fieldMesId').getStore().findRecord('id', 13));
                }

                // if (records.length > 0) {
                //     if (Ext.getCmp('plan_btn_menu'))
                //         Ext.getCmp('plan_btn_menu').setDisabled(false);
                //     if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                //         Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(!records[0].data.aprobada);
                // }
            }
        }
    });

    let edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 2,
        listeners: {
            beforeedit: function (This, e) {
                if (e.record.data.aprobada) {
                    return false;
                }

                if (e.colIdx > 3) {
                    return false;
                }

                if (e.record.data.anno < App.current_year) {
                    return false;
                }
            },
            edit: function (This, e) {
                if (e.originalValue === e.value) {
                    return false;
                }

                let _grid = Ext.getCmp('grid_planificacion_combustible');
                if (Ext.getCmp('planificacion_combustible_btn_mod') !== undefined) {
                    Ext.getCmp('planificacion_combustible_btn_mod').enable();
                    Ext.getCmp('planificacion_combustible_btn_mod').setStyle('borderColor', 'red');
                }

                let norma = e.record.data['tipo_combustible_recirculacion_id'] ===  e.record.data['tipo_combustible_id']
                    ? e.record.data['equipo_tecnologico_norma_recirculacion'] : e.record.data['equipo_tecnologico_norma'];

                if (e.colIdx === 0) {
                    let consumido = e.record.data['combustible_consumido'];
                    if (e.record.data['combustible_total'] < consumido)
                        e.record.data['combustible_total'] = consumido;
                    e.record.data['nivel_actividad_total'] = Ext.util.Format.round(e.record.data['combustible_total'] / norma, 2);
                    DistribuirComb(e.record, consumido);
                }
                
                if (e.colIdx === 1) {
                    let consumido = e.record.data['nivel_actividad_consumido'];
                    if (e.record.data['nivel_actividad_total'] < consumido)
                        e.record.data['nivel_actividad_total'] = consumido;
                    e.record.data['combustible_total'] = Ext.util.Format.round(e.record.data['nivel_actividad_total'] * norma, 2);
                    DistribuirNivelAct(e.record, consumido);
                }

                if (e.colIdx === 2) {
                    e.record.data['nivel_actividad_' + Ext.getCmp('fieldMesId').getValue().toLowerCase() + ''] = Ext.util.Format.round(e.record.data['combustible_' + Ext.getCmp('fieldMesId').getValue().toLowerCase() + ''] / norma, 2);
                }

                if (e.colIdx === 3) {
                    e.record.data['combustible_' + Ext.getCmp('fieldMesId').getValue().toLowerCase() + ''] = Ext.util.Format.round(e.record.data['nivel_actividad_' + Ext.getCmp('fieldMesId').getValue().toLowerCase() + ''] * norma, 2);
                }

                _grid.getView().refresh();
            }
        }
    });

    let searchText = Ext.create('Ext.form.field.SearchText', {
        id: 'find_button_vehiculo',
        emptyText: 'Descripción ...',
        width: 120,
        nameValue: 'search_text'
    });

    let mesesColumns = mesesArray.map((mes, index) => {
        return {
            id: `${Ext.String.capitalize(mes)}`,
            text: `<b>${App.getMonthName(index+1)}</b>`,
            style: { backgroundColor: '#e3e3e3' },
            columns: [
                {
                    text: '<div style="text-align: center"><b>Comb(L)</b></div>',
                    width: 85,
                    dataIndex: `combustible_${mes}`,
                    editor: {
                        xtype: 'numberfield',
                        decimalSeparator: '.',
                        hideTrigger: true
                    },
                    style: {
                        backgroundColor: '#d6e9c6'
                    },
                    hidden: true,
                    hideMode: 'visibility',
                    summaryType: 'sum',
                    summaryRenderer: function (value) {
                        return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                    }
                },
                {
                    text: '<div style="text-align: center"><b>Nivel Act.</b></div>',
                    width: 85,
                    dataIndex: `nivel_actividad_${mes}`,
                    editor: {
                        xtype: 'numberfield',
                        decimalSeparator: '.',
                        hideTrigger: true
                    },
                    style: {
                        backgroundColor: '#d6e9c6'
                    },
                    hidden: true,
                    hideMode: 'visibility',
                    summaryType: 'sum',
                    summaryRenderer: function (value) {
                        return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                    }
                },
            ]
        }
    });

    let grid = Ext.create('Ext.grid.Panel', {
        id: 'grid_planificacion_combustible',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        layout: 'border',
        features: [
            {
                ftype: 'summary',
                dock: 'bottom',
                groupHeaderTpl: [
                    '<div>Tipo de Combustible: {name:this.formatName}</div>',
                    {
                        formatName: function (name) {
                            return Ext.String.trim(name);
                        }
                    }
                ]
            },
            // {
            //     ftype: 'grouping',
            //     groupHeaderTpl: [
            //         '<b>{name:this.formatName}</b>',
            //         {
            //             formatName: function (name) {
            //                 return Ext.String.trim(name);
            //             }
            //         }
            //     ]
            // }
        ],
        store: store_planificacion_combustible,
        plugins: [edit],
        selModel: {
            selType: 'checkboxmodel',
            allowDeselect: true,
            mode: 'MULTI'
        },
        columnWidth: 30,
        columnLines: true,
        columns: [
            {
                text: '<b>Descripción</b>',
                dataIndex: 'equipo_tecnologico_descripcion',
                locked: true,
                width: 140,
                align: 'center',
            },
            {
                text: '<b>Norma</b>',
                // dataIndex: 'equipo_tecnologico_norma',
                locked: true,
                width: 80,
                // formatter: "number('0.0000')",
                align: 'center',
                renderer: function (value, el, record) {
                    return record.data['tipo_combustible_recirculacion_id'] ===  record.data['tipo_combustible_id']
                        ? record.data['equipo_tecnologico_norma_recirculacion'] : record.data['equipo_tecnologico_norma'];
                }
            },
            {
                text: '<b>Año</b>',
                dataIndex: 'anno',
                locked: true,
                width: 60,
                align: 'center',
            },
            {
                id: 'Anno',
                text: '<b>Totales</b>',
                style: { backgroundColor: '#e3e3e3' },
                defaults: { align: 'right' },
                columns: [
                    {
                        text: '<div style="text-align: center"><b>Comb(L)</b></div>',
                        width: 85,
                        dataIndex: 'combustible_total',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: { backgroundColor: '#d6e9c6' },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        text: '<div style="text-align: center"><b>Nivel Act.</b></div>',
                        width: 85,
                        dataIndex: 'nivel_actividad_total',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                ]
            },
            {
                text: '<b>Acumulados Mensuales</b>',
                columns: [
                    {
                        text: '<div style="text-align: center"><b>Comb(L)</b></div>',
                        width: 85,
                        dataIndex: 'combustible_consumido',
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        renderer: function (value, el, record) {
                            record.data['combustible_consumido'] = ConsumidoComb(record);
                            return record.data['combustible_consumido'];
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        text: '<div style="text-align: center"><b>Nivel Act.</b></div>',
                        width: 85,
                        dataIndex: 'nivel_actividad_consumido',
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        renderer: function (value, el, record) {
                            record.data['nivel_actividad_consumido'] = ConsumidoNivelAct(record);
                            return record.data['nivel_actividad_consumido'];
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            ...mesesColumns,
            {
                text: '<div style="text-align: center"><b>Aprobada</b></div>',
                width: 85,
                dataIndex: 'aprobada',
                align: 'center',
                renderer: function (value) {
                    if (value) {
                        return '<div class="text-success"><i class="fa fa-check-circle"></i></div>';
                    } else return '<div class="text-secondary"><i class="fa fa-check-circle"></i></div>';
                }
            }
        ],
        tbar: {
            id: 'planificacion_combustible_tbar',
            items: [
                searchText,
                {
                    xtype: 'numberfield',
                    id: 'fieldAnnoId',
                    fieldLabel: 'Año',
                    labelWidth: 30,
                    width: 100,
                    value: App.selected_year,
                    minValue: 2015,
                    allowBlank: false,
                    listeners: {
                        change: function (This) {
                            if (This.isValid()) grid.getStore().load();
                        }
                    }
                },
                {
                    xtype: 'combomes',
                    id: 'fieldMesId',
                    fieldLabel: 'Mes',
                    valueField: 'min',
                    labelWidth: 30,
                    width: 130,
                    disabled: true,
                    forceSelection: true,
                    triggerAction: 'all',
                    typeAhead: true,
                    queryMode: 'local',
                    listeners: {
                        change: function (combo, newValue) {
                            if (newValue === 'Anno') {
                                Ext.each(Ext.getCmp('fieldMesId').getStore().data.items, function (mes) {
                                    const itemsNewValue = Ext.getCmp(mes.data['min']).items.items;
                                    Ext.each(itemsNewValue, function (item) {
                                        item.setHidden(false);
                                    });
                                });
                            } else {
                                Ext.each(Ext.getCmp('fieldMesId').getStore().data.items, function (mes) {
                                    const itemsNewValue = Ext.getCmp(mes.data['min']).items.items;
                                    Ext.each(itemsNewValue, function (item) {
                                        item.setHidden(true);
                                    });
                                });

                                if (newValue != null) {
                                    const itemsNewValue = Ext.getCmp(newValue).items.items;
                                    Ext.each(itemsNewValue, function (item) {
                                        item.setHidden(false);
                                    });
                                }

                            }
                        }
                    }
                },
                {
                    xtype: 'combobox',
                    name: 'ntipo_combustibleid',
                    id: 'nTipoCombustibleId',
                    fieldLabel: 'Tipo de combustible',
                    labelWidth: 120,
                    width: 280,
                    store: Ext.create('Ext.data.JsonStore', {
                        storeId: 'storeTipoCombustible',
                        fields: [
                            {name: 'id'},
                            {name: 'nombre'}
                        ],
                        proxy: {
                            type: 'ajax',
                            url: App.buildURL('portadores/tipocombustible/loadCombo'),
                            reader: {
                                rootProperty: 'rows'
                            }
                        },
                        autoLoad: true
                    }),
                    displayField: 'nombre',
                    valueField: 'id',
                    typeAhead: true,
                    queryMode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true,
                    editable: true,
                    listeners: {
                        change: function (This, value) {
                            if (value && Ext.getCmp('fieldAnnoId').isValid()) grid.getStore().load();
                        }
                    }
                },
            ]
        },
        listeners: {
            boxready: function (self) {
                searchText.grid = self;
            },
            selectionchange: function (This, selected) {
                if (selected.length === 0) {
                    if (Ext.getCmp('planificacion_combustible_btn_aprobar'))
                        Ext.getCmp('planificacion_combustible_btn_aprobar').setDisabled(true);
                    if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                        Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(true);
                } else if ((selected.length === 1)) {
                    if (Ext.getCmp('planificacion_combustible_btn_aprobar'))
                        Ext.getCmp('planificacion_combustible_btn_aprobar').setDisabled(selected[0].data.aprobada);
                    if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                        Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(!selected[0].data.aprobada);
                }
                if (selected.length > 1) {
                    if (Ext.getCmp('planificacion_combustible_btn_aprobar'))
                        Ext.getCmp('planificacion_combustible_btn_aprobar').setDisabled(false);
                    if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                        Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(false);
                }
            }
        }
    });

    let panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Plan de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panelTree, grid]
    });
    App.render(panelContainer);
});

DistribuirComb = function (record, consumido) {
    let meses = parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year) ? 13 - App.current_month : 12;

    let monto_distribuir = Math.floor((record.data['combustible_total'] - consumido) / meses);

    let resto_distribuir = (record.data['combustible_total'] - consumido) % meses;
    // if (e.record.data['aprobada']) {
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month <= index+1) record.data['combustible_' + mes] = monto_distribuir;
        });
        if (App.current_month <= 12) record.data['combustible_dic'] = monto_distribuir + resto_distribuir;
    } else {
        mesesArray.forEach((mes) => {
            record.data['combustible_' + mes] = monto_distribuir;
        });
        record.data['combustible_dic'] = monto_distribuir + resto_distribuir;
    }

    let norma = record.data['tipo_combustible_recirculacion_id'] ===  record.data['tipo_combustible_id']
        ? record.data['equipo_tecnologico_norma_recirculacion'] : record.data['equipo_tecnologico_norma'];

    let monto = Ext.util.Format.round(monto_distribuir / norma, 2);
    let resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) / norma, 2);

    // if (e.record.data['aprobada']) {
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month <= index+1) record.data['nivel_actividad_' + mes] = monto;
        });
        if (App.current_month <= 12) record.data['nivel_actividad_dic'] = resto;
    }
    else {
        mesesArray.forEach((mes) => {
            record.data['nivel_actividad_' + mes] = monto;
        });
        record.data['nivel_actividad_dic'] = resto;
    }
};

DistribuirNivelAct = function (record, consumido) {
    let meses = parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year) ? 13 - App.current_month : 12;

    let monto_distribuir = Math.floor((record.data['nivel_actividad_total'] - consumido) / meses);
    let resto_distribuir = (record.data['nivel_actividad_total'] - consumido) % meses;

    // if (e.record.data['aprobada']) {
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month <= index+1) record.data['nivel_actividad_' + mes] = monto_distribuir;
        });
        if (App.current_month <= 12) record.data['nivel_actividad_dic'] = monto_distribuir + resto_distribuir;
    } else {
        mesesArray.forEach((mes) => {
            record.data['nivel_actividad_' + mes] = monto_distribuir;
        });
        record.data['nivel_actividad_dic'] = monto_distribuir + resto_distribuir;
    }

    let norma = record.data['tipo_combustible_recirculacion_id'] ===  record.data['tipo_combustible_id']
        ? record.data['equipo_tecnologico_norma_recirculacion'] : record.data['equipo_tecnologico_norma'];

    let monto = Ext.util.Format.round(monto_distribuir * norma, 2);
    let resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) * norma, 2);

    // if (e.record.data['aprobada']) {
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month <= index+1) record.data['nivel_actividad_' + mes] = monto;
        });
        if (App.current_month <= 12) record.data['combustible_dic'] = resto;
    } else {
        mesesArray.forEach((mes) => {
            record.data['nivel_actividad_' + mes] = monto;
        });
        record.data['combustible_dic'] = resto;
    }
};

/**
 * @return {number}
 */
ConsumidoComb = record => {
    let consumido = 0;
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month > index+1) consumido += record.data['combustible_' + mes];
        });
    }
    return consumido;
};

/**
 * @return {number}
 */
ConsumidoNivelAct = record => {
    let consumido = 0;
    // if (e.record.data['aprobada']) {
    if (parseInt(Ext.getCmp('fieldAnnoId').getValue()) === parseInt(App.current_year)) {
        mesesArray.forEach((mes, index) => {
            if (App.current_month > index+1) consumido += record.data['nivel_actividad_' + mes];
        });
    }
    return consumido;
};

//
// Ajustar = function (e, mes, cmn, ccuc, kmmn, kmcuc, lub, lfren) {
//
//     if (mes < 0)
//         return;
//
//     let restoCmn = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + cmn, 2);
//     let restoCcuc = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc, 2);
//     let restoKMmn = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn, 2);
//     let restoKMcuc = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc, 2);
//     let restoLub = Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
//     let restoLFren = Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);
//
//     e.record.data['combustible_' + meses[mes] + ''] = (e.record.data['combustible_' + meses[mes] + ''] + cmn < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + cmn, 2);
//     e.record.data['combustible_' + meses[mes] + '_cuc'] = (e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc, 2);
//     e.record.data['combustible_' + meses[mes]] = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + e.record.data['combustible_' + meses[mes] + '_cuc'], 2);
//     e.record.data['nivel_actividad_' + meses[mes] + ''] = (e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn, 2);
//     e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] = (e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc, 2);
//     e.record.data['nivel_actividad_' + meses[mes]] = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + e.record.data['nivel_actividad_' + meses[mes] + '_cuc'], 2);
//
//     e.record.data['lubricante_' + meses[mes]] = (e.record.data['lubricante_' + meses[mes]] + lub < 0) ? 0 : Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
//     e.record.data['liquido_freno_' + meses[mes]] = (e.record.data['liquido_freno_' + meses[mes]] + lfren < 0) ? 0 : Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);
//
//     if (restoCmn > 0) restoCmn = 0;
//     if (restoCcuc > 0) restoCcuc = 0;
//     if (restoKMmn > 0) restoKMmn = 0;
//     if (restoKMcuc > 0) restoKMcuc = 0;
//     if (restoLub > 0) restoLub = 0;
//     if (restoLFren > 0) restoLFren = 0;
//
//     if ((restoCmn + restoCcuc + restoKMmn + restoKMcuc + restoLub + restoLFren) != 0)
//         Ajustar(e, mes - 1, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLFren);
// };
//
// Calcular = function (grid) {
// //aqui realizo la suma entre las columnas
//     for (let i = 0; i < grid.length; i++) {
//         // grid[i].data.combustible_total = App.round(grid[i].data.combustible_ene + grid[i].data.combustible_feb +
//         //     grid[i].data.combustible_mar + grid[i].data.combustible_abr + grid[i].data.combustible_may
//         //     + grid[i].data.combustible_jun + grid[i].data.combustible_jul + grid[i].data.combustible_ago
//         //     + grid[i].data.combustible_sep + grid[i].data.combustible_oct + grid[i].data.combustible_nov
//         //     + grid[i].data.combustible_dic, 2);
//
//         // grid[i].data.combustible_total_cuc = App.round(grid[i].data.combustible_ene_cuc + grid[i].data.combustible_feb_cuc +
//         //     grid[i].data.combustible_mar_cuc + grid[i].data.combustible_abr_cuc + grid[i].data.combustible_may_cuc
//         //     + grid[i].data.combustible_jun_cuc + grid[i].data.combustible_jul_cuc + grid[i].data.combustible_ago_cuc
//         //     + grid[i].data.combustible_sep_cuc + grid[i].data.combustible_oct_cuc + grid[i].data.combustible_nov_cuc
//         //     + grid[i].data.combustible_dic_cuc, 2);
//
//         // grid[i].data.combustible_total = Ext.util.Format.round(grid[i].data.combustible_total + grid[i].data.combustible_total_cuc, 2);
//
//         // grid[i].data.nivel_actividad_total = App.round(grid[i].data.nivel_actividad_ene + grid[i].data.nivel_actividad_feb +
//         //     grid[i].data.nivel_actividad_mar + grid[i].data.nivel_actividad_abr + grid[i].data.nivel_actividad_may
//         //     + grid[i].data.nivel_actividad_jun + grid[i].data.nivel_actividad_jul + grid[i].data.nivel_actividad_ago
//         //     + grid[i].data.nivel_actividad_sep + grid[i].data.nivel_actividad_oct + grid[i].data.nivel_actividad_nov
//         //     + grid[i].data.nivel_actividad_dic, 2);
//         //
//         // grid[i].data.nivel_actividad_total_cuc = App.round(grid[i].data.nivel_actividad_ene_cuc + grid[i].data.nivel_actividad_feb_cuc +
//         //     grid[i].data.nivel_actividad_mar_cuc + grid[i].data.nivel_actividad_abr_cuc + grid[i].data.nivel_actividad_may_cuc
//         //     + grid[i].data.nivel_actividad_jun_cuc + grid[i].data.nivel_actividad_jul_cuc + grid[i].data.nivel_actividad_ago_cuc
//         //     + grid[i].data.nivel_actividad_sep_cuc + grid[i].data.nivel_actividad_oct_cuc + grid[i].data.nivel_actividad_nov_cuc
//         //     + grid[i].data.nivel_actividad_dic_cuc, 2);
//         //
//         // grid[i].data.nivel_actividad_total = Ext.util.Format.round(grid[i].data.nivel_actividad_total + grid[i].data.nivel_actividad_total_cuc, 2);
//         //
//         // grid[i].data.lubricante_total = App.round(grid[i].data.lubricante_ene + grid[i].data.lubricante_feb + grid[i].data.lubricante_mar
//         //     + grid[i].data.lubricante_abr + grid[i].data.lubricante_may + grid[i].data.lubricante_jun + grid[i].data.lubricante_jul
//         //     + grid[i].data.lubricante_ago + grid[i].data.lubricante_sep + grid[i].data.lubricante_oct + grid[i].data.lubricante_nov
//         //     + grid[i].data.lubricante_dic, 2);
//         //
//         // grid[i].data.liquido_freno_total = App.round(grid[i].data.liquido_freno_ene + grid[i].data.liquido_freno_feb + grid[i].data.liquido_freno_mar
//         //     + grid[i].data.liquido_freno_abr + grid[i].data.liquido_freno_may + grid[i].data.liquido_freno_jun + grid[i].data.liquido_freno_jul
//         //     + grid[i].data.liquido_freno_ago + grid[i].data.liquido_freno_sep + grid[i].data.liquido_freno_oct + grid[i].data.liquido_freno_nov
//         //     + grid[i].data.liquido_freno_dic, 2);
//     }
//     Ext.getCmp('id_grid_planificacion_combustible').getView().refresh();
// };
//
// Ajustar = function (e, mes, cmn, ccuc, kmmn, kmcuc, lub, lfren) {
//
//     if (mes < 0)
//         return;
//
//     let restoCmn = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + cmn, 2);
//     let restoCcuc = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc, 2);
//     let restoKMmn = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn, 2);
//     let restoKMcuc = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc, 2);
//     let restoLub = Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
//     let restoLFren = Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);
//
//     e.record.data['combustible_' + meses[mes] + ''] = (e.record.data['combustible_' + meses[mes] + ''] + cmn < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + cmn, 2);
//     e.record.data['combustible_' + meses[mes] + '_cuc'] = (e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + '_cuc'] + ccuc, 2);
//     e.record.data['combustible_' + meses[mes]] = Ext.util.Format.round(e.record.data['combustible_' + meses[mes] + ''] + e.record.data['combustible_' + meses[mes] + '_cuc'], 2);
//     e.record.data['nivel_actividad_' + meses[mes] + ''] = (e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + kmmn, 2);
//     e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] = (e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + '_cuc'] + kmcuc, 2);
//     e.record.data['nivel_actividad_' + meses[mes]] = Ext.util.Format.round(e.record.data['nivel_actividad_' + meses[mes] + ''] + e.record.data['nivel_actividad_' + meses[mes] + '_cuc'], 2);
//
//     e.record.data['lubricante_' + meses[mes]] = (e.record.data['lubricante_' + meses[mes]] + lub < 0) ? 0 : Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
//     e.record.data['liquido_freno_' + meses[mes]] = (e.record.data['liquido_freno_' + meses[mes]] + lfren < 0) ? 0 : Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);
//
//     if (restoCmn > 0) restoCmn = 0;
//     if (restoCcuc > 0) restoCcuc = 0;
//     if (restoKMmn > 0) restoKMmn = 0;
//     if (restoKMcuc > 0) restoKMcuc = 0;
//     if (restoLub > 0) restoLub = 0;
//     if (restoLFren > 0) restoLFren = 0;
//
//     if ((restoCmn + restoCcuc + restoKMmn + restoKMcuc + restoLub + restoLFren) != 0)
//         Ajustar(e, mes - 1, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLFren);
// };