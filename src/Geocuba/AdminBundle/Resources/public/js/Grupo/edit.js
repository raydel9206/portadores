Ext.onReady(function () {
    Ext.getCmp('toolbar').setVisible(true);

    var gridpanel = Ext.getCmp('gridpanel');

    Ext.define('App.WinForm', {
        extend: 'Ext.window.Window',
        alias: 'winform',

        initComponent: function () {
            this.callParent();
        },

        resizable: false,
        modal: true,
        layout: 'fit',
        bodyPadding: 10,
        width: 350,

        defaultFocus: '[name=nombre]',

        items: {
            xtype: 'form',
            layout: 'vbox',

            items: [{
                xtype: 'hiddenfield',
                name: 'id',
                hidden: true
                // submitValue: action === 'edit' TODO
            }, {
                xtype: 'textfield',
                name: 'nombre',

                fieldLabel: 'Nombre',
                labelClsExtra: 'font-weight-bold',
                afterLabelTextTpl: '<span class="text-danger" data-qtip="Required">*</span>',
                labelAlign: 'right',
                labelWidth: 55,
                width: '100%',

                allowBlank: false,
                maxLength: 150,
                enforceMaxLength: true
            }],

            listeners: {
                validitychange: function (form, valid, eOpts) {
                    var self = this;

                    self.up('window').down('toolbar').items.each(function (item) {
                        if (item.$className === 'Ext.button.Button' && item.getInitialConfig('formBind')) {
                            item.setDisabled(!valid);
                        }
                    })
                }
            }
        },

        bbar: {
            ui: 'footer',
            layout: {
                pack: 'center'
            },

            items: [{
                text: 'Aceptar',
                width: 75,
                formBind: true,
                handler: function (button) {
                    var winform = button.up('window').hide(),
                        form = winform.down('form');

                    App.request('POST', App.buildURL('/admin/grupo/' + (form.getRecord() ? 'edit' : 'add')), form.getForm().getValues(), null, null,
                        function (response) { // success_callback
                            if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                                Ext.getStore('grupos_store').loadPage(1);
                                winform.close();
                            } else {
                                if (response && response.hasOwnProperty('errors') && response.errors) {
                                    winform.down('form').getForm().markInvalid(response.errors);
                                }
                                winform.show();
                            }
                        },
                        function (response) { // failure_callback
                            winform.show();
                        }
                    );
                }
            }, {
                text: 'Cancelar',
                width: 75,
                handler: function (button, event) {
                    button.up('window').hide().close();
                }
            }]
        },

        listeners: {
            boxready: function (self, width, height, eOpts) {
                var record = self.getInitialConfig('record'),
                    form = self.down('form');

                self.setTitle(record ? Ext.String.format('Modificar <span class="font-italic">{0}</span>', record.get('nombre')) : 'Adicionar grupo');
                self.setGlyph(record ? 0xf044 : 0xf0fe);

                if (record) {
                    form.loadRecord(record);
                }

                // form.isValid();
            }
        }
    });
});