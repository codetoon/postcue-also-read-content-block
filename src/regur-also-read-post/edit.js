/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

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
import Post from './post';
export default function Edit() {
	return (
		// returns input for search posts
		<div {...useBlockProps()}>
			<p>
				{__('Search Also Read Post', 'regur-also-read-post')}
			</p>
			<div className='regur-also-read-post-search'>
				<input
				type="text"
				onInput={(event) => {
					handleInputChange(event.target.value);
				}}
				id="regur-also-read-post-search"
				name="regur_also_read_post_search"
				placeholder={__('Search posts', 'regur-also-read-post')}
				className="regur-also-read-post-search-input"
			/>
			<button type='search' className="regur-also-read-post-search-button" onClick={(event) => {console.log('Button clicked!'); event.preventDefault();}}>
				{__('Search', 'regur-also-read-post')}
			</button>
			</div>
		</div>
	);
}

// const handleInputChange = (value) => {
// 	Post(value); // Call the Post function with the input value
// }

