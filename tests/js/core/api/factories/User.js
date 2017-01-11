describe('The User resource factory', function () {
   var $httpBackend;

   beforeEach(module('biigle.api'));

    // mock URL constant which is set inline in the base template
    beforeEach(function() {
        module(function($provide) {
            $provide.constant('URL', '');
        });
    });

   beforeEach(inject(function($injector) {
      var user = {id: 1, firstname: 'joe', lastname: 'user', role_id: 2};

      // Set up the mock http service responses
      $httpBackend = $injector.get('$httpBackend');

      $httpBackend.when('GET', '/api/v1/users/find/j')
                  .respond([user]);

      $httpBackend.when('GET', '/api/v1/users')
                  .respond([user]);

      $httpBackend.when('GET', '/api/v1/users/1')
                  .respond(user);

      $httpBackend.when('POST', '/api/v1/users')
                  .respond(user);

      $httpBackend.when('PUT', '/api/v1/users/1')
                  .respond(200);

      $httpBackend.when('DELETE', '/api/v1/users/1')
                  .respond(200);
   }));

   afterEach(function() {
      $httpBackend.flush();
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should query users', inject(function (User) {
        $httpBackend.expectGET('/api/v1/users');
        var users = User.query(function () {
            var user = users[0];
            expect(user instanceof User).toBe(true);
            expect(user.id).toEqual(1);
        });
   }));

   it('should show users', inject(function (User) {
      $httpBackend.expectGET('/api/v1/users/1');
      var user = User.get({id: 1}, function () {
         expect(user.firstname).toEqual('joe');
      });
   }));

   it('should add users', inject(function (User) {
      $httpBackend.expectPOST('/api/v1/users',
         {firstname: 'joe'}
      );
      var user = User.add({firstname: 'joe'}, function () {
         expect(user.id).toEqual(1);
      });
   }));

   it('should update users', inject(function (User) {
      $httpBackend.expectPUT('/api/v1/users/1', {
         id: 1, firstname: 'jack', lastname: 'user', role_id: 2
      });
      var user = User.get({id: 1}, function () {
         user.firstname = 'jack';
         user.$save();
      });
   }));

    it('should update users directly', inject(function (User) {
        $httpBackend.expectPUT('/api/v1/users/1', {
            id: 1, firstname: 'jack'
        });
        var user = User.save({id: 1, firstname: 'jack'}, function () {
            expect(user.firstname).toEqual('jack');
        });
    }));

   it('should destroy users', inject(function (User) {
      $httpBackend.expectDELETE('/api/v1/users/1');
      var user = User.get({id: 1}, function () {
         user.$delete();
      });
      $httpBackend.expectDELETE('/api/v1/users/1');
      User.delete({id: 1});
   }));

   it('should find users', inject(function (User) {
      $httpBackend.expectGET('/api/v1/users/find/j');
      var users = User.find({query: 'j' }, function () {
         var user = users[0];
         expect(user instanceof User).toBe(true);
         expect(user.id).toEqual(1);
      });
   }));

});
