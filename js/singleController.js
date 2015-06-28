app.factory('session', ['$http', function ($http) {
    this.id = localStorage.getItem("sessionid");

    console.log(this.id);
    if (this.id == undefined) {
        //window.location.href = "index.html#/login"
    }

    this.link = "http://172.28.116.110/api/GetClasses/?sessionid=" + this.id;
    return $http.get(this.link)
        .success(function (info) {
            // alert(info);

            console.log('sessionid factory success');
            return info;
        })
        .error(function (err) {
            console.log(err);
            return err;
        });

}]);


app.controller('singleController', ['$scope', 'pools', 'session', function ($scope, pools, session) {
    pools.success(function (data) {
        session.success(function (info) {
            console.log('session status ' + info.status);

            $scope.classes = info.data.classes;
        });
        //$scope.sessionID = data.data.sessionID;
    });
}]);