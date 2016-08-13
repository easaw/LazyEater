(function($){

    var Frozr_Comments = {

        init: function() {
            $('#frozr-comments-table').on('click', '.frozr-cmt-action', this.setCommentStatus);
            $('#frozr-comments-table').on('click', 'button.frozr-cmt-close-form', this.closeForm);
            $('#frozr-comments-table').on('click', 'button.frozr-cmt-submit-form', this.submitForm);
            $('#frozr-comments-table').on('click', '.frozr-cmt-edit', this.populateForm);
            $('.frozr-check-all').on('click', this.toggleCheckbox);
        },

        toggleCheckbox: function() {
            $(".frozr-check-col").prop('checked', $(this).prop('checked'));
        },

        setCommentStatus: function(e) {
            e.preventDefault();

            var self = $(this),
                comment_id = self.data('comment_id'),
                comment_status = self.data('cmt_status'),
				page_status = self.data('page_status'),
				post_type = self.data('post_type'),
				curr_page = self.data('curr_page'),
                tr = self.closest('tr'),
                data = {
                    'action': 'wpuf_comment_status',
                    'comment_id': comment_id,
                    'comment_status': comment_status,
					'page_status': page_status,
					'post_type': post_type,
					'curr_page': curr_page,
					'nonce': frozr.reviews_nonce
                };


            $.post(frozr.ajax_url, data, function(resp){

                if(page_status === 1) {
                    if ( comment_status === 1 || comment_status === 0) {
                        tr.fadeOut(function() {
                            tr.replaceWith(resp.data['content']).fadeIn();
                        });

                    } else {
                        tr.fadeOut(function() {
                            $(this).remove();
                        });
                    }
                } else {
                    tr.fadeOut(function() {
                        $(this).remove();
                    });
                }

                if(resp.data['pending'] == null) resp.data['pending'] = 0;
                if(resp.data['spam'] == null) resp.data['spam'] = 0;
				if(resp.data['trash'] == null) resp.data['trash'] = 0;

                $('.comments-menu-pending').text(resp.data['pending']);
                $('.comments-menu-spam').text(resp.data['spam']);
				$('.comments-menu-trash').text(resp.data['trash']);
            });
        },

        populateForm: function(e) {
            e.preventDefault();

            var tr = $(this).closest('tr');

            // toggle the edit area
            if ( tr.next().hasClass('frozr-comment-edit-row')) {
                tr.next().remove();
                return;
            }

            var table_form = $('#frozr-edit-comment-row').html(),
                data = {
                    'author': tr.find('.frozr-cmt-hid-author').text(),
                    'email': tr.find('.frozr-cmt-hid-email').text(),
                    'url': tr.find('.frozr-cmt-hid-url').text(),
                    'body': tr.find('.frozr-cmt-hid-body').text(),
                    'id': tr.find('.frozr-cmt-hid-id').text(),
                    'status': tr.find('.frozr-cmt-hid-status').text(),
                };


            tr.after( _.template(table_form, data) );
        },

        closeForm: function(e) {
            e.preventDefault();

            $(this).closest('tr.frozr-comment-edit-row').remove();
        },

        submitForm: function(e) {
            e.preventDefault();

            var self = $(this),
                parent = self.closest('tr.frozr-comment-edit-row'),
                data = {
                    'action': 'wpuf_update_comment',
                    'comment_id': parent.find('input.frozr-cmt-id').val(),
                    'content': parent.find('textarea.frozr-cmt-body').val(),
                    'author': parent.find('input.frozr-cmt-author').val(),
                    'email': parent.find('input.frozr-cmt-author-email').val(),
                    'url': parent.find('input.frozr-cmt-author-url').val(),
                    'status': parent.find('input.frozr-cmt-status').val(),
					'nonce': frozr.reviews_nonce,
					'post_type' : parent.find('input.frozr-cmt-post-type').val(),
                };

            $.post(frozr.ajax_url, data, function(res) {
                if ( res.success === true) {
                    parent.prev().replaceWith(res.data);
                    parent.remove();
                } else {
                    alert( res.data );
                }
            });
        }
    };

    $(function(){

        Frozr_Comments.init();
    });

})(jQuery);