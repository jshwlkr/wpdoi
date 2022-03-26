( function ( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginSidebar = wp.editPost.PluginSidebar;
	var el = wp.element.createElement;
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;

	var MetaBlockField = function ( props ) {
		var metaFieldValue = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute(
				'meta'
			)[ 'wpdoi_doi' ];
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;

		return el( TextControl, {
			label: 'Meta Field',
			value: metaFieldValue,
			onChange: function ( content ) {
				editPost( {
					meta: { wpdoi_doi: content },
				} );
			},
		} );
	};

	registerPlugin( 'wp-doi-sidebar', {
		render: function () {
			return el(
				PluginSidebar,
				{
					name: 'wp-doi-sidebar',
					icon: 'admin-post',
					title: 'DOI',
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
