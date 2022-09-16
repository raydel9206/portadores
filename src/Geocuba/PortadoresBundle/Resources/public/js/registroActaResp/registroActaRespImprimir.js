/**
 * Created by javier on 16/05/16.
 */

Ext.onReady(function () {
    let _btnPrint = Ext.create('Ext.button.MyButton', {
        id: 'registro_acta_resp_btn_print',
        text: 'Imprimir',
        disabled: true,
        iconCls: 'fas fa-print text-primary',
        handler: function (This, e) {

            let selected = Ext.getCmp('id_grid_registro_acta_resp').getSelectionModel().getLastSelected();

            App.request('GET', App.buildURL('/portadores/registroActaResp/printRegistroActaResp'), {id: selected.data.id}, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        var newWindow = window.open('', '', 'width=1200, height=700'),
                            document = newWindow.document.open();
                        document.write(response.html);
                        setTimeout(() => {
                            newWindow.print();
                        }, 500);
                        document.close();
                    }
                },
                function (response) { // failure_callback

                }
            );
        }
    });

    let _btn_Export = Ext.create('Ext.button.MyButton', {
        id: 'registro_acta_resp_btn_export',
        text: 'Exportar',
        iconCls: 'fas fa-file-word text-primary',
        disabled: true,
        handler: function (This, e) {

            let selected = Ext.getCmp('id_grid_registro_acta_resp').getSelectionModel().getLastSelected();

            App.request('GET', App.buildURL('/portadores/registroActaResp/printRegistroActaResp'), {id: selected.data.id}, null, null,
                function (response) { // success_callback
                    if (response && response.hasOwnProperty('success') && response.success) { // success_callback but check if exists errors
                        window.open('data:application/msword,' + encodeURIComponent(response.html));
                    }

                },
                function (response) { // failure_callback

                }
            );

        }
    });

    let _tbar2 = Ext.getCmp('registro_acta_resp_tbar');
    _tbar2.add('->');
    _tbar2.add(_btnPrint);
    _tbar2.add(_btn_Export);
});
