/**
 * WordPress Importer AJAX Progress UI
 */

(function($) {
	'use strict';

	var WPImportUI = {
		importId: null,
		totalItems: 0,
		isPaused: false,

		/**
		 * Initialize the import UI
		 */
		init: function() {
			this.bindEvents();
		},

		/**
		 * Bind UI events
		 */
		bindEvents: function() {
			var self = this;

			// Start import button
			$('#wp-import-start-button').on('click', function(e) {
				e.preventDefault();
				self.startImport();
			});

			// Pause button
			$('#wp-import-pause-button').on('click', function(e) {
				e.preventDefault();
				self.togglePause();
			});

			// Cancel button
			$('#wp-import-cancel-button').on('click', function(e) {
				e.preventDefault();
				if (confirm(wpImportL10n.confirmCancel)) {
					self.cancelImport();
				}
			});
		},

		/**
		 * Start the import process
		 */
		startImport: function() {
			var self = this;

			// Get import ID from hidden field
			this.importId = $('#import_id').val();

			if (!this.importId) {
				this.showError(wpImportL10n.invalidImportId);
				return;
			}

			// Show progress UI, hide start button
			$('#wp-import-start-button').hide();
			$('#wp-import-progress-wrap').show();

			// Initialize import via AJAX
			this.log(wpImportL10n.initializing);

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wp_import_init',
					nonce: wpImportAjax.nonce,
					import_id: this.importId
				},
				success: function(response) {
					if (response.success) {
						self.totalItems = response.data.total_items;
						self.log(wpImportL10n.initSuccess + ' ' + self.totalItems + ' ' + wpImportL10n.itemsFound);
						self.log('Categories: ' + response.data.total_categories);
						self.log('Tags: ' + response.data.total_tags);
						self.log('Terms: ' + response.data.total_terms);
						self.log('Posts: ' + response.data.total_posts);
						self.log('---');

						// Start processing items
						self.processNextItem();
					} else {
						self.showError(response.data.message);
					}
				},
				error: function(xhr, status, error) {
					self.showError(wpImportL10n.ajaxError + ': ' + error);
				}
			});
		},

		/**
		 * Process the next item in the import queue
		 */
		processNextItem: function() {
			var self = this;

			// Check if paused
			if (this.isPaused) {
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wp_import_process_item',
					nonce: wpImportAjax.nonce,
					import_id: this.importId
				},
				success: function(response) {
					if (response.success) {
						var data = response.data;

						// Update progress
						self.updateProgress(
							data.items_processed,
							data.total_items,
							data.percent_complete,
							data.phase,
							data.item_title,
							data.item_type
						);

						// Log current item
						var itemLabel = data.item_type.charAt(0).toUpperCase() + data.item_type.slice(1);
						self.log(wpImportL10n.processing + ' ' + itemLabel + ': ' + data.item_title);

						// Process attachment log if present
						if (data.attachment_log && data.attachment_log.length > 0) {
							self.processAttachmentLog(data.attachment_log);
						} else {
							// Hide attachment progress if no attachments
							$('#wp-import-attachment-progress').hide();
						}

						// Check if complete
						if (data.is_complete) {
							self.completeImport();
						} else {
							// Add small delay before next item to prevent overwhelming server
							setTimeout(function() {
								self.processNextItem();
							}, 100);
						}
					} else {
						self.showError(response.data.message);
					}
				},
				error: function(xhr, status, error) {
					var errorMsg = wpImportL10n.ajaxError + ': ' + error;
					// If it's a parse error, show the actual response
					if (xhr.responseText && error.indexOf('JSON') !== -1) {
						errorMsg += ' (Response: ' + xhr.responseText.substring(0, 200) + '...)';
					}
					self.showError(errorMsg);
					// Stop on error - don't retry automatically
					self.isPaused = true;
				}
			});
		},

		/**
		 * Update progress display
		 */
		updateProgress: function(processed, total, percent, phase, itemTitle, itemType) {
			// Update progress bar
			$('#wp-import-progress-bar').css('width', percent + '%');
			$('#wp-import-progress-text').text(percent + '%');

			// Update counts
			$('#wp-import-progress-count').text(
				wpImportL10n.processed + ' ' + processed + ' ' + wpImportL10n.of + ' ' + total + ' ' + wpImportL10n.items
			);

			// Update current operation
			var phaseLabel = phase.charAt(0).toUpperCase() + phase.slice(1);
			$('#wp-import-current-operation').text(
				wpImportL10n.currentPhase + ': ' + phaseLabel
			);

			// Update current item
			if (itemTitle) {
				var itemLabel = itemType.charAt(0).toUpperCase() + itemType.slice(1);
				$('#wp-import-current-item').text(
					itemLabel + ': ' + itemTitle
				);
			}
		},

		/**
		 * Process attachment log
		 */
		processAttachmentLog: function(attachmentLog) {
			var self = this;
			var total = attachmentLog.length;
			var processed = 0;

			// Show attachment progress bar
			$('#wp-import-attachment-progress').show();

			// Process each attachment in the log
			attachmentLog.forEach(function(attachment, index) {
				processed = index + 1;

				// Update attachment progress bar
				var percent = Math.round((processed / total) * 100);
				$('#wp-import-attachment-progress-bar').css('width', percent + '%');
				$('#wp-import-attachment-progress-text').text(processed + '/' + total);

				// Update status message and log
				var sourceLabel = attachment.source === 'content' ? ' [from content]' : '';

				if (attachment.status === 'downloading') {
					$('#wp-import-attachment-status').text('Downloading: ' + attachment.file);
					self.log('  → Downloading' + sourceLabel + ': ' + attachment.file);
				} else if (attachment.status === 'matched') {
					$('#wp-import-attachment-status').text('Matched existing: ' + attachment.file);
					self.log('  → Matched existing' + sourceLabel + ': ' + attachment.file + ' (ID: ' + attachment.id + ')');
				} else if (attachment.status === 'added') {
					$('#wp-import-attachment-status').text('Added to library: ' + attachment.file);
					self.log('  → Added to library' + sourceLabel + ': ' + attachment.file + ' (ID: ' + attachment.id + ')');
				} else if (attachment.status === 'failed') {
					$('#wp-import-attachment-status').text('Failed: ' + attachment.file);
					self.log('  → ❌ Failed' + sourceLabel + ': ' + attachment.file + ' - ' + attachment.error);
				}
			});

			// Summary
			self.log('  → Processed ' + total + ' attachment(s)');
		},

		/**
		 * Complete the import
		 */
		completeImport: function() {
			this.log('---');
			this.log(wpImportL10n.importComplete);

			// Update UI
			$('#wp-import-progress-bar').css('width', '100%').addClass('complete');
			$('#wp-import-progress-text').text('100%');
			$('#wp-import-current-operation').text(wpImportL10n.importComplete);
			$('#wp-import-current-item').text('');

			// Hide attachment progress and pause button, show done message
			$('#wp-import-attachment-progress').hide();
			$('#wp-import-pause-button').hide();
			$('#wp-import-cancel-button').hide();
			$('#wp-import-complete-message').show();
		},

		/**
		 * Toggle pause/resume
		 */
		togglePause: function() {
			this.isPaused = !this.isPaused;

			if (this.isPaused) {
				$('#wp-import-pause-button').text(wpImportL10n.resume);
				this.log(wpImportL10n.importPaused);
			} else {
				$('#wp-import-pause-button').text(wpImportL10n.pause);
				this.log(wpImportL10n.importResumed);
				this.processNextItem();
			}
		},

		/**
		 * Cancel the import
		 */
		cancelImport: function() {
			this.isPaused = true;
			this.log(wpImportL10n.importCancelled);
			$('#wp-import-progress-wrap').hide();
			$('#wp-import-start-button').show();
		},

		/**
		 * Log a message to the console
		 */
		log: function(message) {
			var $log = $('#wp-import-log');
			var timestamp = new Date().toLocaleTimeString();
			$log.append('<div class="log-entry">[' + timestamp + '] ' + message + '</div>');

			// Auto-scroll to bottom
			$log.scrollTop($log[0].scrollHeight);
		},

		/**
		 * Show an error message
		 */
		showError: function(message) {
			this.log('❌ ' + wpImportL10n.error + ': ' + message);
			$('#wp-import-error-wrap').show();
			$('#wp-import-error-message').text(message);
			this.isPaused = true;
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		if ($('#wp-import-ajax-ui').length) {
			WPImportUI.init();
		}
	});

})(jQuery);
