describe('The Role resource factory', function () {
	var $httpBackend;

	beforeEach(module('dias.api'));

    // mock URL constant which is set inline in the base template
    beforeEach(function() {
        module(function($provide) {
            $provide.constant('URL', '');
        });
    });

	beforeEach(inject(function($injector) {
		var role = {
			id: 1,
			name: "admin"
		};

		// Set up the mock http service responses
		$httpBackend = $injector.get('$httpBackend');

		$httpBackend.when('GET', '/api/v1/roles')
		            .respond([role]);

		$httpBackend.when('GET', '/api/v1/roles/1')
		            .respond(role);
	}));

	afterEach(function() {
		$httpBackend.flush();
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should query roles', inject(function (Role) {
        $httpBackend.expectGET('/api/v1/roles');
        var roles = Role.query(function () {
            var role = roles[0];
            expect(role instanceof Role).toBe(true);
            expect(role.name).toEqual('admin');
        });
	}));

	it('should show a role', inject(function (Role) {
		$httpBackend.expectGET('/api/v1/roles/1');
		var role = Role.get({id: 1}, function () {
			expect(role.name).toEqual('admin');
		});
	}));
});
