/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useState } from 'react'; // Import useState for managing state
import Autosuggest from 'react-autosuggest';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';

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
	const value = attributes.value;
	const [suggestions, setSuggestions] = useState([]); // State to hold the suggestions
	const showInput = attributes.showInput;
	const isLoading = attributes.isLoading;
	const showMessage = attributes.showMessage;
	const editView = attributes.editView;

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
			return;
		}
		try {
			setAttributes({ isLoading: true });
			const res = await fetch(`${window.ajaxurl}?action=post_search&term=${encodeURIComponent(value)}`);
			const data = await res.json();
			setSuggestions(data || []);

		} catch (error) {
			console.error('Suggestion fetch error:', error);
			setSuggestions([]);
		}finally{
			setAttributes({ isLoading: false });
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
		setAttributes({showMessage: false});
		setSelectedPost(suggestion)
	}

	return (
		<div>
			<div {...blockProps}>
				{/* Show the input field only when showInput is true */}
				{showInput && (
					<>
						<label className="regur-also-read-post-label">
							{__('Search for a post:', 'regur-also-read-post')}
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
								id: 'regur-also-read-post-input',
								name: 'regur-also-read-post-input',
								placeholder: __('Type to search posts...', 'regur-also-read-post'),
							}}
						/>
					</>
				)}

				{/* Display loading message when suggestions are being fetched */}
				{isLoading && suggestions.length == 0 && showInput && <p className='regur-also-read-post-loading'>{__('Loading suggestions...', 'regur-also-read-post-loading')}</p>}

				{/* Display no suggestions message when there are no suggestions */}
				{!isLoading && suggestions.length == 0 && value && showInput && showMessage && <p className="regur-also-read-post-no-suggestions"> {__('No posts found for your search.', 'regur-also-read-post')}</p>}

				{/* Render the selected post if available & Show the selected post if it exists */}
				{attributes.selectedPost?.id && !showInput && (
					<Post selectedPost={attributes.selectedPost} />
				)}
			</div>
			{attributes.selectedPost?.id != undefined && (
				<BlockControls>
					<ToolbarGroup>
						{
							editView ? (
								<ToolbarButton
									onClick={() => {
										setAttributes({ showInput: true });
										setAttributes({ editView: false });
										setAttributes({showMessage: true});
									}}
								>
									{__('Edit', 'regur-also-read-post')}
								</ToolbarButton>
							)
								: (
									<ToolbarButton
										onClick={() => {
											setAttributes({ showInput: false });
											setAttributes({ editView: true });
											setAttributes({showMessage: false});
										}}
									>
										{__('Cancel', 'regur-also-read-post')}
									</ToolbarButton>
								)
						}
					</ToolbarGroup>
				</BlockControls>
			)}
		</div>
	);
}

