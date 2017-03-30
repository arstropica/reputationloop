// Define Application Module
var app = angular.module('RLService', [ 'ngRoute', 'ngResource', 'ngSanitize', 'ngAnimate', 'ui.bootstrap', 'ui.bootstrap-slider' ]);

// Bypass TWIG / Angular Tag Conflict
app.config(function($interpolateProvider) {
	$interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

// ngRoute path Config
app.config([ '$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	// configure the routing rules here
	$routeProvider.when('/', {
		controller : 'BusinessShowCtrl',
		templateUrl : 'main'
	}).when('/:id', {
		controller : 'BusinessShowCtrl',
		templateUrl : 'main'
	});
	// use the HTML5 History API
	$locationProvider.html5Mode(true);
} ]);

// ngResource Model Config
app.factory('Business', function($resource) {
	return $resource('/api/get/:id');
});

// ngResource Model / Method Config
app.factory('Reviews', function($resource) {
	return $resource('/api/reviews/get/:id', null, {
		get : {
			method : 'GET',
			params : {
				id : '@id'
			},
			url : '/api/reviews/get/:id'
		}
	});
});

/**
 * Business Listing Controller (not implemented)
 */
app.controller("IndexCtrl", function($scope, Business) {
	// TODO: Listing multuple businesses ...
});

/**
 * Business Info Controller
 */
app.controller("BusinessShowCtrl", [ '$scope', '$routeParams', '$sce', 'Business', function($scope, $routeParams, $sce, Business) {

	$scope.id = $routeParams.id || 1;

	// Default Model
	$scope.businessInfo = {
		"title" : "",
		"data" : {
			"business_name" : "",
			"business_address" : "",
			"business_phone" : "",
			"total_rating" : {
				"total_avg_rating" : "0",
				"total_no_of_reviews" : 0
			},
			"external_url" : "",
			"external_page_url" : ""
		},
		"rating" : 0,
		"ratings" : []
	};
	Business.get({
		id : $scope.id
	}, function(data) {
		if (typeof data == 'object') {
			if (data.data !== undefined) {
				if (data.data.business_address !== undefined) {
					$scope.businessInfo = data;
					$scope.businessInfo.data.business_address = $sce.trustAsHtml($scope.businessInfo.data.business_address);
					$scope.businessInfo.rating = parseFloat($scope.businessInfo.data.total_rating.total_avg_rating);
					var c = {
						'0' : 'danger',
						'15' : 'info',
						'25' : 'warning',
						'50' : 'success'
					};
					if ($.isArray($scope.businessInfo.ratings)) {
						$scope.businessInfo.ratings.forEach(function(r) {
							for (threshold in c) {
								if (parseFloat(r['percent']) >= parseFloat(threshold)) {
									r['class'] = c[threshold];
								}
							}
							if (r['class'] === undefined) {
								r['class'] = 'danger';
							}
						});
					}
				}
			}
		}

	});
} ]);

/**
 * Business Review Controller
 */
app.controller("BusinessReviewsCtrl", [ '$scope', '$routeParams', '$sce', 'Reviews', function($scope, $routeParams, $sce, Reviews) {

	var br = this;
	br.default_filter = {
		0 : 1,
		1 : 1,
		2 : 1,
		rating : [ 0, 5 ],
	};
	br.default_pager = {
		current : 1,
		total : 1,
		items : {
			max : 10,
			count : 0,
		}
	};
	$scope.id = $routeParams.id || 1;
	$scope.reviews = [];
	$scope.filter = br.default_filter;
	$scope.pager = br.default_pager;
	$scope.count = 0;
	$scope.slider = {
		max : 5,
		step : 1,
		min : 1
	};

	// Description : Triggered while displaying Review date.
	$scope.formatDate = function(date) {
		var dateOut = new Date(date);
		return dateOut;
	};

	// Description : Page Change Handler.
	$scope.setPage = function(pageNo) {
		$scope.pager.current = pageNo;
	};

	// Description: Debug Function for displaying scope state.
	$scope.log = function() {
		console.dir($scope);
	};

	// Description: ng-repeat Filter Handler
	$scope.handleFilter = function(review) {
		var match = true;
		for ( var f in $scope.filter) {
			switch (f) {
			case 'rating':
				var r = $scope.filter[f];
				if ($.isArray(r) && r.length == 2) {
					var min = r[0];
					var max = r[1];
					var rating = parseInt(review.rating, 10);
					if (min <= rating && max >= rating) {
					} else {
						match = false;
					}
				}
				break;
			default:
				if ($scope.filter[f] == 0 && review.review_from == f) {
					match = false;
					break;
				}
				break;
			}
		}
		return match;
	};

	// Description: ngResource Handler for GET
	$scope.get = function() {
		var params = {
			id : $scope.id
		};

		Reviews.get(params).$promise.then(function(data) {
			if (data.data !== undefined) {
				data.data.forEach(function(i, v) {
					v.idx = i;
				});
				$scope.reviews = data.data;
			}
			if (data.count !== undefined) {
				$scope.count = data.count;
				$scope.pager.items.count = data.count;
				$scope.pager.total = Math.ceil(data.count / $scope.pager.max);
			}
		});
	};

	$scope.get();
} ]);

// Description: Array Range Utility Function
app.filter('range', function() {
	return function(input, total) {
		total = parseInt(total);

		for (var i = 0; i < total; i++) {
			input.push(i);
		}

		return input;
	};
});

// Description: ngRepeat Pagination Handler
app.filter('paginate', function() {
	return function(data, start) {
		return data.slice(start);
	}
});

// Description: Source Filter Button Directive
app.directive("ctoggle", function() {
	// toggles state of ng-model when clicking
	return {
		restrict : 'A',
		require : '^?ngModel',
		link : function(scope, element, attr, ngModel) {
			element.bind('click', function() {
				var toggle = ngModel.$viewValue ? 0 : 1;
				element.toggleClass('active', toggle);
				element.toggleClass('btn-success', toggle);
				element.toggleClass('btn-default', !toggle);
				scope.$apply(function() {
					ngModel.$setViewValue(toggle);
				});
			});
		}
	}
});

// Description: Sanitize Directive
app.filter('sanitize', function() {
	return function(text) {
		var clean = "";
	    for (var i=0; i<text.length; i++) {
	        if (text.charCodeAt(i) <= 127) {
	        	clean += text.charAt(i);
	        }
	    }
	    return clean;
	};
});


// Description: Read More Truncate Directive
app.filter('truncate', function() {
	return function(text, length, end) {
		if (isNaN(length)) {
			length = 10;
		}
		
		if (end === undefined) {
			end = '...';
		}
		
		if (text === undefined || text.length <= length || text.length - end.length <= length) {
			return text;
		} else {
			return String(text).substring(0, length - end.length) + end;
		}
	};
});

// Description: Read More HREF Directive
app.directive('readMore', function($filter) {
	return {
		restrict : 'A',
		scope : {
			text : '=readMore',
			target : '@readMoreTarget',
			labelExpand : '@readMoreLabelExpand',
			labelCollapse : '@readMoreLabelCollapse',
			limit : '@readMoreLimit'
		},
		transclude : true,
		template : '<span ng-transclude ng-bind-html="desc"></span> <a href="javascript:void();" ng-attr-data-target="#{{target}}" class="more" data-toggle="modal" ng-show="enable()" ng-bind="label"></a>',
		link : function(scope /* , element, attrs */) {

			scope.text = scope.desc = $filter('sanitize')(scope.text);
			var originalText = scope.text;
			
			scope.label = '[ ' + scope.labelExpand + ' ]';
			

			scope.enable = function() {
				return scope.text.length >= scope.limit
			};

			scope.$watch('expanded', function(expandedNew) {
				if (expandedNew) {
					scope.desc = originalText;
					scope.label = '[ ' + scope.labelCollapse + ' ]';
				} else {
					scope.desc = $filter('truncate')(originalText, scope.limit, '...');
					scope.label = '[ ' + scope.labelExpand + ' ]';
				}
			});

			scope.toggleReadMore = function() {
				scope.expanded = !scope.expanded;
			};

		}
	};
});

app.directive('modal', function () {
    return {
        restrict: 'EA',
        scope: {
            title: '@modalTitle',
            header: '@modalHeader',
            body: '@modalBody',
            footer: '@modalFooter',
            callbackbuttonleft: '&ngClickLeftButton',
            handler: '@lolo'
        },
        template: "\
		<div ng-attr-id='{{handler}}' class='modal fade'>\
		    <div class='modal-dialog'>\
		        <div class='modal-content'>\
		            <div class='modal-header'>\
		                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>\
		                <h4 class='modal-title' ng-bind-html='header'></h4>\
		            </div>\
		            <div class='modal-body'>\
		\
		                <p class='text-warning' ng-bind-html='body'></p>\
		\
		            </div>\
		            <div class='modal-footer'>\
		\
		                <p class='text-left' ng-bind-html='footer'></p>\
		\
		                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>\
		\
		            </div>\
		        </div>\
		    </div>\
		</div>",
        transclude: true,
        controller: function ($scope) {
            $scope.handler = 'pop'; 
        },
    };
});

