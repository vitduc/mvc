define([
    'jquery',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate',
    'mage/url',
    'uiRegistry'
], function ($, validator, $t, urlBuilder, registry) {
    'use strict';

    return function (target) {
        var part_number_unique = true;
        var competitor_unique = true;
        var nsk_part_number_unique = true;

        validator.addRule(
            'part_numbers_and_company_nsk_part_number_unique',
            function (value, params, data) {
                var compeData = $("[name=\"competitor\"]").val();
                var nskPartNumber = $("[name=\"nsk_part_number\"]").val();

                if (compeData && nskPartNumber) {
                    var competitor_ui = registry.get('index = competitor');
                    var nsk_part_number_ui = registry.get('index = nsk_part_number');

                    part_number_unique = checkUniquePartNumberAndCompany(compeData, value, nskPartNumber);

                    if (part_number_unique === true && competitor_unique === false) {
                        competitor_ui.validate();
                    }

                    if (part_number_unique === true && nsk_part_number_unique === false) {
                        nsk_part_number_ui.validate();
                    }

                    return part_number_unique;
                } else {
                    return true;
                }
            },
            $t('Competitor, Part Number and NSK Part Number must be unique in pairs')
        );

        validator.addRule(
            'competitor_and_part_numbers_nsk_part_number_unique',
            function (value, params, data) {
                var partNumber = $("[name=\"part_number\"]").val();
                var nskPartNumber = $("[name=\"nsk_part_number\"]").val();
                if (partNumber && nskPartNumber) {
                    var part_number_ui = registry.get('index = part_number');
                    var nsk_part_number_ui = registry.get('index = nsk_part_number');

                    competitor_unique = checkUniquePartNumberAndCompany(value, partNumber, nskPartNumber);

                    if (competitor_unique === true && part_number_unique === false) {
                        part_number_ui.validate();
                    }

                    if (competitor_unique === true && nsk_part_number_unique === false) {
                        nsk_part_number_ui.validate();
                    }

                    return competitor_unique;
                } else {
                    return true;
                }
            },
            $t('Competitor, Part Number and NSK Part Number must be unique in pairs')
        );

        validator.addRule(
            'nsk_part_number_competitor_and_part_numbers_unique',
            function (value, params, data) {
                var partNumber = $("[name=\"part_number\"]").val();
                var compeData = $("[name=\"competitor\"]").val();

                if (partNumber && compeData) {
                    var part_number_ui = registry.get('index = part_number');
                    var competitor_ui = registry.get('index = competitor');

                    nsk_part_number_unique = checkUniquePartNumberAndCompany(compeData, partNumber, value);

                    if (nsk_part_number_unique === true && part_number_unique === false) {
                        part_number_ui.validate();
                    }

                    if (nsk_part_number_unique === true && competitor_unique === false) {
                        competitor_ui.validate();
                    }
                    return nsk_part_number_unique;
                } else {
                    return true;
                }
            },
            $t('Competitor, Part Number and NSK Part Number must be unique in pairs')
        );

        return target;
    };

    function checkUniquePartNumberAndCompany(competitor, partNumber, nskPartNumber) {
        var isUnique = false;
        var url = window['part_number_validate_url'];
        var pathname = window.location.pathname;
        var datas = {
            competitor: competitor,
            partNumber: partNumber,
            nskPartNumber: nskPartNumber
        };

        if (pathname.indexOf('entity_id') > -1) {
            let entity_id = pathname.match(/\d+/)[0];
            datas = {
                competitor: competitor,
                partNumber: partNumber,
                nskPartNumber: nskPartNumber,
                entity_id: entity_id
            };
        }

        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            async: false,
            data: datas,
            showLoader: true,
            success: function (response) {
                isUnique = response.nsk_part_number_part_numbers_and_company_unique;
            },
            error: function (xhr, status, errorThrown) {
                console.log('Error happens. Try again.');
            }
        });

        return isUnique;
    }
});
