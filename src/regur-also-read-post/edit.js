/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import { useState } from 'react'; // Import useState for managing state
import Autosuggest from 'react-autosuggest';

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


export default function Edit() {

	const blockProps = useBlockProps();

	const [suggestions, setSuggestions] = useState([]);
	const [value, setValue] = useState('');

	// Called when input changes
	const onChange = (event, {newValue}) =>{
		setValue(newValue);
	}

	// Autosuggest will call this function every time you need to update suggestions.
    // You already implemented this logic above, so just use it.
	const onSuggestionsFetchRequested = async ({ value }) => {
		if (!value) {
			setSuggestions([]);
			return;
		}
		try {
			const res = await fetch(`${window.ajaxurl}?action=post_search&term=${encodeURIComponent(value)}`);
			const data = await res.json();
			setSuggestions(data || []);

		} catch (error) {
			console.error('Suggestion fetch error:', error);
			setSuggestions([]);
		}
	}

	// Autosuggest will call this function every time you need to clear suggestions.
	const onSuggestionsClearRequested = () => {
		setSuggestions([]);
	}
	// Implement it to teach Autosuggest what should be the input value when suggestion is clicked.
	const getSuggestionValue = suggestion => suggestion.title;

	// Suggestion is rendered in the dropdown
	const renderSuggestion = suggestion => (
		<span>{suggestion.title}</span>
	)

	return (
		<div>
			<div {...blockProps}>
				<Autosuggest
					suggestions={suggestions}
					onSuggestionsFetchRequested={onSuggestionsFetchRequested}
					onSuggestionsClearRequested={onSuggestionsClearRequested}
					getSuggestionValue={getSuggestionValue}
					renderSuggestion={renderSuggestion}
					inputProps={{
						value,
						onChange,
						id: 'regur-also-read-post-input',
						name: 'regur-also-read-post-input',
						placeholder: __('Type to search posts...', 'regur-also-read-post'),
					}}
				/>
				
			</div>
		</div>
	);
}

