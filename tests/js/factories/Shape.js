describe('The Shape resource factory', function () {
	var $httpBackend;

	beforeEach(module('dias.core'));

	beforeEach(inject(function($injector) {
		var shape = {
			id: 1,
			name: "point"
		};

		// Set up the mock http service responses
		$httpBackend = $injector.get('$httpBackend');

		$httpBackend.when('GET', '/api/v1/shapes')
		            .respond([shape]);
		
		$httpBackend.when('GET', '/api/v1/shapes/1')
		            .respond(shape);
	}));

	afterEach(function() {
		$httpBackend.verifyNoOutstandingExpectation();
		$httpBackend.verifyNoOutstandingRequest();
	});

	it('should query shapes', inject(function (Shape) {
		$httpBackend.expectGET('/api/v1/shapes');
		var shapes = Shape.query(function () {
			var shape = shapes[0];
			expect(shape instanceof Shape).toBe(true);
			expect(shape.name).toEqual('point');
		});
		$httpBackend.flush();
	}));

	it('should show a shape', inject(function (Shape) {
		$httpBackend.expectGET('/api/v1/shapes/1');
		var shape = Shape.get({id: 1}, function () {
			expect(shape.name).toEqual('point');
		});
		$httpBackend.flush();
	}));
});