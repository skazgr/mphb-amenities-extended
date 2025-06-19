jQuery(document).ready(function($) {
    function mediaUploader() {
        var frame;
        if (typeof wp === 'undefined' || !wp.media) return;

        $('#amenity-image-upload-button').on('click', function(e) {
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({ title: 'Select or Upload Media', button: { text: 'Use this media' }, multiple: false });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes?.thumbnail?.url || attachment.url;
                if (url) {
                    $('#amenity-image-id').val(attachment.id);
                    $('#amenity-image-wrapper').html('<img src="' + url + '" alt="" />');
                }
            });
            frame.open();
        });

        $('#amenity-image-remove-button').on('click', function(e) {
            e.preventDefault();
            $('#amenity-image-id').val('');
            $('#amenity-image-wrapper').html('');
        });
    }
    mediaUploader();
});
