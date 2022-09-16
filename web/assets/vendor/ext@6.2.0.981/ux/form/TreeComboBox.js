Ext.define('Ext.ux.form.TreeComboBox', {
    extend: 'Ext.form.field.Picker',
    requires: [
        'Ext.ux.form.TreeComboBoxList'
    ],

    xtype: 'treecombobox',
    store: false,
    queryMode: 'local',
    anyMatch: false,
    allowFolderSelect: false,

    filterDelayBuffer: 300,
    enableKeyEvents: true,
    valueField: 'text',
    selectedRecord: false,

    treeConfig: {
        // Tree Config
    },

    initComponent: function () {
        this.on('change', this.onTreeComboValueChange, this, {
            buffer: this.filterDelayBuffer
        });
        this.callParent();
    },

    onTreeComboValueChange: function (field, value) {
        this.selectedRecord = false;
        switch (this.queryMode) {
        case 'local':
            this.getPicker().doLocalQuery(value)
            break;
        case 'remote':
            this.getPicker().doRemoteQuery(value);
            break;
        }
    },

    expand: function () {
        this.getPicker().expandAll();
        this.callParent([arguments]);
    },

    createPicker: function () {
        var treeConfig = Ext.apply({
            xtype: 'treecomboboxlist',
            id: this.getId() + '-TreePicker',
            store: this.getPickerStore(),
            valueField: this.valueField,
            displayField: this.displayField,
            anyMatch: this.anyMatch,
            allowFolderSelect: this.allowFolderSelect
        }, this.treeConfig);
        var treePanelPicker = Ext.widget(treeConfig);

        treePanelPicker.on({
            picked: this.onPicked,
            filtered: this.onFiltered,
            beforeselect: this.onBeforeSelect,
            beforedeselect: this.onBeforeDeselect,
            scope: this
        });
        return treePanelPicker;
    },

    onFiltered: function (store, treeList) {
        if (store.getCount() > 0) {
            this.expand();
            this.focus();
        }
    },

    getPickerStore: function () {
        return this.store;
    },

    onPicked: function (record) {
        this.suspendEvent('change');
        this.selectedRecord = record;
        this.setValue(record.get(this.displayField));
        this.collapse();
        this.resumeEvent('change');
        this.fireEvent('select', record);
    },

    getValue: function () {
        var value;
        if (this.valueField && this.selectedRecord) {
            value = this.selectedRecord.get(this.valueField);
        } else {
            value = this.getRawValue();
        }
        return value;
    },

    getSubmitValue: function () {
        var value = this.getValue();
        if (Ext.isEmpty(value)) {
            value = '';
        }
        return value;
    },
    onBeforeSelect: function (comboBox, record, recordIndex) {
        return this.fireEvent('beforeselect', this, record, recordIndex);
    },

    onBeforeDeselect: function (comboBox, record, recordIndex) {
        return this.fireEvent('beforedeselect', this, record, recordIndex);
    },

    getSelectedRecord: function () {
        return this.selectedRecord;
    }
});