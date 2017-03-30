<?php
$usesCalendar = 1;
$usesDatatables = 1; 
include('header.php'); 
?>

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="bookingsctl">

<h1>Bookings</h1>

<div class="form-inline">
	<div class="form-group">
		<label for="start" class="control-label">Start</label>
		<input moment-picker="startDate"
		  class="form-control"
	     locale="en-gb"
	     format="LL"
	     autoclose="true"
	     today="true"
	     start-view="month"
	     ng-model="startDate"
	     change="newDate()">
	</div>
	<div class="form-group">
		<label for="end" class="control-label">End</label>
		<input moment-picker="endDate"
		  class="form-control"
	     locale="en-gb"
	     format="LL"
	     autoclose="true"
	     today="true"
	     start-view="month"
	     ng-model="endDate"
	     change="newDate()">
	</div>
</div>
<br/>

<table id="table" class="table table-hover" datatable="ng" dt-options="dtOptions" dt-column-defs="dtColumnDefs">
	<thead>
	<tr>
		<th>Date</th>
		<th>Time</th>
		<th></th>
		<th>Room</th>
		<th>Title</th>
		<th>Booker</th>
		<th></th>
	</tr>
	</thead>
	
	<tbody>
	
	<tr ng-repeat="booking in bookings track by $index">
		<td>{{displayDate(booking)}}</td>
		<td>{{displayTime(booking)}}</td>
		<td>{{booking.Date}}</td>
		<td>{{booking.RoomName}}</td>
		<td><a ng-click="displayBooking(booking.Id)" role="button">{{booking.Title}}</a><span ng-show="booking.Provisional==1"><br/>(Provisional)</span></td>
		<td><a ng-click="displayBooker(booking.Id_Booker)" role="button">{{booking.BookerName}}</a></td>
		<td><button class="btn-xs" ng-click="deleteBooking(booking.Id, booking.Title, $index)">Delete</button></td>
	</tr>
	</tbody>
</table>
<button ng-click="displayBooking(0)">Create New Booking</button>



</div>

<script>
var app = angular.module("bookings", ["datatables", "moment-picker"]);
app.controller("bookingsctl", function($scope, $http, $window) {
	
	$scope.dtOptions = {
		'language': {
			'emptyTable': 'No bookings in the date range'
		}
	};
        
	$scope.dtColumnDefs = [
		{ 'orderData': [2], 'targets': [0]},
		{ 'sortable': false, 'targets': [6, 1]},
		{ 'visible': false, 'targets': [2]}
	];
	
	$scope.disableNewDate = false;	
	
	$http.get("getdata.php?type=session")
		.then(function (response) {
			$scope.disableNewDate = true;
			$scope.startDate = moment(response.data.StartDate);
			$scope.endDate = moment(response.data.EndDate);
			$scope.request();
		});
	
	makeUrl = function() {
		return "getdata.php?type=bookings&start=" + $scope.startDate.format('DD-MM-YYYY')
			+ "&end=" + $scope.endDate.format('DD-MM-YYYY');
	}	
		
	$scope.request = function () {
		$http.get(makeUrl())
			.then(function(response) {
				$scope.bookings = response.data;
				$scope.disableNewDate = false;
			});
	};
	
	$scope.newDate = function () {
		if ($scope.disableNewDate) return;
		$http.post("update_session.php?StartDate=" + $scope.startDate.format('YYYY-MM-DD')
				+ "&EndDate=" + $scope.endDate.format('YYYY-MM-DD'))
			.then(function (response) {
				$scope.request();
			});
	};
	
	$scope.displayDate = function (booking) {
		return moment(booking.Date).format('ddd D MMM YYYY');
	};
	
	$scope.displayTime = function(booking) {
		var start = moment({hour: booking.Start});
		var end = moment({hour: booking.Start + booking.Duration});
		return start.format("h A") + "-" + end.format("h A");
	};
	
	$scope.displayBooking = function(bookingId) {
		$window.location.href = "booking.php?id=" + bookingId;
	};
	
	$scope.displayBooker = function (bookerId) {
		$window.location.href = "booker.php?id=" + bookerId;
	};
	
	$scope.deleteBooking = function(bookingId, title, ix) {
		if (!confirm("Delete the booking for '" + title + "'?")) return;
		$http.post("update_booking.php?action=delete", { 'Id_Booking': bookingId })
			.then(function(response) {
				$scope.bookings.splice(ix, 1);
			});
	};
		
});

</script>


<?php include('footer.php'); ?>