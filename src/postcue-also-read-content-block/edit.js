/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect} from 'react'; // Import useState for managing state
import Autosuggest from 'react-autosuggest';
import { BlockControls, InspectorControls, ColorPalette } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton, TextControl, PanelBody, FontSizePicker, ToggleControl } from '@wordpress/components';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */


export default function Edit({attributes, setAttributes}) {

	const blockProps = useBlockProps();
	const {value, showInput, isLoading, showNotFoundMsg, editView } = attributes;
	const [suggestions, setSuggestions] = useState([]); // State to hold the suggestions

	// Get global defaults from window.postcuealsoreadDefaults (set in PHP)
    const globalDefaults = (typeof window !== "undefined" && window.pocualrecb_defaults) ? window.pocualrecb_defaults : {};
	
	// Only initialize local styles once, when custom style is disabled
	useEffect(() => {
		if (!attributes.allowCustomStyle) {
			setAttributes({
				blockTitle: globalDefaults.blockTitle,
				blockTitleTextColor: globalDefaults.blockTitleTextColor,
				blockTitleFontSize: globalDefaults.blockTitleFontSize,
				postTitleTextColor: globalDefaults.postTitleTextColor,
				postTitleFontSize: globalDefaults.postTitleFontSize,
				postBgColor: globalDefaults.postBgColor,
			});
		}
	}, [globalDefaults]);
	
	// Compute final style values based on allowCustomStyle
	const blockTitle = !attributes.allowCustomStyle ? globalDefaults.blockTitle : attributes.blockTitle;
	const blockTitleTextColor = !attributes.allowCustomStyle ? globalDefaults.blockTitleTextColor : attributes.blockTitleTextColor;
	const blockTitleFontSize = !attributes.allowCustomStyle ? globalDefaults.blockTitleFontSize : attributes.blockTitleFontSize;
	const postTitleTextColor = !attributes.allowCustomStyle ? globalDefaults.postTitleTextColor : attributes.postTitleTextColor;
	const postTitleFontSize = !attributes.allowCustomStyle ? globalDefaults.postTitleFontSize : attributes.postTitleFontSize;
	const postBgColor = !attributes.allowCustomStyle ? globalDefaults.postBgColor : attributes.postBgColor;

	const postProps = {
		blockTitle,
		blockTitleTextColor,
		blockTitleFontSize,
		postTitleTextColor,
		postTitleFontSize,
		postBgColor,
		selectedPost: attributes.selectedPost,
	};
	const setSelectedPost = (post) => {
    setAttributes({ selectedPost: {
				id: post.id,
				title: post.title,
				link: post.link,
				thumbnail: post.thumbnail
    }});
	};
	// Called when input changes
	const onChange = (event, {newValue}) =>{
		setAttributes({ value: newValue });
	}

	// Called when suggestions need to be fetched
	const onSuggestionsFetchRequested = async ({ value }) => {
		if (!value) {
			setSuggestions([]);
			setAttributes({ isLoading: false, showNotFoundMsg: false });
			return;
		}
		setAttributes({ isLoading: true, showNotFoundMsg: false });
		try {
            const res = await fetch(`${window.pocualrecb_ajaxurl}?action=pocualrecb_post_search&term=${encodeURIComponent(value)}&_pocualrecb_nonce=${encodeURIComponent(window.pocualrecb_nonce)}`);
			const data = await res.json();
			setSuggestions(data || []);
			setAttributes({ isLoading: false, showNotFoundMsg: (Array.isArray(data) && data.length === 0) });

		} catch (error) {
			console.error('Suggestion fetch error:', error);
			setSuggestions([]);
			setAttributes({ isLoading: false, showNotFoundMsg: true });
		}
	}

	// Called when suggestions need to be cleared
	const onSuggestionsClearRequested = () => {
		setSuggestions([]);
	}
	// Function to get the value of the suggestion
	const getSuggestionValue = suggestion => suggestion.title;

	// Function to render each suggestion
	const renderSuggestion = suggestion => (
		<span dangerouslySetInnerHTML={{__html : suggestion.title }}></span>
	)
	// Function to handle when a suggestion is selected
	function onSuggestionSelected(event, { suggestion, suggestionValue, suggestionIndex, sectionIndex, method }) {
		setAttributes({ showInput: false });
		setAttributes({ editView: true });
		setSelectedPost(suggestion)
	}

	return (
		<div {...blockProps} id="postcue-also-read-content-block">
				{/* Show the input field only when showInput is true */}
				{showInput && (
					<>
						<label className="postcue-also-read-content-block-label" htmlFor='postcue-also-read-content-block-input'>
							{__('Search for a post:', 'postcue-also-read-content-block')}
						</label>
						<Autosuggest
							suggestions={suggestions}
							onSuggestionsFetchRequested={onSuggestionsFetchRequested}
							onSuggestionsClearRequested={onSuggestionsClearRequested}
							getSuggestionValue={getSuggestionValue}
							renderSuggestion={renderSuggestion}
							onSuggestionSelected={onSuggestionSelected}
							inputProps={{
								value,
								onChange,
								id: 'postcue-also-read-content-block-input',
								name: 'postcue-also-read-content-block-input',
								placeholder: __('Type to search posts...', 'postcue-also-read-content-block'),
							}}
						/>
					</>
				)}

				{/* Display loading message when suggestions are being fetched */}
				{isLoading && suggestions.length == 0 && showInput && value && <p className='postcue-also-read-content-block-loading'>{__('Loading suggestions...', 'postcue-also-read-content-block')}</p>}

				{/* Display no suggestions message when there are no suggestions */}
				{showNotFoundMsg && !isLoading && suggestions.length == 0 && value && showInput && <p className="postcue-also-read-content-block-no-suggestions"> {__('No posts found for your search.', 'postcue-also-read-content-block')}</p>}

				{/* Render the selected post if available & Show the selected post if it exists */}
				{attributes.selectedPost?.id && !showInput && (
					<>
						<strong
							class="postcue-also-read-content-block-title"
							style={{
								color: postProps.blockTitleTextColor,
								fontSize: postProps.blockTitleFontSize,
							}}
						>
							{postProps.blockTitle}
						</strong>
						<ul class="postcue-also-read-content-block-post-listing">
							<li class="postcue-also-read-content-block-listing-item" style={{ backgroundColor: postProps.postBgColor }}>
								<div class="postcue-also-read-content-block-post-image">
									<img decoding="async" width="150" height="150" src={postProps.selectedPost.thumbnail} alt="" />
								</div>
								<div
									class="postcue-also-read-content-block-post-title"
									style={{
										color: postProps.postTitleTextColor,
										fontSize: postProps.postTitleFontSize,
									}}
								>
									<span dangerouslySetInnerHTML={{ __html: postProps.selectedPost.title }}></span>
									<a target="_blank" href={postProps.selectedPost.link}>
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="28" height="28" aria-hidden="true" focusable="false">
											<path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path>
										</svg>
									</a>
								</div>
							</li>
						</ul>
					</>

				)}
			{attributes.selectedPost?.id != undefined && (
				<>
					<BlockControls>
						<ToolbarGroup>
							{
								editView ? (
									<ToolbarButton
										onClick={() => {
											setAttributes({ showInput: true });
											setAttributes({ editView: false });
										}}
									>
										{__('Edit', 'postcue-also-read-content-block')}
									</ToolbarButton>
								)
									: (
										<ToolbarButton
											onClick={() => {
												setAttributes({ showInput: false });
												setAttributes({ editView: true });
											}}
										>
											{__('Cancel', 'postcue-also-read-content-block')}
										</ToolbarButton>
									)
							}
						</ToolbarGroup>
					</BlockControls>
					<InspectorControls>
						<PanelBody title={__('Custom Style Settings', 'postcue-also-read-content-block')} initialOpen={true}>
							<ToggleControl
								label={__('Allow Custom style', 'postcue-also-read-content-block')}
								checked={attributes.allowCustomStyle}
								onChange={(val) => setAttributes({ allowCustomStyle: val })}
								help={__('If enabled, this block will use custom styles instead of global defaults.', 'postcue-also-read-content-block')}
							/>
						</PanelBody>
						{
							attributes.allowCustomStyle && (
								<>
									<PanelBody title={__('Title Settings', 'postcue-also-read-content-block')} initialOpen={false}>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Block Title', 'postcue-also-read-content-block')}
											</span>
											<TextControl
												onChange={(placeholder) => setAttributes({ blockTitle: placeholder })}
												value={attributes.blockTitle}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Block Title Text Color', 'postcue-also-read-content-block')}
											</span>
											<ColorPalette
												value={attributes.blockTitleTextColor}
												onChange={(newColor) => setAttributes({ blockTitleTextColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Block Title Font Size', 'postcue-also-read-content-block')}
											</span>
											<FontSizePicker
												value={attributes.blockTitleFontSize}
												onChange={(newSize) => setAttributes({ blockTitleFontSize: newSize })}
												fontSizes={[
													{ name: __('Small', 'postcue-also-read-content-block'), slug: 'small', size: 12 },
													{ name: __('Regular', 'postcue-also-read-content-block'), slug: 'regular', size: 16 },
													{ name: __('Large', 'postcue-also-read-content-block'), slug: 'large', size: 24 },
												]}
											/>
										</div>
									</PanelBody>
									<PanelBody title={__('Post Settings', 'postcue-also-read-content-block')} initialOpen={false}>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Post Title Color', 'postcue-also-read-content-block')}
											</span>
											<ColorPalette
												value={attributes.postTitleTextColor}
												onChange={(newColor) => setAttributes({ postTitleTextColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Post Background Color', 'postcue-also-read-content-block')}
											</span>
											<ColorPalette
												value={attributes.postBgColor}
												onChange={(newColor) => setAttributes({ postBgColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												{__('Post Title Font Size', 'postcue-also-read-content-block')}
											</span>
											<FontSizePicker
												value={attributes.postTitleFontSize}
												onChange={(newSize) => setAttributes({ postTitleFontSize: newSize })}
												fontSizes={[
													{ name: __('Small', 'postcue-also-read-content-block'), slug: 'small', size: 12 },
													{ name: __('Regular', 'postcue-also-read-content-block'), slug: 'regular', size: 16 },
													{ name: __('Large', 'postcue-also-read-content-block'), slug: 'large', size: 24 },
												]}
											/>
										</div>
									</PanelBody>
								</>
							)
						}
					</InspectorControls>
				</>
			)}
		</div>
	);
}

