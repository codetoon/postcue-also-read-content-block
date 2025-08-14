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
import Post from './post.js';
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

	// Get global defaults from window.rtswparbDefaults (set in PHP)
    const globalDefaults = (typeof window !== "undefined" && window.rtswparbDefaults) ? window.rtswparbDefaults : {};
	
	// Only initialize local styles once, when global override is disabled
	useEffect(() => {
		if (!attributes.allowCustomStyle) {
			setAttributes({
				blockTitle: globalDefaults.blockTitle,
				textColor: globalDefaults.textColor,
				fontSize: globalDefaults.fontSize,
				postTitleTextColor: globalDefaults.postTitleTextColor,
				postTitleFontSize: globalDefaults.postTitleFontSize,
				postBgColor: globalDefaults.postBgColor,
			});
		}
	}, [globalDefaults]);
	
	// Compute final style values based on allowCustomStyle
	const blockTitle = !attributes.allowCustomStyle ? globalDefaults.blockTitle : attributes.blockTitle;
	const textColor = !attributes.allowCustomStyle ? globalDefaults.textColor : attributes.textColor;
	const fontSize = !attributes.allowCustomStyle ? globalDefaults.fontSize : attributes.fontSize;
	const postTitleTextColor = !attributes.allowCustomStyle ? globalDefaults.postTitleTextColor : attributes.postTitleTextColor;
	const postTitleFontSize = !attributes.allowCustomStyle ? globalDefaults.postTitleFontSize : attributes.postTitleFontSize;
	const postBgColor = !attributes.allowCustomStyle ? globalDefaults.postBgColor : attributes.postBgColor;

	const postProps = {
		blockTitle,
		textColor,
		fontSize,
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
			const res = await fetch(`${window.ajaxurl}?action=post_search&term=${encodeURIComponent(value)}`);
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
		<div {...blockProps}>
				{/* Show the input field only when showInput is true */}
				{showInput && (
					<>
						<label className="rts-wp-also-read-block-label">
							{__('Search for a post:', 'rts-wp-also-read-block')}
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
								id: 'rts-wp-also-read-block-input',
								name: 'rts-wp-also-read-block-input',
								placeholder: __('Type to search posts...', 'rts-wp-also-read-block'),
							}}
						/>
					</>
				)}

				{/* Display loading message when suggestions are being fetched */}
				{isLoading && suggestions.length == 0 && showInput && value && <p className='rts-wp-also-read-block-loading'>{__('Loading suggestions...', 'rts-wp-also-read-block-loading')}</p>}

				{/* Display no suggestions message when there are no suggestions */}
				{showNotFoundMsg && !isLoading && suggestions.length == 0 && value && showInput && <p className="rts-wp-also-read-block-no-suggestions"> {__('No posts found for your search.', 'rts-wp-also-read-block')}</p>}

				{/* Render the selected post if available & Show the selected post if it exists */}
				{attributes.selectedPost?.id && !showInput && (
					<Post attributes={postProps}/>
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
										{__('Edit', 'rts-wp-also-read-block')}
									</ToolbarButton>
								)
									: (
										<ToolbarButton
											onClick={() => {
												setAttributes({ showInput: false });
												setAttributes({ editView: true });
											}}
										>
											{__('Cancel', 'rts-wp-also-read-block')}
										</ToolbarButton>
									)
							}
						</ToolbarGroup>
					</BlockControls>
					<InspectorControls>
						<PanelBody title="Custom Style Settings" initialOpen={true}>
							<ToggleControl
								label="Allow Custom style"
								checked={attributes.allowCustomStyle}
								onChange={(val) => setAttributes({ allowCustomStyle: val })}
								help="If enable, this block will use custom styles instead of global defaults."
							/>
						</PanelBody>
						{
							attributes.allowCustomStyle && (
								<>
									<PanelBody title="Title Settings" initialOpen={false}>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Block Title
											</span>
											<TextControl

												onChange={(placeholder) => setAttributes({ blockTitle: placeholder })}
												value={attributes.blockTitle}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Block Title Text Color
											</span>
											<ColorPalette
												value={attributes.textColor}
												onChange={(newColor) => setAttributes({ textColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Block Title Font Size
											</span>
											<FontSizePicker
												value={attributes.fontSize}
												onChange={(newSize) => setAttributes({ fontSize: newSize })}
												fontSizes={[
													{ name: 'Small', slug: 'small', size: 12 },
													{ name: 'Regular', slug: 'regular', size: 16 },
													{ name: 'Large', slug: 'large', size: 24 },
												]}
											/>
										</div>
									</PanelBody>
									<PanelBody title="Post Settings" initialOpen={false}>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Post Title Color
											</span>
											<ColorPalette
												value={attributes.postTitleTextColor}
												onChange={(newColor) => setAttributes({ postTitleTextColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Post Background Color
											</span>
											<ColorPalette
												value={attributes.postBgColor}
												onChange={(newColor) => setAttributes({ postBgColor: newColor })}
											/>
										</div>
										<div style={{ marginBottom: '16px' }}>
											<span style={{ display: 'block', marginBottom: '4px', fontWeight: '500' }}>
												Post Title Font Size
											</span>
											<FontSizePicker
												value={attributes.postTitleFontSize}
												onChange={(newSize) => setAttributes({ postTitleFontSize: newSize })}
												fontSizes={[
													{ name: 'Small', slug: 'small', size: 12 },
													{ name: 'Regular', slug: 'regular', size: 16 },
													{ name: 'Large', slug: 'large', size: 24 },
								] }
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

