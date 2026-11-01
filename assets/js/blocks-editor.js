/* Betnero: editor dei blocchi tabella e toplist (senza build). */
( function ( wp ) {
	var el = wp.element.createElement;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var ServerSideRender = wp.serverSideRender;

	function preview( name, attributes ) {
		return el( 'div', useBlockProps(), el( ServerSideRender, { block: name, attributes: attributes } ) );
	}

	wp.blocks.registerBlockType( 'betnero/tabella-casino', {
		title: 'Tabella casino',
		icon: 'list-view',
		category: 'betnero',
		description: 'Tabella dei casino dalla query JetEngine della home, con filtri.',
		edit: function ( props ) {
			var a = props.attributes;
			return [
				el( InspectorControls, { key: 'i' },
					el( PanelBody, { title: 'Impostazioni' },
						el( ToggleControl, { label: 'Mostra i filtri', checked: a.showFilters, onChange: function ( v ) { props.setAttributes( { showFilters: v } ); } } ),
						el( ToggleControl, { label: 'Mostra il parere dell\'esperto', checked: a.showParere, onChange: function ( v ) { props.setAttributes( { showParere: v } ); } } ),
						el( RangeControl, { label: 'Numero di casino (0 = tutti)', value: a.limit, min: 0, max: 20, onChange: function ( v ) { props.setAttributes( { limit: v || 0 } ); } } )
					)
				),
				el( 'div', { key: 'p' }, preview( 'betnero/tabella-casino', a ) )
			];
		},
		save: function () { return null; }
	} );

	wp.blocks.registerBlockType( 'betnero/tabella-scommesse', {
		title: 'Tabella scommesse',
		icon: 'list-view',
		category: 'betnero',
		description: 'Tabella dei bookmaker dalla query JetEngine della home.',
		edit: function ( props ) {
			var a = props.attributes;
			return [
				el( InspectorControls, { key: 'i' },
					el( PanelBody, { title: 'Impostazioni' },
						el( ToggleControl, { label: 'Mostra i filtri', checked: a.showFilters, onChange: function ( v ) { props.setAttributes( { showFilters: v } ); } } ),
						el( RangeControl, { label: 'Numero di bookmaker (0 = tutti)', value: a.limit, min: 0, max: 20, onChange: function ( v ) { props.setAttributes( { limit: v || 0 } ); } } )
					)
				),
				el( 'div', { key: 'p' }, preview( 'betnero/tabella-scommesse', a ) )
			];
		},
		save: function () { return null; }
	} );

	wp.blocks.registerBlockType( 'betnero/toplist', {
		title: 'Toplist compatta',
		icon: 'star-filled',
		category: 'betnero',
		description: 'Le prime posizioni di casino o bookmaker, scorrevole da mobile.',
		edit: function ( props ) {
			var a = props.attributes;
			return [
				el( InspectorControls, { key: 'i' },
					el( PanelBody, { title: 'Impostazioni' },
						el( SelectControl, { label: 'Tipo', value: a.type, options: [ { label: 'Casino', value: 'casino' }, { label: 'Scommesse', value: 'scommesse' } ], onChange: function ( v ) { props.setAttributes( { type: v } ); } } ),
						el( RangeControl, { label: 'Numero di operatori', value: a.limit, min: 1, max: 10, onChange: function ( v ) { props.setAttributes( { limit: v || 5 } ); } } ),
						el( TextControl, { label: 'Titolo (vuoto = automatico)', value: a.title, onChange: function ( v ) { props.setAttributes( { title: v } ); } } )
					)
				),
				el( 'div', { key: 'p' }, preview( 'betnero/toplist', a ) )
			];
		},
		save: function () { return null; }
	} );
} )( window.wp );
