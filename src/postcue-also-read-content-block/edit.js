/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect, useMemo, useRef } from '@wordpress/element';
import Autosuggest from 'react-autosuggest';
import {
	BlockControls,
	InspectorControls,
	ColorPalette,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	ToolbarGroup,
	ToolbarButton,
	TextControl,
	PanelBody,
	FontSizePicker,
	ToggleControl,
} from '@wordpress/components';

import './editor.scss';

const TEMPLATE_STYLE_FALLBACKS = {
	default: {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#696969',
		blockTitleFontSize: '18px',
		postTitleTextColor: '#ffffff',
		postTitleFontSize: '18px',
		postBgColor: '#06b7d3',
	},
	'soft-card': {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#1f2937',
		blockTitleFontSize: '20px',
		postTitleTextColor: '#ffffff',
		postTitleFontSize: '17px',
		postBgColor: '#00a7c5',
	},
	'accent-strip': {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#0f172a',
		blockTitleFontSize: '16px',
		postTitleTextColor: '#ffffff',
		postTitleFontSize: '17px',
		postBgColor: '#0f8ca7',
	},
	'minimal-outline': {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#1f2937',
		blockTitleFontSize: '17px',
		postTitleTextColor: '#0b6a78',
		postTitleFontSize: '16px',
		postBgColor: '#eaf8fb',
	},
	'sleek-card': {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#173d4f',
		blockTitleFontSize: '18px',
		postTitleTextColor: '#000000',
		postTitleFontSize: '18px',
		postBgColor: '#ffffff',
	},
	compact: {
		blockTitle: 'Also Read',
		blockTitleTextColor: '#4b5563',
		blockTitleFontSize: '14px',
		postTitleTextColor: '#ffffff',
		postTitleFontSize: '14px',
		postBgColor: '#0891b2',
	},
};

function ExternalArrowIcon() {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 24 24"
			fill="currentColor"
			width="20"
			height="20"
			aria-hidden="true"
			focusable="false"
		>
			<path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path>
		</svg>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const { value, showInput, isLoading, showNotFoundMsg, editView } =
		attributes;
	const [ suggestions, setSuggestions ] = useState( [] );
	const didInitGlobalDefaults = useRef( false );

	const globalDefaults = useMemo(
		() =>
			typeof window !== 'undefined' && window.pocualrecb_defaults
				? window.pocualrecb_defaults
				: {},
		[]
	);

	const availableTemplates = useMemo( () => {
		const templateStyles = globalDefaults.templateStyles;
		if ( templateStyles && typeof templateStyles === 'object' ) {
			const templateKeys = Object.keys( templateStyles );
			if ( templateKeys.length ) {
				return templateKeys;
			}
		}

		return Object.keys( TEMPLATE_STYLE_FALLBACKS );
	}, [ globalDefaults ] );

	const selectedTemplate = availableTemplates.includes(
		globalDefaults.template
	)
		? globalDefaults.template
		: 'default';

	const selectedTemplateDefaultStyles =
		TEMPLATE_STYLE_FALLBACKS[ selectedTemplate ] ||
		TEMPLATE_STYLE_FALLBACKS.default;

	const selectedTemplateStyles = useMemo( () => {
		const allTemplateStyles =
			globalDefaults.templateStyles &&
			typeof globalDefaults.templateStyles === 'object'
				? globalDefaults.templateStyles
				: {};

		const templateStyle =
			allTemplateStyles[ selectedTemplate ] &&
			typeof allTemplateStyles[ selectedTemplate ] === 'object'
				? allTemplateStyles[ selectedTemplate ]
				: {};

		return {
			...selectedTemplateDefaultStyles,
			...templateStyle,
		};
	}, [ globalDefaults, selectedTemplate, selectedTemplateDefaultStyles ] );

	const globalStyleValues = useMemo(
		() => ( {
			blockTitle:
				selectedTemplateStyles.blockTitle ||
				globalDefaults.blockTitle ||
				selectedTemplateDefaultStyles.blockTitle,
			blockTitleTextColor:
				selectedTemplateStyles.blockTitleTextColor ||
				globalDefaults.blockTitleTextColor ||
				selectedTemplateDefaultStyles.blockTitleTextColor,
			blockTitleFontSize:
				selectedTemplateStyles.blockTitleFontSize ||
				globalDefaults.blockTitleFontSize ||
				selectedTemplateDefaultStyles.blockTitleFontSize,
			postTitleTextColor:
				selectedTemplateStyles.postTitleTextColor ||
				globalDefaults.postTitleTextColor ||
				selectedTemplateDefaultStyles.postTitleTextColor,
			postTitleFontSize:
				selectedTemplateStyles.postTitleFontSize ||
				globalDefaults.postTitleFontSize ||
				selectedTemplateDefaultStyles.postTitleFontSize,
			postBgColor:
				selectedTemplateStyles.postBgColor ||
				globalDefaults.postBgColor ||
				selectedTemplateDefaultStyles.postBgColor,
		} ),
		[
			globalDefaults,
			selectedTemplateDefaultStyles,
			selectedTemplateStyles,
		]
	);

	const blockProps = useBlockProps( {
		className: `pocualrecb-template-${ selectedTemplate }`,
	} );

	// Initialize block-level style values from global defaults only once.
	// This keeps "Allow Custom style" toggle predictable:
	// - OFF uses global style values at render-time.
	// - ON preserves block custom values instead of resetting each toggle.
	useEffect( () => {
		if ( didInitGlobalDefaults.current ) {
			return;
		}

		if ( ! attributes.allowCustomStyle ) {
			setAttributes( {
				blockTitle:
					globalStyleValues.blockTitle || attributes.blockTitle,
				blockTitleTextColor:
					globalStyleValues.blockTitleTextColor ||
					attributes.blockTitleTextColor,
				blockTitleFontSize:
					globalStyleValues.blockTitleFontSize ||
					attributes.blockTitleFontSize,
				postTitleTextColor:
					globalStyleValues.postTitleTextColor ||
					attributes.postTitleTextColor,
				postTitleFontSize:
					globalStyleValues.postTitleFontSize ||
					attributes.postTitleFontSize,
				postBgColor:
					globalStyleValues.postBgColor || attributes.postBgColor,
			} );
		}

		didInitGlobalDefaults.current = true;
	}, [ attributes, globalStyleValues, setAttributes ] );

	// Compute final style values based on allowCustomStyle
	const blockTitle = ! attributes.allowCustomStyle
		? globalStyleValues.blockTitle
		: attributes.blockTitle;
	const blockTitleTextColor = ! attributes.allowCustomStyle
		? globalStyleValues.blockTitleTextColor
		: attributes.blockTitleTextColor;
	const blockTitleFontSize = ! attributes.allowCustomStyle
		? globalStyleValues.blockTitleFontSize
		: attributes.blockTitleFontSize;
	const postTitleTextColor = ! attributes.allowCustomStyle
		? globalStyleValues.postTitleTextColor
		: attributes.postTitleTextColor;
	const postTitleFontSize = ! attributes.allowCustomStyle
		? globalStyleValues.postTitleFontSize
		: attributes.postTitleFontSize;
	const postBgColor = ! attributes.allowCustomStyle
		? globalStyleValues.postBgColor
		: attributes.postBgColor;

	const postProps = {
		blockTitle,
		blockTitleTextColor,
		blockTitleFontSize,
		postTitleTextColor,
		postTitleFontSize,
		postBgColor,
		selectedPost: attributes.selectedPost,
	};

	const setSelectedPost = ( post ) => {
		setAttributes( {
			selectedPost: {
				id: post.id,
				title: post.title,
				link: post.link,
				thumbnail: post.thumbnail,
			},
		} );
	};
	// Called when input changes
	const onChange = ( event, { newValue } ) => {
		setAttributes( { value: newValue } );
	};

	// Called when suggestions need to be fetched
	const onSuggestionsFetchRequested = async ( { value: searchValue } ) => {
		if ( ! searchValue ) {
			setSuggestions( [] );
			setAttributes( { isLoading: false, showNotFoundMsg: false } );
			return;
		}

		setAttributes( { isLoading: true, showNotFoundMsg: false } );

		try {
			const res = await fetch(
				`${
					window.pocualrecb_ajaxurl
				}?action=pocualrecb_post_search&term=${ encodeURIComponent(
					searchValue
				) }&_pocualrecb_nonce=${ encodeURIComponent(
					window.pocualrecb_nonce
				) }`
			);
			const data = await res.json();
			setSuggestions( data || [] );
			setAttributes( {
				isLoading: false,
				showNotFoundMsg: Array.isArray( data ) && data.length === 0,
			} );
		} catch ( error ) {
			setSuggestions( [] );
			setAttributes( { isLoading: false, showNotFoundMsg: true } );
		}
	};

	// Called when suggestions need to be cleared
	const onSuggestionsClearRequested = () => {
		setSuggestions( [] );
	};
	// Function to get the value of the suggestion
	const getSuggestionValue = ( suggestion ) => suggestion.title;

	// Function to render each suggestion
	const renderSuggestion = ( suggestion ) => (
		<span dangerouslySetInnerHTML={ { __html: suggestion.title } }></span>
	);
	// Function to handle when a suggestion is selected
	function onSuggestionSelected( event, { suggestion } ) {
		setAttributes( { showInput: false, editView: true } );
		setSelectedPost( suggestion );
	}

	const renderTemplateImage = () => {
		if ( postProps.selectedPost?.thumbnail ) {
			return (
				<a
					className="postcue-also-read-content-block-post-image"
					target="_blank"
					rel="noopener noreferrer"
					href={ postProps.selectedPost.link }
				>
					<img
						decoding="async"
						width="150"
						height="150"
						src={ postProps.selectedPost.thumbnail }
						alt={ postProps.selectedPost.title || '' }
					/>
				</a>
			);
		}

		return (
			<span
				className="postcue-also-read-content-block-post-image pocualrecb-image-placeholder"
				aria-hidden="true"
			></span>
		);
	};

	const renderTemplatePost = () => {
		if ( ! postProps.selectedPost?.id ) {
			return null;
		}

		if ( selectedTemplate === 'default' ) {
			return (
				<>
					<strong
						className="postcue-also-read-content-block-title"
						style={ {
							color: postProps.blockTitleTextColor,
							fontSize: postProps.blockTitleFontSize,
						} }
					>
						{ postProps.blockTitle }
					</strong>
					<ul className="postcue-also-read-content-block-post-listing">
						<li
							className="postcue-also-read-content-block-listing-item"
							style={ { backgroundColor: postProps.postBgColor } }
						>
							{ postProps.selectedPost?.thumbnail && (
								<div className="postcue-also-read-content-block-post-image">
									<img
										decoding="async"
										width="150"
										height="150"
										src={ postProps.selectedPost.thumbnail }
										alt=""
									/>
								</div>
							) }
							<div
								className="postcue-also-read-content-block-post-title"
								style={ {
									color: postProps.postTitleTextColor,
									fontSize: postProps.postTitleFontSize,
								} }
							>
								<span
									dangerouslySetInnerHTML={ {
										__html: postProps.selectedPost.title,
									} }
								></span>
								<a
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
								>
									<ExternalArrowIcon />
								</a>
							</div>
						</li>
					</ul>
				</>
			);
		}

		if ( selectedTemplate === 'soft-card' ) {
			return (
				<>
					<strong
						className="postcue-also-read-content-block-title"
						style={ {
							color: postProps.blockTitleTextColor,
							fontSize: postProps.blockTitleFontSize,
						} }
					>
						{ postProps.blockTitle }
					</strong>
					<ul className="postcue-also-read-content-block-post-listing">
						<li
							className="postcue-also-read-content-block-listing-item pocualrecb-template-layout-soft-card"
							style={ { backgroundColor: postProps.postBgColor } }
						>
							{ renderTemplateImage() }
							<div className="pocualrecb-template-content">
								<a
									className="postcue-also-read-content-block-post-title"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
										fontSize: postProps.postTitleFontSize,
									} }
								>
									{ postProps.selectedPost.title }
								</a>
								<a
									className="pocualrecb-template-action"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
									} }
								>
									{ __(
										'Read post',
										'postcue-also-read-content-block'
									) }
								</a>
							</div>
						</li>
					</ul>
				</>
			);
		}

		if ( selectedTemplate === 'accent-strip' ) {
			return (
				<>
					<strong
						className="postcue-also-read-content-block-title"
						style={ {
							color: postProps.blockTitleTextColor,
							fontSize: postProps.blockTitleFontSize,
						} }
					>
						{ postProps.blockTitle }
					</strong>
					<ul className="postcue-also-read-content-block-post-listing">
						<li
							className="postcue-also-read-content-block-listing-item pocualrecb-template-layout-accent-strip"
							style={ { backgroundColor: postProps.postBgColor } }
						>
							{ renderTemplateImage() }
							<a
								className="postcue-also-read-content-block-post-title"
								target="_blank"
								rel="noopener noreferrer"
								href={ postProps.selectedPost.link }
								style={ {
									color: postProps.postTitleTextColor,
									fontSize: postProps.postTitleFontSize,
								} }
							>
								{ postProps.selectedPost.title }
							</a>
							<a
								className="pocualrecb-template-arrow-link"
								target="_blank"
								rel="noopener noreferrer"
								href={ postProps.selectedPost.link }
								style={ {
									color: postProps.postTitleTextColor,
								} }
							>
								<ExternalArrowIcon />
							</a>
						</li>
					</ul>
				</>
			);
		}

		if ( selectedTemplate === 'minimal-outline' ) {
			return (
				<>
					<strong
						className="postcue-also-read-content-block-title"
						style={ {
							color: postProps.blockTitleTextColor,
							fontSize: postProps.blockTitleFontSize,
						} }
					>
						{ postProps.blockTitle }
					</strong>
					<ul className="postcue-also-read-content-block-post-listing">
						<li
							className="postcue-also-read-content-block-listing-item pocualrecb-template-layout-minimal-outline"
							style={ { backgroundColor: postProps.postBgColor } }
						>
							<div className="pocualrecb-template-content">
								<a
									className="postcue-also-read-content-block-post-title"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
										fontSize: postProps.postTitleFontSize,
									} }
								>
									{ postProps.selectedPost.title }
								</a>
								<a
									className="pocualrecb-template-action"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
									} }
								>
									{ __(
										'Open article',
										'postcue-also-read-content-block'
									) }
								</a>
							</div>
							{ renderTemplateImage() }
						</li>
					</ul>
				</>
			);
		}

		if ( selectedTemplate === 'sleek-card' ) {
			return (
				<>
					<strong
						className="postcue-also-read-content-block-title"
						style={ {
							color: postProps.blockTitleTextColor,
							fontSize: postProps.blockTitleFontSize,
						} }
					>
						{ postProps.blockTitle }
					</strong>
					<ul className="postcue-also-read-content-block-post-listing">
						<li
							className="postcue-also-read-content-block-listing-item pocualrecb-template-layout-sleek-card"
							style={ { backgroundColor: postProps.postBgColor } }
						>
							{ renderTemplateImage() }
							<div className="pocualrecb-template-content">
								<span className="pocualrecb-template-pill">
									{ postProps.blockTitle }
								</span>
								<a
									className="postcue-also-read-content-block-post-title"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
										fontSize: postProps.postTitleFontSize,
									} }
								>
									{ postProps.selectedPost.title }
								</a>
								<a
									className="pocualrecb-template-action"
									target="_blank"
									rel="noopener noreferrer"
									href={ postProps.selectedPost.link }
									style={ {
										color: postProps.postTitleTextColor,
									} }
								>
									<ExternalArrowIcon />
									<span>
										{ __(
											'Continue reading',
											'postcue-also-read-content-block'
										) }
									</span>
								</a>
							</div>
						</li>
					</ul>
				</>
			);
		}

		return (
			<>
				<strong
					className="postcue-also-read-content-block-title"
					style={ {
						color: postProps.blockTitleTextColor,
						fontSize: postProps.blockTitleFontSize,
					} }
				>
					{ postProps.blockTitle }
				</strong>
				<ul className="postcue-also-read-content-block-post-listing">
					<li
						className="postcue-also-read-content-block-listing-item pocualrecb-template-layout-compact"
						style={ { backgroundColor: postProps.postBgColor } }
					>
						{ renderTemplateImage() }
						<a
							className="postcue-also-read-content-block-post-title"
							target="_blank"
							rel="noopener noreferrer"
							href={ postProps.selectedPost.link }
							style={ {
								color: postProps.postTitleTextColor,
								fontSize: postProps.postTitleFontSize,
							} }
						>
							{ postProps.selectedPost.title }
						</a>
						<a
							className="pocualrecb-template-arrow-link"
							target="_blank"
							rel="noopener noreferrer"
							href={ postProps.selectedPost.link }
							style={ { color: postProps.postTitleTextColor } }
						>
							<ExternalArrowIcon />
						</a>
					</li>
				</ul>
			</>
		);
	};

	return (
		<div { ...blockProps } id="postcue-also-read-content-block">
			{ showInput && (
				<>
					<label
						className="postcue-also-read-content-block-label"
						htmlFor="postcue-also-read-content-block-input"
					>
						{ __(
							'Search for a post:',
							'postcue-also-read-content-block'
						) }
					</label>
					<Autosuggest
						suggestions={ suggestions }
						onSuggestionsFetchRequested={
							onSuggestionsFetchRequested
						}
						onSuggestionsClearRequested={
							onSuggestionsClearRequested
						}
						getSuggestionValue={ getSuggestionValue }
						renderSuggestion={ renderSuggestion }
						onSuggestionSelected={ onSuggestionSelected }
						inputProps={ {
							value,
							onChange,
							id: 'postcue-also-read-content-block-input',
							name: 'postcue-also-read-content-block-input',
							placeholder: __(
								'Type to search posts…',
								'postcue-also-read-content-block'
							),
						} }
					/>
				</>
			) }

			{ /* Display loading message when suggestions are being fetched */ }
			{ isLoading && suggestions.length === 0 && showInput && value && (
				<p className="postcue-also-read-content-block-loading">
					{ __(
						'Loading suggestions…',
						'postcue-also-read-content-block'
					) }
				</p>
			) }

			{ /* Display no suggestions message when there are no suggestions */ }
			{ showNotFoundMsg &&
				! isLoading &&
				suggestions.length === 0 &&
				value &&
				showInput && (
					<p className="postcue-also-read-content-block-no-suggestions">
						{ __(
							'No posts found for your search.',
							'postcue-also-read-content-block'
						) }
					</p>
				) }

			{ ! showInput && renderTemplatePost() }

			{ attributes.selectedPost?.id !== undefined && (
				<>
					<BlockControls>
						<ToolbarGroup>
							{ editView ? (
								<ToolbarButton
									onClick={ () =>
										setAttributes( {
											showInput: true,
											editView: false,
										} )
									}
								>
									{ __(
										'Edit',
										'postcue-also-read-content-block'
									) }
								</ToolbarButton>
							) : (
								<ToolbarButton
									onClick={ () =>
										setAttributes( {
											showInput: false,
											editView: true,
										} )
									}
								>
									{ __(
										'Cancel',
										'postcue-also-read-content-block'
									) }
								</ToolbarButton>
							) }
						</ToolbarGroup>
					</BlockControls>
					<InspectorControls>
						<PanelBody
							title={ __(
								'Custom Style Settings',
								'postcue-also-read-content-block'
							) }
							initialOpen={ true }
						>
							<ToggleControl
								label={ __(
									'Allow Custom style',
									'postcue-also-read-content-block'
								) }
								checked={ attributes.allowCustomStyle }
								onChange={ ( val ) =>
									setAttributes( { allowCustomStyle: val } )
								}
								help={ __(
									'If enabled, this block will use custom styles instead of global defaults.',
									'postcue-also-read-content-block'
								) }
							/>
						</PanelBody>
						{ attributes.allowCustomStyle && (
							<>
								<PanelBody
									title={ __(
										'Title Settings',
										'postcue-also-read-content-block'
									) }
									initialOpen={ false }
								>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Block Title',
												'postcue-also-read-content-block'
											) }
										</span>
										<TextControl
											onChange={ ( placeholder ) =>
												setAttributes( {
													blockTitle: placeholder,
												} )
											}
											value={ attributes.blockTitle }
										/>
									</div>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Block Title Text Color',
												'postcue-also-read-content-block'
											) }
										</span>
										<ColorPalette
											value={
												attributes.blockTitleTextColor
											}
											onChange={ ( newColor ) =>
												setAttributes( {
													blockTitleTextColor:
														newColor,
												} )
											}
										/>
									</div>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Block Title Font Size',
												'postcue-also-read-content-block'
											) }
										</span>
										<FontSizePicker
											value={
												attributes.blockTitleFontSize
											}
											onChange={ ( newSize ) =>
												setAttributes( {
													blockTitleFontSize: newSize,
												} )
											}
											fontSizes={ [
												{
													name: __(
														'Small',
														'postcue-also-read-content-block'
													),
													slug: 'small',
													size: 12,
												},
												{
													name: __(
														'Regular',
														'postcue-also-read-content-block'
													),
													slug: 'regular',
													size: 16,
												},
												{
													name: __(
														'Large',
														'postcue-also-read-content-block'
													),
													slug: 'large',
													size: 24,
												},
											] }
										/>
									</div>
								</PanelBody>
								<PanelBody
									title={ __(
										'Post Settings',
										'postcue-also-read-content-block'
									) }
									initialOpen={ false }
								>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Post Title Color',
												'postcue-also-read-content-block'
											) }
										</span>
										<ColorPalette
											value={
												attributes.postTitleTextColor
											}
											onChange={ ( newColor ) =>
												setAttributes( {
													postTitleTextColor:
														newColor,
												} )
											}
										/>
									</div>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Post Background Color',
												'postcue-also-read-content-block'
											) }
										</span>
										<ColorPalette
											value={ attributes.postBgColor }
											onChange={ ( newColor ) =>
												setAttributes( {
													postBgColor: newColor,
												} )
											}
										/>
									</div>
									<div style={ { marginBottom: '16px' } }>
										<span
											style={ {
												display: 'block',
												marginBottom: '4px',
												fontWeight: '500',
											} }
										>
											{ __(
												'Post Title Font Size',
												'postcue-also-read-content-block'
											) }
										</span>
										<FontSizePicker
											value={
												attributes.postTitleFontSize
											}
											onChange={ ( newSize ) =>
												setAttributes( {
													postTitleFontSize: newSize,
												} )
											}
											fontSizes={ [
												{
													name: __(
														'Small',
														'postcue-also-read-content-block'
													),
													slug: 'small',
													size: 12,
												},
												{
													name: __(
														'Regular',
														'postcue-also-read-content-block'
													),
													slug: 'regular',
													size: 16,
												},
												{
													name: __(
														'Large',
														'postcue-also-read-content-block'
													),
													slug: 'large',
													size: 24,
												},
											] }
										/>
									</div>
								</PanelBody>
							</>
						) }
					</InspectorControls>
				</>
			) }
		</div>
	);
}
