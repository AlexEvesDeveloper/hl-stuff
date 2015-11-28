/**
 * Collapsible sections IIFE.
 */
(function () {

    /**
     * @var string
     */
    var minimiseSectionButtonText = '<i class="fa fa-minus-circle"></i> Collapse Section';

    /**
     * @var string
     */
    var maximiseSectionButtonText = '<i class="fa fa-plus-circle"></i> Expand Section';

    /**
     * Initialise minimise/maximise button events once DOM is fully ready.
     */
    $(function() {
        // On first load *only*, hide all sections that have passed validation as well as show all invalid sections
        //setShowHideSections();

        // Show/hide minimisable sections
        updateMinimisableSections();

        // Register custom form change events (the event itself is fired from form.refresh.js and elsewhere)
        $(document).on('formChangedEvent', function() {
            // On subsequent loads *only*, show all sections that have failed validation
            setShowInvalidSections();
            // Update minimisable sections based on user choices and the previous function
            updateMinimisableSections();
        });
    });

    /**
     * For every hidden isVisible field, check to see if its associated section contains any errors.  If so, tag the
     * field to be visible, otherwise tag to hide it.  Intended to run once when the page loads only.
     */
    function setShowHideSections()
    {
        // Set initial invisible states
        $('input.is-visible').each(function() {
            var hasError = $(this).closest('.minimise-outer').find('.has-error:first').length;
            if (hasError) {
                // Errors in section, set to visible
                $(this).val('true');
            }
            else {
                // No errors in section, set to invisible
                $(this).val('false');
            }
        });
    }

    /**
     * For every hidden isVisible field, check to see if its associated section contains any errors.  If so, tag the
     * field to be visible.  Intended to run every time the validation round trip occurs to ensure erroring fields can
     * be seen by the user.
     */
    function setShowInvalidSections()
    {
        // Set initial visible states
        $('input.is-visible').each(function() {
            var hasError = $(this).closest('.minimise-outer').find('.has-error:first').length;
            if (hasError) {
                // Errors in section, set to visible
                $(this).val('true');
            }
        });
    }

    /**
     * Show/hide all minimisable sections based on a simple hidden tracking field.
     */
    function updateMinimisableSections()
    {
        // Unbind any previous "minimisable" namespace events - namespace is used to identify the events to prevent
        // multiple, identical events being added to the same buttons as the form changes.
        $('.minimise-button').unbind('.minimisable');

        // Bind add/remove click events in the "minimisable" namespace
        $('.minimise-button').bind('click.minimisable', minimiseSection);

        // Set initial visible or invisible states
        $('input.is-visible').each(function() {
            $minimisableSection = $(this).closest('.minimise-content');
            $minimisableSectionButton = $minimisableSection.closest('.minimise-outer').find('.minimise-button:first');
            if ('false' == $(this).val()) {
                // Invisible
                $minimisableSection.hide();
                $minimisableSectionButton.html(maximiseSectionButtonText);
            }
            else {
                // Visible
                $minimisableSection.show();
                $minimisableSectionButton.html(minimiseSectionButtonText);
            }
        });
    }

    /**
     * Show/hide one minimisable section with a visual slide, based on a user's click event.
     */
    function minimiseSection()
    {
        var $this = $(this);
        var $minimiseContent = $this.closest('.minimise-outer').find('.minimise-content:first');
        var $minimiseContentVisibility = $minimiseContent.find('input.is-visible:first');

        if ($minimiseContent.is(':visible')) {
            // Toggle visible to invisible
            $minimiseContentVisibility.val('false');
            $minimiseContent.slideUp();
            $this.html(maximiseSectionButtonText);
        }
        else {
            // Toggle invisible to visible
            $minimiseContentVisibility.val('true');
            $minimiseContent.slideDown();
            $this.html(minimiseSectionButtonText);
        }
    }

}());