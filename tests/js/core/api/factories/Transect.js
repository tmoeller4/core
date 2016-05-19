describe('The Transect resource factory', function () {
	var $httpBackend;

	beforeEach(module('dias.api'));

    // mock URL constant which is set inline in the base template
    beforeEach(function() {
        module(function($provide) {
            $provide.constant('URL', '');
        });
    });

	beforeEach(inject(function($injector) {
		var transect = {
			id: 1,
			name: "transect 1"
		};

		// Set up the mock http service responses
		$httpBackend = $injector.get('$httpBackend');

		$httpBackend.when('GET', '/api/v1/transects/1')
		            .respond(transect);

		$httpBackend.when('PUT', '/api/v1/transects/1')
		            .respond(200);
	}));

	afterEach(function() {
		$httpBackend.flush();
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should show a transect', inject(function (Transect) {
        $httpBackend.expectGET('/api/v1/transects/1');
        var transect = Transect.get({id: 1}, function () {
            expect(transect.name).toEqual('transect 1');
        });
	}));

	it('should update a transect', inject(function (Transect) {
		$httpBackend.expectPUT('/api/v1/transects/1',
			{id: 1, name: "my transect"}
		);
		var transect = Transect.get({id: 1}, function () {
			transect.name = "my transect";
			transect.$save();
		});

		$httpBackend.expectPUT('/api/v1/transects/1',
			{id: 1, name: "my transect"}
		);
		Transect.save({id: 1, name: "my transect"});
	}));
});
