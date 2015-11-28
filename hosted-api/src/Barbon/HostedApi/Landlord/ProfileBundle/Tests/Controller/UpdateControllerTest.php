<?php

namespace Barbon\HostedApi\Landlord\ProfileBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateControllerTest extends WebTestCase
{
     private $client;

     public function setUp()
     {
          $this->client = static::createClient();
     }

    private function sendRequest($method, $url, $json = null, $type = 'application/json')
    {
         $this->client->request(
            strtoupper($method),
            $url,
            array(),
            array(),
            array('CONTENT_TYPE' => $type),
            json_encode(array(
                'name' => 'Alex'
            ))
         );
    }

    public function testPutALandlord()
    {
        $json = array('direct_landlord' => array(
            "title" => "Mrs",
            "firstName" => "Pauline",
            "lastName" => "Swift",
            "email" => "paul.swift@barbon.com",
            "address" => array(
                "flat" => "1",
                "houseName" => "name",
                "houseNumber" => "2",
                "street" => "street",
                "locality" => "locality",
                "district" => "Lincolnshire",
                "town" => "Lincoln",
                "county" => "Lincs",
                "country" => "England",
                "postcode" => "LN67EL",
                "latitude" => 0.0,
                "longitude" => 0.0,
                "isForeign" => false,
                "foreign" => false,
            ),
            "dayPhone" => "01789123123",
            "eveningPhone" => "01789123123",
            "foreigner"=> false,
        ));

//print json_encode($json); exit;
    
        $this->client->request(
            'POST', 
            '/landlord/profile/update', 
            array(), 
            array(), 
            array('CONTENT_TYPE' => 'application/json'), 
            json_encode($json)
        );
        //$this->sendRequest('POST', '/landlord/profile/update', $json);

        print_r($this->client->getResponse()->getContent());
    
        $this->assertEquals($this->client->getResponse()->getStatusCode(), 200);
    }

}
