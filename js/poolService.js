app.factory('pools', ['$http', function ($http) {
    this.getLink = localStorage.getItem("getLink");

    return $http.get(this.getLink)
        .success(function (data) {
            localStorage.setItem("sessionid", data.data.sessionid);
            return data;
        })
        .error(function (err) {
            console.log(err);
            //localStorage.removeItem("getLink");
            //window.location.href = "index.html#/login"
            return err;
        });    

}]);


function getValues() {
    try {
        this.email = document.getElementById('inputEmail').value;
        this.password = document.getElementById('inputPassword').value;
        this.link = "http://172.28.116.110/api/Login/?email=" + this.email + "&password=" + this.password;
        var getLink = this.link;
        localStorage.setItem("getLink", getLink);

    } catch (err) {
        console.log('err');
    }
}

function getValues2() {
    try {
        this.email = document.getElementById('inputEmail').value;
        this.password = document.getElementById('inputPassword').value;
        this.acct = "http://172.28.116.110/api/CreateAccount/?email=" + this.email + "&password=" + this.password;
        localStorage.setItem("newAcct", this.acct);
            window.location.href = "dashboard.html"
    } catch (err) {
        console.log('sign up error');
    }
}

app.factory('newAcct', ['$http', function ($http) {
    this.acct = localStorage.getItem("newAcct");
    return $http.get(this.acct)
        .success(function (data) {
            localStorage.setItem("sessionid", data.data.sessionid);
            return data;
        })
        .error(function (err) {
            console.log(err);
            return err;
        });
}]);
