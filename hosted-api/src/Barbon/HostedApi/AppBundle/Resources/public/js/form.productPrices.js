/**
 * Product prices IIFE.
 */
(function ()
{
    /**
     * @var string
     */
    var productSelectSelector = 'select[id$="productId"]';

    /**
     * @var object
     */
    var prices = {};

    /**
     * Initialise field suppression events once DOM is fully ready.
     */
    $(function() {
        // On first load, try to get prices
        if (typeof productPricesEndpoint != 'undefined' && typeof productPricesEndpoint === 'string') {

            getProductPrices();

            // Register custom form change events (the event itself is fired from form.refresh.js and elsewhere)
            $(document).on('formChangedEvent', updateProductPrices);

        }
    });

    /**
     * Fetch the product prices from the remote product price endpoint; run a price update in the DOM.
     */
    function getProductPrices()
    {
        $.getJSON(productPricesEndpoint, function (data) {
            prices = data;

            updateProductPrices();
        });
    }

    /**
     * Finds the product dropdowns in the DOM and add pricing in to labels.
     */
    function updateProductPrices()
    {
        // Find all product selects
        $productSelects = $(productSelectSelector);

        // Update their prices (if no prices present)
        $productSelects.find('option').each(function () {

            // Get the descriptive product name
            var productName = $(this).text();

            // Make sure there's no price already in it
            if (-1 == productName.indexOf('£')) {

                // Try to find a matching key in the prices object, if so, set the price in the product name text
                var productId = $(this).val();
                if (prices[productId]) {
                    $(this).text(productName + ' (£' + prices[productId].toFixed(2) + ')');
                }

            }

        });
    }

}());