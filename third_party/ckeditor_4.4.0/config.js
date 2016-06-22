/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
//var base_url = location.href.substr(0, location.href.indexOf('vicflora_dev')+12);
//var base_url = 'http://data.rbg.vic.gov.au/vicflora';

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

    config.height = '450px';
    
    config.toolbarCanCollapse = true;
    config.toolbarStartupExpanded = true;
    
    config.stylesSet = 'custom_styles';
    config.contentsCss = base_url + '/third_party/ckeditor_4.4.0/ckeditor_styles.css';

    // Remove some buttons, provided by the standard plugins, which we don't
    // need to have in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Strike,Subscript,Superscript,Format,Font,FontSize';
};

CKEDITOR.stylesSet.add( 'custom_styles', [
    // Block-level styles.
    { name: 'Description', element: 'p', attributes: { 'class': 'description' } },
    { name: 'Phenology',  element: 'p', attributes: { 'class': 'phenology' } },
    { name: 'State distribution',  element: 'p', attributes: { 'class': 'distribution_australia' } },
    { name: 'World distribution',  element: 'p', attributes: { 'class': 'distribution_world' } },
    { name: 'Habitat',  element: 'p', attributes: { 'class': 'habitat' } },
    { name: 'Note',  element: 'p', attributes: { 'class': 'note' } },
    { name: 'References',  element: 'p', attributes: { 'class': 'references' } },

    // Inline styles.
    { name: 'Scientific name', element: 'span', attributes: { 'class': 'scientific_name'} }
]);
