<?php
require_once __DIR__.'/vendor/autoload.php';

class LightningStrikeClientTest extends \PHPUnit\Framework\TestCase {

  public function test_create_invoice(){
    $strike = new LightningStrikeClient(getenv('STRIKE_URL'));
    $invoice = $strike->invoice(50, [ 'customer' => 'Satoshi', 'products' => [ 'potato', 'chips' ]]);

    $this->assertObjectHasAttribute('id', $invoice);
    $this->assertObjectHasAttribute('rhash', $invoice);
    $this->assertObjectHasAttribute('payreq', $invoice);
    $this->assertEquals('50', $invoice->msatoshi);
    $this->assertEquals('Satoshi', $invoice->metadata->customer);
    $this->assertEquals('chips', $invoice->metadata->products[1]);
  }

  public function test_fetch_invoice(){
    $strike = new LightningStrikeClient(getenv('STRIKE_URL'));
    $saved = $strike->invoice(50, 9999);
    $loaded = $strike->fetch($saved->id);

    $this->assertEquals($saved->id, $loaded->id);
    $this->assertEquals($saved->rhash, $loaded->rhash);
    $this->assertEquals($loaded->metadata, 9999);
    $this->assertEquals($loaded->msatoshi, '50');
  }

  public function test_register_webhook(){
    $strike = new LightningStrikeClient(getenv('STRIKE_URL'));
    $invoice = $strike->invoice(50);
    $this->assertTrue($strike->registerHook($invoice->id, 'http://example.com/'));
  }
}
