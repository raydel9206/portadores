Ext.onReady(function () {

    let gridDistribucion = Ext.getCmp('gridDistribucion');

    let action_handler = function (action) {
        let url = App.buildURL(`/portadores/distribucion_tecnologicos/${action}`);

        if (action === 'generate') {
            Ext.Msg.show({
                title: '¿Generar distribución?',
                message: `¿Está seguro que desea generar una nueva distribución?`,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function (btn) {
                    if (btn === 'yes') {
                        let params = {
                            unidad_id: Ext.getCmp('arbolunidades').getSelection()[0].data.id,
                            mes: Ext.getCmp('mes_anno').getValue().getMonth() + 1,
                            anno: Ext.getCmp('mes_anno').getValue().getFullYear(),
                            tipo_combustible_id: Ext.getCmp('tipo_combustible_combo').getValue()
                        };

                        App.request('POST', url, params, null, null, response => {
                            if (response && response.hasOwnProperty('success') && response.success) {
                                gridDistribucion.getStore().load();
                            }
                        });
                    }
                }
            });

        }
        else if (action === 'save') {
            Ext.MessageBox.confirm('Confirmaci&oacute;n', '¿Esta seguro que desea guardar los cambios?', function (btn) {
                if (btn === 'yes') {
                    let data = gridDistribucion.getStore().getUpdatedRecords().map(record => {
                        let modified = {};
                        Object.keys(record.modified).forEach(key => modified[key] = record.data[key]);

                        return {
                            id: record.id,
                            ...modified
                        };
                    });

                    App.request('PUT', url, {data: Ext.encode(data)}, null, null, response => {
                        if (response && response.hasOwnProperty('success') && response.success) {
                            gridDistribucion.getStore().load();
                            Ext.getCmp('btn_save').disable();
                        }
                    });
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

    let _btnSave = Ext.create('Ext.button.MyButton', {
        id: 'btn_save',
        disabled: true,
        text: 'Guardar',
        iconCls: 'fas fa-save text-primary',
        width: 100,
        handler: action_handler.bind(this, 'save')
    });

    let _tbar = Ext.getCmp('gridDistribucionTbar');
    _tbar.add(_btnGenerate);
    _tbar.add('-');
    _tbar.add(_btnSave);
});
