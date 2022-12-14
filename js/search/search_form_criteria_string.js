//NT3 Search form criteria string
;
$(function()
{
	// the widget definition, where 'nt3' is the namespace,
	// 'search_form_criteria_string' the widget name
	$.widget( 'nt3.search_form_criteria_string', $.nt3.search_form_criteria,
	{
		// default options
		options:
		{
			// Overload default operator
			'operator': 'contains',
			// Available operators
			'available_operators': {
				'contains': {
					'label': Dict.S('UI:Search:Criteria:Operator:String:Contains'),
					'code': 'contains',
					'rank': 10,
				},
				'starts_with': {
					'label': Dict.S('UI:Search:Criteria:Operator:String:StartsWith'),
					'code': 'starts_with',
					'rank': 20,
				},
				'ends_with': {
					'label': Dict.S('UI:Search:Criteria:Operator:String:EndsWith'),
					'code': 'ends_with',
					'rank': 30,
				},
				'=': {
					'rank': 40,//pre-existing, reordered
				},
				'REGEXP': {
					'label': Dict.S('UI:Search:Criteria:Operator:String:RegExp'),
					'code': 'reg_exp',
					'rank': 50,
				},
			},
		},

   
		// the constructor
		_create: function()
		{
			var me = this;

			this._super();
			this.element.addClass('search_form_criteria_string');
		},
		// called when created, and later when changing options
		_refresh: function()
		{

		},
		// events bound via _bind are removed automatically
		// revert other modifications here
		_destroy: function()
		{
			this.element.removeClass('search_form_criteria_string');
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

		//------------------
		// Inherited methods
		//------------------
	});
});
