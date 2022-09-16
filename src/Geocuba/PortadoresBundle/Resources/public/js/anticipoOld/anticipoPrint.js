/**
 * Created by pfcadenas on 16/05/16.
 */

Ext.onReady(function () {
    var _btnPrintAnticipo = Ext.create('Ext.button.MyButton', {
        id:'anticipo_print_btn',
        text:'Imprimir',
        width:80,
        iconCls:'fas fa-print text-primary',
        disabled: true,
        handler:function (This, e) {

            // window.open(App.buildURL("/portadores/anticipo/toPDFAnticipo") + "?view_id=" + App.route /*+ "&unidadid=" + Ext.getCmp('arbolunidades').getSelection()[0].data.id + "&accion=true"*/);

            var selection = Ext.getCmp('id_grid_anticipo').getSelection();
            //
            App.request('GET', App.buildURL('/portadores/anticipo/print'), {anticipo_id: selection[0].data.id}, null, null,
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

                }, null, null, true
            );
        }
    });

    var _tbar1 = Ext.getCmp('anticipo_tbar');
    _tbar1.add('->');
    _tbar1.add(_btnPrintAnticipo);

});


