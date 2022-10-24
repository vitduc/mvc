define([
    'jquery',
    'underscore',
    'Magento_Ui/js/lib/spinner',
    'Magento_Ui/js/form/element/abstract'
], function ($, _, loader, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            validationParams: {},
            formName: 'nsk_partnumbers_form.nsk_partnumbers_form',
            defaultsValidateParams: {
                'part_numbers_and_company_unique': true
            }
        },

        /**
         * Initializes UiSelect component.
         *
         * @returns {UiSelect} Chainable.
         */
        initialize: function () {
            this._super();
            window['part_number_validate_url'] = this.partNumberValidateUrl;
            return this;
        }
    });
})
