//NT3 Portal Form field
;
$(function()
{
	// the widget definition, where 'nt3' is the namespace,
	// 'portal_form_field' the widget name
	$.widget( 'nt3.portal_form_field', $.nt3.form_field,
	{
		// default options
		options:
		{
			on_validation_callback: function(me, oResult){
				me.element.removeClass('has-success has-warning has-error')
				me.element.find('.help-block').html('');
				if(!oResult.is_valid)
				{
					me.element.addClass('has-error');
					for(var i in oResult.error_messages)
					{
						me.element.find('.help-block').append($('<p>' + oResult.error_messages[i] + '</p>'));
					}
				}
			}	
		},
   
		// the constructor
		_create: function()
		{
			this.element
			.addClass('portal_form_field');
	
			this._super();
		},  
		// events bound via _bind are removed automatically
		// revert other modifications here
		_destroy: function()
		{
			this.element
			.removeClass('portal_form_field');
	
			this._super();
		},
		// _setOptions is called with a hash of all options that are changing
		// always refresh when changing options
		_setOptions: function()
		{
			this._superApply(arguments);
		},
		// _setOption is called for each individual option that is changing
		_setOption: function( key, value )
		{
			this._super( key, value );
		},
		showOptions: function()
		{
			return this.options;
		}
	});
});
