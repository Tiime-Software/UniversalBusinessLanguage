<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Aggregate;

use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PartyName;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeePartyLegalEntity;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeeParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeePartyIdentification;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class PayeePartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeParty>
    <cac:PartyIdentification>
      <cbc:ID schemeID="SEPA">FR932874294</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
    <cac:PartyLegalEntity>
      <cbc:CompanyID schemeID="0002">FR932874294</cbc:CompanyID>
    </cac:PartyLegalEntity>
  </cac:PayeeParty>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeParty>
    <cac:PartyIdentification>
      <cbc:ID>FR932874294</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
</Invoice>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_TOO_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeParty>
    <cac:PartyIdentification>
      <cbc:ID>FR932874294</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
  <cac:PayeeParty>
    <cac:PartyIdentification>
      <cbc:ID>FR932874294</cbc:ID>
    </cac:PartyIdentification>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
</Invoice>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = PayeeParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeParty::class, $ublObject);
        $this->assertInstanceOf(PayeePartyIdentification::class, $ublObject->getPartyIdentification());
        $this->assertInstanceOf(PartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PayeePartyLegalEntity::class, $ublObject->getPartyLegalEntity());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject = PayeeParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeParty::class, $ublObject);
        $this->assertInstanceOf(PayeePartyIdentification::class, $ublObject->getPartyIdentification());
        $this->assertInstanceOf(PartyName::class, $ublObject->getPartyName());
        $this->assertNull($ublObject->getPartyLegalEntity());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        PayeeParty::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromTooManyTaxTotals(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_TOO_MANY_LINES);
        PayeeParty::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject = PayeeParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertEquals(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}