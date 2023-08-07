jQuery(document).ready(
    function ( $ ) {
        'use strict';

        // Localized variables.
        var ajaxurl    = DuplicatorAdminJsObj.ajaxurl;
        var scan_in_progress  = DuplicatorAdminJsObj.scan_in_progress;
        var scan_completed  = DuplicatorAdminJsObj.scan_completed;
    
        var ajax_nonce = DuplicatorAdminJsObj.ajax_nonce;

        /**
         * Scan the website.
         */
        $(document).on(
            'click', '.dup-scan-site', function (evt) {
                evt.preventDefault();
                var page = 1, files_per_iteration = 200, last_scanned_index = 0, this_button = $(this);
                scanSite(page, files_per_iteration, last_scanned_index, this_button);

                // Hide the table and the progress text.
                if (! $('table.scanned-dirs.form-table').hasClass('d-none') ) {
                    $('table.scanned-dirs.form-table').addClass('d-none');
                }
            } 
        );

        /**
         * Scan the site. This is a self-iterated function.
         *
         * @param {int} page Page.
         * @param {int} files_per_iteration Number of files per iteration.
         * @param {string} last_scanned_index Last scanned file.
         * @param {*} this_button Scan button element.
         */
        function scanSite( page, files_per_iteration, last_scanned_index, this_button )
        {
              var button_text = this_button.text(); // Grab the original button text.

              // Send the AJAX to scan the main directories.
            $.ajax(
                {
                    dataType: 'JSON',
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'scan_site',
                        nonce: ajax_nonce,
                        page: page,
                        files_per_iteration: files_per_iteration,
                        last_scanned_index: last_scanned_index,
                    },
                    beforeSend: function () {
                              block_element($('.dup-scan-site')); // Block the button.
                              $('.dup-scan-site').text(scan_in_progress); // Set the wait text on the button.
                    },
                    success: function ( response ) {
                        // If the scanning is in progress.
                        if ('scan-in-progress' === response.data.code ) {
                            $('table.scanned-dirs.form-table tbody').append(response.data.html);
                            $('table.scanned-dirs.form-table, .scan-in-progress').removeClass('d-none');

                            // Wait for a bit and resume the scan.
                            setTimeout(
                                function () {
                                    scanSite(++page, files_per_iteration, response.data.last_scanned_index);
                                }, 400 
                            );
                        }

                        // If the scanning is completed.
                        if ('scan-complete' === response.data.code ) {
                            // Mark the progress text as completed.
                            $('.scan-in-progress').text(scan_completed);
                        }
                    },
                    error: function ( xhr ) {
                        console.warn('Error occured. Please try again. Status: ' + xhr.statusText + '. Text: ' + xhr.responseText);
                    },
                    complete: function () {
                        // Scroll to the p tag where the waiting text is visible.
                        $('html, body').animate(
                            {
                                scrollTop: $('.scan-in-progress').offset().top
                            }, 3000 
                        );

                        unblock_element($('.dup-scan-site')); // Unblock the button.
                        $('.dup-scan-site').text(button_text); // Set the original text on the button.
                    }
                } 
            );
        }

        /**
         * Block element.
         *
         * @param {string} element
         */
        function block_element( element )
        {
            element.addClass('non-clickable');
        }

        /**
         * Unblock element.
         *
         * @param {string} element
         */
        function unblock_element( element )
        {
            element.removeClass('non-clickable');
        }
    } 
);
