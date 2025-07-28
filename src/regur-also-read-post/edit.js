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

const postsData = [
	{ id: 1, title: 'Top WordPress Development Companies in India: How to Choose the Right Partner?', link: '#' },
	{ id: 2, title: 'How to Build a Scalable Software Business Starting with an MVP?', link: '#' },
]

var filteredPosts = []; // Initialize with an empty array

const returnFilteredPosts = () => {
	return filteredPosts;
}

const handleInputChange = (value) => {
	filteredPosts = postsData.filter(post => post.title.toLowerCase().includes(value.toLowerCase()));

	if (value === '') {
		filteredPosts = []; // Reset to all posts if input is empty
	}

	returnFilteredPosts()
}

export default function Edit() {

	return (
		// returns input for search posts

		<div {...useBlockProps()}>
			<div className='regur-also-read-post-search'>
				<input
					type="text"
					onChange={(event) => {
						handleInputChange(event.target.value);
					}}
					id="regur-also-read-post-search"
					name="regur_also_read_post_search"
					placeholder={__('Search posts', 'regur-also-read-post')}
					className="regur-also-read-post-search-input"
				/>
			</div>

			{
				filteredPosts.length > 0 && (
					<ul className="regur-also-read-post-list">
						{filteredPosts.map(post => (
							<li key={post.id} className="regur-also-read-post-item">
								<a href={post.link} className="regur-also-read-post-link">
									{post.title}
								</a>
							</li>
						))}
					</ul>
				)}

		</div>

	);
}

