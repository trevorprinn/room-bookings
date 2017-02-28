<?php
include ('header.php'); 
?>

<link rel="stylesheet" href="split.css">

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="mergectl">

<h1>Merge Bookers</h1>

<section class="intro">
<div class="col-lg-6 col-sm-6 left">
	<input type="text" ng-model="search">
	<p ng-repeat="booker in bookers | filter: search">
		<input type="checkbox" ng-model="booker.checked" ng-change="checkChanged()">
		{{booker.Name}}
	</p>
</div>
<div class="col-lg-6 col-sm-6 right">
	<form class="form-horizontal" ng-submit="submit()">
		<div class="form-group">
			<label for="merged.Name" class="col-lg-2 col-sm-2 control-label">Name</label>
			<select ng-model="merged.Name" class="col-lg-10 col-sm-10">
				<option ng-repeat="booker in selBookers">{{booker.Name}}</option>
			</select>
		</div>
		<div class="form-group">
			<label for="merged.Address" class="col-lg-2 col-sm-2 control-label">Address</label>	
			<select ng-model="merged.Address" class="col-lg-10 col-sm-10">
				<option ng-repeat="booker in selBookers">{{booker.Address}}</option>
			</select>
		</div>
		<div class="form-group">
			<label for="merged.Phone" class="col-lg-2 col-sm-2 control-label">Phone</label>
			<select ng-model="merged.Phone" class="col-lg-10 col-sm-10">
				<option ng-repeat="booker in selBookers">{{booker.Phone}}</option>
			</select>
		</div>
		<div class="form-group">
			<label for="merged.Email" class="col-lg-2 col-sm-2 control-label">Email</label>
			<select ng-model="merged.Email" class="col-lg-10 col-sm-10">
				<option ng-repeat="booker in selBookers">{{booker.Email}}</option>
			</select>
		</div>
		<div class="form-group">
			<label for="Notes" class="col-sm-2 control-label">Notes</label>
			<div class="col-sm-10">
				<textarea class="form-control vresize"  class="col-lg-10 col-sm-10" style="resize:vertical" rows="8" ng-model="merged.Notes"></textarea>
			</div>
		</div>
		<input type="submit" value="Merge">
		
	</form>
</div>
</section>

</div>

<script>
var app = angular.module("bookings", []);
app.controller("mergectl", function ($scope, $http) {
	$scope.request = function () {
		$scope.merged = { Name: "", Address: "", Phone: "", Email: "", Notes: "" };
		$http.get("getdata.php?type=bookers")
			.then(function (response) {
				$scope.bookers = response.data;
				for (var i = 0; i < $scope.bookers.length; i++) $scope.bookers[i].checked = false;
			});
		}
		
	$scope.checkChanged = function () {
		$scope.selBookers = [];
		$scope.merged = { Name: "", Address: "", Phone: "", Email: "", Notes: "" };
		$scope.mergeIds = []; 
		for (var i = 0; i < $scope.bookers.length; i++) {
			var booker = $scope.bookers[i];
			if (booker.checked) {
				$scope.selBookers.push(booker);
				if (booker.Notes != null && booker.Notes != "") {
					if ($scope.merged.Notes != "") $scope.merged.Notes += "\n";
					$scope.merged.Notes += booker.Notes;
				}
				$scope.mergeIds.push(booker.Id_Booker);
			}
		}
	};
	
	$scope.submit = function () {
		$http.post("update_merge_bookers.php", { ids: $scope.mergeIds, data: $scope.merged })
			.then(function (response) {
				$scope.result = response;
				if ($scope.result.data.success) {
					$scope.request();
				}
			}, function (response) {
				$scope.result = response;
			});
	};
	
	$scope.request();
	
});
</script>

<?php
include ('footer.php'); 
?>

