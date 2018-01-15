<?php

namespace Curve\Test\Service;


class Distance extends AbstractTest
{
    /**
     * Test invalid use of the service
     */
    public function testCallErrors()
    {
        // Unknown service
        $response = $this->sendRequest('GET', 'notAnEndPoint');
        $this->assertError($response, 'No such endpoint');

        // Unknown method
        $response = $this->sendRequest('POST', 'distance?user1=hector&user2=jim');
        $this->assertError($response, 'This service does not support the POST method');
    }

    /**
     * Test valid call of the service but with invalid parameters
     */
    public function testParamErrors()
    {
        // No param
        $response = $this->sendRequest('GET', 'distance');
        $this->assertError($response, 'Missing parameter user1');

        // Unknown user
        $response = $this->sendRequest('GET', 'distance?user1=hector&user2=jim');
        $this->assertError($response, 'An error has occurred when querying github: 500 - No such user');

        // You must provide 2 different users
        $response = $this->sendRequest('GET', 'distance?user1=jim&user2=jim');
        $this->assertError($response, 'You must provide 2 different users');

    }

    public function testDataErrors()
    {
        // User has no contribution
        $response = $this->sendRequest('GET', 'distance?user1=adam&user2=jim');
        $this->assertError($response, 'An error has occurred when querying github: 500 - User has no contribution');

        // Users have no connection
        $response = $this->sendRequest('GET', 'distance?user1=boris&user2=jim');
        $this->assertError($response, 'Users have no connection');
    }

    public function testSuccess()
    {
        // direct connection
        $response = $this->sendRequest('GET', 'distance?user1=jim&user2=john');
        $body = $this->assertSuccess($response, array('distance', 'path'));
        $this->assertEquals($body['distance'], 1);
        $this->assertEquals($body['path'], 'jim -> john (via screen/tv)');

        // one jump
        $response = $this->sendRequest('GET', 'distance?user1=jim&user2=dan');
        $body = $this->assertSuccess($response, array('distance', 'path'));
        $this->assertEquals($body['distance'], 2);
        $this->assertEquals($body['path'], 'jim -> elsa (via screen/tv) -> dan (via screen/phone)');

        // one jump is shorter than two
        /**
         * The path between Dan and Eric could also be a 2 jumps one (Dan -> Elsa -> John -> Eric),
         * so we check that a shorter one is taken
         */
        $response = $this->sendRequest('GET', 'distance?user1=dan&user2=eric');
        $body = $this->assertSuccess($response, array('distance', 'path'));
        $this->assertEquals($body['distance'], 2);
        $this->assertEquals($body['path'], 'dan -> jack (via screen/phone) -> eric (via automotive/4x4)');
    }
}