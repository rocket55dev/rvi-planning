<?php

if (function_exists('acf_add_local_field_group')) {

    /**
     * Add custom wysiwyg toolbar options
     */
    add_filter('acf/fields/wysiwyg/toolbars', 'modify_acf_wysiwyg_toolbars', 10, 1);
    function modify_acf_wysiwyg_toolbars($toolbars){
        $fullToolbar = [
            'styleselect',
            'bold',
            'italic',
            'hr',
            'link',
            'unlink',
            'bullist',
            'numlist',
            'alignleft',
            'aligncenter',
            'alignright',
            'removeformat',
            'forecolor',
        ];

        $basicToolbar = [
            'bold',
            'italic',
            'link',
            'unlink',
            'removeformat',
        ];

        unset($toolbars['Full']);
        unset($toolbars['Basic']);

        $toolbars['Full'] = [];
        $toolbars['Full'][1] = $fullToolbar;

        $toolbars['Basic'] = [];
        $toolbars['Basic'][1] = $basicToolbar;

        return $toolbars;
    }

    /**
     * Add custom wysiwyg format options
     */
    add_filter( 'tiny_mce_before_init', 'modify_tiny_mce_format_options', 10, 1 );
    function modify_tiny_mce_format_options($settings){
        $styleFormats = [
            [
                'title' => 'H1',
                'block' => 'h1',
            ],
            [
                'title' => 'H2',
                'block' => 'h2',
            ],
            [
                'title' => 'H3',
                'block' => 'h3',
            ],
            [
                'title' => 'H4',
                'block' => 'h4',
            ],
            [
                'title' => 'H5',
                'block' => 'h5',
            ],
            [
                'title' => 'Paragraph',
                'block' => 'p',
            ],
            [
                'title' => 'Button',
                'inline' => 'a',
                'classes' => 'btn btn-primary',
                'selector' => 'a'
            ]
        ];
        $settings['style_formats'] = json_encode($styleFormats);
        
        return $settings;
    }    
    
}