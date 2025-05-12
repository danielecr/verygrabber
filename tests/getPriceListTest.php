<?php

use \PHPUnit\Framework\TestCase;

use \smrtg\VeryGrabber\GrabFromSchema;

class getPriceListTest extends TestCase
{
    public function testExtractData()
    {
        $doc = file_get_contents(dirname(__FILE__).'/data/file.html');
        $grab = new GrabFromSchema($doc);

        $schema = file_get_contents(dirname(__FILE__).'/data/schema.json');

        $data = $grab->getStruct($schema);
        //print_r($data);
        $this->assertEquals(15, count($data));
        $this->assertEquals("tinteguenstiger.de", $data[14]->seller);
        $this->assertEquals("Vorkasse", $data[14]->shippingOptions[0]->name);
        $this->assertEquals("\n60,80 € inkl. Versand\n", $data[14]->shippingOptions[0]->price);
    }
}
