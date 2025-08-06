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

	const [suggestions, setSuggestions] = useState([]); // State to hold the suggestions
	const [value, setValue] = useState(''); // State to hold the input value
	const [showInput, setShowInput] = useState(true); // State to control the visibility of the input field
	const [isLoading, setIsLoading] = useState(false); // State to control the loading state
	const [showMessage, setShowMessage] = useState(true); // State to control the visibility of the message

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
		setValue(newValue);
	}

	// Called when suggestions need to be fetched
	const onSuggestionsFetchRequested = async ({ value }) => {
		if (!value) {
			setSuggestions([]);
			return;
		}
		try {
			setIsLoading(true);
			const res = await fetch(`${window.ajaxurl}?action=post_search&term=${encodeURIComponent(value)}`);
			const data = await res.json();
			setSuggestions(data || []);

		} catch (error) {
			console.error('Suggestion fetch error:', error);
			setSuggestions([]);
		}finally{
			setIsLoading(false);
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
		setShowInput(false);
		setSelectedPost(suggestion)

	}

	return (
		<div>
			<div {...blockProps}>
				{/* Show the input field only when showInput is true */}
				{showInput && attributes.selectedPost?.id == undefined && (
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
				{isLoading && suggestions.length == 0 && <p className='regur-also-read-post-loading'>{__('Loading suggestions...', 'regur-also-read-post-loading')}</p>}

				{/* Display no suggestions message when there are no suggestions */}
				{!isLoading && suggestions.length === 0 && value && showInput && showMessage && <p className="regur-also-read-post-no-suggestions"> {__('No posts found for your search.', 'regur-also-read-post')}</p>}

				{/* Render the selected post if available & Show the selected post if it exists */}	
				{attributes.selectedPost?.id && (
					<Post selectedPost={attributes.selectedPost} />
				)}
			</div>
			{/* Show the InspectorControls only when not showing the input */}
			{
				attributes.selectedPost?.id != undefined && (
					<BlockControls>
						<ToolbarGroup>
							<ToolbarButton
								onClick={() => {
									setShowInput(true);
									setSelectedPost('')
									setShowMessage(false);
								}}
							>
								{__('Edit', 'regur-also-read-post')}
							</ToolbarButton>
						</ToolbarGroup>
					</BlockControls>

				)
			}
		</div>
	);
}

