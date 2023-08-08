jQuery(document).ready(
    function ( $ ) {
        'use strict';

        // Localized variables.
        var ajaxurl        = DuplicatorAdminJsObj.ajaxurl;
        var scanInProgress = DuplicatorAdminJsObj.scan_in_progress;
        var scanCompleted  = DuplicatorAdminJsObj.scan_completed;
        var ajaxNonce      = DuplicatorAdminJsObj.ajax_nonce;

        /**
         * Scan the website.
         */
        $(document).on(
            'click', '.dup-scan-site', function (evt) {
                evt.preventDefault();
                var page = 1, filesPerIteraton = 500, lastScannedIndex = 0;
                scanSite(page, filesPerIteraton, lastScannedIndex);

                // Hide the table and the progress text.
                if (! $('table.scanned-dirs.form-table').hasClass('d-none') ) {
                    $('table.scanned-dirs.form-table').addClass('d-none');
                }
            } 
        );

        /**
         * Scan the site. This is a self-iterated function.
         *
         * @param {int}    page Page.
         * @param {int}    filesPerIteraton Number of files per iteration.
         * @param {string} lastScannedIndex Last scanned file.
         */
        function scanSite( page, filesPerIteraton, lastScannedIndex )
        {
              // Send the AJAX to scan the main directories.
            $.ajax(
                {
                    dataType: 'JSON',
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'scan_site',
                        nonce: ajaxNonce,
                        page: page,
                        filesPerIteraton: filesPerIteraton,
                        lastScannedIndex: lastScannedIndex,
                    },
                    beforeSend: function () {
                              block_element($('.dup-scan-site')); // Block the button.
                              $('.dup-scan-site').text(scanInProgress); // Set the wait text on the button.
                    },
                    success: function ( response ) {
                        // If the scanning is in progress.
                        if ('scan-in-progress' === response.data.code ) {
                            $('table.scanned-dirs.form-table tbody').append(response.data.html);
                            $('table.scanned-dirs.form-table, .scan-in-progress').removeClass('d-none');

                            // Wait for a bit and resume the scan.
                            setTimeout(
                                function () {
                                    scanSite(++page, filesPerIteraton, response.data.lastScannedIndex);
                                }, 400 
                            );
                        }

                        // If the scanning is completed.
                        if ('scan-complete' === response.data.code ) {
                            // Mark the progress text as completed.
                            $('.scan-in-progress').text(scanCompleted);
                            unblock_element($('.dup-scan-site')); // Unblock the button.
                            $('.dup-scan-site').text('Scan Site'); // Set the original text on the button.
                        }
                    },
                    error: function ( xhr ) {
                        console.warn('Error occured. Please try again. Status: ' + xhr.statusText + '. Text: ' + xhr.responseText);
                    },
                    complete: function () {
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
