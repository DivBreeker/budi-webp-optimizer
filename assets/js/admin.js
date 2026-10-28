/**
 * BUDI WebP Optimizer - Admin JavaScript
 * Pure vanilla / jQuery lightweight controller.
 */

(function($) {
	'use strict';

	var BWO = {
		state: {
			paged: 1,
			perPage: 10,
			search: '',
			status: 'all',
			selectedIds: [],
			bulkRunning: false,
			bulkPaused: false
		},

		init: function() {
			this.bindEvents();
			this.loadImages();
			this.updateDonutChart();
		},

		bindEvents: function() {
			var self = this;

			// Tab Navigation
			$('.bwo-tab-btn').on('click', function() {
				var target = $(this).data('tab');
				$('.bwo-tab-btn').removeClass('active');
				$(this).addClass('active');

				$('.bwo-tab-content').removeClass('active');
				$('#' + target).addClass('active');
			});

			// Filter Dropdown Toggle
			$('#bwo-btn-filter-toggle').on('click', function(e) {
				e.stopPropagation();
				$('#bwo-filter-dropdown').toggle();
			});

			$(document).on('click', function() {
				$('#bwo-filter-dropdown').hide();
			});

			// Filter Option Selection
			$('.bwo-filter-opt').on('click', function(e) {
				e.preventDefault();
				$('.bwo-filter-opt').removeClass('active');
				$(this).addClass('active');

				self.state.status = $(this).data('status');
				$('#bwo-current-filter-label').text($(this).text());
				$('#bwo-filter-dropdown').hide();

				self.state.paged = 1;
				self.loadImages();
			});

			// Debounced Search Input
			var searchTimer;
			$('#bwo-search-input').on('keyup input', function() {
				clearTimeout(searchTimer);
				var query = $(this).val();
				searchTimer = setTimeout(function() {
					self.state.search = query;
					self.state.paged = 1;
					self.loadImages();
				}, 350);
			});

			// Scan Media Library Button
			$('#bwo-btn-scan').on('click', function() {
				var $btn = $(this);
				var $icon = $btn.find('.bwo-icon-spin-target');
				$icon.addClass('bwo-spinning');
				$btn.prop('disabled', true);

				$.post(bwoData.ajaxUrl, {
					action: 'bwo_scan_library',
					bwo_nonce: bwoData.nonce
				}, function(response) {
					$icon.removeClass('bwo-spinning');
					$btn.prop('disabled', false);

					if (response.success && response.data.stats) {
						self.applyStats(response.data.stats);
						self.loadImages();
					}
				}).fail(function() {
					$icon.removeClass('bwo-spinning');
					$btn.prop('disabled', false);
				});
			});

			// Table Select All Checkbox
			$('#bwo-select-all').on('change', function() {
				var checked = $(this).prop('checked');
				$('.bwo-row-check').prop('checked', checked);
				self.updateSelection();
			});

			// Individual Row Checkbox
			$(document).on('change', '.bwo-row-check', function() {
				self.updateSelection();
			});

			// Clear Selection
			$('#bwo-btn-clear-selection').on('click', function() {
				$('#bwo-select-all').prop('checked', false);
				$('.bwo-row-check').prop('checked', false);
				self.updateSelection();
			});

			// Quick Action: Convert All Images
			$('#bwo-btn-convert-all').on('click', function() {
				if (confirm(bwoData.strings.confirmConvertAll)) {
					self.startBulkConversion('all');
				}
			});

			// Quick Action: Select & Convert
			$('#bwo-btn-select-convert').on('click', function() {
				// Switch to Recent Images tab if not on it
				$('.bwo-tab-btn[data-tab="tab-recent"]').trigger('click');
				$('html, body').animate({
					scrollTop: $('.bwo-tabs-container').offset().top - 40
				}, 400);

				if (self.state.selectedIds.length > 0) {
					self.startBulkConversion('selected', self.state.selectedIds);
				}
			});

			// Bulk Action: Convert Selected
			$('#bwo-btn-convert-selected').on('click', function() {
				if (self.state.selectedIds.length > 0) {
					self.startBulkConversion('selected', self.state.selectedIds);
				}
			});

			// Bulk Action: Restore Selected
			$('#bwo-btn-restore-selected').on('click', function() {
				if (self.state.selectedIds.length === 0) return;
				if (!confirm(bwoData.strings.confirmRestore)) return;

				var idsToRestore = self.state.selectedIds.slice();
				self.restoreMultiple(idsToRestore);
			});

			// Single Row Action: Convert
			$(document).on('click', '.bwo-row-convert-btn', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var id = $btn.data('id');
				$btn.prop('disabled', true).text(bwoData.strings.converting);

				$.post(bwoData.ajaxUrl, {
					action: 'bwo_convert_single',
					attachment_id: id,
					bwo_nonce: bwoData.nonce
				}, function(res) {
					if (res.success) {
						if (res.data.stats) {
							self.applyStats(res.data.stats);
						}
						self.loadImages();
					} else {
						alert(res.data && res.data.message ? res.data.message : bwoData.strings.failed);
						$btn.prop('disabled', false).text('Convert');
					}
				});
			});

			// Single Row Action: Restore
			$(document).on('click', '.bwo-btn-restore, .bwo-row-restore-btn', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var id = $btn.data('id');

				if (!confirm(bwoData.strings.confirmRestore)) return;

				$btn.prop('disabled', true).text(bwoData.strings.restoring);

				$.post(bwoData.ajaxUrl, {
					action: 'bwo_restore_single',
					attachment_id: id,
					bwo_nonce: bwoData.nonce
				}, function(res) {
					if (res.success) {
						if (res.data.stats) {
							self.applyStats(res.data.stats);
						}
						self.loadImages();
					} else {
						alert(res.data && res.data.message ? res.data.message : 'Restore error');
						$btn.prop('disabled', false).text('Restore');
					}
				});
			});

			// Single Row Action: Preview Modal
			$(document).on('click', '.bwo-row-preview-btn', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				self.openPreviewModal(id);
			});

			// Close Modals
			$('[data-close-modal], .bwo-modal-backdrop').on('click', function(e) {
				if (e.target === this || $(this).is('[data-close-modal]')) {
					$('.bwo-modal-backdrop').fadeOut(150);
				}
			});

			$('.bwo-modal-dialog').on('click', function(e) {
				e.stopPropagation();
			});

			// Help Modal Link
			$('#bwo-btn-help').on('click', function(e) {
				e.preventDefault();
				$('#bwo-help-modal').fadeIn(150);
			});

			// Bulk Modal Controls
			$('#bwo-btn-bulk-pause').on('click', function() {
				if (self.state.bulkPaused) {
					self.state.bulkPaused = false;
					$(this).text('Pause');
					$('#bwo-bulk-status-text').text('Processing...');
					self.runBulkLoop();
				} else {
					self.state.bulkPaused = true;
					$(this).text('Resume');
					$('#bwo-bulk-status-text').text('Paused by user');
				}
			});

			$('#bwo-btn-bulk-cancel, #bwo-bulk-close-btn').on('click', function() {
				self.state.bulkRunning = false;
				$.post(bwoData.ajaxUrl, {
					action: 'bwo_cancel_bulk',
					bwo_nonce: bwoData.nonce
				});
				$('#bwo-bulk-modal').fadeOut(150);
				self.loadImages();
			});

			$('#bwo-btn-bulk-finish').on('click', function() {
				$('#bwo-bulk-modal').fadeOut(150);
				self.loadImages();
			});

			// Pagination
			$('#bwo-pg-prev').on('click', function() {
				if (self.state.paged > 1) {
					self.state.paged--;
					self.loadImages();
				}
			});

			$('#bwo-pg-next').on('click', function() {
				self.state.paged++;
				self.loadImages();
			});

			// Radio Card Selection
			$('.bwo-radio-card input').on('change', function() {
				$('.bwo-radio-card').removeClass('selected');
				$(this).closest('.bwo-radio-card').addClass('selected');
			});

			// Quality Slider
			$('#bwo-quality-slider, #bwo-quality-slider-2').on('input', function() {
				var val = $(this).val();
				$('#bwo-quality-display, #bwo-quality-display-2').text(val + '%');
			});

			$('.bwo-tick-btn').on('click', function() {
				var val = $(this).data('val');
				$('.bwo-tick-btn').removeClass('active');
				$(this).addClass('active');
				$('#bwo-quality-slider').val(val).trigger('input');
			});

			// Settings Form Submit
			$('#bwo-settings-form, #bwo-standalone-settings-form').on('submit', function(e) {
				e.preventDefault();
				var $form = $(this);
				var formData = $form.serializeArray();
				var settingsObj = {};

				$.each(formData, function(i, field) {
					settingsObj[field.name] = field.value;
				});

				// Include unchecked checkboxes
				if (!settingsObj.keep_backup) settingsObj.keep_backup = 0;
				if (!settingsObj.auto_convert) settingsObj.auto_convert = 0;
				if (!settingsObj.convert_thumbnails) settingsObj.convert_thumbnails = 0;

				var $status = $form.find('.bwo-save-status');
				$status.text('Saving...');

				$.post(bwoData.ajaxUrl, {
					action: 'bwo_save_settings',
					settings: settingsObj,
					bwo_nonce: bwoData.nonce
				}, function(res) {
					if (res.success) {
						$status.text(bwoData.strings.settingsSaved).fadeIn();
						setTimeout(function() {
							$status.fadeOut();
						}, 3000);
					}
				});
			});
		},

		updateSelection: function() {
			var ids = [];
			$('.bwo-row-check:checked').each(function() {
				ids.push($(this).val());
			});

			this.state.selectedIds = ids;
			if (ids.length > 0) {
				$('#bwo-selected-count').text(ids.length);
				$('#bwo-bulk-bar').slideDown(150);
			} else {
				$('#bwo-bulk-bar').slideUp(150);
			}
		},

		loadImages: function() {
			var self = this;
			var $tbody = $('#bwo-table-body');
			$tbody.html('<tr><td colspan="8" class="bwo-td-loading"><div class="bwo-spinner"></div><span>Loading...</span></td></tr>');

			$.post(bwoData.ajaxUrl, {
				action: 'bwo_get_images',
				paged: self.state.paged,
				per_page: self.state.perPage,
				search: self.state.search,
				status: self.state.status,
				bwo_nonce: bwoData.nonce
			}, function(res) {
				if (res.success && res.data) {
					self.renderTable(res.data);
				} else {
					$tbody.html('<tr><td colspan="8" class="bwo-td-empty">Error loading images</td></tr>');
				}
			}).fail(function() {
				$tbody.html('<tr><td colspan="8" class="bwo-td-empty">Failed to communicate with server</td></tr>');
			});
		},

		renderTable: function(data) {
			var items = data.items || [];
			var $tbody = $('#bwo-table-body');

			if (items.length === 0) {
				$tbody.html('<tr><td colspan="8" class="bwo-td-empty">No images found matching your filter.</td></tr>');
				$('#bwo-pagination').hide();
				return;
			}

			var html = '';
			$.each(items, function(i, item) {
				var statusBadge = '';
				var actionBtns = '';

				if (item.status === 'converted') {
					statusBadge = '<span class="bwo-status-pill bwo-pill-converted"><span class="bwo-dot-conv"></span> Converted</span>';
					actionBtns = '<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-row-preview-btn" data-id="' + item.id + '"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Preview</button>';
					if (item.has_backup) {
						actionBtns += ' <button type="button" class="bwo-btn-dots bwo-row-restore-btn" data-id="' + item.id + '" title="Restore original file">↺</button>';
					}
				} else if (item.status === 'failed') {
					statusBadge = '<span class="bwo-status-pill bwo-pill-failed"><span class="bwo-dot-fail"></span> Failed</span>';
					actionBtns = '<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-row-convert-btn" data-id="' + item.id + '"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> Retry</button>';
				} else {
					statusBadge = '<span class="bwo-status-pill bwo-pill-pending"><span class="bwo-dot-pend"></span> Pending</span>';
					actionBtns = '<button type="button" class="bwo-btn bwo-btn-sm bwo-btn-white bwo-row-convert-btn" data-id="' + item.id + '"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> Convert</button>';
				}

				html += '<tr>';
				html += '<td class="bwo-td-check"><input type="checkbox" class="bwo-row-check" value="' + item.id + '" /></td>';
				html += '<td><img class="bwo-table-thumb" src="' + item.thumbnail_url + '" alt="" loading="lazy" /></td>';
				html += '<td><div class="bwo-file-info"><span class="bwo-file-name">' + item.filename + '</span><span class="bwo-file-dims">' + item.dimensions + '</span></div></td>';
				html += '<td>' + item.original_size_f + '</td>';
				html += '<td>' + item.webp_size_f + '</td>';
				html += '<td class="bwo-td-savings">' + item.savings_percent + '</td>';
				html += '<td>' + statusBadge + '</td>';
				html += '<td><div class="bwo-actions-cell">' + actionBtns + '</div></td>';
				html += '</tr>';
			});

			$tbody.html(html);

			// Render Pagination
			if (data.total_pages > 1) {
				$('#bwo-pagination').show();
				$('#bwo-pagination-info').text('Showing page ' + data.paged + ' of ' + data.total_pages + ' (' + data.total_items + ' items)');
				$('#bwo-pg-num').text(data.paged);
				$('#bwo-pg-prev').prop('disabled', data.paged <= 1);
				$('#bwo-pg-next').prop('disabled', data.paged >= data.total_pages);
			} else {
				$('#bwo-pagination').hide();
			}

			// Clear select all header check
			$('#bwo-select-all').prop('checked', false);
			this.updateSelection();
		},

		applyStats: function(stats) {
			$('#bwo-stat-total').text(Number(stats.total_images).toLocaleString());
			$('#bwo-stat-converted').text(Number(stats.converted_images).toLocaleString());
			$('#bwo-stat-percent').text(stats.percent_completed + '% completed');
			$('#bwo-stat-saved').text(stats.storage_saved_f);
			$('#bwo-stat-reduction').text(stats.reduction_label);
			$('#bwo-stat-pending').text(Number(stats.pending_images).toLocaleString());

			$('#bwo-donut-pct').text(stats.percent_donut + '%');
			$('#bwo-legend-conv').text(Number(stats.converted_images).toLocaleString());
			$('#bwo-legend-pend').text(Number(stats.pending_images).toLocaleString());
			$('#bwo-legend-fail').text(Number(stats.failed_images).toLocaleString());

			$('#bwo-metric-orig').text(stats.original_size_f);
			$('#bwo-metric-curr').text(stats.current_size_f);
			$('#bwo-metric-saved').text(stats.storage_saved_f);

			this.updateDonutChart(stats.percent_completed);
		},

		updateDonutChart: function(pct) {
			var $circle = $('#bwo-donut-circle');
			if (!$circle.length) return;

			if (pct === undefined) {
				var pctText = $('#bwo-donut-pct').text();
				pct = parseFloat(pctText) || 0;
			}

			var circumference = 251.2;
			var offset = circumference - (circumference * (pct / 100));
			$circle.css('stroke-dashoffset', offset);
		},

		openPreviewModal: function(id) {
			var $modal = $('#bwo-preview-modal');
			$modal.fadeIn(150);

			$('#bwo-pv-orig-size').text('...');
			$('#bwo-pv-webp-size').text('...');
			$('#bwo-pv-savings-badge').text('...');
			$('#bwo-pv-orig-img').attr('src', '');
			$('#bwo-pv-webp-img').attr('src', '');

			$.post(bwoData.ajaxUrl, {
				action: 'bwo_get_preview',
				attachment_id: id,
				bwo_nonce: bwoData.nonce
			}, function(res) {
				if (res.success && res.data) {
					var d = res.data;
					$('#bwo-pv-orig-size').text(d.original_size);
					$('#bwo-pv-webp-size').text(d.webp_size);
					$('#bwo-pv-savings-badge').text('-' + d.savings);
					$('#bwo-pv-orig-img').attr('src', d.original_url);
					$('#bwo-pv-webp-img').attr('src', d.webp_url);
				}
			});
		},

		startBulkConversion: function(mode, ids) {
			var self = this;
			var postData = {
				action: 'bwo_start_bulk',
				mode: mode,
				bwo_nonce: bwoData.nonce
			};
			if (ids) {
				postData.ids = ids;
			}

			$('#bwo-bulk-modal').fadeIn(150);
			$('#bwo-bulk-pct-display').text('0%');
			$('#bwo-bulk-progress-bar').css('width', '0%');
			$('#bwo-bulk-status-text').text('Queueing attachments...');
			$('#bwo-btn-bulk-pause').show().text('Pause');
			$('#bwo-btn-bulk-cancel').show();
			$('#bwo-btn-bulk-finish').hide();

			$.post(bwoData.ajaxUrl, postData, function(res) {
				if (res.success && res.data.queue) {
					self.state.bulkRunning = true;
					self.state.bulkPaused = false;
					self.runBulkLoop();
				} else {
					alert(res.data && res.data.message ? res.data.message : 'Failed to start bulk conversion');
					$('#bwo-bulk-modal').fadeOut(150);
				}
			});
		},

		runBulkLoop: function() {
			var self = this;
			if (!self.state.bulkRunning || self.state.bulkPaused) return;

			$.post(bwoData.ajaxUrl, {
				action: 'bwo_process_batch',
				bwo_nonce: bwoData.nonce
			}, function(res) {
				if (!res.success) {
					alert('Batch processing error.');
					self.state.bulkRunning = false;
					return;
				}

				var d = res.data;
				var pct = d.percent || 0;
				$('#bwo-bulk-pct-display').text(pct + '%');
				$('#bwo-bulk-progress-bar').css('width', pct + '%');
				$('#bwo-bulk-processed-count').text(d.processed + ' / ' + d.total);
				$('#bwo-bulk-success-count').text(d.successful);
				$('#bwo-bulk-fail-count').text(d.failed);

				if (d.current_item) {
					$('#bwo-bulk-current-filename').text(d.current_item);
				}

				if (d.completed) {
					self.state.bulkRunning = false;
					$('#bwo-bulk-status-text').text('All images processed successfully!');
					$('#bwo-btn-bulk-pause').hide();
					$('#bwo-btn-bulk-cancel').hide();
					$('#bwo-btn-bulk-finish').fadeIn();

					if (d.stats) {
						self.applyStats(d.stats);
					}
				} else {
					$('#bwo-bulk-status-text').text('Converting batch in background...');
					// Continue next batch
					setTimeout(function() {
						self.runBulkLoop();
					}, 200);
				}
			}).fail(function() {
				// Retry on transient failure
				if (self.state.bulkRunning && !self.state.bulkPaused) {
					setTimeout(function() {
						self.runBulkLoop();
					}, 1000);
				}
			});
		},

		restoreMultiple: function(ids) {
			var self = this;
			if (ids.length === 0) {
				self.loadImages();
				return;
			}

			var id = ids.shift();
			$.post(bwoData.ajaxUrl, {
				action: 'bwo_restore_single',
				attachment_id: id,
				bwo_nonce: bwoData.nonce
			}, function(res) {
				if (res.success && res.data.stats) {
					self.applyStats(res.data.stats);
				}
				self.restoreMultiple(ids);
			});
		}
	};

	$(document).ready(function() {
		BWO.init();
	});

})(jQuery);
