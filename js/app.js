var app = angular.module('commitApp', ['ngRoute']);

app.config(function ($routeProvider, $httpProvider) {
    $routeProvider
    .when('/', {
        templateUrl: "directives/front.html"
    })
    .when('/login', {
        templateUrl: "directives/login.html"
    })
    .when('/signup', {
        templateUrl: "directives/signup.html"
    })
    .when('/about', {
        templateUrl: "directives/about.html"
    })
    .when('/faq', {
        controller: "faqController",
        templateUrl: "directives/faq.html"
    })
    .when('/contact', {
        templateUrl: "directives/contact.html"
    })
    .when('/singleClass', {
        controller: "singleController",
        templateUrl: "directives/singleClass.html"
    })
    .when('/singleClass/:id', {
        controller: "detailController",
        templateUrl: "directives/classDetails.html"
    })
    .when('/create', {
        controller: "createController",
        templateUrl: "directives/create.html"
    })
    .otherwise({
        redirectTo: '/'
    });

});


function gotoBottom() {
    window.scrollTo(0, document.body.scrollHeight);

    document.location.href = "#/";

    document.getElementById("theThing").style.color = "#00c0da";
    document.getElementById("theThing").style.animation = "learning 2s ease infinite";
}

function gotoSignup() {
    window.scrollTo(0, document.body.scrollHeight);

    document.location.href = "#/signup";
}

function gotoLogin() {
    window.scrollTo(0, document.body.scrollHeight);

    document.location.href = "#/login";
}

function gotoDash() {
    document.location.href = "#/singleClass";
}

function clearSession() {
    localStorage.removeItem("getLink");
    localStorage.removeItem("sessionid");
}