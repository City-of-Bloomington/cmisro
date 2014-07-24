"use strict";
(function($) {
	Drupal.behaviors.cmisro = {
		/**
		 * Attach a button to all the cmisro form inputs
		 *
		 * This button should open a new window to the cmisro/browser
		 * The user will browse and choose a file to attach.
		 */
		attach: function (context, settings) {
			$('.field-widget-cmisro-chooser .form-item .form-text').after(function () {
				var id          = $(this).attr('id'),
					button      = document.createElement('button'),
					openChooser = function() {
						window.open(
							Drupal.settings.basePath + 'cmisro/browser?popup=1&id=' + id,
							'browser',
							'width=700,height=480, resizeable=yes,scrollbars=yes'
						);
					};

				button.setAttribute('type', 'button');
				button.appendChild(document.createTextNode('Choose'));
				$(button).click(openChooser);

				return button;
			});
		}
	}
})(jQuery);

