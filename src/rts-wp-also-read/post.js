export default function Post({attributes}) {
    
    return (
        <>
            <h2
                class="display-posts-title"
                style={{
                    color: attributes.blockTitleTextColor,
                    blockTitleFontSize: attributes.blockTitleFontSize
                }}
            >   
                {attributes.blockTitle}
            </h2>
            <ul class="display-posts-listing">
                <li class="listing-item" style={{backgroundColor: attributes.postBgColor}}>
                    <a class="image" target="_blank" href={attributes.selectedPost.link}>
                        <img decoding="async" width="150" height="150" src={attributes.selectedPost.thumbnail} class="attachment-thumbnail size-thumbnail wp-post-image" alt="" />
                    </a>
                    <a
                        class="title"
                        target="_blank"
                        href={attributes.selectedPost.link}
                        style={{
                            color: attributes.postTitleTextColor,
                            fontSize: attributes.postTitleFontSize
                        }}
                    >
                        <span dangerouslySetInnerHTML={{__html : attributes.selectedPost.title }}></span>
                    </a>
                </li>
            </ul>
        </>
    )
}