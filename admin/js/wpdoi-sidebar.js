( function ( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var el = wp.element.createElement;
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;


//https://rudrastyh.com/gutenberg/add-custom-panels-to-post-settings-sidebar.html

	var MetaBlockField = function ( props ) {
		var metaFieldValue = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute(
				'meta'
			)[ 'wpdoi_doi' ];
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;

		return el( TextControl, {
			label: 'DOI',
			value: metaFieldValue,
			onChange: function ( content ) {
				editPost( {
					meta: { wpdoi_doi: content },
				} );
			},
		} );
	};

	registerPlugin( 'wp-doi-panel', {
		render: function () {
			return el(
				PluginDocumentSettingPanel,
				{
					name: 'wp-doi-panel',
					title: 'WP-DOI',
				},
				el(
					'div',
					{ className: 'wp-doi-content' },
					el( MetaBlockField )
				)
			);
		},
	} );
} )( window.wp );
