<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\PeppolBIS\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PayeeParty;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PayeePartyBACIdentification;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PayeePartyLegalEntity;
use Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType\Aggregate\PayeePartyName;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class CreditNotePayeePartyTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
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
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeParty>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
</CreditNote>
XML;

    protected const XML_VALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PayeeParty>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
    <cac:PayeeParty>
    <cac:PartyName>
      <cbc:Name>Payee Name Ltd</cbc:Name>
    </cac:PartyName>
  </cac:PayeeParty>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = PayeeParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeParty::class, $ublObject);
        $this->assertNull($ublObject->getPartyIdentification());
        $this->assertInstanceOf(PayeePartyBACIdentification::class, $ublObject->getPartyBACIdentification());
        $this->assertInstanceOf(PayeePartyName::class, $ublObject->getPartyName());
        $this->assertInstanceOf(PayeePartyLegalEntity::class, $ublObject->getPartyLegalEntity());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = PayeeParty::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeeParty::class, $ublObject);
        $this->assertNull($ublObject->getPartyIdentification());
        $this->assertNull($ublObject->getPartyBACIdentification());
        $this->assertInstanceOf(PayeePartyName::class, $ublObject->getPartyName());
        $this->assertNull($ublObject->getPartyLegalEntity());
    }

    public function testCanBeCreatedFromNoLine(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_NO_LINE);
        $ublObject      = PayeeParty::fromXML($this->xpath, $currentElement);
        $this->assertNull($ublObject);
    }

    public function testCannotBeCreatedFromManyLines(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        PayeeParty::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = PayeeParty::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
