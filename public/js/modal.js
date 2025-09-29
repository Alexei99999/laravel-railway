$(document).ready(function() {
    // Rely on Bootstrap's data-dismiss="modal" for closing
    $('#createModal, #editModal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
        // Clean up Flatpickr instances
        $('.flatpickr-initialized').each(function() {
            if (this._flatpickr) {
                this._flatpickr.destroy();
                $(this).removeClass('flatpickr-initialized');
            }
        });
    });
});