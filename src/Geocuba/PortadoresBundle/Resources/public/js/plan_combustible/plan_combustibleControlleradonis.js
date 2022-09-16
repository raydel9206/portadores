Ext.onReady(function () {
    // var store_vehiculos_unidad = Ext.create('Ext.data.JsonStore', {
    //     storeId: 'id_store_vehiculo',
    //     fields: [
    //         {name: 'id'},
    //         {name: 'matricula'},
    //         {name: 'nmarca_vehiculo'}
    //     ],
    //     proxy: {
    //         type: 'ajax',
    //         // url: App.buildURL('/portadores/vehiculo/load'),
    //         url: App.buildURL('/portadores/vehiculo/loadCombo'),
    //         reader: {
    //             rootProperty: 'rows'
    //         }
    //     },
    //     pageSize: 1000,
    //     autoLoad: false,
    //     listeners: {
    //         beforeload: function (This, operation, eOpts) {
    //             operation.setParams({
    //                 unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id,
    //                 tipoCombustible: Ext.getCmp('nTipoCombustibleId').getValue()
    //             });
    //         },
    //         // load: function (This, operation, eOpts) {
    //         //     Ext.getCmp('window_paralizacion_id').unmask();
    //         // }
    //     }
    // });
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
        header: {             style: {                 backgroundColor: 'white',                 borderBottom: '1px solid #c1c1c1 !important'             },         },
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
                gridPlan.enable();
                storePlan.removeAll();
                if (Ext.getStore('id_store_persona_chofer'))
                    Ext.getStore('id_store_persona_chofer').load();
            }
        }
    });
    const storePlan = Ext.create('Ext.data.JsonStore', {
        storeId: 'id_store_plan',
        fields: [
            {name: 'id'},
            {name: 'vehiculoid'},
            {name: 'matricula'},
            {name: 'vehiculo_norma'},
            {name: 'anno'},
            {name: 'combustible_litros_total_mn'},
            {name: 'combustible_litros_total_cuc'},
            {name: 'nivel_act_kms_total_mn'},
            {name: 'nivel_act_kms_total_cuc'},
            {name: 'lubricante_total'},
            {name: 'liquido_freno_total'},
            {name: 'combustible_litros_ene_mn'},
            {name: 'combustible_litros_ene_cuc'},
            {name: 'nivel_act_kms_ene_mn'},
            {name: 'nivel_act_kms_ene_cuc'},
            {name: 'lubricante_ene'},
            {name: 'liquido_freno_ene'},
            {name: 'combustible_litros_feb_mn'},
            {name: 'combustible_litros_feb_cuc'},
            {name: 'nivel_act_kms_feb_mn'},
            {name: 'nivel_act_kms_feb_cuc'},
            {name: 'lubricante_feb'},
            {name: 'liquido_freno_feb'},
            {name: 'combustible_litros_mar_mn'},
            {name: 'combustible_litros_mar_cuc'},
            {name: 'nivel_act_kms_mar_mn'},
            {name: 'nivel_act_kms_mar_cuc'},
            {name: 'lubricante_mar'},
            {name: 'liquido_freno_mar'},
            {name: 'combustible_litros_abr_mn'},
            {name: 'combustible_litros_abr_cuc'},
            {name: 'nivel_act_kms_abr_mn'},
            {name: 'nivel_act_kms_abr_cuc'},
            {name: 'lubricante_abr'},
            {name: 'liquido_freno_abr'},
            {name: 'combustible_litros_may_mn'},
            {name: 'combustible_litros_may_cuc'},
            {name: 'nivel_act_kms_may_mn'},
            {name: 'nivel_act_kms_may_cuc'},
            {name: 'lubricante_may'},
            {name: 'liquido_freno_may'},
            {name: 'combustible_litros_jun_mn'},
            {name: 'combustible_litros_jun_cuc'},
            {name: 'nivel_act_kms_jun_mn'},
            {name: 'nivel_act_kms_jun_cuc'},
            {name: 'lubricante_jun'},
            {name: 'liquido_freno_jun'},
            {name: 'combustible_litros_jul_mn'},
            {name: 'combustible_litros_jul_cuc'},
            {name: 'nivel_act_kms_jul_mn'},
            {name: 'nivel_act_kms_jul_cuc'},
            {name: 'lubricante_jul'},
            {name: 'liquido_freno_jul'},
            {name: 'combustible_litros_ago_mn'},
            {name: 'combustible_litros_ago_cuc'},
            {name: 'nivel_act_kms_ago_mn'},
            {name: 'nivel_act_kms_ago_cuc'},
            {name: 'lubricante_ago'},
            {name: 'liquido_freno_ago'},
            {name: 'combustible_litros_sep_mn'},
            {name: 'combustible_litros_sep_cuc'},
            {name: 'nivel_act_kms_sep_mn'},
            {name: 'nivel_act_kms_sep_cuc'},
            {name: 'lubricante_sep'},
            {name: 'liquido_freno_sep'},
            {name: 'combustible_litros_oct_mn'},
            {name: 'combustible_litros_oct_cuc'},
            {name: 'nivel_act_kms_oct_mn'},
            {name: 'nivel_act_kms_oct_cuc'},
            {name: 'lubricante_oct'},
            {name: 'liquido_freno_oct'},
            {name: 'combustible_litros_nov_mn'},
            {name: 'combustible_litros_nov_cuc'},
            {name: 'nivel_act_kms_nov_mn'},
            {name: 'nivel_act_kms_nov_cuc'},
            {name: 'lubricante_nov'},
            {name: 'liquido_freno_nov'},
            {name: 'combustible_litros_dic_mn'},
            {name: 'combustible_litros_dic_cuc'},
            {name: 'nivel_act_kms_dic_mn'},
            {name: 'nivel_act_kms_dic_cuc'},
            {name: 'lubricante_dic'},
            {name: 'liquido_freno_dic'},
        ],
        proxy: {
            type: 'ajax',
            url: App.buildURL('/portadores/plan_combustible/load'),
            reader: {
                rootProperty: 'rows',
                sortRoot: 'id'
            }
        },
        sorters: ['nro_orden', 'ndenominacion_vehiculo'],
        groupField: 'ndenominacion_vehiculo',
        autoLoad: false,
        listeners: {
            beforeload: function (This, operation, eOpts) {
                operation.setParams(
                    {
                        tipo_combustibleid: Ext.getCmp('nTipoCombustibleId').getValue(),
                        anno: Ext.getCmp('fieldAnnoId').getValue(),
                        unidadid: Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id
                    }
                );

            }
        }
    });
    var edit = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 1,
        listeners: {
            beforeedit: function (This, e, eOpts) {

                if (e.grid.store.data.items[e.rowIdx].data.aprobada && e.colIdx < 8) {
                    return false;
                }

                if (e.grid.store.data.items[e.rowIdx].data.anno != App.current_year) {
                    return false;
                }

                var ncolanteriores = 16;
                var nindicador = 8;
                if (e.colIdx > 7 && (e.colIdx < (App.current_month - 1) * nindicador + ncolanteriores  ) || isNaN(e.value)) {
                    return false;
                }

                if (( (e.colIdx - 2) % nindicador == 0 ) || ( (e.colIdx - 5) % nindicador == 0 ) || isNaN(e.value)) {
                    return false;
                }
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
                if (e.colIdx == 0 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_ene_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_ene_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 1 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_feb_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_feb_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 2 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_mar_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_mar_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 3 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_abr_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_abr_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 4 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_may_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_may_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 5 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_jun_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_jun_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 6 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_jul_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_jul_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 7 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_ago_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_ago_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 8 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_sep_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_sep_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 9 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_oct_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_oct_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 10 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_nov_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_nov_mn'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 11 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_dic_mn'] = Ext.util.Format.round(e.record.data['combustible_litros_dic_mn'] * e.record.data['vehiculo_norma'], 2);
                }

                ncolanteriores = 17;
                if (e.colIdx == 1) {
                    var consumido = ConsumidoCCUC(e);
                    if (e.record.data['combustible_litros_total_anno_cuc'] < consumido)
                        e.record.data['combustible_litros_total_anno_cuc'] = consumido;
                    e.record.data['nivel_act_kms_total_anno_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_total_anno_cuc'] * e.record.data['vehiculo_norma'], 2);
                    DistribuirCCUC(e, consumido);
                }
                if (e.colIdx == 0 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_ene_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_ene_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 1 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_feb_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_feb_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 2 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_mar_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_mar_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 3 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_abr_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_abr_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 4 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_may_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_may_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 5 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_jun_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_jun_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 6 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_jul_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_jul_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 7 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_ago_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_ago_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 8 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_sep_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_sep_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 9 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_oct_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_oct_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 10 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_nov_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_nov_cuc'] * e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 11 * nindicador + ncolanteriores) {
                    e.record.data['nivel_act_kms_dic_cuc'] = Ext.util.Format.round(e.record.data['combustible_litros_dic_cuc'] * e.record.data['vehiculo_norma'], 2);
                }

                ncolanteriores = 19;
                if (e.colIdx == 3) {
                    var consumido = ConsumidoKMMN(e);
                    if (e.record.data['nivel_act_kms_total_anno_mn'] < consumido)
                        e.record.data['nivel_act_kms_total_anno_mn'] = consumido;
                    e.record.data['combustible_litros_total_anno_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_mn'] / e.record.data['vehiculo_norma'], 2);
                    DistribuirKMMN(e, consumido);
                }
                if (e.colIdx == 0 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_ene_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ene_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 1 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_feb_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_feb_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 2 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_mar_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_mar_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 3 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_abr_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_abr_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 4 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_may_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_may_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 5 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_jun_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jun_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 6 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_jul_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jul_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 7 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_ago_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ago_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 8 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_sep_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_sep_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 9 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_oct_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_oct_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 10 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_nov_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_nov_mn'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 11 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_dic_mn'] = Ext.util.Format.round(e.record.data['nivel_act_kms_dic_mn'] / e.record.data['vehiculo_norma'], 2);
                }

                ncolanteriores = 20;
                if (e.colIdx == 4) {
                    var consumido = ConsumidoKMCUC(e);
                    if (e.record.data['nivel_act_kms_total_anno_cuc'] < consumido)
                        e.record.data['nivel_act_kms_total_anno_cuc'] = consumido;
                    e.record.data['combustible_litros_total_anno_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_total_anno_cuc'] / e.record.data['vehiculo_norma'], 2);
                    DistribuirKMCUC(e, consumido);
                }
                if (e.colIdx == 0 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_ene_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ene_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 1 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_feb_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_feb_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 2 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_mar_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_mar_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 3 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_abr_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_abr_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 4 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_may_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_may_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 5 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_jun_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jun_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 6 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_jul_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_jul_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 7 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_ago_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_ago_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 8 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_sep_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_sep_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 9 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_oct_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_oct_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 10 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_nov_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_nov_cuc'] / e.record.data['vehiculo_norma'], 2);
                }
                if (e.colIdx == 11 * nindicador + ncolanteriores) {
                    e.record.data['combustible_litros_dic_cuc'] = Ext.util.Format.round(e.record.data['nivel_act_kms_dic_cuc'] / e.record.data['vehiculo_norma'], 2);
                }

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

                gridPlan.getView().refresh();
                Calcular(gridPlan.getStore().data.items);

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

    const gridPlan = Ext.create('Ext.grid.Panel', {
        id: 'id_grid_planificacion_combustible',
        region: 'center',
        width: '75%',
        store: storePlan,
        plugins: [edit],
        selModel: {
            allowDeselect: true,
            mode: 'SIMPLE'
        },
        features: [{
            ftype: 'summary',
            dock: 'bottom',
            // groupHeaderTpl: [
            //     '<div>Tipo de Combustible: {name:this.formatName}</div>',
            //     {
            //         formatName: function (name) {
            //             return Ext.String.trim(name);
            //         }
            //     }
            // ]
        }],
        columns: [
            {text: 'Matrícula', dataIndex: 'vehiculo', width: 100, align:'center', locked: true},
            {text: 'Norma', dataIndex: 'vehiculo_norma', width: 70, align:'center',locked: true},
            {text: 'Año', dataIndex: 'anno', width: 50, align:'center',locked: true},
            {
                id: 'Anno', text: 'Totales', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        // xtype: 'gridcolumn',
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_total_mn',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        width: 100,
                        align:'right',
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility',
                        editor: {
                            xtype: 'numberfield',
                            decimalSeparator: '.',
                            hideTrigger: true
                        },
                        summaryType: 'sum',
                        summaryRenderer: function (value) {
                            return Ext.String.format('<strong>{0}</strong>', value);
                        }
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_total_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                text: 'Acumulados mensuales', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {text: 'Comb.', width: 100, style: {backgroundColor: '#d6e9c6'}},
                    {text: 'Kms', width: 100, style: {backgroundColor: '#d6e9c6'}},
                ]
            },
            {
                id: 'Ene', text: 'Enero', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_ene_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_ene_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Feb', text: 'Febrero', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_feb_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_feb_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Mar', text: 'Marzo', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_mar_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_mar_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Abr', text: 'Abril', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_abr_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_abr_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'May', text: 'Mayo', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_may_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_may_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Jun', text: 'Junio', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_jun_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_jun_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Jul', text: 'Julio', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_jul_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_jul_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Ago', text: 'Agosto', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_ago_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_ago_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Sep', text: 'Septiembre', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_sep_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_sep_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Oct', text: 'Octubre', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_oct_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_oct_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Nov', text: 'Noviembre', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_nov_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_nov_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            },
            {
                id: 'Dic', text: 'Diciembre', style: {backgroundColor: '#e3e3e3'},
                columns: [
                    {
                        text: 'Comb.',
                        dataIndex: 'combustible_litros_dic_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                    {
                        text: 'Kms.',
                        dataIndex: 'nivel_act_kms_dic_mn',
                        width: 100,
                        style: {backgroundColor: '#d6e9c6'},
                        hidden: true,
                        hideMode: 'visibility'
                    },
                ]
            }
        ],
        disabled: true,
        dockedItems: [
            {
                xtype: 'toolbar',
                id: 'planificacion_combustible_tbar',
                dock: 'top',
                items: [
                    {
                        xtype: 'numberfield',
                        id: 'fieldAnnoId',
                        fieldLabel: 'Año',
                        labelWidth: 30,
                        width: 100,
                        value: new Date().getFullYear()
                    },
                    {
                        xtype: 'combomes',
                        id: 'fieldMesId',
                        fieldLabel: 'Mes',
                        valueField: 'min',
                        labelWidth: 30,
                        width: 150,
                        forceSelection: true,
                        triggerAction: 'all',
                        typeAhead: true,
                        queryMode: 'local',
                        listeners: {
                            change: function (combo, newValue, oldValue, eOpts) {
                                if (newValue == null)
                                    Ext.getCmp('filterButton').disable();
                                else {
                                    Ext.getCmp('filterButton').enable();
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
                                // console.log('aaa');
                                gridPlan.getStore().load();
                            }
                        }
                    },
                    {
                        id: 'filterButton',
                        glyph: 0xf002,
                        cls: 'border-secondary',
                        tooltip: 'Busca los vehículos a partir de las opciones seleccionadas',
                        disabled: true,
                        handler: function () {
                            Ext.getStore('id_store_plan').load(
                                {
                                    params: {
                                        anno: Ext.getCmp('fieldAnnoId').getValue(),
                                        mes: Ext.getCmp('fieldMesId').getValue(),
                                        tipoCombustibleId: Ext.getCmp('nTipoCombustibleId').getValue()
                                    }
                                });
                        }
                    }
                ]
            }
        ]
    });
    var panelContainer = Ext.create('Ext.panel.Panel', {
        title: 'Plan de combustible por vehículos',
        frame: true,
        closable: true,
        layout: 'border',
        padding: '2 0 0',
        items: [panelTree, gridPlan]
    });
    App.render(panelContainer);
});

ConsumidoCMN = function (e) {

    var consumido = 0;

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

    return consumido;
};

DistribuirCMN = function (e, consumido) {

    var monto_distribuir = Math.floor((e.record.data['combustible_litros_total_anno_mn'] - consumido) / (13 - App.current_month));
    var resto_distribuir = (e.record.data['combustible_litros_total_anno_mn'] - consumido) % (13 - App.current_month);

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

    var monto = Ext.util.Format.round(monto_distribuir * e.record.data['vehiculo_norma'], 2);
    var resto = Ext.util.Format.round((monto_distribuir + resto_distribuir) * e.record.data['vehiculo_norma'], 2);

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

var meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];