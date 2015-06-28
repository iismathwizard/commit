app.controller('detailController', ['$scope', 'session', '$routeParams', function ($scope, session, $routeParams) {
    session.success(function (info) {
        $scope.class = info.data.classes[$routeParams.id];
    });
    // get something
}]);