/**
 * Form refresh IIFE.
 */
(function () {

    /**
     * @var string
     */
    var formSelector = '#generic_step_form';

    /**
     * @var boolean
     */
    var formInFlight = false;

    /**
     * @var int
     */
    var formInFlightMaxTimeBeforeTimeout = 30000;

    /**
     * @var int
     */
    var formInFlightTimeoutId = null;

    /**
     * Initialise validation events once DOM is fully ready.
     */
    $(function()
    {
        // Do an initial validation for when the page loads, as it may have loaded as a GET
        //refreshForm($(formSelector), null);

        // Build initial form navigation
        buildFormNav();

        // Wire up events
        registerFormValidation();

        // Register custom form change events (the event itself is fired from here and collections.js)
        $(document).on('formChangedEvent', buildFormNav);
        $(document).on('formChangedEvent', registerFormValidation);
    });

    /**
     * Attach form validation events to the form.
     */
    function registerFormValidation()
    {
        $(formSelector + ' input.form-refresh').blur(function() {
            // Tiny delay to allow focused field ID discovery
            var $element = $(this);
            setTimeout(function() {
                refreshForm($(formSelector), $element);
            }, 10);
        });

        $(formSelector + ' select.form-refresh').change(function() {
            // Tiny delay to allow focused field ID discovery
            var $element = $(this);
            setTimeout(function() {
                refreshForm($(formSelector), $element);
            }, 10);
        });
    }

    /**
     * Perform a validation round trip.
     *
     * @param jQuery $form
     * @param jQuery|null $element
     */
    function refreshForm($form, $element)
    {
        if (formInFlight) {
            return;
        }

        formInFlight = true;

        // Show AJAX indicator
        if ($element) {
            $element.closest('.form-group').addClass('ajax-loading');
        }

        // Note active field ID if one has focus
        var focusedId = (document.activeElement) ? document.activeElement.id : null;

        // Disable all form fields, serializing ahead of that so that data entered will be read
        var serializedFormData = $form.serialize();
        $form.find('input, select, textarea, button').addClass('form-refresh-disabled').attr('disabled', 'disabled');

        // Set the form in flight timeout ticking...
        setFormInFlightTimeout();

        // Validate form and replace chunk of HTML (this also re-enables all relevant fields)
        $.post(
            getValidationUrl($form, 'validate'),
            serializedFormData,
            function (data) {
                // Replace form chunk of HTML
                $form.replaceWith(data);

                // Trigger a form replaced event
                $(document).trigger('formChangedEvent');

                // Re-register validation events
                registerFormValidation();
            },
            'html'
        )
        .success(function () {

            // If old active field still exists, give it focus again
            if (focusedId) {
                $('#' + focusedId).focus();
            }

            // Remove timeout timer
            window.clearTimeout(formInFlightTimeoutId);

            formInFlight = false;
        });
    }

    /**
     * @param jQuery $form
     *
     * @return string
     */
    function getValidationUrl($form)
    {
        return ($form.attr('action')) ? $form.attr('action') : window.location.href;
    }

    /**
     * Build the form navigation from a semi-manual parse of its DOM.
     *
     * @param jQuery $form
     */
    function buildFormNav($form)
    {
        $nav = $('ul.form-navigation');

        navHtml = '<ul class="nav form-navigation">';
        apps = '';

        navHtml += '<li><ul class="nav"><li class="first">';
        navHtml += '<a href="#rental-property">Rental Property</a>';
        navHtml += '</li></ul></li>';

        navHtml += '<li><ul class="nav"><li class="first">';
        navHtml += '<a href="#applications-outer">References</a>';
        navHtml += '</li>';

        // Iterate through applications
        var appCount = 0;
        $('#reference_case_applications > .form-group').each(function() {
            appCount++;

            // Get application ID
            var appId = $(this).find('.single-application').attr('id');

            navHtml += '<li><ul class="nav"><li class="first">';
            navHtml += '<a href="#' + appId + '">Reference ' + appCount + '</a>';

            // Within this particular application sub-header, go through details

            // Letting referee
            if ($('#' + appId + '-letting-referee').length) {
                navHtml += '<li>';
                navHtml += '<a href="#' + appId + '-letting-referee">Letting Referee</a>';
                navHtml += '</li>';
            }

            // Address history
            navHtml += buildLeafNav(appId, '-address-history', 'Address History', '_addressHistories', 'Address');

            // Financial referees
            navHtml += buildLeafNav(appId, '-financial-referees', 'Financial Referees', '_financialReferees', 'Financial Referee');

            // Guarantors
            navHtml += '<li>';

            // Iterate through guarantors
            var guarantorCount = 0;
            $('#' + appId + '_guarantors > .form-group').each(function() {
                guarantorCount++;

                // Get guarantor ID
                var guarantorId = $(this).find('.single-application-block').attr('id');

                navHtml += '<ul class="nav"><li class="first">';
                navHtml += '<a href="#' + guarantorId + '">Guarantor ' + guarantorCount + '</a>';

                // Guarantor letting referee
                if ($('#' + guarantorId + '-letting-referee').length) {
                    navHtml += '<ul class="nav"><li>';
                    navHtml += '<a href="#' + guarantorId + '-letting-referee">Letting Referee</a>';
                    navHtml += '</li></ul>';
                }

                // Address history
                navHtml += buildLeafNav(guarantorId, '-address-history', 'Address History for Guarantor ' + guarantorCount, '_addressHistories', 'Address');

                // Financial referees
                navHtml += buildLeafNav(guarantorId, '-financial-referees', 'Financial Referees for Guarantor ' + guarantorCount, '_financialReferees', 'Financial Referee');

                navHtml += '</li></ul>';


            });

            navHtml += '</li>';

            navHtml += '</li></ul></li>';

        });

        navHtml += '</ul></li>';
        navHtml += '</ul>';

        $nav.replaceWith(navHtml);

        /**
         * Build leaf navigation chunk of HTML.
         *
         * @param string baseId
         * @param string idExtension1
         * @param string heading
         * @param string idExtension2
         * @param string subheading
         * @return string
         */
        function buildLeafNav(baseId, idExtension1, heading, idExtension2, subheading)
        {
            navHtml = '';

            if ($('#' + baseId + idExtension1).length) {
                navHtml += '<li>';
                navHtml += '<a href="#' + baseId + idExtension1 + '">' + heading + '</a>';

                // Iterate through subheadings
                var count = 0;
                $('#' + baseId + idExtension2 + ' > .form-group').each(function() {
                    count++;

                    // Get address ID
                    var finRefId = $(this).find('.single-application').attr('id');

                    navHtml += '<ul class="nav"><li>';
                    navHtml += '<a href="#' + finRefId + '">' + subheading + ' ' + count + '</a>';
                    navHtml += '</li></ul>';
                });

                navHtml += '</li>';
            }

            return navHtml;
        }
    }

    /**
     * Set a timeout timer with the opportunity to reload the form (with a submit to not lose data).
     */
    function setFormInFlightTimeout()
    {
        formInFlightTimeoutId = window.setTimeout(
            function() {
                if (confirm('The server seems to be taking a while, would you like to try reloading the form?')) {
                    $form = $(formSelector);
                    $form.find('.form-refresh-disabled').removeAttr('disabled').removeClass('form-refresh-disabled');
                    $form.submit();
                }
                else {
                    setFormInFlightTimeout();
                }
            },
            formInFlightMaxTimeBeforeTimeout
        );
    }

})();