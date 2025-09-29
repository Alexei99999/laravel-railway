function fetchGoogleCalendarEvents(apiKey, calendarId) {
    return $.ajax({
        url: `https://www.googleapis.com/calendar/v3/calendars/${calendarId}/events?key=${apiKey}`,
        method: 'GET',
        success: function(data) {
            return data.items.map(event => ({
                title: event.summary,
                start: event.start.dateTime || event.start.date,
                end: event.end.dateTime || event.end.date,
                allDay: !event.start.dateTime
            }));
        },
        error: function() {
            console.error('Error fetching Google Calendar events');
            return [];
        }
    });
}
