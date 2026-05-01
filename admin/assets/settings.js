document.addEventListener( 'DOMContentLoaded', function () {
	const templateSelect = document.querySelector(
		'select[name="pocualrecb_defaults[template]"]'
	);
	const templateCards = Array.from(
		document.querySelectorAll( '.pocualrecb-template-preview-card' )
	);

	if ( ! templateSelect || ! templateCards.length ) {
		return;
	}

	const templateOrder = templateCards.map(
		( templateCard ) => templateCard.dataset.template
	);

	const setActivePreview = ( activeTemplate ) => {
		templateCards.forEach( ( templateCard ) => {
			const isActive = templateCard.dataset.template === activeTemplate;
			templateCard.classList.toggle( 'is-active', isActive );
			templateCard.setAttribute(
				'aria-pressed',
				isActive ? 'true' : 'false'
			);
		} );
	};

	const moveToNextTemplate = () => {
		const currentTemplate = templateSelect.value;
		const currentIndex = templateOrder.indexOf( currentTemplate );
		const nextIndex =
			currentIndex >= 0 ? ( currentIndex + 1 ) % templateOrder.length : 0;
		const nextTemplate = templateOrder[ nextIndex ];

		templateSelect.value = nextTemplate;
		setActivePreview( nextTemplate );
	};

	templateSelect.addEventListener( 'change', function () {
		setActivePreview( templateSelect.value );
	} );

	templateCards.forEach( ( templateCard ) => {
		templateCard.addEventListener( 'click', moveToNextTemplate );
		templateCard.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				moveToNextTemplate();
			}
		} );
	} );

	setActivePreview( templateSelect.value );
} );
