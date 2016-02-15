<?php
// Variables used in this script:
//   $summary     - text title of the event
//   $datestart   - the starting date (in seconds since unix epoch)
//   $dateend     - the ending date (in seconds since unix epoch)
//   $address     - the event's address
//   $uri         - the URL of the event (add http://)
//   $description - text description of the event
//   $filename    - the name of this file for saving (e.g. my-event-name.ics)
//
// Notes:
//  - the UID should be unique to the event, so in this case I'm just using
//    uniqid to create a uid, but you could do whatever you'd like.
//
//  - iCal requires a date format of "yyyymmddThhiissZ". The "T" and "Z"
//    characters are not placeholders, just plain ol' characters. The "T"
//    character acts as a delimeter between the date (yyyymmdd) and the time
//    (hhiiss), and the "Z" states that the date is in UTC time. Note that if
//    you don't want to use UTC time, you must prepend your date-time values
//    with a TZID property. See RFC 5545 section 3.3.5
//
//  - The Content-Disposition: attachment; header tells the browser to save/open
//    the file. The filename param sets the name of the file, so you could set
//    it as "my-event-name.ics" or something similar.
//
//  - Read up on RFC 5545, the iCalendar specification. There is a lot of helpful
//    info in there, such as formatting rules. There are also many more options
//    to set, including alarms, invitees, busy status, etc.
//
//      https://www.ietf.org/rfc/rfc5545.txt
// // 1. Set the correct headers for this file/*
// header('Content-type: text/calendar; charset=utf-8');
// header('Content-Disposition: attachment; filename='.$filename);*/
// 2. Define helper functions
// Converts a unix timestamp to an ics-friendly format
// NOTE: "Z" means that this timestamp is a UTC timestamp. If you need
// to set a locale, remove the "\Z" and modify DTEND, DTSTAMP and DTSTART
// with TZID properties (see RFC 5545 section 3.3.5 for info)
//
// Also note that we are using "H" instead of "g" because iCalendar's Time format
// requires 24-hour time (see RFC 5545 section 3.3.12 for info).
function dateToCal($timestamp) {
	return date('Ymd\THis\Z', $timestamp);
}

function generateBase64Ics($datestart, $dateend, $attendeeName,
	$attendeeEmail, $summary, $uid){

	$ics = 'BEGIN:VCALENDAR
PRODID:-//Google Inc//Google Calendar 70.9054//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:'.dateToCal($datestart).'
DTEND:'.dateToCal($dateend).'
DTSTAMP:'.dateToCal(time()).'
ORGANIZER;CN=Toolwatch:mailto:hello@toolwatch.com
UID:'.$uid.'
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=
 TRUE;CN='.$attendeeName.';X-NUM-GUESTS=0:mailto:'.$attendeeEmail.'
CREATED:'.dateToCal(time()).'
DESCRIPTION:'.$summary.'
LAST-MODIFIED:'.dateToCal(time()).'
LOCATION:https://toolwatch.io
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:'.$summary.'
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR';

	return base64_encode($ics);
}
// 3. Echo out the ics file's contents
?>
