(function () {
    tinymce.PluginManager.add('ucf_com_shortcodes_key', function (editor, url){

        // Add a button that opens a window
        editor.addButton('ucf_com_shortcodes_key', {
            title: 'Custom UCF Shortcodes',
            text: 'Shortcodes',
            icon: false,
            type: 'menubutton',
            menu: [
                {
                    title: 'Accordion layout',
                    text: 'Accordion',
                    icon: 'icon dashicons-format-image', // video icon
                    onclick: function(){
                        editor.insertContent('[accordion]');
                    }
                },
                {
                    title: 'Domain portion of URL',
                    text: 'Base URL',
                    icon: 'icon dashicons-wordpress', // video icon
                    onclick: function(){
                        editor.insertContent('[base_url]');
                    }
                },
                {
                    title: 'Eight Box image layout',
                    text: 'Eight Box',
                    icon: 'icon dashicons-format-image', // video icon
                    onclick: function(){
                        editor.insertContent('[eight_box]');
                    }
                },
                {
                    title: 'Google Map',
                    text: 'Google Map',
                    icon: 'icon dashicons-wordpress', // video icon
                    onclick: function () {
                        // Open window
                        editor.windowManager.open({
                            title: 'Google Map',
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'address',
                                    label: 'Location full street address, or coordinates.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'width',
                                    label: 'CSS width of iframe, in pixels. Leave blank for default.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'height',
                                    label: 'CSS hight of iframe, in pixels. Leave blank for default.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'zoom',
                                    label: 'Zoom level. Larger numbers are closer zoom levels.'
                                }
                            ],
                            onsubmit: function (e) {
                                // Insert content when the window form is submitted
                                editor.insertContent('[google_map ' +
                                    ((e.data.address) ? ' address="' + e.data.address + '"' : '') +
                                    ((e.data.width) ? ' width="' + e.data.width + '"' : '') +
                                    ((e.data.height) ? ' height="' + e.data.height + '"' : '') +
                                    ((e.data.zoom) ? ' zoom="' + e.data.zoom + '"' : '') +
                                    ']');
                            }

                        });
                    }
                },
                {
                    title: 'Library',
                    text: 'Library',
                    icon: 'icon dashicons-welcome-widgets-menus', // video icon
                    onclick: function(){
                        editor.insertContent('[library]');
                    }
                },
                {
                    title: 'Newsfeed Listings',
                    text: 'Newsfeed',
                    icon: 'icon dashicons-welcome-widgets-menus', // kind of newspaper icon
                    onclick: function () {
                        // Open window
                        editor.windowManager.open({
                            title: 'Newsfeed Listings',
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'category',
                                    label: 'News Category. If unspecified, all articles are shown.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'hide_news',
                                    label: 'Hide news listing. Useful if you only want the slider.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'show_slider',
                                    label: 'Show slider. If unspecified, news image slider will not be included.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'news_count',
                                    label: 'How many articles to include in news listing. If unspecified, all articles are shown  (unless a global default is defined).'
                                },
                                {
                                    type: 'textbox',
                                    name: 'slider_count',
                                    label: 'How many articles to include in slider. If unspecified, it will be equal to news_count.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'blog',
                                    label: 'Slug or numeric id of the blog from which to pull news. If unspecified, it will pull from current blog.'
                                }
                            ],
                            onsubmit: function (e) {
                                // Insert content when the window form is submitted
                                editor.insertContent('[newsfeed ' +
                                ((e.data.category) ? ' category="' + e.data.category + '"' : '') +
                                ((e.data.hide_news) ? ' hide_news="' + e.data.hide_news + '"' : '') +
                                ((e.data.show_slider) ? ' show_slider="' + e.data.show_slider + '"' : '') +
                                ((e.data.news_count) ? ' news_count="' + e.data.news_count + '"' : '') +
                                ((e.data.slider_count) ? ' slider_count="' + e.data.slider_count + '"' : '') +
                                ((e.data.blog) ? ' blog="' + e.data.blog + '"' : '') +
                                ']');
                            }

                        });
                    }
                },
                {
                    title: 'Staff Listing',
                    text: 'Staff',
                    icon: 'icon dashicons-admin-users', // user silhouette icon
                    onclick: function () {
                        // Open window
                        editor.windowManager.open({
                            title: 'Staff Listing',
                            body: [
                                {
                                    type: 'textbox',
                                    name: 'category',
                                    label: 'Staff Category. If unspecified, all profiles are shown.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'hide_photo',
                                    label: 'Hide Staff Photos. Leave blank to show photos.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'profile_count',
                                    label: 'Number of profiles to show per page. If unset (or 0), return all profiles on a single page.'
                                },
                                {
                                    type: 'textbox',
                                    name: 'use_current_site_profiles',
                                    label: 'If set, use current site profiles. Leave blank for most subsites that have profiles stored on the primary site.'
                                }
                            ],
                            onsubmit: function (e) {
                                // Insert content when the window form is submitted
                                editor.insertContent('[staff ' +
                                    ((e.data.category) ? ' category="' + e.data.category + '"' : '') +
                                    ((e.data.hide_photo) ? ' hide_photo="' + e.data.hide_photo + '"' : '') +
                                    ((e.data.profile_count) ? ' profile_count="' + e.data.profile_count + '"' : '') +
                                    ((e.data.use_current_site_profiles) ? ' use_current_site_profiles="' + e.data.use_current_site_profiles + '"' : '') +
                                    ']');
                            }

                        });
                    }
                },
                {
                    title: 'Three Box image layout',
                    text: 'Three Box',
                    icon: 'icon dashicons-format-image', // video icon
                    onclick: function(){
                        editor.insertContent('[three_box]');
                    }
                },
                {
                    title: 'Two columns, side by side',
                    text: 'Two Column',
                    icon: 'icon dashicons-welcome-widgets-menus', // video icon
                    onclick: function(){
                        editor.insertContent('[two_column]');
                    }
                },
                /*{
                    title: 'Server Status',
                    text: 'Server Status',
                    icon: 'icon dashicons-admin-users', // user silhouette icon
                    onclick: function(){
                        editor.insertContent('[server_status]');
                    }
                },*/

            ]


        });

    });


})();