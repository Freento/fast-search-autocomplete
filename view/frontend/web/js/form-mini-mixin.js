define(['jquery'], function ($) {
    'use strict';

    const modalWidgetMixin = {
        options: {
            template:
                '<li class="<%- data.row_class %>" id="qs-option-<%- data.index %>" role="option">' +
                '<input type="radio" id="qs-option-<%- data.id %>" name="id" value="<%- data.id %>" style="visibility: hidden; width: 0;">' +
                '<label for="qs-option-<%- data.id %>" style="cursor: inherit">' +
                '<span class="qs-option-name">' +
                ' <%- data.title %>' +
                '</span>' +
                '</label>' +
                '</li>',
        },

        /**
         * Executes when the search box is submitted. Sets the search input field to the
         * value of the selected item.
         * @private
         * @param {Event} e - The submit event
         */
        _onSubmit: function (e) {
            this._super();

            // activate checkbox to send id parameter
            let searchResultItem;
            if (this.responseList.selected) {
                searchResultItem = this.responseList.selected;
            } else if (this.responseList.indexList) {
                searchResultItem = $(this.responseList.indexList[0]);
            } else {
                e.preventDefault();
                return;
            }

            const radioButton = searchResultItem.find('input[type=radio][name=id]');
            if (radioButton) {
                radioButton.prop("checked", true);
            } else {
                e.preventDefault();
            }
        },
    };

    return function (targetWidget) {
        // Example how to extend a widget by mixin object
        $.widget('mage.quickSearch', targetWidget, modalWidgetMixin); // the widget alias should be like for the target widget

        return $.mage.quickSearch; //  the widget by parent alias should be returned
    };
});
