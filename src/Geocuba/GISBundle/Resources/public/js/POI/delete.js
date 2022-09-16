Ext.onReady(function () {
    const map = window.Map,
        default_hover = map.getInteractions().item(map.getInteractions().getLength() - 1);

    // -----------------------------------------------------------------------------------------------------------------

    let select, hover;

    $('#del-point').click(function (evt) {
        const panel = map.get('panel'),
            points_layer = map.getLayers().getArray()[2];

        if (!$(evt.currentTarget).hasClass('active')) {
            panel.enableAction(evt.currentTarget.id, false);

            default_hover.setActive(false);

            select = new ol.interaction.Select({
                layers: [points_layer],
                condition: ol.events.condition.click,
                multi: true
            });
            hover = new ol.interaction.Select({
                layers: [points_layer],
                condition: ol.events.condition.pointerMove
            });

            select.on('select', function (evt) {
                if (evt.selected.length !== 0) {
                    const feature = evt.selected[0];

                    Ext.Msg.show({
                        title: '¿Eliminar Punto de interés (POI)?',
                        message: Ext.String.format('¿Está seguro que desea eliminar <strong>{0}</strong>?', feature.get('point_name')),
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.Msg.QUESTION,
                        fn: function (btn) {
                            if (btn === 'yes') {
                                const url = App.buildURL('/gis/poi/del'),
                                    params = {node_id: feature.get('node_id')};

                                App.request('DELETE', url, params, null, function (response) { // complete_callback
                                    select.getFeatures().pop();
                                }, function (response) { // success_callback
                                    points_layer.getSource().removeFeature(feature);
                                });
                            } else {
                                select.getFeatures().pop();
                            }
                        }
                    });
                }
            });

            map.addInteraction(select);
            map.addInteraction(hover);
        } else {
            panel.disableAction(evt.currentTarget.id);

            default_hover.setActive(true);
            map.removeInteraction(select);
            map.removeInteraction(hover);

            select = hover = null;
        }
    });
});