export default function Post({attributes}) {
    
    return (
        <>
            <h2
                class="display-posts-title"
                style={{
                    color: attributes.blockTitleTextColor,
                    fontSize: attributes.blockTitleFontSize
                }}
            >   
                {attributes.blockTitle}
            </h2>
            <ul class="display-posts-listing">
                <li class="listing-item" style={{backgroundColor: attributes.postBgColor}}>
                    <div class="image">
                        <img decoding="async" width="150" height="150" src={attributes.selectedPost.thumbnail} class="attachment-thumbnail size-thumbnail wp-post-image" alt="" />
                    </div>
                    <div
                        class="title"
                        style={{
                            color: attributes.postTitleTextColor,
                            fontSize: attributes.postTitleFontSize
                        }}
                    >
                        <span dangerouslySetInnerHTML={{__html : attributes.selectedPost.title }}></span>
                        <a target="_blank" href={attributes.selectedPost.link}>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="28" height="28" aria-hidden="true" focusable="false"><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg>
                        </a>
                    </div>
                </li>
            </ul>
        </>
    )
}