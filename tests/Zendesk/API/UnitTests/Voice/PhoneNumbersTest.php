<?php

namespace Zendesk\API\UnitTests\Voice;

use Faker\Factory;
use Zendesk\API\UnitTests\BasicTest;

class PhoneNumbersTest extends BasicTest
{
    /**
     * Tests if the search endpoint can be accessed
     */
    public function testSearch()
    {
        $queryParams = [
            'country'   => 'US',
            'area_code' => 410,
            'contains'  => 'pizza',
            'toll_free' => 1,
        ];

        $this->assertEndpointCalled(function () use ($queryParams) {
            $this->client->voice->phoneNumbers()->search($queryParams);
        }, 'channels/voice/phone_numbers/search.json', 'GET', ['queryParams' => $queryParams]);
    }

    /**
     * Tests if the client can call and build the update phone numbers endpoint
     */
    public function testUpdate()
    {
        $faker = Factory::create();
        $id = $faker->numberBetween(1);
        $params = [
            'greeting_ids' => [
                $faker->numberBetween(1000000000, 2000000000)
            ],
        ];

        $this->assertEndpointCalled(function () use ($params, $id) {
            $this->client->voice->phoneNumbers()->update($id, $params);
        }, "channels/voice/phone_numbers/{$id}.json", 'PUT', [
            'postFields' => ['phone_number' => $params],
        ]);
    }
}
