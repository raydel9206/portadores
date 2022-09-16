Ext.define('Ext.ux.form.TreeComboBoxList', {
    extend: 'Ext.tree.Panel',
    alias: 'widget.treecomboboxlist',

    floating: true,
    hidden: true,
    rootVisible: false,
    value: false,
    anyMatch: false,
    allowFolderSelect: false,

    initComponent: function () {
        this.listeners = {
            'cellclick': this.onCellClick,
            'itemkeydown': this.onItemKeyDown
        };
        this.callParent();
    },

    onCellClick: function (tree, td, cellIndex, record, tr, rowIndex, e, eOpts) {
        if (this.allowFolderSelect || record.isLeaf()) {
            this.fireEvent('picked', record);
        }
    },

    onItemKeyDown: function (view, record, item, index, e, eOpts) {
        if (this.allowFolderSelect || record.isLeaf() && e.keyCode == e.ENTER) {
            this.fireEvent('picked', record);
        }
    },

    selectFirstLeaf: function () {
        var firstLeaf = this.getStore().findRecord('leaf', true);
        this.getSelectionModel().select(firstLeaf);
    },

    doLocalQuery: function (searchValue) {
        var store = this.getStore();
        this.searchValue = searchValue.toLowerCase();

        store.setRemoteFilter(false);
        store.filterBy(this.pickerStoreFilter, this);
        this.fireEvent('filtered', store, this);
    },

    pickerStoreFilter: function (record) {

        var itemValue = record.get(this.displayField).toLowerCase();

        if (this.anyMatch) {
            if (itemValue.indexOf(this.searchValue) != -1) {
                return true;
            }
        } else {
            if (itemValue.startsWith(this.searchValue)) {
                return true;
            }

        }


        return false;
    },

    doRemoteQuery: function (searchValue) {
        var store = this.getStore();
        store.setRemoteFilter(true);
        store.on('load', this.onPickerStoreLoad, this, {
            single: true
        });
        store.filter(new Ext.util.Filter({
            anyMatch: this.anyMatch,
            disableOnEmpty: true,
            property: this.displayField,
            value: searchValue
        }));
    },

    onPickerStoreLoad: function (store, records) {
        this.fireEvent('filtered', store, this);
    }
});