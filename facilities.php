<?php include('header.php'); ?>

<div ng-cloak class="container-fluid" ng-app="bookings" ng-controller="facs">

<h1>Facilities</h1>

<table class="table">
	<tr ng-repeat="fac in facilities track by $index">
		<td><input ng-model="fac.Name"></td>
		<td><button ng-click="update(fac.Id_Facility, fac.Name)">Save</button></td>
		<td><button ng-click="delete(fac.Id_Facility, $index)">Delete</button></td>
	</tr>
	<tr>
		<td><input ng-model="newName" placeholder="New Facility"></td>
		<td><button ng-click="addNew()">Add</button></td>
	</tr>
</table>
</div>


<script>
var app = angular.module("bookings", []);
app.controller("facs", function($scope, $http) {
	$http.get("getdata.php?type=facilities")
		.then(function(response) {
			$scope.facilities = response.data;
		});
	$scope.newName = "";	
	
	$scope.addNew = function() {
		if ($scope.newName == "") {
			alert("You need to enter a facility name first");
			return;
		};
		$http.post("update_facility.php?action=add", {
				'Name': $scope.newName
			})
			.then(function(response) {
				$scope.facilities.push(response.data);
				$scope.newName = "";
			});
	};
	$scope.update = function(id, name) {
		$http.post("update_facility.php?action=update", {
			'id': id,
			'Name': name
		}); 
	};
	$scope.delete = function(id, ix) {
		$http.post("update_facility.php?action=delete", {
			'id': id
		});
		$scope.facilities.splice(ix, 1);
	};
});

</script>


<?php include('footer.php'); ?>