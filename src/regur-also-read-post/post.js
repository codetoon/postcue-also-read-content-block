export default function Post({ selectedPost }) {
    return (
        <>
            <h2 class="display-posts-title">Also Read</h2>
            <ul class="display-posts-listing">
                <li class="listing-item">
                    <a class="image" target="_blank" href={selectedPost.link}><img decoding="async" width="150" height="150" src={selectedPost.thumbnail} class="attachment-thumbnail size-thumbnail wp-post-image" alt="" /></a> <a class="title" target="_blank" href={selectedPost.link}><span dangerouslySetInnerHTML={{__html : selectedPost.title }}></span></a>
                </li>
            </ul>
        </>
    )
}