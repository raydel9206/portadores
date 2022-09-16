/**
 * Created by pfcadenas on 16/05/16.
 */
Ext.onReady(function () {
    Ext.define('Portadores.cerraranticipo.Window', {
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
                        type: 'hbox',
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
                            name: 'fecha',
                            id: 'fechaCierre',
                            flex: 0.5,
                            margin: '0 5 0 0',
                            editable: false,
                            value: new Date(),
                            listeners: {
                               afterrender: function (This) {
                                   let dias = App.getDaysInMonth(App.current_year, App.current_month);
                                   let anno = App.current_year;
                                   let min = new Date(App.current_month + '/' + 1 + '/' + anno);
                                   let max = new Date(App.current_month + '/' + dias + '/' + anno);
                                   This.setMinValue(min);
                                   This.setMaxValue(max);
                               }
                            },
                            format: 'd/m/Y',
                            fieldLabel: 'Fecha de devoluci&oacute;n',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        },
                        {
                            xtype: 'timefield',
                            increment: 15,
                            flex: 0.5,
                            margin: '0 0 0 5',
                            name: 'hora',
                            id: 'horaCierre',
                            value: new Date(),
                            fieldLabel: 'Hora',
                            afterLabelTextTpl: [
                                '<span style="color:red;font-weight:bold" data-qtip="Campo Obligatorio">*</span>'
                            ]
                        }
                    ]
                }
            ];
            this.callParent();
        }
    });

    let _btnCerrarAnticipo = Ext.create('Ext.button.MyButton', {
        id: 'anticipo_cerrar_btn',
        disabled: true,
        text: 'Cerrar',
        iconCls: 'fas fa-lock text-primary',
        handler: function (This, e) {
            Ext.create('Portadores.cerraranticipo.Window', {
                title: 'Cerrar anticipo',
                id: 'window_cerraranticipo_id',
                buttons: [
                    {
                        text: 'Aceptar',
                        width: 70,
                        handler: function () {
                            let selection = Ext.getCmp('id_grid_anticipo').getSelectionModel().getLastSelected();
                            let window = Ext.getCmp('window_cerraranticipo_id');
                            let form = window.down('form').getForm();
                            if (form.isValid()) {
                                window.hide();
                                let obj = form.getValues();
                                obj.id = selection.data.id;
                                App.request('POST', App.buildURL('/portadores/anticipo/cerrar'), obj, null, null,
                                    function (response) {
                                        if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                            form.reset();
                                            window.close();
                                            Ext.getCmp('id_grid_anticipo').getStore().load();
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
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        width: 70,
                        handler: function () {
                            Ext.getCmp('window_cerraranticipo_id').close()
                        }
                    }
                ]
            }).show();
        }
    });

    let _tbar1 = Ext.getCmp('anticipo_tbar');
    _tbar1.add(_btnCerrarAnticipo);

});


