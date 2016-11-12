<?php

class iisoghatTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * Test of iisoghat plugin
     */
    public function testOghat()
    {
        $service = IISOGHAT_BOL_Service::getInstance();
        $cityName = 'city_test_1';
        $service->addCity($cityName, 10, 10);
        $existCity = $service->existCity($cityName, 10, 10);
        $this->assertEquals(true, $existCity);

        $existCity = $service->deleteCity($cityName);
        $this->assertEquals(false, $existCity);
    }
}