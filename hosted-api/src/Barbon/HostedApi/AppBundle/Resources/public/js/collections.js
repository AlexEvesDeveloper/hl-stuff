/**
 * Collections IIFE.
 */
(function () {

    /**
     * Initialise add/remove collection button events once DOM is fully ready.
     */
    $(function() {
        // Wire up events
        registerAddRemoveButtonEvents();

        // Register custom form change events (the event itself is fired from here and form.refresh.js)
        $(document).on('formChangedEvent', registerAddRemoveButtonEvents);
    });

    /**
     * Attach button click events to the form.
     */
    function registerAddRemoveButtonEvents()
    {
        // Unbind any previous "collection" namespace events - namespace is used to identify the events to prevent
        // multiple, identical events being added to the same buttons as the form changes.
        $('.add-to-collection, .remove-from-collection').unbind('.collection');

        // Bind add/remove click events in the "collection" namespace
        $('.add-to-collection').bind('click.collection', addCollectionItem);

        $('.remove-from-collection').bind('click.collection', removeCollectionItem);
    }

    /**
     * Add a new collection prototype to the collection.
     */
    function addCollectionItem()
    {
        var $this = $(this);
        var collectionId = $this.data('collectionid');
        var $collection = $('#' + collectionId);
        var collectionTagName = $this.data('prototype-name');

        // Build prototype
        var index = $collection.data('index');

        if (undefined == index) {
            // Initialise
            index = $collection.children().length;
        }

        // Wrap prototype in a form group class to mimic the structure as generated server-side
        var prototype = '<div class="form-group">' + $this.data('prototype') + '</div>';

        // Replace the prototype's tag name with the index and increment the collection index
        prototype = prototype.replace(new RegExp(collectionTagName, 'g'), index);
        $collection.data('index', index + 1);

        // Add prototype to DOM
        $collection.append(prototype);

        // Trigger a form changed event
        $(document).trigger('formChangedEvent');
    }

    /**
     * Remove an item from the collection.
     */
    function removeCollectionItem()
    {
        if (confirm('Are you sure you want to remove this item and any data you may have entered for it?')) {
            var $this = $(this);
            $this.closest('.form-group').remove();

            // Trigger a form changed event
            $(document).trigger('formChangedEvent');
        }
    }
}());