/* Betnero: "Titolo come" per il blocco Dettagli (accordion). Senza build. */
( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;

	wp.hooks.addFilter( 'blocks.registerBlockType', 'betnero/details-heading-attr', function ( settings, name ) {
		if ( 'core/details' !== name ) {
			return settings;
		}
		settings.attributes = Object.assign( {}, settings.attributes, {
			bnHeading: { type: 'string', default: '' }
		} );
		return settings;
	} );

	var withControl = wp.compose.createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( 'core/details' !== props.name ) {
				return el( BlockEdit, props );
			}
			var cls = props.attributes.className || '';
			var def = /(^|\s)bn-guide-item(\s|$)/.test( cls ) ? 'H3' : 'testo semplice';
			return el( Fragment, {},
				el( BlockEdit, props ),
				el( InspectorControls, {},
					el( PanelBody, { title: 'Titolo dell\'accordion', initialOpen: true },
						el( SelectControl, {
							label: 'Titolo come',
							help: 'Livello del titolo per Google. Nella pagina l\'aspetto non cambia.',
							value: props.attributes.bnHeading || '',
							options: [
								{ label: 'Predefinito (' + def + ')', value: '' },
								{ label: 'Testo semplice', value: 'p' },
								{ label: 'H2', value: 'h2' },
								{ label: 'H3', value: 'h3' },
								{ label: 'H4', value: 'h4' }
							],
							onChange: function ( v ) {
								props.setAttributes( { bnHeading: v } );
							}
						} )
					)
				)
			);
		};
	}, 'withBetneroDetailsHeading' );

	wp.hooks.addFilter( 'editor.BlockEdit', 'betnero/details-heading-control', withControl );
} )( window.wp );
