//var base_url = location.href.substr(0, location.href.indexOf('vicflora_dev')+12);
//var base_url = location.href.substr(0, location.href.indexOf('vicflora')+8);;
CKEDITOR.config.customConfig = base_url + '/js/ckeditor_customconfig.js';

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		//{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	// config.removeButtons = 'Underline,Subscript,Superscript';
    
    config.removePlugins = 'templates';
    
    // config.autoGrow_onStartup = true;
    config.autoGrow_maxHeight = 420;
    config.height = '420px';
    config.toolbarCanCollapse = true;
    config.toolbarStartupExpanded = true;
    
    config.stylesSet = 'custom_styles';
    config.contentsCss = base_url + '/third_party/ckeditor_4.4.0/ckeditor_styles.css';
    
    //config.pasteFromWordRemoveStyles = false;
};

