var mergefieldCount = 0;

$(function()
{
    // Work out field count
    mergefieldCount = $("#mergefields-tbody > tr").length;
});

function addMergeField()
{
    if ($('#mergefields-none').length) {
        $('#mergefields-none').remove();
    }

    ++mergefieldCount;

    $('#mergefields-tbody').append('<tr id="mergefields-' + mergefieldCount + '"><td><input type="text" class="form-control" name="merge_keys[]" placeholder="FNAME" maxlength="255"></td><td><input type="text" class="form-control" name="merge_values[]" placeholder="Bob" maxlength="255"></td><td class="actions"><a href="#" onclick="return removeMergeField(\'mergefields-' + mergefieldCount + '\')" title="Delete field"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td></tr>');

    return false;
}

function removeMergeField(field)
{
    if ($('#' + field).length) {
        $('#' + field).remove();
    }

    if (!$("#mergefields-tbody > tr").length) {
        $('#mergefields-tbody').append('<tr id="mergefields-none"><td colspan="3">No merge fields are defined, maybe you could <a href="#" onclick="return addMergeField();">add one (or two)</a>?</td></tr>');
    }

    return false;
}
