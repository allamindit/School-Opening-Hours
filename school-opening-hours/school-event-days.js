jQuery(document).ready(function($) {
    // Datepicker for event dates
    $(document).on('focus', '.event-date', function() {
        $(this).datepicker({ dateFormat: 'yy-mm-dd' });
    });

    // Add new event day
    $('#add-event-day').click(function() {
        let newRow = `<div class="event-day-row">
                        <input type="text" class="event-name" placeholder="Event Name" />
                        <input type="text" class="event-date" placeholder="Event Date" />
                        <button type="button" class="remove-event-day">Remove -</button>
                      </div>`;
        $('#event-days-container').append(newRow);
    });

    // Remove event day
    $(document).on('click', '.remove-event-day', function() {
        $(this).closest('.event-day-row').remove();
    });

    // Update hidden input value on form submission
    $('form').on('submit', function() {
        let eventDays = [];
        $('#event-days-container .event-day-row').each(function() {
            let name = $(this).find('.event-name').val();
            let date = $(this).find('.event-date').val();
            if (name && date) {
                eventDays.push({ name: name, date: date });
            }
        });
        $('#school_event_days_input').val(JSON.stringify(eventDays));
    });
});
