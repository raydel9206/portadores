/**
 * Created by pfcadenas on 11/11/2016.
 */

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
            select: function (This, record, tr, rowIndex, e, eOpts) {
                grid.enable();
                store_planificacion_combustible.load();
            }
        }
    });

    var store_planificacion_combustible = Ext.create('Ext.data.JsonStore', {
        storeId: 'store_planificacion_combustible',
        fields: [
            {name: 'id'},
            {name: 'aprobada'},
            {name: 'vehiculoid'},
            {name: 'vehiculo'},
            {name: 'vehiculo_marca'},
            {name: 'vehiculo_denominacion'},
            {name: 'tipo_combustible'},
            {name: 'vehiculo_norma'},
            {name: 'vehiculo_norma_lubricante'},
            {name: 'vehiculo_norma_liquido_freno'},
            {name: 'anno'},

            {name: 'combustible_litros_ene_mn', type: 'float'},
            {name: 'combustible_litros_feb_mn', type: 'float'},
            {name: 'combustible_litros_mar_mn', type: 'float'},
            {name: 'combustible_litros_abr_mn', type: 'float'},
            {name: 'combustible_litros_may_mn', type: 'float'},
            {name: 'combustible_litros_jun_mn', type: 'float'},
            {name: 'combustible_litros_jul_mn', type: 'float'},
            {name: 'combustible_litros_ago_mn', type: 'float'},
            {name: 'combustible_litros_sep_mn', type: 'float'},
            {name: 'combustible_litros_oct_mn', type: 'float'},
            {name: 'combustible_litros_nov_mn', type: 'float'},
            {name: 'combustible_litros_dic_mn', type: 'float'},
            {name: 'combustible_litros_total_mn', type: 'float'},
            {name: 'combustible_litros_total_anno_mn', type: 'float'},

            {name: 'combustible_litros_ene_cuc', type: 'float'},
            {name: 'combustible_litros_feb_cuc', type: 'float'},
            {name: 'combustible_litros_mar_cuc', type: 'float'},
            {name: 'combustible_litros_abr_cuc', type: 'float'},
            {name: 'combustible_litros_may_cuc', type: 'float'},
            {name: 'combustible_litros_jun_cuc', type: 'float'},
            {name: 'combustible_litros_jul_cuc', type: 'float'},
            {name: 'combustible_litros_ago_cuc', type: 'float'},
            {name: 'combustible_litros_sep_cuc', type: 'float'},
            {name: 'combustible_litros_oct_cuc', type: 'float'},
            {name: 'combustible_litros_nov_cuc', type: 'float'},
            {name: 'combustible_litros_dic_cuc', type: 'float'},
            {name: 'combustible_litros_total_cuc', type: 'float'},
            {name: 'combustible_litros_total_anno_cuc', type: 'float'},

            {name: 'combustible_litros_ene', type: 'float'},
            {name: 'combustible_litros_feb', type: 'float'},
            {name: 'combustible_litros_mar', type: 'float'},
            {name: 'combustible_litros_abr', type: 'float'},
            {name: 'combustible_litros_may', type: 'float'},
            {name: 'combustible_litros_jun', type: 'float'},
            {name: 'combustible_litros_jul', type: 'float'},
            {name: 'combustible_litros_ago', type: 'float'},
            {name: 'combustible_litros_sep', type: 'float'},
            {name: 'combustible_litros_oct', type: 'float'},
            {name: 'combustible_litros_nov', type: 'float'},
            {name: 'combustible_litros_dic', type: 'float'},
            {name: 'combustible_litros_total', type: 'float'},
            {name: 'combustible_litros_total_anno', type: 'float'},

            {name: 'nivel_act_kms_ene_mn', type: 'float'},
            {name: 'nivel_act_kms_feb_mn', type: 'float'},
            {name: 'nivel_act_kms_mar_mn', type: 'float'},
            {name: 'nivel_act_kms_abr_mn', type: 'float'},
            {name: 'nivel_act_kms_may_mn', type: 'float'},
            {name: 'nivel_act_kms_jun_mn', type: 'float'},
            {name: 'nivel_act_kms_jul_mn', type: 'float'},
            {name: 'nivel_act_kms_ago_mn', type: 'float'},
            {name: 'nivel_act_kms_sep_mn', type: 'float'},
            {name: 'nivel_act_kms_oct_mn', type: 'float'},
            {name: 'nivel_act_kms_nov_mn', type: 'float'},
            {name: 'nivel_act_kms_dic_mn', type: 'float'},
            {name: 'nivel_act_kms_total_mn', type: 'float'},
            {name: 'nivel_act_kms_total_anno_mn', type: 'float'},

            {name: 'nivel_act_kms_ene_cuc', type: 'float'},
            {name: 'nivel_act_kms_feb_cuc', type: 'float'},
            {name: 'nivel_act_kms_mar_cuc', type: 'float'},
            {name: 'nivel_act_kms_abr_cuc', type: 'float'},
            {name: 'nivel_act_kms_may_cuc', type: 'float'},
            {name: 'nivel_act_kms_jun_cuc', type: 'float'},
            {name: 'nivel_act_kms_jul_cuc', type: 'float'},
            {name: 'nivel_act_kms_ago_cuc', type: 'float'},
            {name: 'nivel_act_kms_sep_cuc', type: 'float'},
            {name: 'nivel_act_kms_oct_cuc', type: 'float'},
            {name: 'nivel_act_kms_nov_cuc', type: 'float'},
            {name: 'nivel_act_kms_dic_cuc', type: 'float'},
            {name: 'nivel_act_kms_total_cuc', type: 'float'},
            {name: 'nivel_act_kms_total_anno_cuc', type: 'float'},

            {name: 'nivel_act_kms_ene', type: 'float'},
            {name: 'nivel_act_kms_feb', type: 'float'},
            {name: 'nivel_act_kms_mar', type: 'float'},
            {name: 'nivel_act_kms_abr', type: 'float'},
            {name: 'nivel_act_kms_may', type: 'float'},
            {name: 'nivel_act_kms_jun', type: 'float'},
            {name: 'nivel_act_kms_jul', type: 'float'},
            {name: 'nivel_act_kms_ago', type: 'float'},
            {name: 'nivel_act_kms_sep', type: 'float'},
            {name: 'nivel_act_kms_oct', type: 'float'},
            {name: 'nivel_act_kms_nov', type: 'float'},
            {name: 'nivel_act_kms_dic', type: 'float'},
            {name: 'nivel_act_kms_total', type: 'float'},
            {name: 'nivel_act_kms_total_anno', type: 'float'},


            {name: 'lubricante_ene', type: 'float'},
            {name: 'lubricante_feb', type: 'float'},
            {name: 'lubricante_mar', type: 'float'},
            {name: 'lubricante_abr', type: 'float'},
            {name: 'lubricante_may', type: 'float'},
            {name: 'lubricante_jun', type: 'float'},
            {name: 'lubricante_jul', type: 'float'},
            {name: 'lubricante_ago', type: 'float'},
            {name: 'lubricante_sep', type: 'float'},
            {name: 'lubricante_oct', type: 'float'},
            {name: 'lubricante_nov', type: 'float'},
            {name: 'lubricante_dic', type: 'float'},
            {name: 'lubricante_total', type: 'float'},
            {name: 'lubricante_total_anno', type: 'float'},
            {name: 'liquido_freno_ene', type: 'float'},
            {name: 'liquido_freno_feb', type: 'float'},
            {name: 'liquido_freno_mar', type: 'float'},
            {name: 'liquido_freno_abr', type: 'float'},
            {name: 'liquido_freno_may', type: 'float'},
            {name: 'liquido_freno_jun', type: 'float'},
            {name: 'liquido_freno_jul', type: 'float'},
            {name: 'liquido_freno_ago', type: 'float'},
            {name: 'liquido_freno_sep', type: 'float'},
            {name: 'liquido_freno_oct', type: 'float'},
            {name: 'liquido_freno_nov', type: 'float'},
            {name: 'liquido_freno_dic', type: 'float'},
            {name: 'liquido_freno_total', type: 'float'},
            {name: 'liquido_freno_total_anno', type: 'float'}
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_combustible/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        groupField: 'vehiculo_denominacion',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                var tipo_combustible = Ext.getCmp('nTipoCombustibleId').getValue() == null ? '' : Ext.getCmp('nTipoCombustibleId').getValue();
                Ext.getCmp('id_grid_planificacion_combustible').getSelectionModel().deselectAll();
                operation.setParams({
                    tipo_combustibleid: tipo_combustible,
                    anno: Ext.getCmp('fieldAnnoId').getValue(),
                    unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                });
            },
            load: function (This, records, successful, eOpts) {
                if (Ext.getCmp('planificacion_combustible_btn_mod'))
                    Ext.getCmp('planificacion_combustible_btn_mod').setDisabled(true);
                if(records){
                if (records.length > 0) {
                    if (Ext.getCmp('plan_btn_menu'))
                        Ext.getCmp('plan_btn_menu').setDisabled(false);
                    // if (Ext.getCmp('planificacion_combustible_btn_print'))
                    //     Ext.getCmp('planificacion_combustible_btn_print').setDisabled(records[0].data.aprobada);
                    // if (Ext.getCmp('planificacion_combustible_btn_export'))
                    //     Ext.getCmp('planificacion_combustible_btn_export').setDisabled(records[0].data.aprobada);

                    if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                        Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(!records[0].data.aprobada);
                }
                }
            }
        }
    });
    var edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 2,
        listeners: {
            beforeedit: function (This, e, eOpts) {
                console.log(e.colIdx);

                if (e.grid.store.data.items[e.rowIdx].data.aprobada && e.colIdx < 8) {
                    console.log('a');
                    return false;
                }

                if (e.grid.store.data.items[e.rowIdx].data.anno < App.current_year) {
                    console.log('b');
                    return false;
                }

                var ncolanteriores = 16;
                var nindicador = 8;
                // if (e.colIdx > 7 && (e.colIdx < (App.current_month - 1) * nindicador + ncolanteriores) || isNaN(e.value)) {
                //     console.log('c');
                //     return false;
                // }

                // if (((e.colIdx - 2) % nindicador == 0) || ((e.colIdx - 5) % nindicador == 0) || isNaN(e.value)) {
                //     console.log('d');
                //     return false;
                // }
            },
            edit: function (This, e, eOpts) {
                if (e.originalValue == e.value)
                    return false;

                var _grid = Ext.getCmp('id_grid_planificacion_combustible');
                if (Ext.getCmp('planificacion_combustible_btn_mod') != undefined) {
                    Ext.getCmp('planificacion_combustible_btn_mod').enable();
                    Ext.getCmp('planificacion_combustible_btn_mod').setStyle('borderColor', 'red');
                }

                var ncolanteriores = 16;
                var nindicador = 8;
                if (e.colIdx == 0) {
                    var consumido = ConsumidoCMN(e);
                    if (e.record.data['combustible_litros_total_anno_mn'] < consumido)
                        e.record.data['combustible_litros_total_anno_mn'] = consumido;
                    e.record.data['nivel_act_kms_total_anno_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_mn'] * e.record.data['vehiculo_norma'], 2);
                    DistribuirCMN(e, consumido);
                }
                if (e.colIdx == 8) {
                    e.record.data['nivel_act_kms_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                // if (e.colIdx == 1 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_feb_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_feb_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 2 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_mar_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_mar_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 3 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_abr_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_abr_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 4 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_may_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_may_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 5 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_jun_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_jun_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 6 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_jul_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_jul_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 7 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_ago_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_ago_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 8 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_sep_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_sep_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 9 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_oct_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_oct_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 10 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_nov_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_nov_mn'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 11 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_dic_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_dic_mn'] * e.record.data['vehiculo_norma'], 2);
                // }

                ncolanteriores = 17;
                if (e.colIdx == 1) {
                    var consumido = ConsumidoCCUC(e);
                    if (e.record.data['combustible_litros_total_anno_cuc'] < consumido)
                        e.record.data['combustible_litros_total_anno_cuc'] = consumido;
                    e.record.data['nivel_act_kms_total_anno_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_cuc'] * e.record.data['vehiculo_norma'], 2);
                    DistribuirCCUC(e, consumido);
                }
                if (e.colIdx == 9) {
                    e.record.data['nivel_act_kms_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                // if (e.colIdx == 1 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_feb_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_feb_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 2 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_mar_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_mar_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 3 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_abr_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_abr_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 4 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_may_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_may_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 5 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_jun_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_jun_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 6 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_jul_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_jul_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 7 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_ago_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_ago_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 8 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_sep_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_sep_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 9 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_oct_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_oct_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 10 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_nov_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_nov_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 11 * nindicador + ncolanteriores) {
                //     e.record.data['nivel_act_kms_dic_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_dic_cuc'] * e.record.data['vehiculo_norma'], 2);
                // }

                ncolanteriores = 19;
                if (e.colIdx == 3) {
                    var consumido = ConsumidoKMMN(e);
                    if (e.record.data['nivel_act_kms_total_anno_mn'] < consumido)
                        e.record.data['nivel_act_kms_total_anno_mn'] = consumido;
                    e.record.data['combustible_litros_total_anno_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_mn'] / e.record.data['vehiculo_norma'], 2);
                    DistribuirKMMN(e, consumido);
                }
                if (e.colIdx == 11) {
                    e.record.data['combustible_litros_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                // if (e.colIdx == 1 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_feb_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_feb_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 2 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_mar_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_mar_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 3 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_abr_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_abr_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 4 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_may_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_may_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 5 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_jun_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jun_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 6 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_jul_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jul_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 7 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_ago_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ago_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 8 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_sep_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_sep_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 9 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_oct_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_oct_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 10 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_nov_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_nov_mn'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 11 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_dic_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_dic_mn'] / e.record.data['vehiculo_norma'], 2);
                // }

                ncolanteriores = 20;
                if (e.colIdx == 4) {
                    var consumido = ConsumidoKMCUC(e);
                    if (e.record.data['nivel_act_kms_total_anno_cuc'] < consumido)
                        e.record.data['nivel_act_kms_total_anno_cuc'] = consumido;
                    e.record.data['combustible_litros_total_anno_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_cuc'] / e.record.data['vehiculo_norma'], 2);
                    DistribuirKMCUC(e, consumido);
                }
                if (e.colIdx == 12) {
                    e.record.data['combustible_litros_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_'+Ext.getCmp('fieldMesId').getValue().toLowerCase()+'_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                // if (e.colIdx == 1 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_feb_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_feb_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 2 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_mar_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_mar_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 3 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_abr_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_abr_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 4 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_may_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_may_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 5 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_jun_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jun_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 6 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_jul_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jul_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 7 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_ago_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ago_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 8 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_sep_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_sep_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 9 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_oct_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_oct_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 10 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_nov_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_nov_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }
                // if (e.colIdx == 11 * nindicador + ncolanteriores) {
                //     e.record.data['combustible_litros_dic_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_dic_cuc'] / e.record.data['vehiculo_norma'], 2);
                // }

                if (e.colIdx % nindicador != 6 && e.colIdx % nindicador != 7) {
                    e.record.data['combustible_litros_total_anno'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_mn'] + e.record.data['combustible_litros_total_anno_cuc'], 2);
                    e.record.data['nivel_act_kms_total_anno'] = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_mn'] + e.record.data['nivel_act_kms_total_anno_cuc'], 2);
                    e.record.data['lubricante_total_anno'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_total_anno'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_ene'] = Ext.util.Format.round(e.record.data['combustible_litros_ene_mn'] + e.record.data['combustible_litros_ene_cuc'], 2);
                    e.record.data['nivel_act_kms_ene'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ene_mn'] + e.record.data['nivel_act_kms_ene_cuc'], 2);
                    e.record.data['lubricante_ene'] = Ext.util.Format.round(e.record.data['combustible_litros_ene'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_ene'] = Ext.util.Format.round(e.record.data['combustible_litros_ene'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_feb'] = Ext.util.Format.round(e.record.data['combustible_litros_feb_mn'] + e.record.data['combustible_litros_feb_cuc'], 2);
                    e.record.data['nivel_act_kms_feb'] = Ext.util.Format.round(e.record.data['nivel_act_kms_feb_mn'] + e.record.data['nivel_act_kms_feb_cuc'], 2);
                    e.record.data['lubricante_feb'] = Ext.util.Format.round(e.record.data['combustible_litros_feb'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_feb'] = Ext.util.Format.round(e.record.data['combustible_litros_feb'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_mar'] = Ext.util.Format.round(e.record.data['combustible_litros_mar_mn'] + e.record.data['combustible_litros_mar_cuc'], 2);
                    e.record.data['nivel_act_kms_mar'] = Ext.util.Format.round(e.record.data['nivel_act_kms_mar_mn'] + e.record.data['nivel_act_kms_mar_cuc'], 2);
                    e.record.data['lubricante_mar'] = Ext.util.Format.round(e.record.data['combustible_litros_mar'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_mar'] = Ext.util.Format.round(e.record.data['combustible_litros_mar'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_abr'] = Ext.util.Format.round(e.record.data['combustible_litros_abr_mn'] + e.record.data['combustible_litros_abr_cuc'], 2);
                    e.record.data['nivel_act_kms_abr'] = Ext.util.Format.round(e.record.data['nivel_act_kms_abr_mn'] + e.record.data['nivel_act_kms_abr_cuc'], 2);
                    e.record.data['lubricante_abr'] = Ext.util.Format.round(e.record.data['combustible_litros_abr'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_abr'] = Ext.util.Format.round(e.record.data['combustible_litros_abr'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_may'] = Ext.util.Format.round(e.record.data['combustible_litros_may_mn'] + e.record.data['combustible_litros_may_cuc'], 2);
                    e.record.data['nivel_act_kms_may'] = Ext.util.Format.round(e.record.data['nivel_act_kms_may_mn'] + e.record.data['nivel_act_kms_may_cuc'], 2);
                    e.record.data['lubricante_may'] = Ext.util.Format.round(e.record.data['combustible_litros_may'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_may'] = Ext.util.Format.round(e.record.data['combustible_litros_may'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_jun'] = Ext.util.Format.round(e.record.data['combustible_litros_jun_mn'] + e.record.data['combustible_litros_jun_cuc'], 2);
                    e.record.data['nivel_act_kms_jun'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jun_mn'] + e.record.data['nivel_act_kms_jun_cuc'], 2);
                    e.record.data['lubricante_jun'] = Ext.util.Format.round(e.record.data['combustible_litros_jun'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_jun'] = Ext.util.Format.round(e.record.data['combustible_litros_jun'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_jul'] = Ext.util.Format.round(e.record.data['combustible_litros_jul_mn'] + e.record.data['combustible_litros_jul_cuc'], 2);
                    e.record.data['nivel_act_kms_jul'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jul_mn'] + e.record.data['nivel_act_kms_jul_cuc'], 2);
                    e.record.data['lubricante_jul'] = Ext.util.Format.round(e.record.data['combustible_litros_jul'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_jul'] = Ext.util.Format.round(e.record.data['combustible_litros_jul'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_ago'] = Ext.util.Format.round(e.record.data['combustible_litros_ago_mn'] + e.record.data['combustible_litros_ago_cuc'], 2);
                    e.record.data['nivel_act_kms_ago'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ago_mn'] + e.record.data['nivel_act_kms_ago_cuc'], 2);
                    e.record.data['lubricante_ago'] = Ext.util.Format.round(e.record.data['combustible_litros_ago'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_ago'] = Ext.util.Format.round(e.record.data['combustible_litros_ago'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_sep'] = Ext.util.Format.round(e.record.data['combustible_litros_sep_mn'] + e.record.data['combustible_litros_sep_cuc'], 2);
                    e.record.data['nivel_act_kms_sep'] = Ext.util.Format.round(e.record.data['nivel_act_kms_sep_mn'] + e.record.data['nivel_act_kms_sep_cuc'], 2);
                    e.record.data['lubricante_sep'] = Ext.util.Format.round(e.record.data['combustible_litros_sep'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_sep'] = Ext.util.Format.round(e.record.data['combustible_litros_sep'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_oct'] = Ext.util.Format.round(e.record.data['combustible_litros_oct_mn'] + e.record.data['combustible_litros_oct_cuc'], 2);
                    e.record.data['nivel_act_kms_oct'] = Ext.util.Format.round(e.record.data['nivel_act_kms_oct_mn'] + e.record.data['nivel_act_kms_oct_cuc'], 2);
                    e.record.data['lubricante_oct'] = Ext.util.Format.round(e.record.data['combustible_litros_oct'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_oct'] = Ext.util.Format.round(e.record.data['combustible_litros_oct'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_nov'] = Ext.util.Format.round(e.record.data['combustible_litros_nov_mn'] + e.record.data['combustible_litros_nov_cuc'], 2);
                    e.record.data['nivel_act_kms_nov'] = Ext.util.Format.round(e.record.data['nivel_act_kms_nov_mn'] + e.record.data['nivel_act_kms_nov_cuc'], 2);
                    e.record.data['lubricante_nov'] = Ext.util.Format.round(e.record.data['combustible_litros_nov'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_nov'] = Ext.util.Format.round(e.record.data['combustible_litros_nov'] * e.record.data['vehiculo_norma_liquido_freno'], 2);

                    e.record.data['combustible_litros_dic'] = Ext.util.Format.round(e.record.data['combustible_litros_dic_mn'] + e.record.data['combustible_litros_dic_cuc'], 2);
                    e.record.data['nivel_act_kms_dic'] = Ext.util.Format.round(e.record.data['nivel_act_kms_dic_mn'] + e.record.data['nivel_act_kms_dic_cuc'], 2);
                    e.record.data['lubricante_dic'] = Ext.util.Format.round(e.record.data['combustible_litros_dic'] * e.record.data['vehiculo_norma_lubricante'], 2);
                    e.record.data['liquido_freno_dic'] = Ext.util.Format.round(e.record.data['combustible_litros_dic'] * e.record.data['vehiculo_norma_liquido_freno'], 2);
                }

                Calcular(Ext.getCmp('id_grid_planificacion_combustible').getStore().data.items);

                var restoCmn = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_mn'] - e.record.data['combustible_litros_total_mn'], 2);
                var restoCcuc = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_cuc'] - e.record.data['combustible_litros_total_cuc'], 2);
                var restoKMmn = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_mn'] - e.record.data['nivel_act_kms_total_mn'], 2);
                var restoKMcuc = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_cuc'] - e.record.data['nivel_act_kms_total_cuc'], 2);
                var restoLub = Ext.util.Format.round(e.record.data['lubricante_total_anno'] - e.record.data['lubricante_total'], 2);
                var restoLfre = Ext.util.Format.round(e.record.data['liquido_freno_total_anno'] - e.record.data['liquido_freno_total'], 2);

                if ((e.colIdx == 11 * nindicador + 20) || (e.colIdx == 11 * nindicador + 19) || (e.colIdx == 11 * nindicador + 17) || (e.colIdx == 11 * nindicador + 16) || (e.colIdx == 11 * nindicador + 22) || (e.colIdx == 11 * nindicador + 23)) {
                    if ((restoCmn < 0) || (restoCcuc < 0) || (restoKMmn < 0) || (restoKMcuc < 0)) {
                        Ajustar(e, 11, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLfre);
                    }
                } else {
                    Ajustar(e, 11, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLfre);
                }
                Calcular(Ext.getCmp('id_grid_planificacion_combustible').getStore().data.items);

                _grid.getView().refresh();
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_planificacion_combustible',
        region: 'center',
        width: '75%',
        height: '100%',
        disabled: true,
        layout: 'border',
        features: [{
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
        }, {
            ftype: 'grouping',
            groupHeaderTpl: [
                '<b>{name:this.formatName}</b>',
                {
                    formatName: function (name) {
                        return Ext.String.trim(name);
                    }
                }
            ]
        }],
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
                xtype: 'gridcolumn',
                text: '<b>Vehículo</b>',
                dataIndex: 'vehiculo',
                locked: true,
                width: 100,
                align: 'center',
                name: 'vehiculo'
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Norma</b>',
                dataIndex: 'vehiculo_norma',
                locked: true,
                width: 70,
                formatter: "number('0.00')",
                align: 'center',
                name: 'vehiculo_norma'
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Año</b>',
                dataIndex: 'anno',
                locked: true,
                width: 60,
                align: 'center',
                name: 'anno'
            },
            {
                xtype: 'gridcolumn',
                id: 'Anno',
                text: '<b>Totales</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },

                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total_anno_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total_anno_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total_anno',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total_anno_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total_anno_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total_anno',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_total_anno',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_total_anno',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                text: '<b>Acumulados Mensuales</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total_mn',
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total_cuc',
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_total',
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total_mn',
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total_cuc',
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_total',
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        // hidden: true,
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_total',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        formatter: "number('0.00')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_total',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        formatter: "number('0.00')",
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Ene',
                text: '<b>Enero</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ene_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ene_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ene',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ene_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ene_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ene',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_ene',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_ene',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Feb',
                text: '<b>Febrero</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_feb_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_feb_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_feb',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_feb_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_feb_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_feb',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_feb',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_feb',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Mar',
                text: '<b>Marzo</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_mar_mn',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        style: {
                            backgroundColor: '#d6e9c6'
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_mar_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_mar',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_mar_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_mar_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_mar',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_mar',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_mar',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Abr',
                text: '<b>Abril</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_abr_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_abr_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_abr',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_abr_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_abr_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_abr',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_abr',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_abr',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'May',
                text: '<b>Mayo</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_may_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_may_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_may',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_may_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_may_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_may',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_may',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_may',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Jun',
                text: '<b>Junio</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jun_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jun_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jun',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jun_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jun_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jun',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_jun',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_jun',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Jul',
                text: '<b>Julio</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jul_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jul_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_jul',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jul_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jul_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_jul',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_jul',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_jul',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Ago',
                text: '<b>Agosto</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ago_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ago_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_ago',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ago_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ago_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_ago',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_ago',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_ago',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Sep',
                text: '<b>Septiembre</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_sep_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_sep_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_sep',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_sep_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_sep_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_sep',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_sep',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_sep',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Oct',
                text: '<b>Octubre</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_oct_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_oct_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_oct',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_oct_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_oct_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_oct',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_oct',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_oct',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Nov',
                text: '<b>Noviembre</b>',
                style: {
                    backgroundColor: '#e3e3e3'
                },
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_nov_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_nov_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_nov',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_nov_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_nov_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_nov',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_nov',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_nov',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                id: 'Dic',
                text: '<b>Diciembre</b>',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. MN</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_dic_mn',
                        editor: {
                            xtype: 'numberfield',
                            // decimalSeparator: '.',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. CUC</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_dic_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Comb. Total</b></div>',
                        width: 85,
                        dataIndex: 'combustible_litros_dic',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. MN</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_dic_mn',
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
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. CUC</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_dic_cuc',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#faebcc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Kms. Total</b></div>',
                        width: 85,
                        dataIndex: 'nivel_act_kms_dic',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#cccccc'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        },
                        renderer: function (value, eOpts) {
                            eOpts.style = 'font-weight: bold;background: #cccccc;';
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value,2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Lubricante</b></div>',
                        width: 85,
                        dataIndex: 'lubricante_dic',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        text: '<div style="text-align: center"><b>Liquido de <br>Freno</b></div>',
                        width: 85,
                        dataIndex: 'liquido_freno_dic',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        style: {
                            backgroundColor: '#fff'
                        },
                        hidden: true,
                        hideMode: 'visibility',
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', Ext.util.Format.round(value, 2));
                        }
                    }
                ]
            },
            {
                xtype: 'gridcolumn',
                text: '<div style="text-align: center"><b>Aprobada</b></div>',
                width: 85,
                dataIndex: 'aprobada',
                renderer: function (value) {
                    if (value) {
                        return '<div class="badge-true">SI</div>';
                    }
                    else return '<div class="badge-false">NO</div>';
                }
            }
        ],
        dockedItems: [
            {
                xtype: 'toolbar',
                id: 'planificacion_combustible_tbar',
                dock: 'top',
                items: [

                    Ext.create('Ext.form.field.Text', {
                        id: 'find_button_vehiculo',
                        emptyText: 'Matrícula...',
                        width: 90,
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (This, e, eOpts) {
                                store_planificacion_combustible.filterBy(function (record) {
                                    return record.data.vehiculo.search(This.value) !== -1;
                                }, this);
                            },
                            change: function (field, newValue, oldValue, eOpt) {
                                field.getTrigger('clear').setVisible(newValue);
                            },
                        },
                        triggers: {
                            clear: {
                                cls: Ext.baseCSSPrefix + 'form-clear-trigger',
                                hidden: true,
                                handler: function () {
                                    this.setValue(null);
                                    this.updateLayout();

                                    store_planificacion_combustible.clearFilter();
                                    this.setMarked(false);
                                }
                            }
                        },

                        setMarked: function (marked) {
                            var el = this.getEl(),
                                id = '#' + this.getId();

                            this.marked = marked;

                            if (marked) {
                                el.down(id + '-inputEl').addCls('x-form-invalid-field x-form-invalid-field-default');
                                el.down(id + '-inputWrap').addCls('form-text-wrap-invalid');
                                el.down(id + '-triggerWrap').addCls('x-form-trigger-wrap-invalid');
                            } else {
                                el.down(id + '-inputEl').removeCls('x-form-invalid-field x-form-invalid-field-default');
                                el.down(id + '-inputWrap').removeCls('form-text-wrap-invalid');
                                el.down(id + '-triggerWrap').removeCls('x-form-trigger-wrap-invalid');
                            }
                        }
                    }),
                    {
                        xtype: 'numberfield',
                        id: 'fieldAnnoId',
                        fieldLabel: 'Año',
                        labelWidth: 30,
                        width: 100,
                        value: App.selected_year,
                        minValue: 2015,
                        listeners: {
                            change: function (This, newValue, oldValue, eOpts) {
                                if (newValue >= 2015)
                                    grid.getStore().load();
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
                        forceSelection: true,
                        triggerAction: 'all',
                        typeAhead: true,
                        queryMode: 'local',
                        listeners: {
                            change: function (combo, newValue, oldValue, eOpts) {
                                if (newValue != null) {
                                    const itemsNewValue = Ext.getCmp(newValue).items.items;
                                    Ext.each(itemsNewValue, function (item, index) {
                                        item.setHidden(false);
                                    });
                                }
                                if (oldValue != null) {
                                    const itemsOldValue = Ext.getCmp(oldValue).items.items;
                                    Ext.each(itemsOldValue, function (item, index) {
                                        item.setHidden(true);
                                    });
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
                            change: function (This, newValue, oldValue, eOpts) {
                                grid.getStore().load();
                            }
                        }
                    },
                ]
            }

        ],
        // tbar: {
        //     id: 'planificacion_combustible_tbar',
        //     height: 36,
        //     items: [cmbTipoCombustibleSearch, textSearch, textAnnoSearch, btnSearch, btnClearSearch, '-']
        // },

        listeners: {
            selectionchange: function (This, selected, eOpts) {
                if (selected.length == 0) {
                    if (Ext.getCmp('planificacion_combustible_btn_aprobar'))
                        Ext.getCmp('planificacion_combustible_btn_aprobar').setDisabled(true);
                    if (Ext.getCmp('planificacion_combustible_btn_desaprobar'))
                        Ext.getCmp('planificacion_combustible_btn_desaprobar').setDisabled(true);
                }
                else if ((selected.length == 1)) {
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
    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Plan de Combustible',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panelTree, grid]
    });
    App.render(panelContainer);
});

ConsumidoCMN = function (e) {

    var consumido = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month > 1) consumido += e.record.data['combustible_litros_ene_mn'];
        if (App.current_month > 2) consumido += e.record.data['combustible_litros_feb_mn'];
        if (App.current_month > 3) consumido += e.record.data['combustible_litros_mar_mn'];
        if (App.current_month > 4) consumido += e.record.data['combustible_litros_abr_mn'];
        if (App.current_month > 5) consumido += e.record.data['combustible_litros_may_mn'];
        if (App.current_month > 6) consumido += e.record.data['combustible_litros_jun_mn'];
        if (App.current_month > 7) consumido += e.record.data['combustible_litros_jul_mn'];
        if (App.current_month > 8) consumido += e.record.data['combustible_litros_ago_mn'];
        if (App.current_month > 9) consumido += e.record.data['combustible_litros_sep_mn'];
        if (App.current_month > 10) consumido += e.record.data['combustible_litros_oct_mn'];
        if (App.current_month > 11) consumido += e.record.data['combustible_litros_nov_mn'];
        if (App.current_month > 12) consumido += e.record.data['combustible_litros_dic_mn'];
    }

    return consumido;
};

DistribuirCMN = function (e, consumido) {
    var meses = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year)
        meses = 13 - App.current_month;
    else
        meses = 12;

    var monto_distribuir = Math.floor((e.record.data['combustible_litros_total_anno_mn'] - consumido) / meses);
    var resto_distribuir = (e.record.data['combustible_litros_total_anno_mn'] - consumido) % meses;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['combustible_litros_ene_mn'] = monto_distribuir;
        if (App.current_month <= 2) e.record.data['combustible_litros_feb_mn'] = monto_distribuir;
        if (App.current_month <= 3) e.record.data['combustible_litros_mar_mn'] = monto_distribuir;
        if (App.current_month <= 4) e.record.data['combustible_litros_abr_mn'] = monto_distribuir;
        if (App.current_month <= 5) e.record.data['combustible_litros_may_mn'] = monto_distribuir;
        if (App.current_month <= 6) e.record.data['combustible_litros_jun_mn'] = monto_distribuir;
        if (App.current_month <= 7) e.record.data['combustible_litros_jul_mn'] = monto_distribuir;
        if (App.current_month <= 8) e.record.data['combustible_litros_ago_mn'] = monto_distribuir;
        if (App.current_month <= 9) e.record.data['combustible_litros_sep_mn'] = monto_distribuir;
        if (App.current_month <= 10) e.record.data['combustible_litros_oct_mn'] = monto_distribuir;
        if (App.current_month <= 11) e.record.data['combustible_litros_nov_mn'] = monto_distribuir;
        if (App.current_month <= 12) e.record.data['combustible_litros_dic_mn'] = monto_distribuir + resto_distribuir;
    }else{
        e.record.data['combustible_litros_ene_mn'] = monto_distribuir;
        e.record.data['combustible_litros_feb_mn'] = monto_distribuir;
        e.record.data['combustible_litros_mar_mn'] = monto_distribuir;
        e.record.data['combustible_litros_abr_mn'] = monto_distribuir;
        e.record.data['combustible_litros_may_mn'] = monto_distribuir;
        e.record.data['combustible_litros_jun_mn'] = monto_distribuir;
        e.record.data['combustible_litros_jul_mn'] = monto_distribuir;
        e.record.data['combustible_litros_ago_mn'] = monto_distribuir;
        e.record.data['combustible_litros_sep_mn'] = monto_distribuir;
        e.record.data['combustible_litros_oct_mn'] = monto_distribuir;
        e.record.data['combustible_litros_nov_mn'] = monto_distribuir;
        e.record.data['combustible_litros_dic_mn'] = monto_distribuir + resto_distribuir;
    }

    var monto = Ext.util.Format.round(monto_distribuir * e.record.data['vehiculo_norma'], 2);
    var resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) * e.record.data['vehiculo_norma'], 2);

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['nivel_act_kms_ene_mn'] = monto;
        if (App.current_month <= 2) e.record.data['nivel_act_kms_feb_mn'] = monto;
        if (App.current_month <= 3) e.record.data['nivel_act_kms_mar_mn'] = monto;
        if (App.current_month <= 4) e.record.data['nivel_act_kms_abr_mn'] = monto;
        if (App.current_month <= 5) e.record.data['nivel_act_kms_may_mn'] = monto;
        if (App.current_month <= 6) e.record.data['nivel_act_kms_jun_mn'] = monto;
        if (App.current_month <= 7) e.record.data['nivel_act_kms_jul_mn'] = monto;
        if (App.current_month <= 8) e.record.data['nivel_act_kms_ago_mn'] = monto;
        if (App.current_month <= 9) e.record.data['nivel_act_kms_sep_mn'] = monto;
        if (App.current_month <= 10) e.record.data['nivel_act_kms_oct_mn'] = monto;
        if (App.current_month <= 11) e.record.data['nivel_act_kms_nov_mn'] = monto;
        if (App.current_month <= 12) e.record.data['nivel_act_kms_dic_mn'] = resto;
    }else{
        e.record.data['nivel_act_kms_ene_mn'] = monto;
        e.record.data['nivel_act_kms_feb_mn'] = monto;
        e.record.data['nivel_act_kms_mar_mn'] = monto;
        e.record.data['nivel_act_kms_abr_mn'] = monto;
        e.record.data['nivel_act_kms_may_mn'] = monto;
        e.record.data['nivel_act_kms_jun_mn'] = monto;
        e.record.data['nivel_act_kms_jul_mn'] = monto;
        e.record.data['nivel_act_kms_ago_mn'] = monto;
        e.record.data['nivel_act_kms_sep_mn'] = monto;
        e.record.data['nivel_act_kms_oct_mn'] = monto;
        e.record.data['nivel_act_kms_nov_mn'] = monto;
        e.record.data['nivel_act_kms_dic_mn'] = resto;
    }
};

ConsumidoKMMN = function (e) {

    var consumido = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month > 1) consumido += e.record.data['nivel_act_kms_ene_mn'];
        if (App.current_month > 2) consumido += e.record.data['nivel_act_kms_feb_mn'];
        if (App.current_month > 3) consumido += e.record.data['nivel_act_kms_mar_mn'];
        if (App.current_month > 4) consumido += e.record.data['nivel_act_kms_abr_mn'];
        if (App.current_month > 5) consumido += e.record.data['nivel_act_kms_may_mn'];
        if (App.current_month > 6) consumido += e.record.data['nivel_act_kms_jun_mn'];
        if (App.current_month > 7) consumido += e.record.data['nivel_act_kms_jul_mn'];
        if (App.current_month > 8) consumido += e.record.data['nivel_act_kms_ago_mn'];
        if (App.current_month > 9) consumido += e.record.data['nivel_act_kms_sep_mn'];
        if (App.current_month > 10) consumido += e.record.data['nivel_act_kms_oct_mn'];
        if (App.current_month > 11) consumido += e.record.data['nivel_act_kms_nov_mn'];
        if (App.current_month > 12) consumido += e.record.data['nivel_act_kms_dic_mn'];
    }

    return consumido;
};

DistribuirKMMN = function (e, consumido) {
    var meses = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year)
        meses = 13 - App.current_month;
    else
        meses = 12;

    var monto_distribuir = Math.floor((e.record.data['nivel_act_kms_total_anno_mn'] - consumido) / meses);
    var resto_distribuir = (e.record.data['nivel_act_kms_total_anno_mn'] - consumido) % meses;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['nivel_act_kms_ene_mn'] = monto_distribuir;
        if (App.current_month <= 2) e.record.data['nivel_act_kms_feb_mn'] = monto_distribuir;
        if (App.current_month <= 3) e.record.data['nivel_act_kms_mar_mn'] = monto_distribuir;
        if (App.current_month <= 4) e.record.data['nivel_act_kms_abr_mn'] = monto_distribuir;
        if (App.current_month <= 5) e.record.data['nivel_act_kms_may_mn'] = monto_distribuir;
        if (App.current_month <= 6) e.record.data['nivel_act_kms_jun_mn'] = monto_distribuir;
        if (App.current_month <= 7) e.record.data['nivel_act_kms_jul_mn'] = monto_distribuir;
        if (App.current_month <= 8) e.record.data['nivel_act_kms_ago_mn'] = monto_distribuir;
        if (App.current_month <= 9) e.record.data['nivel_act_kms_sep_mn'] = monto_distribuir;
        if (App.current_month <= 10) e.record.data['nivel_act_kms_oct_mn'] = monto_distribuir;
        if (App.current_month <= 11) e.record.data['nivel_act_kms_nov_mn'] = monto_distribuir;
        if (App.current_month <= 12) e.record.data['nivel_act_kms_dic_mn'] = monto_distribuir + resto_distribuir;
    }else{
        e.record.data['nivel_act_kms_ene_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_feb_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_mar_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_abr_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_may_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_jun_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_jul_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_ago_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_sep_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_oct_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_nov_mn'] = monto_distribuir;
        e.record.data['nivel_act_kms_dic_mn'] = monto_distribuir + resto_distribuir;
    }

    var monto = Ext.util.Format.round(monto_distribuir / e.record.data['vehiculo_norma'], 2);
    var resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) / e.record.data['vehiculo_norma'], 2);

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['combustible_litros_ene_mn'] = monto;
        if (App.current_month <= 2) e.record.data['combustible_litros_feb_mn'] = monto;
        if (App.current_month <= 3) e.record.data['combustible_litros_mar_mn'] = monto;
        if (App.current_month <= 4) e.record.data['combustible_litros_abr_mn'] = monto;
        if (App.current_month <= 5) e.record.data['combustible_litros_may_mn'] = monto;
        if (App.current_month <= 6) e.record.data['combustible_litros_jun_mn'] = monto;
        if (App.current_month <= 7) e.record.data['combustible_litros_jul_mn'] = monto;
        if (App.current_month <= 8) e.record.data['combustible_litros_ago_mn'] = monto;
        if (App.current_month <= 9) e.record.data['combustible_litros_sep_mn'] = monto;
        if (App.current_month <= 10) e.record.data['combustible_litros_oct_mn'] = monto;
        if (App.current_month <= 11) e.record.data['combustible_litros_nov_mn'] = monto;
        if (App.current_month <= 12) e.record.data['combustible_litros_dic_mn'] = resto;
    }else{
        e.record.data['combustible_litros_ene_mn'] = monto;
        e.record.data['combustible_litros_feb_mn'] = monto;
        e.record.data['combustible_litros_mar_mn'] = monto;
        e.record.data['combustible_litros_abr_mn'] = monto;
        e.record.data['combustible_litros_may_mn'] = monto;
        e.record.data['combustible_litros_jun_mn'] = monto;
        e.record.data['combustible_litros_jul_mn'] = monto;
        e.record.data['combustible_litros_ago_mn'] = monto;
        e.record.data['combustible_litros_sep_mn'] = monto;
        e.record.data['combustible_litros_oct_mn'] = monto;
        e.record.data['combustible_litros_nov_mn'] = monto;
        e.record.data['combustible_litros_dic_mn'] = resto;
    }
};

ConsumidoCCUC = function (e) {

    var consumido = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month > 1) consumido += e.record.data['combustible_litros_ene_cuc'];
        if (App.current_month > 2) consumido += e.record.data['combustible_litros_feb_cuc'];
        if (App.current_month > 3) consumido += e.record.data['combustible_litros_mar_cuc'];
        if (App.current_month > 4) consumido += e.record.data['combustible_litros_abr_cuc'];
        if (App.current_month > 5) consumido += e.record.data['combustible_litros_may_cuc'];
        if (App.current_month > 6) consumido += e.record.data['combustible_litros_jun_cuc'];
        if (App.current_month > 7) consumido += e.record.data['combustible_litros_jul_cuc'];
        if (App.current_month > 8) consumido += e.record.data['combustible_litros_ago_cuc'];
        if (App.current_month > 9) consumido += e.record.data['combustible_litros_sep_cuc'];
        if (App.current_month > 10) consumido += e.record.data['combustible_litros_oct_cuc'];
        if (App.current_month > 11) consumido += e.record.data['combustible_litros_nov_cuc'];
        if (App.current_month > 12) consumido += e.record.data['combustible_litros_dic_cuc'];
    }

    return consumido;
};

DistribuirCCUC = function (e, consumido) {
    var meses = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year)
        meses = 13 - App.current_month;
    else
        meses = 12;

    var monto_distribuir = Math.floor((e.record.data['combustible_litros_total_anno_cuc'] - consumido) / meses);
    var resto_distribuir = (e.record.data['combustible_litros_total_anno_cuc'] - consumido) % meses;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['combustible_litros_ene_cuc'] = monto_distribuir;
        if (App.current_month <= 2) e.record.data['combustible_litros_feb_cuc'] = monto_distribuir;
        if (App.current_month <= 3) e.record.data['combustible_litros_mar_cuc'] = monto_distribuir;
        if (App.current_month <= 4) e.record.data['combustible_litros_abr_cuc'] = monto_distribuir;
        if (App.current_month <= 5) e.record.data['combustible_litros_may_cuc'] = monto_distribuir;
        if (App.current_month <= 6) e.record.data['combustible_litros_jun_cuc'] = monto_distribuir;
        if (App.current_month <= 7) e.record.data['combustible_litros_jul_cuc'] = monto_distribuir;
        if (App.current_month <= 8) e.record.data['combustible_litros_ago_cuc'] = monto_distribuir;
        if (App.current_month <= 9) e.record.data['combustible_litros_sep_cuc'] = monto_distribuir;
        if (App.current_month <= 10) e.record.data['combustible_litros_oct_cuc'] = monto_distribuir;
        if (App.current_month <= 11) e.record.data['combustible_litros_nov_cuc'] = monto_distribuir;
        if (App.current_month <= 12) e.record.data['combustible_litros_dic_cuc'] = monto_distribuir + resto_distribuir;
    }else{
        e.record.data['combustible_litros_ene_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_feb_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_mar_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_abr_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_may_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_jun_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_jul_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_ago_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_sep_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_oct_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_nov_cuc'] = monto_distribuir;
        e.record.data['combustible_litros_dic_cuc'] = monto_distribuir + resto_distribuir;
    }

    var monto = Ext.util.Format.round(monto_distribuir * e.record.data['vehiculo_norma'], 2);
    var resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) * e.record.data['vehiculo_norma'], 2);

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['nivel_act_kms_ene_cuc'] = monto;
        if (App.current_month <= 2) e.record.data['nivel_act_kms_feb_cuc'] = monto;
        if (App.current_month <= 3) e.record.data['nivel_act_kms_mar_cuc'] = monto;
        if (App.current_month <= 4) e.record.data['nivel_act_kms_abr_cuc'] = monto;
        if (App.current_month <= 5) e.record.data['nivel_act_kms_may_cuc'] = monto;
        if (App.current_month <= 6) e.record.data['nivel_act_kms_jun_cuc'] = monto;
        if (App.current_month <= 7) e.record.data['nivel_act_kms_jul_cuc'] = monto;
        if (App.current_month <= 8) e.record.data['nivel_act_kms_ago_cuc'] = monto;
        if (App.current_month <= 9) e.record.data['nivel_act_kms_sep_cuc'] = monto;
        if (App.current_month <= 10) e.record.data['nivel_act_kms_oct_cuc'] = monto;
        if (App.current_month <= 11) e.record.data['nivel_act_kms_nov_cuc'] = monto;
        if (App.current_month <= 12) e.record.data['nivel_act_kms_dic_cuc'] = resto;
    }
    else {
        e.record.data['nivel_act_kms_ene_cuc'] = monto;
        e.record.data['nivel_act_kms_feb_cuc'] = monto;
        e.record.data['nivel_act_kms_mar_cuc'] = monto;
        e.record.data['nivel_act_kms_abr_cuc'] = monto;
        e.record.data['nivel_act_kms_may_cuc'] = monto;
        e.record.data['nivel_act_kms_jun_cuc'] = monto;
        e.record.data['nivel_act_kms_jul_cuc'] = monto;
        e.record.data['nivel_act_kms_ago_cuc'] = monto;
        e.record.data['nivel_act_kms_sep_cuc'] = monto;
        e.record.data['nivel_act_kms_oct_cuc'] = monto;
        e.record.data['nivel_act_kms_nov_cuc'] = monto;
        e.record.data['nivel_act_kms_dic_cuc'] = resto;
    }
};

ConsumidoKMCUC = function (e) {

    var consumido = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month > 1) consumido += e.record.data['nivel_act_kms_ene_cuc'];
        if (App.current_month > 2) consumido += e.record.data['nivel_act_kms_feb_cuc'];
        if (App.current_month > 3) consumido += e.record.data['nivel_act_kms_mar_cuc'];
        if (App.current_month > 4) consumido += e.record.data['nivel_act_kms_abr_cuc'];
        if (App.current_month > 5) consumido += e.record.data['nivel_act_kms_may_cuc'];
        if (App.current_month > 6) consumido += e.record.data['nivel_act_kms_jun_cuc'];
        if (App.current_month > 7) consumido += e.record.data['nivel_act_kms_jul_cuc'];
        if (App.current_month > 8) consumido += e.record.data['nivel_act_kms_ago_cuc'];
        if (App.current_month > 9) consumido += e.record.data['nivel_act_kms_sep_cuc'];
        if (App.current_month > 10) consumido += e.record.data['nivel_act_kms_oct_cuc'];
        if (App.current_month > 11) consumido += e.record.data['nivel_act_kms_nov_cuc'];
        if (App.current_month > 12) consumido += e.record.data['nivel_act_kms_dic_cuc'];
    }

    return consumido;
};

DistribuirKMCUC = function (e, consumido) {
    var meses = 0;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year)
        meses = 13 - App.current_month;
    else
        meses = 12;

    var monto_distribuir = Math.floor((e.record.data['nivel_act_kms_total_anno_cuc'] - consumido) / meses);
    var resto_distribuir = (e.record.data['nivel_act_kms_total_anno_cuc'] - consumido) % meses;

    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['nivel_act_kms_ene_cuc'] = monto_distribuir;
        if (App.current_month <= 2) e.record.data['nivel_act_kms_feb_cuc'] = monto_distribuir;
        if (App.current_month <= 3) e.record.data['nivel_act_kms_mar_cuc'] = monto_distribuir;
        if (App.current_month <= 4) e.record.data['nivel_act_kms_abr_cuc'] = monto_distribuir;
        if (App.current_month <= 5) e.record.data['nivel_act_kms_may_cuc'] = monto_distribuir;
        if (App.current_month <= 6) e.record.data['nivel_act_kms_jun_cuc'] = monto_distribuir;
        if (App.current_month <= 7) e.record.data['nivel_act_kms_jul_cuc'] = monto_distribuir;
        if (App.current_month <= 8) e.record.data['nivel_act_kms_ago_cuc'] = monto_distribuir;
        if (App.current_month <= 9) e.record.data['nivel_act_kms_sep_cuc'] = monto_distribuir;
        if (App.current_month <= 10) e.record.data['nivel_act_kms_oct_cuc'] = monto_distribuir;
        if (App.current_month <= 11) e.record.data['nivel_act_kms_nov_cuc'] = monto_distribuir;
        if (App.current_month <= 12) e.record.data['nivel_act_kms_dic_cuc'] = monto_distribuir + resto_distribuir;
    }
    else{
        e.record.data['nivel_act_kms_ene_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_feb_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_mar_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_abr_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_may_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_jun_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_jul_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_ago_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_sep_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_oct_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_nov_cuc'] = monto_distribuir;
        e.record.data['nivel_act_kms_dic_cuc'] = monto_distribuir + resto_distribuir;
    }

    var monto = Ext.util.Format.round(monto_distribuir / e.record.data['vehiculo_norma'], 2);
    var resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) / e.record.data['vehiculo_norma'], 2);


    if (Ext.getCmp('fieldAnnoId').getValue() == App.current_year) {
        if (App.current_month <= 1) e.record.data['combustible_litros_ene_cuc'] = monto;
        if (App.current_month <= 2) e.record.data['combustible_litros_feb_cuc'] = monto;
        if (App.current_month <= 3) e.record.data['combustible_litros_mar_cuc'] = monto;
        if (App.current_month <= 4) e.record.data['combustible_litros_abr_cuc'] = monto;
        if (App.current_month <= 5) e.record.data['combustible_litros_may_cuc'] = monto;
        if (App.current_month <= 6) e.record.data['combustible_litros_jun_cuc'] = monto;
        if (App.current_month <= 7) e.record.data['combustible_litros_jul_cuc'] = monto;
        if (App.current_month <= 8) e.record.data['combustible_litros_ago_cuc'] = monto;
        if (App.current_month <= 9) e.record.data['combustible_litros_sep_cuc'] = monto;
        if (App.current_month <= 10) e.record.data['combustible_litros_oct_cuc'] = monto;
        if (App.current_month <= 11) e.record.data['combustible_litros_nov_cuc'] = monto;
        if (App.current_month <= 12) e.record.data['combustible_litros_dic_cuc'] = resto;
    }
    else {
        e.record.data['combustible_litros_ene_cuc'] = monto;
        e.record.data['combustible_litros_feb_cuc'] = monto;
        e.record.data['combustible_litros_mar_cuc'] = monto;
        e.record.data['combustible_litros_abr_cuc'] = monto;
        e.record.data['combustible_litros_may_cuc'] = monto;
        e.record.data['combustible_litros_jun_cuc'] = monto;
        e.record.data['combustible_litros_jul_cuc'] = monto;
        e.record.data['combustible_litros_ago_cuc'] = monto;
        e.record.data['combustible_litros_sep_cuc'] = monto;
        e.record.data['combustible_litros_oct_cuc'] = monto;
        e.record.data['combustible_litros_nov_cuc'] = monto;
        e.record.data['combustible_litros_dic_cuc'] = resto;
    }

};

var meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
Ajustar = function (e, mes, cmn, ccuc, kmmn, kmcuc, lub, lfren) {

    if (mes < 0)
        return;

    var restoCmn = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn, 2);
    var restoCcuc = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc, 2);
    var restoKMmn = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn, 2);
    var restoKMcuc = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc, 2);
    var restoLub = Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
    var restoLFren = Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);

    e.record.data['combustible_litros_' + meses[mes] + '_mn'] = (e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn, 2);
    e.record.data['combustible_litros_' + meses[mes] + '_cuc'] = (e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc, 2);
    e.record.data['combustible_litros_' + meses[mes]] = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + e.record.data['combustible_litros_' + meses[mes] + '_cuc'], 2);
    e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] = (e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn, 2);
    e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] = (e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc, 2);
    e.record.data['nivel_act_kms_' + meses[mes]] = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'], 2);

    e.record.data['lubricante_' + meses[mes]] = (e.record.data['lubricante_' + meses[mes]] + lub < 0) ? 0 : Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
    e.record.data['liquido_freno_' + meses[mes]] = (e.record.data['liquido_freno_' + meses[mes]] + lfren < 0) ? 0 : Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);

    if (restoCmn > 0) restoCmn = 0;
    if (restoCcuc > 0) restoCcuc = 0;
    if (restoKMmn > 0) restoKMmn = 0;
    if (restoKMcuc > 0) restoKMcuc = 0;
    if (restoLub > 0) restoLub = 0;
    if (restoLFren > 0) restoLFren = 0;

    if ((restoCmn + restoCcuc + restoKMmn + restoKMcuc + restoLub + restoLFren) != 0)
        Ajustar(e, mes - 1, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLFren);
};

Calcular = function (grid) {
//aqui realizo la suma entre las columnas
    for (var i = 0; i < grid.length; i++) {
        grid[i].data.combustible_litros_total_mn = App.round(grid[i].data.combustible_litros_ene_mn + grid[i].data.combustible_litros_feb_mn +
            grid[i].data.combustible_litros_mar_mn + grid[i].data.combustible_litros_abr_mn + grid[i].data.combustible_litros_may_mn
            + grid[i].data.combustible_litros_jun_mn + grid[i].data.combustible_litros_jul_mn + grid[i].data.combustible_litros_ago_mn
            + grid[i].data.combustible_litros_sep_mn + grid[i].data.combustible_litros_oct_mn + grid[i].data.combustible_litros_nov_mn
            + grid[i].data.combustible_litros_dic_mn, 2);

        grid[i].data.combustible_litros_total_cuc = App.round(grid[i].data.combustible_litros_ene_cuc + grid[i].data.combustible_litros_feb_cuc +
            grid[i].data.combustible_litros_mar_cuc + grid[i].data.combustible_litros_abr_cuc + grid[i].data.combustible_litros_may_cuc
            + grid[i].data.combustible_litros_jun_cuc + grid[i].data.combustible_litros_jul_cuc + grid[i].data.combustible_litros_ago_cuc
            + grid[i].data.combustible_litros_sep_cuc + grid[i].data.combustible_litros_oct_cuc + grid[i].data.combustible_litros_nov_cuc
            + grid[i].data.combustible_litros_dic_cuc, 2);

        grid[i].data.combustible_litros_total = Ext.util.Format.round(grid[i].data.combustible_litros_total_mn + grid[i].data.combustible_litros_total_cuc, 2);

        grid[i].data.nivel_act_kms_total_mn = App.round(grid[i].data.nivel_act_kms_ene_mn + grid[i].data.nivel_act_kms_feb_mn +
            grid[i].data.nivel_act_kms_mar_mn + grid[i].data.nivel_act_kms_abr_mn + grid[i].data.nivel_act_kms_may_mn
            + grid[i].data.nivel_act_kms_jun_mn + grid[i].data.nivel_act_kms_jul_mn + grid[i].data.nivel_act_kms_ago_mn
            + grid[i].data.nivel_act_kms_sep_mn + grid[i].data.nivel_act_kms_oct_mn + grid[i].data.nivel_act_kms_nov_mn
            + grid[i].data.nivel_act_kms_dic_mn, 2);

        grid[i].data.nivel_act_kms_total_cuc = App.round(grid[i].data.nivel_act_kms_ene_cuc + grid[i].data.nivel_act_kms_feb_cuc +
            grid[i].data.nivel_act_kms_mar_cuc + grid[i].data.nivel_act_kms_abr_cuc + grid[i].data.nivel_act_kms_may_cuc
            + grid[i].data.nivel_act_kms_jun_cuc + grid[i].data.nivel_act_kms_jul_cuc + grid[i].data.nivel_act_kms_ago_cuc
            + grid[i].data.nivel_act_kms_sep_cuc + grid[i].data.nivel_act_kms_oct_cuc + grid[i].data.nivel_act_kms_nov_cuc
            + grid[i].data.nivel_act_kms_dic_cuc, 2);

        grid[i].data.nivel_act_kms_total = Ext.util.Format.round(grid[i].data.nivel_act_kms_total_mn + grid[i].data.nivel_act_kms_total_cuc, 2);

        grid[i].data.lubricante_total = App.round(grid[i].data.lubricante_ene + grid[i].data.lubricante_feb + grid[i].data.lubricante_mar
            + grid[i].data.lubricante_abr + grid[i].data.lubricante_may + grid[i].data.lubricante_jun + grid[i].data.lubricante_jul
            + grid[i].data.lubricante_ago + grid[i].data.lubricante_sep + grid[i].data.lubricante_oct + grid[i].data.lubricante_nov
            + grid[i].data.lubricante_dic, 2);

        grid[i].data.liquido_freno_total = App.round(grid[i].data.liquido_freno_ene + grid[i].data.liquido_freno_feb + grid[i].data.liquido_freno_mar
            + grid[i].data.liquido_freno_abr + grid[i].data.liquido_freno_may + grid[i].data.liquido_freno_jun + grid[i].data.liquido_freno_jul
            + grid[i].data.liquido_freno_ago + grid[i].data.liquido_freno_sep + grid[i].data.liquido_freno_oct + grid[i].data.liquido_freno_nov
            + grid[i].data.liquido_freno_dic, 2);
    }
    Ext.getCmp('id_grid_planificacion_combustible').getView().refresh();
};

Ajustar = function (e, mes, cmn, ccuc, kmmn, kmcuc, lub, lfren) {

    if (mes < 0)
        return;

    var restoCmn = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn, 2);
    var restoCcuc = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc, 2);
    var restoKMmn = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn, 2);
    var restoKMcuc = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc, 2);
    var restoLub = Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
    var restoLFren = Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);

    e.record.data['combustible_litros_' + meses[mes] + '_mn'] = (e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + cmn, 2);
    e.record.data['combustible_litros_' + meses[mes] + '_cuc'] = (e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc < 0) ? 0 : Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_cuc'] + ccuc, 2);
    e.record.data['combustible_litros_' + meses[mes]] = Ext.util.Format.round(e.record.data['combustible_litros_' + meses[mes] + '_mn'] + e.record.data['combustible_litros_' + meses[mes] + '_cuc'], 2);
    e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] = (e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + kmmn, 2);
    e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] = (e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc < 0) ? 0 : Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'] + kmcuc, 2);
    e.record.data['nivel_act_kms_' + meses[mes]] = Ext.util.Format.round(e.record.data['nivel_act_kms_' + meses[mes] + '_mn'] + e.record.data['nivel_act_kms_' + meses[mes] + '_cuc'], 2);

    e.record.data['lubricante_' + meses[mes]] = (e.record.data['lubricante_' + meses[mes]] + lub < 0) ? 0 : Ext.util.Format.round(e.record.data['lubricante_' + meses[mes]] + lub, 2);
    e.record.data['liquido_freno_' + meses[mes]] = (e.record.data['liquido_freno_' + meses[mes]] + lfren < 0) ? 0 : Ext.util.Format.round(e.record.data['liquido_freno_' + meses[mes]] + lfren, 2);

    if (restoCmn > 0) restoCmn = 0;
    if (restoCcuc > 0) restoCcuc = 0;
    if (restoKMmn > 0) restoKMmn = 0;
    if (restoKMcuc > 0) restoKMcuc = 0;
    if (restoLub > 0) restoLub = 0;
    if (restoLFren > 0) restoLFren = 0;

    if ((restoCmn + restoCcuc + restoKMmn + restoKMcuc + restoLub + restoLFren) != 0)
        Ajustar(e, mes - 1, restoCmn, restoCcuc, restoKMmn, restoKMcuc, restoLub, restoLFren);
};