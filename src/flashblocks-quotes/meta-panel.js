/**
 * Editor sidebar panel for the flashblocks_quote post type.
 *
 * Shows Author Name and Author Role fields in the Document settings panel.
 * Only renders when the current post type is flashblocks_quote.
 */

import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { TextControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';

function FlashblocksQuoteMetaPanel() {
	const postType = useSelect(
		( select ) => select( editorStore ).getCurrentPostType(),
		[]
	);

	// Only render for the quotes CPT.
	if ( postType !== 'flashblocks_quote' ) {
		return null;
	}

	const [ meta, setMeta ] = useEntityProp( 'postType', 'flashblocks_quote', 'meta' );

	const authorName = meta?._flashblocks_quote_author_name ?? '';
	const authorRole = meta?._flashblocks_quote_author_role ?? '';

	return (
		<PluginDocumentSettingPanel
			name="flashblocks-quote-author"
			title={ __( 'Author Information', 'flashblocks-quotes' ) }
			className="flashblocks-quote-author-panel"
		>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Author Name', 'flashblocks-quotes' ) }
				help={ __( 'Full name of the person being quoted.', 'flashblocks-quotes' ) }
				value={ authorName }
				onChange={ ( value ) =>
					setMeta( {
						...meta,
						_flashblocks_quote_author_name: value,
					} )
				}
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Author Role / Title', 'flashblocks-quotes' ) }
				help={ __( 'e.g. CEO, Acme Corp', 'flashblocks-quotes' ) }
				value={ authorRole }
				onChange={ ( value ) =>
					setMeta( {
						...meta,
						_flashblocks_quote_author_role: value,
					} )
				}
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'flashblocks-quote-meta-panel', {
	render: FlashblocksQuoteMetaPanel,
} );
