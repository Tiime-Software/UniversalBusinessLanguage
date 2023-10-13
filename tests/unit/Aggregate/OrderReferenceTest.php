<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\EN16931\DataType\Reference\PurchaseOrderReference;
use Tiime\EN16931\DataType\Reference\SalesOrderReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OrderReference;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class OrderReferenceTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:OrderReference>
    <cbc:ID>98776</cbc:ID>
    <cbc:SalesOrderID>112233</cbc:SalesOrderID>
  </cac:OrderReference>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:OrderReference>
    <cbc:ID>98776</cbc:ID>
  </cac:OrderReference>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_ID = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:OrderReference>
    <cbc:ID>98776</cbc:ID>
    <cbc:ID>98776</cbc:ID>
  </cac:OrderReference>
</Invoice>
XML;
    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:OrderReference>
    <cbc:ID>98776</cbc:ID>
  </cac:OrderReference>
  <cac:OrderReference>
    <cbc:ID>98776</cbc:ID>
  </cac:OrderReference>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = OrderReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(OrderReference::class, $ublObject);
        $this->assertInstanceOf(PurchaseOrderReference::class, $ublObject->getIdentifier());
        $this->assertInstanceOf(SalesOrderReference::class, $ublObject->getSalesOrderIdentifier());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = OrderReference::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(OrderReference::class, $ublObject);
        $this->assertInstanceOf(PurchaseOrderReference::class, $ublObject->getIdentifier());
        $this->assertNull($ublObject->getSalesOrderIdentifier());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublDocument    = OrderReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublDocument);
    }

    public function testCannotBeCreatedFromTooManyId(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_ID);
        OrderReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        OrderReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = OrderReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
