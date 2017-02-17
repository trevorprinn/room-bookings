<?php include('header.php') ?>

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="bookerctl">

<h1>{{Heading}}</h1>

<form class="form-horizontal" ng-submit="sendData()">
<div class="form-group">
	<label for="Name" class="col-sm-2 control-label">Name</label>
	<div class="col-sm-10">
		<input class="form-control" type="text" ng-model="booker.Name">
	</div>
</div>
<div class="form-group">
	<label for="Address" class="col-sm-2 control-label">Address</label>
	<div class="col-sm-10">
		<textarea class="form-control vresize" style="resize:vertical" rows="4" ng-model="booker.Address"></textarea>
	</div>
</div>
<div class="form-group">
	<label for="Phone" class="col-sm-2 control-label">Phone</label>
	<div class="col-sm-10">
		<input class="form-control" type="text" ng-model="booker.Phone">
	</div>
</div>
<div class="form-group">
	<label for="Email" class="col-sm-2 control-label">Email</label>
	<div class="col-sm-10">
		<input class="form-control" type="text" ng-model="booker.Email">
	</div>
</div>
<div class="form-group">
	<label for="Notes" class="col-sm-2 control-label">Notes</label>
	<div class="col-sm-10">
		<textarea class="form-control vresize" style="resize:vertical" rows="8" ng-model="booker.Notes"></textarea>
	</div>
</div>
<div class="form-group">
	<div class="col-sm-10 col-sm-offset-2">
		<button class="btn btn-primary" type="submit">{{submitCaption}}</button>
	</div>
</div>

</form>

</div>

<script>
var app = angular.module("bookings", []);
app.controller("bookerctl", function($scope, $http, $window) {
	var id = querystring.parse()['id'];
	$http.get("getdata.php?type=booker&id=" + id)
		.then(function(response) {
			$scope.booker = response.data;
			$scope.Heading = id == 0 ? "Create New Booker" : "Edit Booker";
			$scope.submitCaption = id == 0 ? "Create" : "Update";
		});
	
	$scope.sendData = function() {
		$http.post("update_booker.php", $scope.booker)
			.then(function(response) {
				$window.location.href = "bookers.php";
			});
	};
});

</script>

<?php include('footer.php') ?>