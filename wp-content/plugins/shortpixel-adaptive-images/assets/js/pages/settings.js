;( function( $ ) {
	document.addEventListener( 'DOMContentLoaded', function() {
		var $chart = document.getElementById( 'chart' );

		if ( !( $chart instanceof HTMLElement ) ) {
			return false;
		}

		var $chartWrap = $chart.parentNode;

		var chartMaxDaysAmount = 14;

		// Preparing used arrays (we should slice unnecessary items)
		window.statusBox.chart.cdn.data.mb = window.statusBox.chart.cdn.data.mb.length <= chartMaxDaysAmount ? window.statusBox.chart.cdn.data.mb : window.statusBox.chart.cdn.data.mb.slice( window.statusBox.chart.cdn.data.mb.length - chartMaxDaysAmount );
		window.statusBox.chart.cdn.labels = window.statusBox.chart.cdn.labels.length <= chartMaxDaysAmount ? window.statusBox.chart.cdn.labels : window.statusBox.chart.cdn.labels.slice( window.statusBox.chart.cdn.labels.length - chartMaxDaysAmount );
		window.statusBox.chart.credits.labels = window.statusBox.chart.credits.labels.length <= chartMaxDaysAmount ? window.statusBox.chart.credits.labels : window.statusBox.chart.credits.labels.slice( window.statusBox.chart.credits.labels.length - chartMaxDaysAmount );
		window.statusBox.chart.credits.data.paid = window.statusBox.chart.credits.data.paid.length <= chartMaxDaysAmount ? window.statusBox.chart.credits.data.paid : window.statusBox.chart.credits.data.paid.slice( window.statusBox.chart.credits.data.paid.length - chartMaxDaysAmount );

		// Sorting out... is here a difference in values for datasets?
		if ( window.statusBox.chart.cdn.data.mb.length !== window.statusBox.chart.credits.data.paid.length ) {
			var difference = window.statusBox.chart.cdn.data.mb.length > window.statusBox.chart.credits.data.paid.length
				? window.statusBox.chart.cdn.data.mb.length - window.statusBox.chart.credits.data.paid.length
				: window.statusBox.chart.credits.data.paid.length - window.statusBox.chart.cdn.data.mb.length,
				emptyData  = new Array( difference ).fill( 0 ); // filling the empty array with 0 values

			if ( window.statusBox.chart.cdn.data.mb.length > window.statusBox.chart.credits.data.paid.length ) {
				window.statusBox.chart.credits.data.paid = emptyData.concat( window.statusBox.chart.credits.data.paid );
			}
			else {
				window.statusBox.chart.cdn.data.mb = emptyData.concat( window.statusBox.chart.cdn.data.mb );
			}
		}

		var labels      = window.statusBox.chart.cdn.labels.length >= window.statusBox.chart.credits.labels.length ? window.statusBox.chart.cdn.labels : window.statusBox.chart.credits.labels,
			cdnData     = window.statusBox.chart.cdn.data.mb,
			creditsData = window.statusBox.chart.credits.data.paid;

		if ( !$chartWrap.classList.contains( 'expanded' ) ) {
			labels = labels.slice( labels.length / 2 );
			cdnData = cdnData.slice( cdnData.length / 2 );
			creditsData = creditsData.slice( creditsData.length / 2 );
		}

		window.statusBox.chart.instance = new Chart( document.getElementById( 'chart' ).getContext( '2d' ), {
			type    : 'line',
			data    : {
				labels   : labels,
				datasets : [ {
					label           : window.statusBox.chart.titles.cdn,
					borderColor     : window.statusBox.chart.colors.cdn,
					backgroundColor : window.statusBox.chart.backgrounds.cdn,
					fill            : true,
					pointHitRadius  : 10,
					data            : cdnData,
					yAxisID         : 'cdn'
				}, {
					label           : window.statusBox.chart.titles.credits,
					borderColor     : window.statusBox.chart.colors.credits,
					backgroundColor : window.statusBox.chart.backgrounds.credits,
					fill            : true,
					pointHitRadius  : 10,
					data            : creditsData,
					yAxisID         : 'credits'
				} ]
			},
			options : {
				responsive : true,
				hoverMode  : 'index',
				stacked    : false,
				legend     : {
					position : 'top',
					labels   : {
						boxWidth      : 20,
						usePointStyle : true
					}
				},
				scales     : {
					yAxes : [ {
						type     : 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
						display  : true,
						position : 'left',
						id       : 'cdn'
					}, {
						type     : 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
						display  : true,
						position : 'right',
						id       : 'credits',

						// grid line settings
						gridLines : {
							drawOnChartArea : false // only want the grid lines for one axis to show up
						}
					} ]
				}
			}
		} );
	} );

	var Settings   = {},
		Exclusions = {};

	Settings.noticesQtyAtOneMoment = 3;
	Settings.previousNotices = [];

	Settings.parseTab = function( id ) {
		if ( id.indexOf( '#top' ) === 0 ) {
			id = id.replace( '#top', '' );
		}

		return id.indexOf( '#' ) === 0 ? id : false;
	};

	/**
	 * @param {jQuery} $element
	 */
	Settings.prepareValue = function( $element ) {
		var type    = $element.data( 'type' ),
			tagName = $element.prop( 'tagName' ).toLowerCase();

		switch ( type ) {
			case 'string':
				// current value of textarea retrieves using jQuery.fn.val() function
				return tagName === 'input' || tagName === 'textarea' ? $element.val() : undefined;

			case 'int':
				return tagName === 'input' ? parseInt( $element.val(), 10 ) : ( tagName === 'textarea' ? parseInt( $element.text(), 10 ) : undefined );

			case 'bool':
				return $element.prop( 'checked' );

			default:
				return undefined;
		}
	}

	Settings.showNotice = function( noticeBody ) {
		if ( typeof noticeBody !== 'string' || noticeBody === '' ) {
			return;
		}

		var $wpBodyTitle     = $( '#wpbody .wrap h1' ),
			$existingNotices = $( '.notice' ),
			$notice          = $( noticeBody ).hide();

		// pulling the result notice after the heading
		if ( $existingNotices.length > 0 ) {
			$existingNotices.last().after( $notice );
		}
		else {
			$wpBodyTitle.after( $notice );
		}

		// adding the notice to the array of added by this page
		this.previousNotices.push( $notice );

		if ( this.previousNotices.length > this.noticesQtyAtOneMoment ) {
			for ( var index = 0; index < this.previousNotices.length - this.noticesQtyAtOneMoment; index++ ) {
				var $previousNotice = this.previousNotices[ index ];

				if ( $previousNotice === $notice ) continue;

				$previousNotice.slideUp( 'fast', function() {
					$previousNotice.remove();
				} );

				this.previousNotices.splice( index, 1 );
			}
		}

		// Fires "wp-updates-notice-added" event, to WordPress could parse and add the standard dismiss button and pin "click" event on it
		$( document ).trigger( 'wp-updates-notice-added' );

		$notice.slideDown( 'fast', function() {
			$notice.removeAttr( 'style' );
			$( 'html:not(:animated), body:not(:animated)' ).animate( {
				scrollTop : $wpBodyTitle.offset().top
			}, 500 );
		} );

		setTimeout( function() {
			$notice.slideUp( 'fast', function() {
				$notice.remove();
			} );
		}, 20000 );
	};

	Settings.save = function( event ) {
		event.preventDefault();

		var $form         = $( this ),
			$tabs         = $form.find( '.spai_settings_tab' ),
			$submitButton = $form.find( '[type="submit"]' );

		var options = {};

		var ajax = {
			url    : $form.attr( 'action' ),
			method : $form.attr( 'method' )
		};

		var submitButton = {
			text : {
				default : $submitButton.val(),
				saving  : $submitButton.data( 'saving-text' )
			}
		};

		ajax.url = !ajax.url ? '/wp-admin/admin-ajax.php' : ajax.url;
		ajax.method = !ajax.method ? 'post' : ajax.method;

		submitButton.text.default = !submitButton.text.default ? 'Save Changes' : submitButton.text.default;
		submitButton.text.saving = !submitButton.text.saving ? 'Saving...' : submitButton.text.saving;

		$tabs.each( function() {
			var $this         = $( this ),
				$optionFields = $this.find( '[name]' );

			var tab = $this.attr( 'id' );

			if ( tab ) {
				options[ tab ] = {};
			}

			$optionFields.each( function() {
				var $this = $( this );

				var type      = $this.attr( 'type' ),
					name      = $this.attr( 'name' ),
					isChecked = $this.prop( 'checked' );

				if ( type === 'radio' ) {
					if ( isChecked ) {
						options[ tab ][ name ] = Settings.prepareValue( $this );
					}
				}
				else {
					options[ tab ][ name ] = Settings.prepareValue( $this );
				}
			} );
		} );

		$.ajax( {
			url        : ajax.url,
			method     : ajax.method,
			dataType   : 'json',
			data       : {
				action : 'shortpixel_ai_handle_page_action',
				page   : 'settings',
				data   : {
					action  : 'save',
					options : JSON.stringify( options )
				}
			},
			beforeSend : function() {
				if ( submitButton.text.saving ) {
					$submitButton.prop( 'disabled', true );
					$submitButton.val( submitButton.text.saving );
				}
			},
			success    : function( response ) {
				if ( typeof response.notice === 'string' && response.notice !== '' ) {
					Settings.showNotice( response.notice );
				}
			},
			complete   : function() {
				if ( submitButton.text.default ) {
					$submitButton.prop( 'disabled', false );
					$submitButton.val( submitButton.text.default );
				}
			}
		} );
	}

	Settings.updateChartData = function() {
		var chart       = window.statusBox.chart.instance,
			labels      = window.statusBox.chart.cdn.labels.length >= window.statusBox.chart.credits.labels.length ? window.statusBox.chart.cdn.labels : window.statusBox.chart.credits.labels,
			cdnData     = window.statusBox.chart.cdn.data.mb,
			creditsData = window.statusBox.chart.credits.data.paid;

		if ( !chart.canvas.parentNode.classList.contains( 'expanded' ) ) {
			labels = labels.slice( labels.length / 2 );
			cdnData = cdnData.slice( cdnData.length / 2 );
			creditsData = creditsData.slice( creditsData.length / 2 );
		}

		chart.data.labels = labels;

		chart.data.datasets = [ {
			label           : window.statusBox.chart.titles.cdn,
			borderColor     : window.statusBox.chart.colors.cdn,
			backgroundColor : window.statusBox.chart.backgrounds.cdn,
			fill            : true,
			pointHitRadius  : 10,
			data            : cdnData,
			yAxisID         : 'cdn'
		}, {
			label           : window.statusBox.chart.titles.credits,
			borderColor     : window.statusBox.chart.colors.credits,
			backgroundColor : window.statusBox.chart.backgrounds.credits,
			fill            : true,
			pointHitRadius  : 10,
			data            : creditsData,
			yAxisID         : 'credits'
		} ];
	};

	Exclusions.validateSelector = function( selector ) {
		try {
			document.querySelector( selector );
			return true;
		}
		catch ( e ) {
			return false;
		}
	};

	Exclusions.prepare = function() {
		var $exclusionFields = $( '[ data-setting="exclusion"]' );

		$exclusionFields.each( function() {
			var $field = $( this ),
				texts  = $field.data( 'texts' ),
				value  = $field.val();

			if ( typeof texts !== 'object' ) {
				texts = {
					add  : 'Add',
					save : 'Save'
				};
			}

			var separator     = $field.data( 'separator' ),
				exclusionType = $field.data( 'exclusion-type' );

			separator = typeof separator === 'string' && separator !== '' ? separator : ',';
			exclusionType = typeof exclusionType === 'string' && exclusionType !== '' ? exclusionType : 'selectors';

			var exclusionSelectors     = typeof value === 'string' && value !== '' ? value.split( typeof separator === 'string' && separator !== '' ? separator : ',' ) : [],
				fakeExclusionInnerHtml = '';

			for ( var index = 0, selector = exclusionSelectors[ index ]; index < exclusionSelectors.length; selector = exclusionSelectors[ ++index ] ) {
				fakeExclusionInnerHtml += '<div data-index="' + index + '" data-action="edit"><span class="selector">' + selector + '</span><span data-action="delete"></span></div>';
			}

			var $exclusonWrap = $( '<div class="exclusion-wrap" data-type="' + exclusionType + '">' +
				'<div class="exclusions-content clearfix" data-action="add"><div class="plus"></div>' + fakeExclusionInnerHtml + '</div>' +
				'<div class="buttons-wrap hidden">' +
				'<div class="error-message hidden"></div>' +
				( exclusionType === 'urls' ? '<select><option value="path">Path</option><option value="regex">RegEx</option><option value="http">HTTP</option><option value="https">HTTPS</option></select> : ' : '' ) +
				'<input type="text"><button type="button" class="dark_blue_link" data-action="confirm" data-texts=' + JSON.stringify( texts ) + '>' + texts.add + '</button></div></div>' );

			$field.before( $exclusonWrap );
			$exclusonWrap.append( $field );
		} );
	};

	Exclusions.updateRealField = function( $field, $fakePieces ) {
		var selectors = [],
			separator = $field.data( 'separator' );

		separator = typeof separator === 'string' && separator !== '' ? separator : ',';

		if ( $fakePieces.length > 0 ) {
			$fakePieces.each( function() {
				selectors.push( $( this ).text() );
			} );
		}

		$field.val( selectors.join( separator ) );
	};

	Exclusions.updateWarningMessage = function( $message, currentQty ) {
		var limit = parseInt( $message.data( 'limit' ), 10 );

		$message.find( 'span' ).text( currentQty );

		if ( currentQty > limit ) {
			$message.slideDown( 'fast' );
		}
		else {
			$message.slideUp( 'fast' );
		}
	};

	Exclusions.inputActionsHandler = function( event ) {
		var $this          = $( this ),
			$select        = $this.siblings( 'select' ),
			$options       = $select.find( 'option' ),
			$confirmButton = $this.siblings( 'button' );

		if ( event.type === 'keypress' ) {
			var allowedKeys = [ 'Enter' ];

			if ( allowedKeys.includes( event.key ) ) {
				event.preventDefault();
				event.stopPropagation();

				$confirmButton.click();
			}
		}
		else if ( 'input' || 'focus' || 'blur' || 'change' ) {
			if ( $select.length === 0 ) {
				return;
			}

			var value         = $this.val(),
				possibleCases = [];

			if ( $options.length > 0 ) {
				$options.each( function() {
					possibleCases.push( $( this ).val() );
				} );
			}

			possibleCases.forEach( function( item ) {
				if ( value.indexOf( item + ':' ) === 0 ) {
					$this.val( value.replace( item + ':', '' ) );
					$select.val( item );
				}
			} );
		}
	}

	Exclusions.actionsHandler = function( event ) {
		event.preventDefault();
		event.stopPropagation();

		var $this               = $( this ),
			$tdWrap             = $this.parents( 'td' ),
			$exclusionWarning   = $tdWrap.find( 'p.warning' ),
			$parent             = $this.parents( '.exclusion-wrap' ),
			$textarea           = $parent.find( 'textarea' ),
			$buttonsWrap        = $parent.find( '.buttons-wrap' ),
			$errorMessage       = $buttonsWrap.find( '.error-message' ),
			$input              = $buttonsWrap.find( 'input' ),
			$select             = $buttonsWrap.find( 'select' ),
			$confirmButton      = $buttonsWrap.find( 'button' ),
			$exclusionsContent  = $parent.find( '.exclusions-content' ),
			$exclusionSelectors = $exclusionsContent.find( '.selector' ),
			$exclusionElements  = $exclusionsContent.find( 'div[data-index]' );

		var value  = $input.val(),
			texts  = $confirmButton.attr( 'data-texts' ),
			action = $this.attr( 'data-action' ),
			state  = $this.attr( 'data-state' );

		var exclusionType = $textarea.data( 'exclusion-type' );

		value = typeof value === 'string' && value !== '' ? value.trim() : '';
		action = typeof action === 'string' && action !== '' ? action : undefined;
		state = action === 'confirm' && typeof state === 'string' && state !== '' ? state : 'add';
		exclusionType = typeof exclusionType === 'string' && exclusionType !== '' ? exclusionType : 'selectors';

		try {
			texts = JSON.parse( texts );
		}
		catch ( e ) {
			texts = {
				add  : 'Add',
				save : 'Save'
			};
		}

		switch ( action ) {
			case 'add' :
				$confirmButton.text( texts.add ).attr( 'data-state', 'add' ).removeAttr( 'data-editing' );
				$input.removeClass( 'error' ).val( '' );
				$buttonsWrap.slideDown( 'fast' );
				$errorMessage.text( '' ).slideUp( 'fast' );

				// after buttons-wrap has been shown focus on input
				$input.focus();

				break;
			case 'edit' :
				var $selector = $this.find( '.selector' );
				$input.removeClass( 'error' ).val( $selector.text() );
				$confirmButton.text( texts.save ).attr( 'data-state', 'save' ).attr( 'data-editing', $this.attr( 'data-index' ) );
				$buttonsWrap.slideDown( 'fast' );
				$errorMessage.text( '' ).slideUp( 'fast' );

				// after buttons-wrap has been shown focus on input
				$input.focus();

				break;
			case 'delete' :
				$this.parents( 'div[data-index]' ).fadeOut( 'fast', function() {
					$( this ).remove();
					Exclusions.updateWarningMessage( $exclusionWarning, $tdWrap.find( '.selector' ).length );
					Exclusions.updateRealField( $textarea, $exclusionsContent.find( '.selector' ) );
				} );

				$confirmButton.removeAttr( 'data-editing' ).removeAttr( 'data-state' );
				$buttonsWrap.slideUp( 'fast' );
				$input.removeClass( 'error' ).val( '' );
				$errorMessage.text( '' ).slideUp( 'fast' );

				break;
			case 'confirm' :
				var type       = $select.val(),
					hasError   = false,
					/**
					 *
					 * @type {string} Full entered content
					 */
					inputValue = exclusionType === 'urls' ? ( type + ':' + value ) : value,
					// Split the selectors here to avoid adding multiple selectors in a row
					selectors  = exclusionType === 'urls' ? [ inputValue ] : inputValue.split( ',' );

				selectors = selectors.map( function( selector ) {
					return typeof selector === 'string' ? selector.trim() : selector;
				} );

				if ( exclusionType === 'selectors' && !Exclusions.validateSelector( value ) ) {
					$input.addClass( 'error' );
					$errorMessage.html( window.exclusionsL10n.messages.selectors.invalid ).slideDown( 'fast' );
					break;
				}

				for ( var index = 0, $selector = $( $exclusionSelectors[ index ] ); index < $exclusionSelectors.length; $selector = $( $exclusionSelectors[ ++index ] ) ) {
					var currentSelectorContent = $selector.text();

					// filter new selectors list to be selectors unique
					selectors = selectors.filter( function( selector ) {
						return ( state === 'save' && $selector.parent().attr( 'data-index' ) === $this.attr( 'data-editing' ) && currentSelectorContent === selector ) || currentSelectorContent !== selector;
					} );

					if ( selectors.length <= 0 ) {
						hasError = true;

						$input.addClass( 'error' );
						$errorMessage.html( window.exclusionsL10n.messages.selectors.alreadyExists ).slideDown( 'fast' );
						break;
					}
				}

				if ( !!hasError ) {
					break;
				}

				if ( state === 'add' && value !== '' && selectors.length > 0 ) {
					var id = $exclusionElements.length > 0 ? parseInt( $exclusionElements.last().attr( 'data-index' ), 10 ) + 1 : 0;

					selectors.map( function( selector, key ) {
						$exclusionsContent.append( $( '<div data-index="' + ( id + key ) + '" data-action="edit" class="hidden"><span class="selector">' + selector + '</span><span data-action="delete"></span></div>' ).fadeIn( 'fast' ) );
					} );
				}
				else if ( state === 'save' && value !== '' && selectors.length > 0 ) {
					var id = $this.attr( 'data-editing' );

					$exclusionsContent.find( 'div[data-index="' + id + '"] .selector' ).text( selectors.shift() );

					if ( selectors.length > 0 ) {
						id = $exclusionElements.length > 0 ? parseInt( $exclusionElements.last().attr( 'data-index' ), 10 ) + 1 : 0;

						selectors.map( function( selector, key ) {
							$exclusionsContent.append( $( '<div data-index="' + ( id + key ) + '" data-action="edit" class="hidden"><span class="selector">' + selector + '</span><span data-action="delete"></span></div>' ).fadeIn( 'fast' ) );
						} );
					}
				}

				$buttonsWrap.slideUp( 'fast' );
				$input.removeClass( 'error' ).val( '' );
				$errorMessage.text( '' ).slideUp( 'fast' );

				$confirmButton.removeAttr( 'data-editing' ).removeAttr( 'data-state' );
				break;
		}

		Exclusions.updateWarningMessage( $exclusionWarning, $tdWrap.find( '.selector' ).length );
		Exclusions.updateRealField( $textarea, $exclusionsContent.find( '.selector' ) );
	};

	$( function() {
		var $document = $( this );

		if ( document.location.hash !== '' ) {
			var $soughtLink = $( 'a[href="' + document.location.hash + '"]' );

			$( '#wpspai-tabs a.nav-tab' ).removeClass( 'nav-tab-active' );
			$( '.spai_settings_tab' ).removeClass( 'active' );

			var id = Settings.parseTab( $soughtLink.attr( 'href' ) );

			if ( !!id ) {
				var $soughtTab = $( id );

				if ( $soughtTab.length > 0 ) {
					$soughtLink.addClass( 'nav-tab-active' );
					$soughtTab.addClass( 'active' );

					if ( typeof window.Beacon === 'function' && typeof window.beaconConstants === 'object' ) {
						var suggestion = $soughtTab.attr( 'id' );

						if ( typeof window.beaconConstants.suggestions[ suggestion ] !== 'undefined' ) {
							window.Beacon( 'suggest', window.beaconConstants.suggestions[ suggestion ] );
						}
					}
				}
			}
		}

		Exclusions.prepare();

		$document.on( 'click', '.exclusion-wrap [data-action]', Exclusions.actionsHandler )
		$document.on( 'change focus blur input keypress', '.exclusion-wrap input', Exclusions.inputActionsHandler );

		$document.on( 'submit', 'form#settings-form', Settings.save );

		$document.on( 'click', '.chart-wrap .toggle', function() {
			$( this ).parent().toggleClass( 'expanded' );
			Settings.updateChartData();
		} );

		$document.on( 'dblclick', '.chart-wrap canvas', function() {
			if ( window.matchMedia( '(min-width : 850px)' ).matches ) {
				$( this ).parent().toggleClass( 'expanded' );
				Settings.updateChartData();
			}
		} );

		$document.on( 'click', '#wpspai-tabs a.nav-tab', function( event ) {
			var $this = $( this );

			$( '#wpspai-tabs a.nav-tab' ).removeClass( 'nav-tab-active' );
			$( '.spai_settings_tab' ).removeClass( 'active' );

			var id = Settings.parseTab( $this.attr( 'href' ) );

			if ( !!id ) {
				var $soughtTab = $( id );

				if ( $soughtTab.length > 0 ) {
					$this.addClass( 'nav-tab-active' );
					$soughtTab.addClass( 'active' );

					if ( typeof window.Beacon === 'function' && typeof window.beaconConstants === 'object' ) {
						var suggestion = $soughtTab.attr( 'id' );

						if ( typeof window.beaconConstants.suggestions[ suggestion ] !== 'undefined' ) {
							window.Beacon( 'suggest', window.beaconConstants.suggestions[ suggestion ] );
						}
					}
				}
			}
		} );

		$document.on( 'change', 'input[type="radio"]', function() {
			var $this = $( this );

			var value = $this.val();

			if ( typeof value === 'string' && value !== '' ) {
				var $parent              = $this.parent(),
					$targetedExplanation = $parent.siblings( 'p[data-explanation="' + value + '"]' ),
					$explanationSiblings = $parent.siblings( 'p[data-explanation]' ).not( $targetedExplanation ),
					$targetedChildren    = $parent.siblings( '.children-wrap[data-parent="' + value + '"]' ),
					$childrenSiblings    = $parent.siblings( '.children-wrap[data-parent]' ).not( $targetedChildren );

				$explanationSiblings.addClass( 'hidden' );
				$targetedExplanation.removeClass( 'hidden' );
				$childrenSiblings.addClass( 'hidden' );
				$targetedChildren.removeClass( 'hidden' );
			}
		} );

		$document.on( 'change', 'input[type="checkbox"]', function() {
			var $this                  = $( this ),
				$depended              = $this.parent( '[data-depended]' ),
				$dependedSiblings      = $depended.siblings( '[data-depended]' ),
				$dependedEnabledFields = $dependedSiblings.find( 'input[type="checkbox"]:checked' ),
				$popUp                 = $this.siblings( '.notification_popup' );

			var isChecked = $this.prop( 'checked' );

			if ( isChecked ) {
				if ( $popUp.length > 0 ) {
					$popUp.removeClass( 'hidden' );
					$this.prop( 'checked', false );
				}
				else {
					$this.siblings( '.children-wrap' ).removeClass( 'hidden' );
					$this.siblings( '[data-depended]' ).find( 'input[type="checkbox"]' ).prop( 'checked', true );
				}
			}
			else {
				$this.siblings( '.children-wrap' ).addClass( 'hidden' );

				if ( $depended.length > 0 ) {
					if ( $dependedEnabledFields.length === 0 ) {
						var depended = $depended.data( 'depended' );
						depended = typeof depended === 'string' && depended !== '' ? depended : '';

						var $dependentField = $( 'input[name="' + depended + '"]' );

						console.log( $depended, $dependentField );

						$dependentField.prop( 'checked', false )
									   .siblings( '.children-wrap' ).addClass( 'hidden' );
					}
				}
			}
		} );

		$document.on( 'click', '.notification_popup input[type="button"]', function() {
			var $this         = $( this ),
				$popUp        = $this.parent( '.notification_popup' ),
				$childrenWrap = $popUp.siblings( '.children-wrap' ),
				$checkBox     = $popUp.siblings( 'input[type="checkbox"]' );

			$checkBox.prop( 'checked', true );
			$childrenWrap.removeClass( 'hidden' );
			$popUp.addClass( 'hidden' );
		} );

		$document.on( 'click', '.box_dropdown .title', function() {
			$( this ).parent().toggleClass( 'opened' );
		} );

		$document.on( 'click', '.status_box_wrap .title_wrap', function() {
			if ( document.body.clientWidth <= 850 ) {
				var $this       = $( this ),
					$parent     = $this.parent(),
					$boxContent = $parent.find( '.box_content' );

				$parent.toggleClass( 'expanded' );
				$boxContent.slideToggle( 'fast' );
			}
		} );

		$document.on( 'click', '#clear_css_cache', function( event ) {
			var $this = $( this );

			var actionTexts = {
				default : $this.text(),
				onPress : $this.attr( 'data-pressed-text' )
			};

			$.ajax( {
				method     : 'post',
				url        : '/wp-admin/admin-ajax.php',
				data       : {
					action : 'shortpixel_ai_handle_page_action',
					page   : 'settings',
					data   : {
						action : 'clear css cache'
					}
				},
				beforeSend : function() {
					$this.addClass( 'button' );
					$this.text( actionTexts.onPress );
					$this.prop( 'disabled', true );
				},
				success    : function( response ) {
					if ( typeof response.notice === 'string' && response.notice !== '' ) {
						Settings.showNotice( response.notice );
					}
				},
				complete   : function() {
					$this.text( actionTexts.default );
				}
			} );
		} );

		$document.on( 'submit', 'form#api-key-form', function( event ) {
			event.preventDefault();

			var $form         = $( this ),
				$fields       = $form.find( '[name]:not([type="hidden"])' ),
				$submitButton = $form.find( '[type="submit"]' );

			var ajax = {
				url    : $form.attr( 'action' ),
				method : $form.attr( 'method' )
			};

			var submitButton = {
				text : {
					default : $submitButton.val(),
					saving  : $submitButton.data( 'saving-text' )
				}
			};

			var formHasValues = false;

			$fields.each( function() {
				var $this = $( this ),
					value = $this.val();

				if ( typeof value === 'string' && value !== '' ) {
					formHasValues = true;
				}
			} );

			if ( !formHasValues ) {
				return;
			}

			ajax.url = !ajax.url ? '/wp-admin/admin-ajax.php' : ajax.url;
			ajax.method = !ajax.method ? 'post' : ajax.method;

			submitButton.text.default = !submitButton.text.default ? 'Save' : submitButton.text.default;
			submitButton.text.saving = !submitButton.text.saving ? 'Saving...' : submitButton.text.saving;

			$.ajax( {
				method     : ajax.method,
				url        : ajax.url,
				data       : $form.serialize(),
				beforeSend : function() {
					$submitButton.text( submitButton.text.saving );
					$submitButton.prop( 'disabled', true );
				},
				success    : function( response ) {
					if ( response.success ) {
						if ( response.reload ) {
							document.location.reload();
						}
					}
					else if ( response.notice ) {
						Settings.showNotice( response.notice );
					}
				},
				complete   : function() {
					$submitButton.text( submitButton.text.default );
					$submitButton.prop( 'disabled', false );
				}
			} );
		} );

		$document.on( 'click', '.status_box_wrap [data-action]', function( event ) {
			event.preventDefault();

			var $this = $( this );

			var action  = $this.attr( 'data-action' ),
				tagName = $this.prop( 'tagName' ).toLowerCase();

			action = typeof action === 'string' || action !== '' ? action : undefined;

			$.ajax( {
				method     : 'post',
				url        : '/wp-admin/admin-ajax.php',
				data       : {
					action : 'shortpixel_ai_handle_page_action',
					page   : 'settings',
					data   : {
						action : action
					}
				},
				beforeSend : function() {
					if ( tagName === 'button' || tagName === 'input' ) {
						$this.prop( 'disabled', true );
					}
				},
				success    : function( response ) {
					if ( response.success ) {
						if ( response.reload ) {
							document.location.reload();
						}
					}
					else if ( response.notice ) {
						Settings.showNotice( response.notice );
					}
				},
				complete   : function() {
					if ( tagName === 'button' || tagName === 'input' ) {
						$this.prop( 'disabled', false );
					}
				}
			} );
		} );
	} );
} )( jQuery );