<?php
$usesCalendar=1;
include('header.php');
?>

<script type='text/javascript'>
$(document).ready(function() {
	$('#calendar').fullCalendar({
		header: {
			left: 'title',
			center: '',
			right: 'today agendaWeek,month prev,next'
		},
		events: 'calbookings.php',
		dayClick: function (date, jsEvent, view) {
			var d = date.format('DD-MM-YYYY');
			var t = date.get('hour');
			var msg = "Create a new booking for "+d;
			if (t > 0) msg += " at " + date.format("kk:mm:ss");
			if (confirm(msg+"?")) {
				var url = "booking.php?date="+date.format('YYYY-MM-DD');
				if (t > 0) url += "&time="+t;
				location.href = url;
			}
		},
		minTime: "09:00:00",
		allDaySlot: false,
		slotDuration: "01:00:00"
	});
});
</script>


<div class="container-fluid">
<div id="calendar"></div>
</div>
<?php
include('footer.php');
?>