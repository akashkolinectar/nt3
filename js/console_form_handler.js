
//NT3 Form handler
;
$(function()
{
    // the widget definition, where 'nt3' is the namespace,
    // 'consoleform_handler' the widget name
    $.widget( 'nt3.console_form_handler', $.nt3.form_handler,
    {
        // default options
        options:
        {
            wizard_helper_var_name: '', // Name of the global variable pointing to the wizard helper
            custom_field_attcode: ''
        },

        // the constructor
        _create: function()
        {
            var me = this;
            
            this.element
                .append('<div class="last-error"></div>')
                .addClass('console_form_handler');

            this.options.oWizardHelper = window[this.options.wizard_helper_var_name];

            this._super();
        },
   
        // events bound via _bind are removed automatically
        // revert other modifications here
        _destroy: function()
        {
            this.element
            .removeClass('console_form_handler');
            this._super();
        },
        _onUpdateFields: function(event, data)
        {
            var me = this;
            var sFormPath = data.form_path;
            var sUpdateUrl = GetAbsoluteUrlAppRoot()+'pages/ajax.render.php';

            this.element.find('[data-form-path="' + sFormPath + '"]').block({message:''});
            $.post(
                sUpdateUrl,
                {
                    operation: 'custom_fields_update',
                    attcode: this.options.custom_field_attcode,
                    //current_values: this.getCurrentValues(),
                    requested_fields: data.requested_fields,
                    form_path: sFormPath,
                    json_obj: this.options.oWizardHelper.UpdateWizardToJSON()
                },
                function(data){
                    me.element.find('.last-error').text('');
                    if ('form' in data) {
                        me._onUpdateSuccess(data, sFormPath);
                    }
                }
            )
                .fail(function(data){ me._onUpdateFailure(data, sFormPath); })
                .always(function(data){
                    me.alignColumns();
                    me.element.find('[data-form-path="' + sFormPath + '"]').unblock();
                    if ('error' in data) {
                        console.log('Update field failure: '+data.error);
                        me.element.find('.last-error').text(data.error);
                    }
                    me._onUpdateAlways(data, sFormPath);
                });
        },
        // On initialization or update
        alignColumns: function()
        {
            var iMaxWidth = 0;
            var oLabels = this.element.find('td.form-field-label');
            // Reset the width to the automatic (original) value
            oLabels.width('');
            oLabels.each(function() {
                iMaxWidth = Math.max(iMaxWidth, $(this).width());
            });
            oLabels.width(iMaxWidth);
        },
        // Intended for overloading in derived classes
        _onSubmitClick: function()
        {
        },
        // Intended for overloading in derived classes
        _onCancelClick: function()
        {
        },
        // Intended for overloading in derived classes
        _onUpdateFailure: function(data)
        {
        },
        // Intended for overloading in derived classes
        _disableFormBeforeLoading: function()
        {
        },
        // Intended for overloading in derived classes
        _enableFormAfterLoading: function()
        {
        },
    });
});
