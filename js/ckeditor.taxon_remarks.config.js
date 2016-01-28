/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here.
    // For the complete reference:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config

    // The toolbar groups arrangement, optimized for two toolbar rows.
    config.toolbarGroups = [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'styles', groups: ['style']},
            //{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
            //{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
            { name: 'links' },
            //{ name: 'insert' },
            //{ name: 'forms' },
            { name: 'mode' },
            { name: 'tools' }
            //{ name: 'others' },
            //{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
            //{ name: 'colors' },
            //{ name: 'about' }
    ];

    config.height = '120px';
    
    config.toolbarCanCollapse = true;
    config.toolbarStartupExpanded = true;
    
    config.stylesSet = 'custom_styles';
    config.contentsCss = base_url + '/third_party/ckeditor_4.4.0/ckeditor_styles.css';

    // Remove some buttons, provided by the standard plugins, which we don't
    // need to have in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Strike,Subscript,Superscript,Format,Font,FontSize';
};

CKEDITOR.stylesSet.add( 'custom_styles', [
    // Inline styles.
    { name: 'Scientific name', element: 'span', attributes: { 'class': 'scientific_name'} }
]);
