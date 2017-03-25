<?php 
$usesCalendar = 1;
include('header.php');
?>

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="bookingctl">

<h1>{{Heading}}</h1>

<form class="form-horizontal" ng-submit="save()">
	<div class="form-group">
		<label for="Provisional" class="col-sm-2 control-label">Provisional</label>
		<div class="col-sm-10">
			<input type="checkbox" ng-model="booking.Provisional">
		</div>
	</div>
	<div class="form-group">
		<label for="Title" class="col-sm-2 control-label">Title</label>
		<div class="col-sm-10">
			<input class="form-control" type="text" ng-model="booking.Title" required>
		</div>
	</div>
	<div class="form-group">
		<label for="booking.Id_Booker" class="col-sm-2 control-label">Booker</label>
		<div class="col-sm-10">
			<select class="form-control" ng-model="booking.Id_Booker" ng-hide="UseNewBooker" ng-required="!UseNewBooker">
				<option></option>
				<option ng_repeat="booker in bookers" value="{{booker.Id_Booker}}">{{booker.Name}}</option>
			</select>
			<input class="form-control" type="text" ng-model="booking.NewBooker" ng-show="UseNewBooker" ng-required="UseNewBooker">
			<button type="button" ng-show="ShowNewBooker" ng-click="newBookerClick()">New</button>
		</div>
	</div>
	<div class="form-group">
		<label for="booking.Id_Room" class="col-sm-2 control-label">Room</label>
		<div class="col-sm-10">
			<select class="form-control" ng-model="booking.Id_Room" ng-change="newRoom(booking.Id_Room)" required>
				<option></option>
				<option ng-repeat="room in rooms" value="{{room.Id_Room}}">{{room.Name}}</option>
			</select>
			<span ng-repeat="fac in facilities">
				<br/><input type="checkbox" ng-change="facChanged(fac.Id_Facility, fac.checked)" ng-model="fac.checked">{{fac.Name}}
			</span>
		</div>
	</div>
	<div class="form-group">
		<label for="Date" class="col-sm-2 control-label">Date</label>
		<div class="col-sm-10">
			<input moment-picker="booking.Date"
				class="form-control"
				locale="en-gb"
		     	format="LL"
		     	autoclose="true"
		     	today="true"
		     	start-view="month"
		     	ng-model="booking.Date"
		     	required>
		</div>
	</div>
	<div class="form-group" ng-show="booking.Id_Booking == 0">
		<label for="time-band" class="col-sm-2 control-label"></label>
		<div class="col-sm-2">
			<input type="radio" ng-model="timeband" value="0" ng-change="setTime()">Morning 
		</div>
		<div class="col-sm-2">
			<input type="radio" ng-model="timeband" value="1" ng-change="setTime()">Afternoon 
		</div>
		<div class="col-sm-2">
			<input type="radio" ng-model="timeband" value="2" ng-change="setTime()">Evening
		</div>
	</div>
	<div class="form-group">
		<label for="booking.Start" class="col-sm-2 control-label">Start Time</label>
		<div class="col-sm-2">
			<select class="form-control" ng-model="booking.Start" ng-options="x.hour as x.display for x in StartTimes" ng-change="checkDuration()">
			</select>
		</div>
		<label for="booking.Duration" class="col-sm-2 control-label">Duration (Hours)</label>
		<div class="col-sm-2">
			<select class="form-control" ng-model="booking.Duration" ng-options="x for x in Durations">
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="booking.Notes" class="col-sm-2 control-label">Notes</label>
		<div class="col-sm-10">
			<textarea class="form-control vresize" style="resize:vertical" rows="8" ng-model="booking.Notes"></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-10 col-sm-offset-2">
			<input class="btn btn-primary" type="submit" value="{{btn}}">
			<button type="button" ng-click="delete()">Delete</button>
		</div>
	</div>

</form>

</div>


<script>
var app = angular.module("bookings", ["moment-picker"]);
app.controller("bookingctl", function($scope, $http, $window) {
	
	$scope.newRoom = function (roomId) {
		var room = null;
		for (var i = 0; i < $scope.rooms.length; i++) {
			if ($scope.rooms[i].Id_Room == roomId) {
				room = $scope.rooms[i];
			}
		};
		if (room == null) return new Array();
		var facilities = new Array();
		for (var i = 0; i < room.facilities.length; i++) {
			var f = room.facilities[i];
			if (f.Used == 1) {
				var fac = new Object();
				fac.Id_Facility = f.Id_Facility;
				fac.Name = f.Name;
				fac.checked = $.inArray(f.Id_Facility, $scope.booking.facilities) >= 0; 
				facilities.push(fac);
			}
		}
		$scope.facilities = facilities;
	};
	
	$scope.facChanged = function (facId, sel) {
		var currsel = $.inArray(facId, $scope.booking.facilities) >= 0; 
		if (!currsel && sel) {
			facilities = $scope.booking.facilities.push(facId);
		} else if (currsel && !sel) {
			$scope.booking.facilities = $.grep($scope.booking.facilities, function(value) {
				return value != facId;
			});
		}
	}
	
	var id = querystring.parse()['id'];
	if (id == null) id = 0;
	var date = querystring.parse()['date'];
	var time = querystring.parse()['time'];
	$scope.facilities = [];
	$scope.ShowNewBooker = id == 0;
	$scope.UseNewBooker = false;
	$http.get("getdata.php?type=booking&id=" + id)
		.then(function (response) {
			$scope.booking = response.data.booking;
			
			$scope.booking.Id_Booker = $scope.booking.Id_Booker == 0 ? "" : $scope.booking.Id_Booker.toString(); // For the Select
			$scope.booking.Id_Room = $scope.booking.Id_Room == 0 ? "" : $scope.booking.Id_Room.toString(); // For the Select
			if (id == 0 && date != null) {
				$scope.booking.Date = moment(date);
			} else {
				$scope.booking.Date = moment($scope.booking.Date);
			}
			if (id == 0 && time != null) {
				$scope.booking.Start = parseInt(time);
			}
			
			$scope.booking.Provisional = $scope.booking.Provisional == 1;

			$scope.rooms = response.data.rooms;
			$scope.bookers = response.data.bookers;
			$scope.timebands = response.data.timebands;
			
			$scope.Heading = id == 0 ? "Create new Booking" : "Edit Booking";
			$scope.btn = id == 0 ? "Create" : "Update";
			
			$scope.StartTimes = [];
			for (var i = 9; i < 24; i++) {
				var t = { hour: i, display: moment({hour: i}).format('h A') };
				$scope.StartTimes.push(t);
			}
			
			$scope.Durations = [1, 2, 3, 4, 5, 6];
			
			$scope.newRoom($scope.booking.Id_Room);
			
			if (id == 0) {
				$scope.timeband = "2"; // Default to evening times
				$scope.setTime();
			}
		});
		
	$scope.newBookerClick = function () {
		$scope.ShowNewBooker = false;
		$scope.UseNewBooker = true;
	};
		
	$scope.save = function () {
		$http.post("update_booking.php?action=update", $scope.booking)
			.then(function(response) {
				$window.location.href = "bookings.php";
			});
	};
	
	$scope.delete = function () {
		if ($window.confirm('Are you sure you want to delete this booking?')) {
			$http.post("update_booking.php?action=delete", { 'Id_Booking': $scope.booking.Id_Booking })
				.then(function(response) {
					$window.location.href = "bookings.php";
				});
		}
	};
	
	$scope.setTime = function () {
		$scope.booking.Start = $scope.timebands[$scope.timeband][0];
		$scope.booking.Duration = $scope.timebands[$scope.timeband][1];
	};
	
	$scope.checkDuration = function () {
		if ($scope.booking.Start + $scope.booking.Duration > 24) {
			$scope.booking.Duration = 24 - $scope.booking.Start;
		}
	};
		
});
</script>

<?php include('footer.php'); ?>