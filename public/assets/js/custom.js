$(document).ready(function() {
    $('#parent_services').select2();
    $('#select_team').select2();
    $('#order_team_member').select2();
    $('#ticket_team_member').select2();
    $('#onboarding_field').select2();
    $('#country').select2();
    $('#language').select2();
    $('#timeZones').select2();
    $('#currency').select2();
    $('.applies_to').select2();
    $('#collaborators').select2();
});

$(document).ready(function() {
    // Initialize Select2 on modal show
    $('#emailInvoiceModal').on('shown.bs.modal', function () {
        $('#emailRecipient').select2({
            dropdownParent: $('#emailInvoiceModal'),  // Ensures the dropdown is inside the modal
            placeholder: 'Select recipients',
            allowClear: true,
            width: '100%'
        });
    });

    $('#addTicketModal').on('shown.bs.modal', function () {
        $('#cc').select2({
            dropdownParent: $('#addTicketModal'),  // Ensures the dropdown is inside the modal
            placeholder: 'Select recipients',
            allowClear: true,
            width: '100%'
        });
    });
});
