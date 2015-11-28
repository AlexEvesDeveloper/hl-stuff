/**
 * Field suppression IIFE.
 */
(function () {

    /**
     * @var string
     */
    var baseId = 'reference_case_';

    /**
     * @var string For suppressed fields, what parent to hide when suppressed.
     */
    var parentSelectorForHiding = '.form-group';

    /**
     * @var array
     */
    var fields = [];

    /**
     * Initialise field suppression events once DOM is fully ready.
     */
    $(function() {
        // On first load, check if any fields are to be suppressed
        if (typeof suppressFieldConfig != 'undefined' && typeof suppressFieldConfig === 'object') {

            // On first load, decode field strings and suppress fields
            fields = decodeFieldKeysToRegexSelectors(suppressFieldConfig);
            suppressFields();

            // Register custom form change events (the event itself is fired from form.refresh.js and elsewhere)
            $(document).on('formChangedEvent', suppressFields);

        }
    });

    /**
     * Runs through all input fields finding matches from the pre-processed global config object "suppressFieldConfig",
     * and any matches get set with their default values and hidden from view.
     */
    function suppressFields()
    {
        $inputFields = $('input[id^="' + baseId + '"], textarea[id^="' + baseId + '"], select[id^="' + baseId + '"]');

        for (var key in fields) {
            var val = fields[key];

            // Find matching field(s) if it's in the DOM
            $field = $inputFields.filter(function () {
                return $(this).attr('id').match(new RegExp(key)) != null;
            });

            // Set field value
            $field.val(val);

            // Hide field from view
            $field.closest(parentSelectorForHiding).hide();
        }
    }

    /**
     * Takes a set of field definitions such as 'prospectiveLandlord:address:houseName' or
     * 'applications:*:completionMethod' and converts them into regex-friendly strings such as
     * '^prospectiveLandlord_address_houseName$' or '^applications_[^_]+_completionMethod$' respectively ready for ID
     * matching.
     *
     * @param fieldConfig
     * @return array
     */
    function decodeFieldKeysToRegexSelectors(fieldConfig)
    {
        var selectors = [];

        for (var key in fieldConfig) {
            var val = fieldConfig[key]
            var selector = baseId + key;

            // Convert colon separators into underscores
            selector = selector.replace(/:/g, '_');

            // Convert wildcards into less-greedy wildcards
            selector = selector.replace(/\*/g, '[^_]+');
            
            // Add anchors
            selector = '^' + selector + '$';

            selectors[selector] = val;
        }

        return selectors;
    }

}());