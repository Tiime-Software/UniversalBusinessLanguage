<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ExternalReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxPointDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\UniversalBusinessLanguage;

class UniversalBusinessLanguageTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
XML;
    protected string $xmlValidContent = "";

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_URI = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ExternalReference>
  </cac:ExternalReference>
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:ExternalReference>
    <cbc:URI>http://www.example.com/index1.html</cbc:URI>
  </cac:ExternalReference>
  <cac:ExternalReference>
    <cbc:URI>http://www.example.com/index2.html</cbc:URI>
  </cac:ExternalReference>
</Invoice>
XML;

    public function setUp(): void
    {
        parent::setUp();
        $this->xmlValidContent = file_get_contents(__DIR__.'/../sample/ubl_fullcontent.xml');
        if($this->xmlValidContent == "") {
            $this->fail("cant load valid full sample");
        }
    }

    public function testCanBeCreatedFromContent(): void
    {
        //$this->loadXMLDocument($this->xmlValidContent);
        //var_dump($this->document->documentElement);
        $this->document = new \DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;
        $this->document->formatOutput = true;
        if (!$this->document->loadXML($this->xmlValidContent)) {
            $this->fail('Source is not valid');
        }
        var_dump($this->document->documentElement);

        $ublObject = UniversalBusinessLanguage::fromXML($this->document);
        $this->assertInstanceOf(UniversalBusinessLanguage::class, $ublObject);
        $this->assertInstanceOf(SpecificationIdentifier::class, $ublObject->getCustomizationID());
        $this->assertEquals("urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0", $ublObject->getCustomizationID()->value);
        $this->assertEquals("urn:fdc:peppol.eu:2017:poacc:billing:01:1.0", $ublObject->getProfileIdentifier());
        $this->assertInstanceOf(InvoiceIdentifier::class, $ublObject->getIdentifier());
        $this->assertInstanceOf(IssueDate::class, $ublObject->getIssueDate());
        $this->assertInstanceOf(DueDate::class, $ublObject->getDueDate());
        $this->assertInstanceOf(InvoiceTypeCode::class, $ublObject->getInvoiceTypeCode());
        $this->assertEquals("Please note our new phone number 33 44 55 660", $ublObject->getNote());
        $this->assertInstanceOf(TaxPointDate::class, $ublObject->getTaxPointDate());
        $this->assertInstanceOf(CurrencyCode::class, $ublObject->getDocumentCurrencyCode());
        $this->assertEquals("4217:2323:2323", $ublObject->getAccountingCost());
        $this->assertEquals("abs1234", $ublObject->getBuyerReference());
        $this->assertInstanceOf(InvoicePeriod::class, $ublObject->getInvoicePeriod());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject = ExternalReference::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromNotEnoughLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_URI);
        ExternalReference::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        ExternalReference::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject = ExternalReference::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_CONTENT, $generatedOutput);
    }
}