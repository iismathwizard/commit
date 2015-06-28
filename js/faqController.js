app.controller('faqController', ['$scope', function ($scope) {
    $scope.questions = [
        {
            question: "What did you use to make this?",
            answer: "PHP, AngularJS, iOS, Sendgrid, and Braintree"    
        },
        {
            question: "Another question?",
            answer: "An answer!"
        },
        {
            question: "????",
            answer: "!!!"
        }
    ]
    ;
}]);