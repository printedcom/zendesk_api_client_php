<?php

namespace Zendesk\API\MockTests;

use Zendesk\API\Resources\Users;

/**
 * Users test class
 */
class UsersTest extends BasicTest
{
    protected $number;

    public function testCreate()
    {
        $testUser = array(
          'id'          => '12345',
          'name'        => 'Roger Wilco',
          'email'       => 'roge@example.org',
          'role'        => 'agent',
          'verified'    => true,
          'external_id' => '3000'
        );

        $this->mockApiCall(
          'POST',
          'users.json',
          [ 'user' => $testUser ],
          [
            'statusCode' => 201,
            'bodyParams' => [ 'user' => $testUser ],
          ]
        );

        $user = $this->client->users()->create( $testUser );
        $this->httpMock->verify();

        $this->assertEquals( is_object( $user ), true, 'Should return an object' );
        $this->assertEquals( is_object( $user->user ), true, 'Should return an object called "user"' );
        $this->assertGreaterThan( 0, $user->user->id, 'Returns a non-numeric id for user' );
    }

    public function testDelete()
    {
        $this->mockApiCall( 'DELETE', 'users/12345.json', array() );
        $this->client->users( 12345 )->delete();
        $this->httpMock->verify();
    }

    public function testAll()
    {
        $this->mockApiCall( 'GET', 'users.json', array( 'users' => array( array( 'id' => 12345 ) ) ) );

        $users = $this->client->users()->findAll();
        $this->httpMock->verify();
        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true,
          'Should return an object containing an array called "users"' );

        $this->assertGreaterThan( 0, $users->users[0]->id, 'Returns a non-numeric id for requests[0]' );
    }

    public function testFind()
    {
        $this->mockApiCall(
          'GET',
          'users/12345.json',
          [ 'user' => [ 'id' => 12345 ] ]
        );

        $user = $this->client->user( 12345 )->find();
        $this->httpMock->verify();
        $this->assertEquals( is_object( $user ), true, 'Should return an object' );
        $this->assertEquals( is_object( $user->user ), true, 'Should return an object called "user"' );
        $this->assertGreaterThan( 0, $user->user->id, 'Returns a non-numeric id for user' );
    }

    public function testFindMultiple()
    {
        $findIds  = array( 12345, 80085 );
        $response = array(
          'users' => array(
            array( 'id' => $findIds[0] ),
            array( 'id' => $findIds[1] ),
          )
        );

        $this->mockApiCall(
          'GET',
          'users/show_many.json',
          $response,
          [ "queryParams" => array( 'ids' => implode( ",", [ $findIds[0], $findIds[1] ] ) ) ]
        );

        $users = $this->client->users( $findIds )->findMany();
        $this->httpMock->verify();
        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true, 'Should return an array called "users"' );
        $this->assertEquals( $users->users[0]->id, $findIds[0] );
        $this->assertEquals( $users->users[1]->id, $findIds[1] );
    }

    public function testShowManyUsingIds()
    {
        $findIds  = array( 12345, 80085 );
        $response = array(
          'users' => array(
            array( 'id' => $findIds[0] ),
            array( 'id' => $findIds[1] ),
          )
        );

        $this->mockApiCall(
          'GET',
          'users/show_many.json',
          $response,
          [ 'queryParams' => [ 'ids' => implode( ',', $findIds ) ] ]
        );

        $users = $this->client->users()->showMany( array( 'ids' => $findIds ) );
        $this->httpMock->verify();
        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true, 'Should return an array called "users"' );
        $this->assertEquals( is_object( $users->users[0] ), true,
          'Should return an object as first "users" array element' );
    }

    public function testShowManyUsingExternalIds()
    {
        $findIds  = [ 12345, 80085 ];
        $response = [
          'users' => [
            [ 'id' => $findIds[0] ],
            [ 'id' => $findIds[1] ],
          ]
        ];

        $this->mockApiCall(
          'GET',
          'users/show_many.json',
          $response,
          [ 'queryParams' => [ 'external_ids' => implode( ',', $findIds ) ] ]
        );

        $users = $this->client->users()->showMany( [ 'external_ids' => $findIds ] );
        $this->httpMock->verify();

        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true, 'Should return an array called "users"' );
        $this->assertEquals( is_object( $users->users[0] ), true,
          'Should return an object as first "users" array element' );
    }

    public function testRelated()
    {
        $this->mockApiCall(
          'GET',
          'users/12345/related.json',
          [ 'user_related' => [ 'requested_tickets' => 1 ] ]
          );

        $related = $this->client->user( 12345 )->related();
        $this->httpMock->verify();
        $this->assertEquals( is_object( $related ), true, 'Should return an object' );
        $this->assertEquals( is_object( $related->user_related ), true,
          'Should return an object called "user_related"' );
        $this->assertGreaterThan( 0, $related->user_related->requested_tickets,
          'Returns a non-numeric requested_tickets for user' );
    }

    public function testMerge()
    {
        $this->markTestSkipped( 'Need to refactor merge' );
        $this->mockApiCall( 'PUT', '/users/me/merge.json', array( 'user' => array( 'id' => 12345 ) ) );
        $this->client->user( 'me' )->merge( array( 'id' => 12345 ) );
    }

    public function testCreateMany()
    {
        $bodyParams = array(
          array(
            'name'     => 'Roger Wilco 3',
            'email'    => 'roge3@example.org',
            'verified' => true
          ),
          array(
            'name'     => 'Roger Wilco 4',
            'email'    => 'roge4@example.org',
            'verified' => true
          )
        );

        $this->mockApiCall(
          'POST',
          'users/create_many.json',
          [ 'job_status' => [ 'id' => 1 ] ],
          [ 'bodyParams' => [ 'users' => $bodyParams ] ]
        );

        $jobStatus = $this->client->users()->createMany( $bodyParams );
        $this->httpMock->verify();
        $this->assertEquals( is_object( $jobStatus ), true, 'Should return an object' );
        $this->assertEquals( is_object( $jobStatus->job_status ), true, 'Should return an object called "job_status"' );
        $this->assertGreaterThan( 0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]' );
    }

    public function testUpdate()
    {
        $this->markTestSkipped( 'Need to refactor update' );
        $this->mockApiCall( 'PUT', '/users/12345.json', array( 'user' => array() ) );
        $user = $this->client->user( 12345 )->update( array(
          'name' => 'Joe Soap'
        ) );
        $this->httpMock->verify();
    }

    public function testUpdateMany()
    {
        $updateIds     = array( 12345, 80085 );
        $requestParams = array(
          'ids'   => implode( ',', $updateIds ),
          'phone' => '1234567890'
        );
        $this->mockApiCall( 'PUT',
          'users/update_many.json',
          [ 'job_status' => [ 'id' => 1 ] ],
          [ 'bodyParams' => [ Users::OBJ_NAME => [ 'phone' => $requestParams['phone'] ] ] ]
        );

        $jobStatus = $this->client->users()->updateMany( $requestParams );
        $this->httpMock->verify();
        $this->assertEquals( is_object( $jobStatus ), true, 'Should return an array' );
        $this->assertEquals( is_object( $jobStatus->job_status ), true, 'Should return an object called "job_status"' );
        $this->assertGreaterThan( 0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]' );
    }

    public function testUpdateManyIndividualUsers()
    {
        $requestParams = [
          [
            'id'    => 12345,
            'phone' => '1234567890'
          ],
          [
            'id'    => 80085,
            'phone' => '0987654321'
          ]
        ];

        $this->mockApiCall(
          'PUT',
          'users/update_many.json',
          [ 'job_status' => [ 'id' => 1 ] ],
          [ 'bodyParams' => [ Users::OBJ_NAME_PLURAL => $requestParams ] ]
        );

        $jobStatus = $this->client->users()->updateManyIndividualUsers( $requestParams );
        $this->httpMock->verify();
        $this->assertEquals( is_object( $jobStatus ), true, 'Should return an array' );
        $this->assertEquals( is_object( $jobStatus->job_status ), true, 'Should return an object called "job_status"' );
        $this->assertGreaterThan( 0, $jobStatus->job_status->id, 'Returns a non-numeric id for users[0]' );
    }

    public function testSuspend()
    {
        $this->markTestSkipped( 'Need to refactor suspend' );
        $this->mockApiCall( 'PUT', '/users/12345.json', array( 'user' => array( 'id' => 12345 ) ) );
        $user = $this->client->user( 12345 )->suspend();
        $this->assertEquals( is_object( $user ), true, 'Should return an object' );
        $this->assertEquals( is_object( $user->user ), true, 'Should return an object called "user"' );
        $this->assertGreaterThan( 0, $user->user->id, 'Returns a non-numeric id for request' );
    }

    public function testSearch()
    {
        $this->markTestSkipped( 'Need to refactor search' );
        $this->mockApiCall( 'GET', '/users/search.json?query=Roger&',
          array( 'users' => array( array( 'id' => 12345 ) ) ) );
        $users = $this->client->users()->search( array( 'query' => 'Roger' ) );
        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true,
          'Should return an object containing an array called "users"' );
        $this->assertGreaterThan( 0, $users->users[0]->id, 'Returns a non-numeric id for user' );
    }

    /*
     * Needs an existed User with specified query 'name' keyword to run this function
     */
    public function testAutocomplete()
    {
        $this->markTestSkipped( 'Need to refactor autocomplete' );
        $this->mockApiCall( 'POST', '/users/autocomplete.json?name=joh',
          array( 'users' => array( array( 'id' => 12345 ) ) ) );

        $users = $this->client->users()->autocomplete( array( 'name' => 'joh' ) );

        $this->assertEquals( is_object( $users ), true, 'Should return an object' );
        $this->assertEquals( is_array( $users->users ), true,
          'Should return an object containing an array called "users"' );
        $this->assertGreaterThan( 0, $users->users[0]->id, 'Returns a non-numeric id for user' );
    }

    public function testUpdateProfileImage()
    {
        $this->markTestSkipped( 'Need to refactor update profile image.' );
        $this->mockApiCall( 'GET', '/users/12345.json?', array( 'id' => 12345 ) );
        $this->mockApiCall( 'PUT', '/users/12345.json', array( 'user' => array( 'id' => 12345 ) ) );

        $user = $this->client->user( 12345 )->updateProfileImage( array(
          'file' => getcwd() . '/tests/assets/UK.png'
        ) );

        $contentType = $this->http->requests->first()->getHeader( "Content-Type" )->toArray()[0];
        $this->assertEquals( $contentType, "application/binary" );

        $this->assertEquals( is_object( $user ), true, 'Should return an object' );
        $this->assertEquals( is_object( $user->user ), true, 'Should return an object called "user"' );
        $this->assertGreaterThan( 0, $user->user->id, 'Returns a non-numeric id for request' );
    }

    public function testAuthenticatedUser()
    {
        $this->markTestSkipped( 'Need to refactor get authenticated user' );
        $this->mockApiCall( 'GET', '/users/me.json?', array( 'user' => array( 'id' => 12345 ) ) );
        $user = $this->client->users()->me();
        $this->assertEquals( is_object( $user ), true, 'Should return an object' );
        $this->assertEquals( is_object( $user->user ), true, 'Should return an object called "user"' );
        $this->assertGreaterThan( 0, $user->user->id, 'Returns a non-numeric id for request' );
    }

    public function testSetPassword()
    {
        $this->markTestSkipped( 'Need to refactor set password' );
        $this->mockApiCall( 'POST', '/users/12345/password.json', array() );
        $user = $this->client->user( 12345 )->setPassword( array( 'password' => "aBc12345" ) );
    }

    public function testChangePassword()
    {
        $this->markTestSkipped( 'Need to refactor change password' );
        $this->mockApiCall( 'PUT', '/users/421450109/password.json', array() );
        $user = $this->client->user( 421450109 )->changePassword( array(
          'previous_password' => '12346',
          'password'          => '12345'
        ) );
    }

}