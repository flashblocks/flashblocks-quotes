/**
 * Editor component for flashblocks/flashblocks-quotes.
 *
 * - Inside a Query Loop: reads context.postId and renders a live preview.
 * - Standalone: shows a searchable post picker, then renders a live preview
 *   with a "Change quote" control beneath it.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { ComboboxControl, Placeholder, Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';

import './editor.scss';

export default function Edit( { attributes, setAttributes, context } ) {
	const { selectedPostId } = attributes;

	// Query Loop passes the current iteration's post ID via context.
	const contextPostId = context?.postId;
	const isInQueryLoop = !! contextPostId;
	const postId = isInQueryLoop ? contextPostId : selectedPostId || 0;

	// Search string driving the post picker options.
	const [ searchInput, setSearchInput ] = useState( '' );

	// Fetch quote posts for the picker dropdown.
	const { pickerOptions, isLoadingOptions } = useSelect(
		( select ) => {
			const { getEntityRecords, isResolving } = select( coreStore );
			const query = {
				per_page: 20,
				status: 'publish',
				_fields: 'id,title',
				...(searchInput ? { search: searchInput } : {}),
			};
			const records = getEntityRecords( 'postType', 'flashblocks_quote', query );
			return {
				pickerOptions: ( records ?? [] ).map( ( p ) => ( {
					value: String( p.id ),
					label: p.title?.rendered || `Quote #${ p.id }`,
				} ) ),
				isLoadingOptions: isResolving( 'getEntityRecords', [
					'postType',
					'flashblocks_quote',
					query,
				] ),
			};
		},
		[ searchInput ]
	);

	// Fetch the active post data for the editor preview.
	const { quotePost, featuredImage, isLoadingPost } = useSelect(
		( select ) => {
			if ( ! postId ) {
				return { quotePost: null, featuredImage: null, isLoadingPost: false };
			}

			const { getEntityRecord, isResolving } = select( coreStore );

			const post = getEntityRecord( 'postType', 'flashblocks_quote', postId );
			const mediaId = post?.featured_media;
			const media = mediaId
				? getEntityRecord( 'root', 'media', mediaId )
				: null;

			return {
				quotePost: post ?? null,
				featuredImage: media ?? null,
				isLoadingPost: isResolving( 'getEntityRecord', [
					'postType',
					'flashblocks_quote',
					postId,
				] ),
			};
		},
		[ postId ]
	);

	const blockProps = useBlockProps( { className: 'wp-block-flashblocks-flashblocks-quotes' } );

	// ── No quote selected and not in a loop ──────────────────────────────────
	if ( ! isInQueryLoop && ! selectedPostId ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="format-quote"
					label={ __( 'Flashblocks Quote', 'flashblocks-quotes' ) }
					instructions={ __(
						'Search for a quote to display.',
						'flashblocks-quotes'
					) }
				>
					<ComboboxControl
						__nextHasNoMarginBottom
						label={ __( 'Select a quote', 'flashblocks-quotes' ) }
						value=""
						options={ pickerOptions }
						onFilterValueChange={ setSearchInput }
						onChange={ ( val ) => {
							if ( val ) {
								setAttributes( { selectedPostId: Number( val ) } );
							}
						} }
						isLoading={ isLoadingOptions }
					/>
				</Placeholder>
			</div>
		);
	}

	// ── Loading ──────────────────────────────────────────────────────────────
	if ( isLoadingPost || ( postId && ! quotePost ) ) {
		return (
			<div { ...blockProps }>
				<Spinner />
			</div>
		);
	}

	// ── Post not found (e.g. deleted) ────────────────────────────────────────
	if ( ! quotePost ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="warning"
					label={ __( 'Quote not found', 'flashblocks-quotes' ) }
					instructions={ __(
						'The selected quote could not be found. Choose another.',
						'flashblocks-quotes'
					) }
				>
					{ ! isInQueryLoop && (
						<ComboboxControl
							__nextHasNoMarginBottom
							label={ __( 'Select a quote', 'flashblocks-quotes' ) }
							value={ String( selectedPostId ) }
							options={ pickerOptions }
							onFilterValueChange={ setSearchInput }
							onChange={ ( val ) => {
								if ( val ) {
									setAttributes( { selectedPostId: Number( val ) } );
								}
							} }
							isLoading={ isLoadingOptions }
						/>
					) }
				</Placeholder>
			</div>
		);
	}

	// ── Live preview ─────────────────────────────────────────────────────────
	const authorName = quotePost.meta?._flashblocks_quote_author_name || '';
	const authorRole = quotePost.meta?._flashblocks_quote_author_role || '';
	const photoUrl   = featuredImage?.source_url || '';
	const content    = quotePost.content?.rendered || '';

	const hasAttribution = authorName || authorRole || photoUrl;

	return (
		<figure { ...blockProps }>
			{ /* Quote body */ }
			<blockquote
				className="wp-block-flashblocks-flashblocks-quotes__body"
				// eslint-disable-next-line react/no-danger
				dangerouslySetInnerHTML={ { __html: content } }
			/>

			{ /* Attribution */ }
			{ hasAttribution && (
				<figcaption className="wp-block-flashblocks-flashblocks-quotes__attribution">
					{ photoUrl && (
						<div
							className="wp-block-flashblocks-flashblocks-quotes__photo-wrap"
							aria-hidden="true"
						>
							<img
								className="wp-block-flashblocks-flashblocks-quotes__author-photo"
								src={ photoUrl }
								alt={ authorName || '' }
							/>
						</div>
					) }
					<cite className="wp-block-flashblocks-flashblocks-quotes__cite">
						{ authorName && (
							<span className="wp-block-flashblocks-flashblocks-quotes__author-name">
								{ authorName }
							</span>
						) }
						{ authorRole && (
							<span className="wp-block-flashblocks-flashblocks-quotes__author-role">
								{ authorRole }
							</span>
						) }
					</cite>
				</figcaption>
			) }

			{ /* Post picker shown below the preview when standalone */ }
			{ ! isInQueryLoop && (
				<div className="wp-block-flashblocks-flashblocks-quotes__editor-controls">
					<ComboboxControl
						__nextHasNoMarginBottom
						label={ __( 'Change quote', 'flashblocks-quotes' ) }
						value={ String( selectedPostId ) }
						options={ pickerOptions }
						onFilterValueChange={ setSearchInput }
						onChange={ ( val ) => {
							if ( val ) {
								setAttributes( { selectedPostId: Number( val ) } );
							}
						} }
						isLoading={ isLoadingOptions }
					/>
				</div>
			) }
		</figure>
	);
}
