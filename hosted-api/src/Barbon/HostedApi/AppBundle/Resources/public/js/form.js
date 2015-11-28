/**
 * Form IIFE.
 */
(function () {

    /**
     * @var string
     */
    var loadingImage = '/images/ajax-loader.gif';

    /**
     * Initialise once DOM is fully ready.
     */
    $(function()
    {
        // Affix affixed navigation with affix()
        $('#affixed-navigation').affix({
            offset: {
                top: 335
            }
        });

        // Show loader indicator when form is submitted, and disable button
        addSubmitIndicator();

        // Register custom form change event (the event itself is fired from elsewhere)
        $(document).on('formChangedEvent', registerSubmitIndicator);
    });

    function registerSubmitIndicator()
    {
        // Unbind any previous "formsubmit" namespace events - namespace is used to identify the events to prevent
        // multiple, identical events being added to the same buttons as the form changes.
        $('form').unbind('.formsubmit');

        // Bind add/remove click events in the "formsubmit" namespace
        $('form').bind('submit.formsubmit', addSubmitIndicator);
    }

    /**
     * Add submit indicator click event
     */
    function addSubmitIndicator()
    {
        // Pseudo-disable and indicator image
        $('form').submit(function(e) {
            var $submitButton = $(this).find('button[type="submit"]');
            if ($submitButton.data('clicked')) {
                e.preventDefault();
                return;
            }
            $submitButton.after('<img src="' + loadingImage + '" alt="loading" />');
            $submitButton.data('clicked', true);
        });
    }

    $(document).ready(function(){
        // Simulate a click to add an applicant as long as the 'first name'
        // field for the first applicant doesn't exist in the form
        if (false == $('#reference_case_applications_0_firstName').length) {
            $('#add-tenant-to-collection').click();
        }

        var addressOption = $('.address-selection option:selected');
        addressOption.val('GB');
        addressOption.text('United Kingdom');
    });

})();