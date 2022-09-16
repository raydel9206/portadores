/**
 * Created by pfcadenas on 11/11/2016.
 */
Ext.onReady(function () {

Ext.define('Portadores.planificacion_combustible.Window', {
    extend: 'Ext.window.Window',
    width: 300,
    height: 120,
    modal: true,
    plain: true,
    resizable: false,
    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                frame: true,
                width: 300,
                height: 120,
                defaultType: 'textfield',
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'combobox',
                        name: 'vehiculoid',
                        id: 'vehiculoid',
                        fieldLabel: 'Vehículo',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                        ],
                        // store: store_vehiculo,
                        displayField: 'matricula',
                        valueField: 'id',
                        labelWidth: 60,
                        width: '96%',
                        typeAhead: true,
                        queryMode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        emptyText: 'Seleccione el vehículo...',
                        selectOnFocus: true,
                        editable: true,
                        allowBlank: false
                    }

                ]
            }
        ];

        this.callParent();
    }
});

    let _btnMenu = Ext.create('Ext.button.MyButton', {
        id: 'plan_btn_menu',
        text: 'Menu',
        disabled: true,
        iconCls: 'fa fa-bars',
        // cls: 'fa fa-pencil-square-o fa-1_4',
        width: 100,
        menu: [
            {
                id: 'planificacion_combustible_btn_add',
                text: 'Adicionar',
                iconCls: 'fa fa-plus-square-o fa-1_4',
                width: 100,
                handler: function (This, e) {
                    Ext.create('Portadores.planificacion_combustible.Window', {
                        title: 'Adicionar planificacion de combustible',
                        id: 'window_planificacion_combustible_id',
                        buttons: [
                            {
                                text: 'Aceptar',
                                width: 70,
                                handler: function () {
                                    var window = Ext.getCmp('window_planificacion_combustible_id');
                                    var form = window.down('form').getForm();
                                    if (form.isValid()) {
                                        App.ShowWaitMsg();
                                        window.hide();
                                        var _result = App.PerformSyncServerRequest(Routing.generate('addPlanCombustible'), form.getValues());
                                        App.HideWaitMsg();
                                        if (_result.success) {
                                            form.reset();
                                            Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                                        }
                                        window.show();
                                        App.InfoMessage('Información', _result.message, _result.cls);
                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                width: 70,
                                handler: function () {
                                    Ext.getCmp('window_planificacion_combustible_id').close()
                                }
                            }
                        ]
                    }).show();
                }
            },
            {
                id: 'planificacion_combustible_btn_mod',
                text: 'Actualizar',
                iconCls: 'fa fa-pencil-square-o fa-1_4',
                disabled: true,
                width: 100,
                handler: function (This, e) {
                    App.ShowWaitMsg();
                    var store = Ext.getCmp('id_grid_planificacion_combustible').getStore();
                    var send = [];
                    var flag1 = false;
                    var flag2 = false;
                    var flag3 = false;
                    var flag4 = false;
                    Ext.Array.each(store.data.items, function (valor) {
                        if (parseFloat(valor.data['combustible_litros_total_anno']) < parseFloat(valor.data['combustible_litros_total'])) {
                            flag1 = true;
                        }
                        if (parseFloat(valor.data['nivel_act_kms_total_anno']) < parseFloat(valor.data['nivel_act_kms_total'])) {
                            flag2 = true;
                        }
                        if (parseFloat(valor.data['lubricante_total_anno']) < parseFloat(valor.data['lubricante_total'])) {
                            flag3 = true;
                        }
                        if (parseFloat(valor.data['liquido_freno_total_anno']) < parseFloat(valor.data['liquido_freno_total'])) {
                            flag4 = true;
                        }
                        send.push(valor.data);
                    });
                    if (flag1) {
                        App.InfoMessage('Información', 'La distribución mensual de combustible no puede superar el monto planificado para el año', 'danger');
                        App.HideWaitMsg();
                        return;
                    }
                    if (flag2) {
                        App.InfoMessage('Información', 'La distribución mensual de actividad no puede superar el monto planificado para el año', 'danger');
                        App.HideWaitMsg();
                        return;
                    }
                    if (flag3) {
                        App.InfoMessage('Información', 'La distribución mensual de lubricante no puede superar el monto planificado para el año', 'danger');
                        App.HideWaitMsg();
                        return;
                    }
                    if (flag4) {
                        App.InfoMessage('Información', 'La distribución mensual de liquido de freno no puede superar el monto planificado para el año', 'danger');
                        App.HideWaitMsg();
                        return;
                    }
                    var store_send = Ext.encode(send);
                    var _result = App.PerformSyncServerRequest(Routing.generate('modPlanCombustible'), {store: store_send});
                    App.HideWaitMsg();
                    if (_result.success) {
                        This.setStyle('borderColor', '#d8d8d8');
                        This.disable();
                        Ext.getCmp('id_grid_planificacion_combustible').getStore().load();
                    }
                    App.InfoMessage('Información', _result.message, _result.cls);
                }
            },
            {
                id: 'planificacion_combustible_btn_del',
                text: 'Eliminar',
                iconCls: 'fa fa-minus-square-o fa-1_4',
                disabled: true,
                width: 100,
                handler: function (This, e) {

                    App.ConfirmMessage(function () {
                        var send = [];
                        var selection = Ext.getCmp('id_grid_planificacion_combustible').getSelectionModel().getSelection();
                        Ext.Array.each(selection, function (value) {
                                send.push(value.data.id)
                            }
                        );
                        App.ShowWaitMsg();
                        var _result = App.PerformSyncServerRequest(Routing.generate('delPlanCombustible'), {id: send});
                        App.HideWaitMsg();
                        App.InfoMessage('Información', _result.message, _result.cls);
                        Ext.getCmp('id_grid_planificacion_combustible').getStore().load();

                    }, "¿Está seguro que desea eliminar la planificación seleccionada'?");
                }
            }


        ],

    });


    var _btnCrear = Ext.create('Ext.button.MyButton', {
        id: 'planificacion_combustible_btn_crear',
        text: 'Crear',
        iconCls: 'fa fa-plus-square-o fa-1_4',
        width: 100,
        handler: function (This, e) {
            Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea Crear el Plan del año ' + Ext.getCmp('fieldAnnoId').getValue() + ' ?', function (btn) {
                if (btn == 'yes') {
                    var obj = {};
                    obj.tipo_combustibleid = Ext.getCmp('nTipoCombustibleId').getValue();
                    obj.anno = Ext.getCmp('fieldAnnoId').getValue();
                    obj.unidadid = Ext.getCmp('arbolunidades').getSelectionModel().getLastSelected().data.id;

                    App.request('POST', App.buildURL('/portadores/plan_conbustible/crear'), obj, null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getStore('id_store_plan').load({})
                            }
                        }
                    );
                }

            });
        }
    });
    var _btnAdd = Ext.create('Ext.button.MyButton', );
    var _btnMod = Ext.create('Ext.button.MyButton', );
    var _btn_Del = Ext.create('Ext.button.MyButton', );

    var _tbar = Ext.getCmp('planificacion_combustible_tbar');
   _tbar.add(_btnCrear);
   _tbar.add('-');
    _tbar.add(_btnMod);
    _tbar.add('-');
    _tbar.add(_btn_Del);
    _tbar.add('-');
    _tbar.setHeight(36);
});

Enable = function () {
    Ext.getCmp('planificacion_combustible_btn_del').enable();
    if (Ext.getCmp('planificacion_combustible_btn_aprobar') != undefined)
        Ext.getCmp('planificacion_combustible_btn_aprobar').enable();
    if (Ext.getCmp('planificacion_combustible_btn_desaprobar') != undefined)
        Ext.getCmp('planificacion_combustible_btn_desaprobar').enable();
};

Disable = function () {
    Ext.getCmp('planificacion_combustible_btn_del').disable();
    if (Ext.getCmp('planificacion_combustible_btn_aprobar') != undefined)
        Ext.getCmp('planificacion_combustible_btn_aprobar').disable();
    if (Ext.getCmp('planificacion_combustible_btn_desaprobar') != undefined)
        Ext.getCmp('planificacion_combustible_btn_desaprobar').disable();
};

