
function ExportStartExport() {
    var oParams = {};
    oParams.operation = 'export_build';
    oParams.format = sFormat;
    oParams.expression = sOQL;
    oParams.fields = sFields;
    $.post(GetAbsoluteUrlAppRoot() + 'pages/ajax.render.php', oParams, function (data) {
        if (data == null) {
            ExportError('Export failed (no data provided), please contact your administrator');
        }
        else {
            ExportRun(data);
        }
    }, 'json')
        .fail(function () {
            ExportError('Export failed, please contact your administrator');
        });
}

function ExportError(sMessage) {
    sDataState = 'error';
    $('#export-feedback').hide();
    $('#export-text-result').show();
    $('#export-error').html(sMessage);
}

function ExportRun(data) {
    switch (data.code) {
        case 'run':
            // Continue
            $('.progress').progressbar({value: data.percentage});
            $('.export-message').html(data.message);
            oParams = {};
            oParams.token = data.token;
            if (sDataState == 'cancelled') {
                oParams.operation = 'export_cancel';
                $('#export-cancel').hide();
                $('#export-close').show();
            }
            else {
                oParams.operation = 'export_build';
            }

            $.post(GetAbsoluteUrlAppRoot() + 'pages/ajax.render.php', oParams, function (data) {
                    ExportRun(data);
                },
                'json');
            break;

        case 'done':
            sDataState = 'done';
            $('#export-cancel').hide();
            $('#export-close').show();
            $('.progress').progressbar({value: data.percentage});
            sMessage = '<a href="' + GetAbsoluteUrlAppRoot() + 'pages/ajax.render.php?operation=export_download&token=' + data.token + '" target="_blank">' + data.message + '</a>';
            $('.export-message').html(sMessage);
            if (data.text_result != undefined) {
                if (data.mime_type == 'text/html') {
                    $('#export-content').parent().html(data.text_result);
                    $('#export-text-result').show();
                    $('#export-text-result .listResults').tableHover();
                    $('#export-text-result .listResults').tablesorter({widgets: ['myZebra']});
                }
                else {
                    if ($('#export-text-result').closest('ui-dialog').length == 0) {
                        // not inside a dialog box, adjust the height... approximately
                        var jPane = $('#export-text-result').closest('.ui-layout-content');
                        var iTotalHeight = jPane.height();
                        jPane.children(':visible').each(function () {
                            if ($(this).attr('id') != '') {
                                iTotalHeight -= $(this).height();
                            }
                        });
                        $('#export-content').height(iTotalHeight - 80);
                    }
                    $('#export-content').val(data.text_result);
                    $('#export-text-result').show();
                }
            }
            break;

        case 'error':
            sDataState = 'error';
            $('#export-feedback').hide();
            $('#export-text-result').show();
            $('#export-error').html(data.message);
            $('#export-cancel').hide();
            $('#export-close').show();
        default:
    }
}
