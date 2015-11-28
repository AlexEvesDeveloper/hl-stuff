
$(function() {
    // Register callbacks
    $('#addField').click(addRequestField);
    $('#submitRequest').click(submitForm);
    $('#loadSampleData').click(loadSampleData);
});

/**
 * Add field to the request
 */
function addRequestField()
{
    var requestFieldName = $('#requestFieldName');
    var requestFieldValue = $('#requestFieldValue');
    var requestFields = $('#requestFields');

    var requestFieldNameValue = requestFieldName.val();
    var requestFieldValueValue = requestFieldValue.val();
    var requestFieldWrapperId = ('requestFieldWrapper-' + requestFieldNameValue)
        .replace(/\]\[/g, '_')
        .replace(/\[/g, '_')
        .replace(/\]/g, '_')
    ;
    var requestFieldId = ('requestFieldId-' + requestFieldNameValue)
        .replace(/\]\[/g, '_')
        .replace(/\[/g, '_')
        .replace(/\]/g, '_')
    ;

    if (undefined == requestFieldNameValue || '' == requestFieldNameValue ||
        undefined == requestFieldValueValue || '' == requestFieldValueValue) {
        return;
    }

    var requestFieldHtml =
        $('<div class="row" style="padding-top: 15px;"></div>')
            .attr('id', requestFieldWrapperId)
            .append(
                $('<div class="col-md-5"></div>')
                .html(
                    $('<label></label>')
                        .attr('for', requestFieldId)
                        .html(requestFieldNameValue))
            )
            .append(
                $('<div class="col-md-6"></div>')
                .html(
                    ($('<input class="form-control" type="text" style="width: 100%;">')
                        .attr('id', requestFieldId)
                        .attr('name', requestFieldNameValue)
                        .attr('value', requestFieldValueValue))
                )
            )
            .append(
                $('<div class="col-md-1"></div>')
                .html(
                    $('<button type="button" class="close"><span aria-hidden="true">&times;</span></button>')
                        .click(function() {removeRequestField(requestFieldWrapperId);})
                )
            )
    ;

    requestFields.append(requestFieldHtml);

    requestFieldName.val(undefined);
    requestFieldValue.val(undefined);
}

/**
 * Remove a request field
 */
function removeRequestField(fieldId)
{
    $('#' + fieldId).remove();
}




