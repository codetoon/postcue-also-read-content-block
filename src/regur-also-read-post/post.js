import "./post.scss";

export default function Post({ selectedPost }) {
    return (
        <>
            <h2 class="post-title">Also Read</h2>
            <ul class="post-listing">
                <li class="listing-item">
                    <a class="image" target="_blank" href={selectedPost.link}><img decoding="async" width="150" height="150" src={selectedPost.thumbnail} class="attachment-thumbnail size-thumbnail wp-post-image" alt="" /></a> <a class="title" target="_blank" href={selectedPost.link}>{selectedPost.title}</a>
                </li>
            </ul>
        </>
    )
}