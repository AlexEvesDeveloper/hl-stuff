/**
 * Form date pickers IIFE.
 */
(function () {

    /**
     * @var string
     */
    var hiddenFieldIdExtension = '_datepicker_hidden';

    /**
     * @var string
     */
    var dayFieldIdExtension = '_day';

    /**
     * @var string
     */
    var monthFieldIdExtension = '_month';

    /**
     * @var string
     */
    var yearFieldIdExtension = '_year';

    /**
     * Initialise validation events once DOM is fully ready.
     */
    $(function()
    {
        // Run now
        addDatepickers();

        // Register custom form change event
        $(document).on('formChangedEvent', addDatepickers);
    });

    /**
     * Add datepickers into the form where a data-provider attribute of "datepicker" is specified.
     */
    function addDatepickers()
    {
        // Kill off any existing datepickers
        $('.form-inline-date-hidden, a.dp-choose-date').remove();

        // Iterate through all inputs with a provider of "datepicker" and attach datepickers
        $('input[data-provide="datepicker"]').each(function() {
            $(this).attr('type', 'text');
            $(this).datePicker({
                startDate: ($(this).data('startDate') ? $(this).data('startDate') : '01/01/' + ((new Date().getFullYear()) - 100)),
                endDate: ($(this).data('endDate') ? $(this).data('endDate') : null),
                format: 'dd/mm/yyyy',
                verticalPosition: $.dpConst.POS_BOTTOM
            });
        });

        // Iterate through all .form-inline-dates with a provider of "datepicker" and attach datepickers
        $('.form-inline-date[data-provide="datepicker"]').each(function() {
            // Get ID
            var id = $(this).attr('id');

            // Make a special hidden field for the date picker to attach to
            var datePickerFieldId = id + hiddenFieldIdExtension;
            var datePickerField = '<span class="form-inline-date-hidden"><input type="text" id="' + datePickerFieldId + '" style="display: none;" /></span>';

            // Add hidden datepicker field into the DOM
            $(this).prepend(datePickerField);

            // Set hidden field's value from the dropdowns
            dateDropdownsToSingleHiddenField(id);

            // Wire up datepicker
            $('#' + datePickerFieldId).datePicker({
                startDate: ($(this).data('startDate') ? $(this).data('startDate') : '01/01/' + ((new Date().getFullYear()) - 100)),
                endDate: ($(this).data('endDate') ? $(this).data('endDate') : null),
                format: 'dd/mm/yyyy',
                verticalPosition: $.dpConst.POS_BOTTOM
            });

            // Add update events so that datepicker can update the dropdowns and vice-versa via the hidden field
            $('#' + datePickerFieldId).change(function(e) {
                var baseId = $(this).closest('.form-inline-date').attr('id');
                singleHiddenFieldToDateDropdowns(baseId);
            });

            $('#' + id + dayFieldIdExtension + ', #' + id + monthFieldIdExtension + ', #' + id + yearFieldIdExtension).change(function(e) {
                var baseId = $(this).closest('.form-inline-date').attr('id');
                dateDropdownsToSingleHiddenField(baseId);
            });
        });
    }

    /**
     * Take the value of a set of dropdown fields representing day, month and year, and set a single text value with it.
     *
     * @param string baseId
     */
    function dateDropdownsToSingleHiddenField(baseId)
    {
        var day = $('#' + baseId + dayFieldIdExtension).val();
        var month = $('#' + baseId + monthFieldIdExtension).val();
        var year = $('#' + baseId + yearFieldIdExtension).val();

        $('#' + baseId + hiddenFieldIdExtension).val(
            lpad(day, '0', 2) + '/' +
            lpad(month, '0', 2) + '/' +
            year
        );
    }

    /**
     * Take the value of a single text date field value and set a set of dropdown fields representing day, month and
     * year with it.
     *
     * @param string baseId
     */
    function singleHiddenFieldToDateDropdowns(baseId)
    {
        var date = $('#' + baseId + hiddenFieldIdExtension).val();

        $('#' + baseId + dayFieldIdExtension).val(parseInt(date.substr(0, 2), 10));
        $('#' + baseId + monthFieldIdExtension).val(parseInt(date.substr(3, 2), 10));
        $('#' + baseId + yearFieldIdExtension).val(parseInt(date.substr(6, 4), 10));
    }

    /**
     * Utility function for left-padding a string.
     *
     * @param string str
     * @param string padString
     * @param int len
     * @return string
     */
    function lpad(str, padString, len) {
        while (str.length < len) {
            str = padString + str;
        }
        return str;
    }

})();