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

	const templatePanels = Array.from(
		document.querySelectorAll( '.pocualrecb-template-settings-panel' )
	);

	const getTemplateCard = ( template ) =>
		templateCards.find(
			( templateCard ) => templateCard.dataset.template === template
		);

	const getTemplatePanel = ( template ) =>
		templatePanels.find(
			( templatePanel ) => templatePanel.dataset.template === template
		);

	const getTemplateFieldValue = ( templatePanel, field ) => {
		if ( ! templatePanel ) {
			return '';
		}

		const templateInput = templatePanel.querySelector(
			`.pocualrecb-template-setting-input[data-field="${ field }"]`
		);

		if ( ! templateInput ) {
			return '';
		}

		const inputValue =
			typeof templateInput.value === 'string'
				? templateInput.value.trim()
				: '';

		if ( inputValue !== '' ) {
			return templateInput.value;
		}

		return templateInput.dataset.defaultValue || '';
	};

	const applyPreviewStyles = ( template ) => {
		const templateCard = getTemplateCard( template );
		const templatePanel = getTemplatePanel( template );

		if ( ! templateCard || ! templatePanel ) {
			return;
		}

		const previewTitle = templateCard.querySelector(
			'.pocualrecb-template-preview-title'
		);
		const previewItem = templateCard.querySelector(
			'.pocualrecb-template-preview-item'
		);
		const previewText = templateCard.querySelector(
			'.pocualrecb-template-preview-text'
		);

		if ( previewTitle ) {
			const titleValue = getTemplateFieldValue(
				templatePanel,
				'blockTitle'
			);

			previewTitle.textContent = titleValue || 'Also Read';
			previewTitle.style.color = getTemplateFieldValue(
				templatePanel,
				'blockTitleTextColor'
			);
			previewTitle.style.fontSize = getTemplateFieldValue(
				templatePanel,
				'blockTitleFontSize'
			);
		}

		if ( previewItem ) {
			previewItem.style.backgroundColor = getTemplateFieldValue(
				templatePanel,
				'postBgColor'
			);
		}

		if ( previewText ) {
			previewText.style.color = getTemplateFieldValue(
				templatePanel,
				'postTitleTextColor'
			);
			previewText.style.fontSize = getTemplateFieldValue(
				templatePanel,
				'postTitleFontSize'
			);
		}
	};

	const setActiveTemplate = ( activeTemplate ) => {
		const resolvedTemplate = getTemplateCard( activeTemplate )
			? activeTemplate
			: templateCards[ 0 ].dataset.template;

		templateSelect.value = resolvedTemplate;

		templateCards.forEach( ( templateCard ) => {
			const isActive = templateCard.dataset.template === resolvedTemplate;

			templateCard.classList.toggle( 'is-active', isActive );
			templateCard.setAttribute(
				'aria-pressed',
				isActive ? 'true' : 'false'
			);
		} );

		templatePanels.forEach( ( templatePanel ) => {
			templatePanel.classList.toggle(
				'is-active',
				templatePanel.dataset.template === resolvedTemplate
			);
		} );

		applyPreviewStyles( resolvedTemplate );
	};

	templateSelect.addEventListener( 'change', function () {
		setActiveTemplate( templateSelect.value );
	} );

	templateCards.forEach( ( templateCard ) => {
		templateCard.addEventListener( 'click', function () {
			setActiveTemplate( templateCard.dataset.template );
		} );

		templateCard.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				setActiveTemplate( templateCard.dataset.template );
			}
		} );
	} );

	templatePanels.forEach( ( templatePanel ) => {
		templatePanel
			.querySelectorAll( '.pocualrecb-template-setting-input' )
			.forEach( ( templateInput ) => {
				const updatePreview = () =>
					applyPreviewStyles( templatePanel.dataset.template );

				templateInput.addEventListener( 'input', updatePreview );
				templateInput.addEventListener( 'change', updatePreview );
			} );
	} );

	templateCards.forEach( ( templateCard ) =>
		applyPreviewStyles( templateCard.dataset.template )
	);
	setActiveTemplate( templateSelect.value );
} );
