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
	// Using useState to manage filtered posts
	const [filteredPosts, setFilteredPosts] = useState([]);

	// Function to handle the search input change
	const handleInputChange = async (value) => {
		// Update input state (optional)
		setFilteredPosts([]); // clear while loading
		
		// Make AJAX call
		try{
			const res  = await fetch(`${window.ajaxurl}?action=post_search&term=${encodeURIComponent(value)}`);
			const data = await res.json();
			setFilteredPosts(value === '' ? [] : data); // Reset if input is empty
		}
		catch (error) {
			console.error('Search error:', error);
		}	
	};

	return (
		<div {...useBlockProps()}>
			<div className="regur-also-read-post-search">
				<input
					type="text"
					onChange={(event) => handleInputChange(event.target.value)}
					id="regur-also-read-post-search"
					name="regur_also_read_post_search"
					placeholder={__('Search posts', 'regur-also-read-post')}
					className="regur-also-read-post-search-input"
				/>
			</div>

			{/* Show dropdown only if there are filtered posts */}
			{filteredPosts.length > 0 && (
				<ul className="regur-also-read-post-list">
					{filteredPosts.map((post) => (
						<li key={post.id} className="regur-also-read-post-item">
							<span className="regur-also-read-post-title">
								{post.title}
							</span>
						</li>
					))}
				</ul>
			)}
		</div>
	);
}

