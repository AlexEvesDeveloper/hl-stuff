/**
 * IRIS Address Finder jQuery Plugin
 *
 * Interfaces with address finder service to generate an address
 * select widget to populate the \Iris\Referencing\Form\TypeAddressType
 *
 * <code>
 *     <div id="address_finder"></div>
 *     <script>
 *         $('#address_finder').addressfinder({
 *             formId: 'property_address'
 *         });
 *     </script>
 * </code>
 *
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 * @author Paul Swift <paul.swift@barbon.com>
 */
(function ($) {

    $.fn.addressfinder = function (options) {

        $.settings = $.extend({
            formId: null
            , preExecutionCallback: null
            , postExecutionCallback: null
            , findAddressButtonLabel: 'Find Address'
            , findAddressButtonClass: ''
            , addressSelector: null
            , postcodeBlankErrorCallback: null
            , postcodeBlankErrorMessage: 'Please enter a postcode'
            , serverErrorCallback: null
            , serverErrorMessage: 'Unable to find addresses - please check the postcode is correct and try again.'
            , addressFinderUri: '/iris-referencing/find-addresses?postcode='
            , loadingSpinnerHtml: '<img src="/images/ajax-loader.gif" class="loading-spinner" />'
        }, options);

        var $findAddress = $('<a href="#" class="'
            + $.settings.findAddressButtonClass + '" data-formId="'
            + $.settings.formId + '">'
            + $.settings.findAddressButtonLabel + '</a>'
        );

        // Push settings for this instantiation into DOM object for later retrieval
        $findAddress.data('settings', $.settings);

        var that = this;

        $findAddress.click(function (e) {

            // Read original form ID back from $findAddress
            var formId = $(this).attr('data-FormId');

            // Read original settings back from DOM object
            $.settings = $(this).data('settings');

            // Do optional pre-execution callback
            if (typeof($.settings.preExecutionCallback) === 'function') {
                $.settings.preExecutionCallback(e);
            }

            e.preventDefault();

            // Validate postcode
            var $postcodeField = $('#' + formId + '_postcode');
            if (!$postcodeField.val()) {
                if (typeof($.settings.postcodeBlankErrorCallback) === 'function') {
                    $.settings.postcodeBlankErrorCallback();
                } else {
                    alert($.settings.postcodeBlankErrorMessage);
                }
                return false;
            }

            // Append loading spinner
            var $loadingSpinner = $($.settings.loadingSpinnerHtml);
            that.append($loadingSpinner);

            // Query address finder service
            $.getJSON($.settings.addressFinderUri + $postcodeField.val(), function (data) {

                // Remove previous selects
                that.find('.address-select').remove();

                // Populate address select
                var addressSelectField = '<div class="address-select"><select id="' + $postcodeField.attr('id') + '_select">';
                addressSelectField += '<option>- Please Select Address -</option>';
                for (key in data) {
                    addressSelectField += '<option value="' + key + '">' + methods.formatAddressForSelect(data[key]) + '</option>';
                }
                addressSelectField += '</select></div>';

                $('.address-lines').slideDown();

                // Insert address selector field
                var $addressSelectField = $(addressSelectField);
                var $where;
                if (
                    $.settings.addressSelector &&
                    $.settings.addressSelector.insertionSelector
                ) {
                    $where = $($.settings.addressSelector.insertionSelector);
                }
                else {
                    $where = that;
                }

                if (
                    $.settings.addressSelector &&
                    $.settings.addressSelector.placement &&
                    'prepend' == $.settings.addressSelector.placement)
                {
                    $where.prepend($addressSelectField);
                }
                else {
                    $where.append($addressSelectField);
                }

                // Add change event to address select
                $addressSelectField.find('select').change(function () {
                    if ($(this).val()) {
                        var address = data[$(this).val()];
                        $('#' + formId + '_flat').val(address.flat);
                        $('#' + formId + '_houseName').val(address.houseName);
                        $('#' + formId + '_houseNumber').val(address.houseNumber);
                        $('#' + formId + '_street').val(address.street);
                        $('#' + formId + '_locality').val(address.locality);
                        $('#' + formId + '_town').val(address.town);
                        $('#' + formId + '_postcode').val(address.postcode);
                        $addressSelectField.remove();
                    }
                });

            })
            .done(function (data) {
                // On successful completion of lookup, remove indicator
                $loadingSpinner.remove();
            })
            .fail(function (jqxhr, textStatus, error) {
                // On unsuccessful completion of lookup, remove indicator and execute callback or show error alert
                $loadingSpinner.remove();
                if (typeof($.settings.serverErrorCallback) === 'function') {
                    $.settings.serverErrorCallback(jqxhr, textStatus, error);
                } else {
                    alert($.settings.serverErrorMessage);
                }
            })
            .always(function () {
                // Do optional post-execution callback
                if (typeof($.settings.postExecutionCallback) === 'function') {
                    $.settings.postExecutionCallback(e);
                }
            });
        });

        that.prepend($findAddress);

    };

    var methods = {
        formatAddressForSelect: function (addressData) {
            var address = '';
            if (addressData.flat) {
                address += addressData.flat + ', ';
            }
            if (addressData.houseNumber) {
                address += addressData.houseNumber + ', ';
            }
            if (addressData.houseName) {
                address += addressData.houseName + ', ';
            }
            if (addressData.street) {
                address += addressData.street + ', ';
            }
            if (addressData.locality) {
                address += addressData.locality + ', ';
            }
            if (addressData.town) {
                address += addressData.town + ', ';
            }
            if (addressData.postcode) {
                address += addressData.postcode + ', ';
            }
            return address.replace(/, +$/, '');
        }
    };

})(jQuery);