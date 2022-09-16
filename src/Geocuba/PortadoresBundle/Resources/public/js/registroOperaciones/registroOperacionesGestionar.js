Ext.onReady(function () {
    let gridRegistroOperaciones = Ext.getCmp('gridRegistroOperaciones');
    let gridEquiposTecnologicos = Ext.getCmp('gridEquiposTecnologicos');

    const calcularXHora = function (This, attach, use = true) {
        if (!use) return false;
        let form = This.up('form');
        let formValues = form.getForm().getValues();
        let horaArranque = formValues['hora_arranque' + attach] ? Date.parse('1 January 1970 ' + formValues['hora_arranque' + attach].replace(/\./g, '')) : null;
        let horaParada = formValues['hora_parada' + attach] ? Date.parse('1 January 1970 ' + formValues['hora_parada' + attach].replace(/\./g, '')) : null;

        let consumoNormadoCmp = form.down('numberfield[name*=consumo_normado' + attach + ']');

        if (horaArranque && horaParada) {
            // (hora_parada - hora_arranque) / 1000(ms => s) / 3600 (s => h)
            let tiempoTrabajado = (horaParada - horaArranque) / 3600000;
            let consumoNormado = gridEquiposTecnologicos.getSelection()[0].get('norma' + attach) * tiempoTrabajado;

            consumoNormadoCmp.setValue(consumoNormado);
        } else consumoNormadoCmp.setValue(0);
    };

    const calcularXHorametro = function (This) {
        let form = This.up('form');
        let formValues = form.getForm().getValues();
        let consumoNormadoCmp = form.down('numberfield[name*=consumo_normado]');

        if (formValues['horametro_arranque'] && formValues['horametro_parada']) {
            let horametroArranqueArr = formValues['horametro_arranque'].split(':');
            let horametroParadaArr = formValues['horametro_parada'].split(':');

            let diferenciaHoras = horametroParadaArr[0] - horametroArranqueArr[0];
            if (diferenciaHoras > 23) {
                App.showAlert('No se puede registra mas de 23:59 horas de trabajo.');
                form.down('textfield[name*=horametro_parada]').setValue((parseFloat(horametroArranqueArr[0]) + 23) + ':59').focus();
                consumoNormadoCmp.setValue(0);
                return false;
            }

            let horaArranque = Date.parse('1 January 1970 00:' + horametroArranqueArr[1]);
            let horaParada = Date.parse('1 January 1970 ' + (horametroParadaArr[0] - horametroArranqueArr[0]) + ':' + horametroParadaArr[1]);

            // (horametro_parada - horametro_arranque) / 1000(ms => s) / 3600 (s => h)
            let tiempoTrabajado = (horaParada - horaArranque) / 3600000;
            let consumoNormado = gridEquiposTecnologicos.getSelection()[0].get('norma') * tiempoTrabajado;

            consumoNormadoCmp.setValue(consumoNormado);

        } else consumoNormadoCmp.setValue(0);
    };

    const calcularXCombustible = function (This) {
        let form = This.up('form');
        let formValues = form.getForm().getValues();
        let consumoCmp = form.down('numberfield[name*=consumo_real]');

        consumoCmp.setValue(Number.parseFloat(formValues['combustible_inicial'])
            + Number.parseFloat(formValues['combustible_abastecido'])
            - Number.parseFloat(formValues['combustible_final']));
    };

    const formContainer = {
        xtype: 'form',
        defaults: {
            afterLabelTextTpl: [
                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
            ],
            labelWidth: 110,
            labelAlign: 'right',
            allowBlank: false,
            xtype: 'numberfield'
        },
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        items: []
    };

    const baseContainer = {
        xtype: 'container',
        defaults: {
            afterLabelTextTpl: ['<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'],
            labelAlign: 'top',
            width: 150,
            margin: '0 5 5 5',
            allowBlank: false,
            editable: false,
            xtype: 'numberfield'
        },
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        items: []
    };

    const baseItems1 = [
        {
            xtype: 'datefield',
            fieldLabel: 'Fecha',
            name: 'fecha'
        },
        {
            xtype: 'combobox',
            name: 'actividad_id',
            id: 'actividad_combo',
            fieldLabel: 'Actividad',
            store: {
                storeId: 'storeActividades',
                fields: [
                    {name: 'id'},
                    {name: 'nombre'},
                ],
                proxy: {
                    type: 'ajax',
                    url: App.buildURL('portadores/actividad/loadCombo'),
                    reader: {
                        rootProperty: 'rows'
                    }
                },
                pageSize: 1000,
                autoLoad: false
            },
            displayField: 'nombre',
            valueField: 'id',
            queryMode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            emptyText: 'Seleccione la actividad...'
        }];

    const baseItems2 = [
        {
            fieldLabel: 'Consumo real',
            name: 'consumo_real',
            editable: true,
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0
        },
        {
            fieldLabel: 'Consumo normado',
            name: 'consumo_normado',
            hideTrigger: true,
            afterLabelTextTpl: '',
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0,
            value: 0
        }
    ];

    const horaItems = [
        {
            xtype: 'timefield',
            fieldLabel: 'Hora Arranque',
            name: 'hora_arranque',
            listeners: {
                change: function (This) {
                    calcularXHora(This, '');
                }
            }
        },
        {
            xtype: 'timefield',
            fieldLabel: 'Hora Parada',
            name: 'hora_parada',
            listeners: {
                change: function (This) {
                    calcularXHora(This, '');
                }
            }
        }
    ];

    const calderaHoraRecItems = [
        {
            xtype: 'timefield',
            fieldLabel: 'Hora Arranque',
            name: 'hora_arranque_recirculacion',
            listeners: {
                change: function (This) {
                    calcularXHora(This, '_recirculacion');
                }
            }
        },
        {
            xtype: 'timefield',
            fieldLabel: 'Hora Parada',
            name: 'hora_parada_recirculacion',
            listeners: {
                change: function (This) {
                    calcularXHora(This, '_recirculacion');
                }
            }
        }
    ];

    const calderaConsumoRecItems = [
        {
            fieldLabel: 'Consumo real',
            name: 'consumo_real_recirculacion',
            decimalSeparator: '.',
            decimalPrecision: 4,
            editable: true,
            minValue: 0
        },
        {
            fieldLabel: 'Consumo normado',
            name: 'consumo_normado_recirculacion',
            afterLabelTextTpl: '',
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0,
            value: 0
        }
    ];

    const montacargaHorametroItems = [
        {
            xtype: 'textfield',
            fieldLabel: 'Horámetro Arranque',
            name: 'horametro_arranque',
            editable: true,
            maskRe: /[0-9:]/,
            regex: /^([0-9]+:[0-9][0-9])$/,
            regexText: 'Formato inválido. Requerido (0:00)',
            listeners: {
                // validitychange: function (This, isValid) { if(isValid) calcularXHorametro(This); },
                change: function (This) {
                    if (This.isValid()) calcularXHorametro(This);
                }
            }
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Horámetro Parada',
            name: 'horametro_parada',
            editable: true,
            maskRe: /[0-9:]/,
            regex: /^([0-9]+:[0-9][0-9])$/,
            regexText: 'Formato inválido. Requerido (0:00)',
            listeners: {
                // validitychange: function (This, isValid) { if(isValid) calcularXHorametro(This); }
                change: function (This) {
                    if (This.isValid()) calcularXHorametro(This);
                }
            }
        }
    ];

    const combustibleItems = [
        {
            fieldLabel: 'Combustible inicial',
            name: 'combustible_inicial',
            editable: true,
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0,
            value: 0,
            listeners: {change: calcularXCombustible}
        },
        {
            fieldLabel: 'Combustible abastecido',
            name: 'combustible_abastecido',
            editable: true,
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0,
            value: 0,
            listeners: {change: calcularXCombustible}
        },
        {
            fieldLabel: 'Combustible final',
            name: 'combustible_final',
            editable: true,
            decimalSeparator: '.',
            decimalPrecision: 4,
            minValue: 0,
            value: 0,
            listeners: {change: calcularXCombustible}
        }
    ];

    Ext.define('Portadores.registro_operaciones.Window', {
        extend: 'Ext.window.Window',
        bodyPadding: '10',
        modal: true,
        resizable: false,
        items: []
    });

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/registro_operaciones/${action}`),
            selection = (action !== 'add' && gridRegistroOperaciones.getSelectionModel().hasSelection()) ? gridRegistroOperaciones.getSelection()[0] : null;

        if (action === 'delete') {
            Ext.Msg.show({
                title: '¿Eliminar medición?',
                message: `¿Está seguro que desea eliminar la medición?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {
                            id: selection.get('id'),
                            tanque_id: selection.get('tanque_id'),
                            mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                            anno: Ext.getCmp('mes_anno').getValue().getFullYear()
                        };
                        App.request('DELETE', url, params, null, null, response => { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                gridRegistroOperaciones.getStore().reload();
                            }
                        });
                    }
                }
            });

        } else {
            let comboDenominacionValue = Ext.getCmp('combo_denominacion').getValue();

            let formItem = {};
            formContainer.items = [];
            baseContainer.items = [];
            if (comboDenominacionValue === 'otro') {
                let container1 = {...baseContainer};
                container1.items = [];
                baseItems1.forEach((item) => container1.items.push({...item}));

                let container2 = {...baseContainer};
                container2.items = [];
                horaItems.forEach((item) => container2.items.push({...item}));

                let subcontainer = {
                    ...baseContainer,
                    id: 'combustible_container',
                    margin: '-10 0 0 0',
                    hidden: true,
                    disabled: true
                };
                subcontainer.items = [];
                combustibleItems.forEach((item) => subcontainer.items.push({...item}));
                let container3 = {
                    xtype: 'container',
                    layout: 'vbox',
                    margin: '10 0',
                    items: [
                        {
                            xtype: 'checkboxfield',
                            boxLabel: 'Medición de combustible',
                            listeners: {
                                change: function (This, value) {
                                    let combustibleContainer = Ext.getCmp('combustible_container');
                                    combustibleContainer.setDisabled(!value);
                                    combustibleContainer.setHidden(!value);
                                    combustibleContainer.items.items.forEach(item => item.reset());

                                    let consumoReal = combustibleContainer.findParentByType('container').nextSibling().items.items[0];
                                    consumoReal.setEditable(!value);
                                    consumoReal.setHideTrigger(value);
                                    consumoReal.setValue(0);
                                }
                            }
                        },
                        subcontainer
                    ]
                };

                let container4 = {...baseContainer};
                container4.items = [];
                baseItems2.forEach((item) => container4.items.push({...item}));

                formItem = {...formContainer};
                formItem.items = [container1, container2, container3, container4];
            }
            else if (comboDenominacionValue === 'static_tec_denomination_1') {
                let container1 = {...baseContainer};
                container1.items = [];
                baseItems1.forEach((item) => container1.items.push({...item}));

                let container2 = {...baseContainer};
                container2.items = [];
                horaItems.forEach((item) => container2.items.push({...item}));

                let container3 = {...baseContainer};
                container3.items = [];
                baseItems2.forEach((item) => container3.items.push({...item}));

                let container4 = {...baseContainer};
                container4.items = [];
                calderaHoraRecItems.forEach((item) => container4.items.push({...item}));

                let container5 = {...baseContainer};
                container5.items = [];
                calderaConsumoRecItems.forEach((item) => container5.items.push({...item}));
                container5.margin = '0 0 10 0';

                formItem = {...formContainer};
                formItem.items = [
                    container1, container2, container3,
                    {
                        xtype: 'fieldset',
                        title: 'Recirculación',
                        margin: '10 0 0 0',
                        items: [container4, container5]
                    }
                ];
                formItem.width = 340;
            }
            else if (comboDenominacionValue === 'static_tec_denomination_3') {
                let container1 = {...baseContainer};
                container1.items = [];
                baseItems1.forEach((item) => container1.items.push({...item}));

                let container2 = {...baseContainer};
                container2.items = [];
                montacargaHorametroItems.forEach((item) => container2.items.push({...item}));

                let container3 = {...baseContainer};
                container3.items = [];
                combustibleItems.forEach((item) => container3.items.push({...item}));

                let container4 = {...baseContainer};
                container4.items = [];
                baseItems2.forEach((item) => container4.items.push({...item}));
                container4.items[0].editable = false;
                container4.items[0].hideTrigger = true;
                container4.items[0].afterLabelTextTpl = '';
                container4.items[0].value = 0;

                formItem = {...formContainer};
                formItem.items = [container1, container2, container3, container4];
            }

            let winform = Ext.create('Portadores.registro_operaciones.Window', {
                title: !selection ? 'Adicionar registro' : `Modificar ${selection.get('fecha')}`,
                items: formItem,
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let form = winform.down('form').getForm();
                            if (form.isValid()) {
                                let params = form.getValues();
                                params.id = action === 'upd' ? selection.data.id : null;
                                params.equipo_tecnologico_id = action === 'add' ? gridEquiposTecnologicos.getSelection()[0].data.id : selection.data.equipo_tecnologico_id;

                                params.mes = Ext.getCmp('mes_anno').getValue().getMonth() + 1;
                                params.anno = Ext.getCmp('mes_anno').getValue().getFullYear();

                                let method = action === 'add' ? 'POST' : 'PUT';
                                App.request(method, url, params, null, null, response => {
                                    if (response && response.hasOwnProperty('success') && response.success) {
                                        gridRegistroOperaciones.getStore().load();
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
                    boxready: function () {
                        // if (action === 'upd') winform.down('form').getForm().loadRecord(selection);
                        if (gridEquiposTecnologicos.getSelectionModel().hasSelection()) {
                            let { tipo_combustible_id, actividad_id } = gridEquiposTecnologicos.getSelection()[0].data;
                            let actividadStore = Ext.getCmp('actividad_combo').getStore();
                            actividadStore.load({
                                params: { tipo_combustibleid: tipo_combustible_id },
                                callback: function () {
                                    let record = actividadStore.findRecord('id', actividad_id);
                                    if (record) Ext.getCmp('actividad_combo').select(record);
                                }
                            })
                        }
                    }
                }
            }).show();
        }
    };

    let _btnAdd = Ext.create('Ext.button.MyButton', {
        text: 'Adicionar',
        iconCls: 'fas fa-plus-square text-primary',
        width: 100,
        handler: action_handler.bind(this, 'add')
    });

    let _btnDel = Ext.create('Ext.button.MyButton', {
        text: 'Eliminar',
        iconCls: 'fas fa-trash-alt text-primary',
        bind: {disabled: '{!gridRegistroOperaciones.selection}'},
        width: 100,
        handler: action_handler.bind(this, 'delete')
    });

    let _tbarRegistro = Ext.getCmp('gridRegistroTbar');
    _tbarRegistro.add(_btnAdd);
    _tbarRegistro.add('-');
    _tbarRegistro.add(_btnDel);
});
