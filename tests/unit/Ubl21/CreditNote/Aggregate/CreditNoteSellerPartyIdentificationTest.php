<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Ubl21\CreditNote\DataType\Aggregate\SellerPartyIdentification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNoteSellerPartyIdentificationTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="0009">10000000900017</cbc:ID>
  </cac:PartyIdentification>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_BAD_SCHEME_ID = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyIdentification>
    <cbc:ID schemeID="AZERTY">10000000900017</cbc:ID>
  </cac:PartyIdentification>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects     = SellerPartyIdentification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(1, $ublObjects);
        $this->assertInstanceOf(SellerPartyIdentification::class, $ublObjects[0]);
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObjects     = SellerPartyIdentification::fromXML($this->xpath, $currentElement);
        $this->assertIsArray($ublObjects);
        $this->assertCount(0, $ublObjects);
    }

    public function testCannotBeCreatedFromBadSchemeId(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_BAD_SCHEME_ID);
        SellerPartyIdentification::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObjects      = SellerPartyIdentification::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();

        foreach ($ublObjects as $ublObject) {
            $rootDestination->appendChild($ublObject->toXML($this->document));
        }
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
