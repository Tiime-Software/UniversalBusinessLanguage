<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\CreditNote\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\BuyerPartyLegalEntity;

class CreditNoteBuyerPartyLegalEntityTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</CreditNote>
XML;

    protected const XML_INVALID_NOT_ENOUGH_DATA = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_INVALID_WRONG_DATA = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="WTF42">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS_1 = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS_2 = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
    <cbc:CompanyID schemeID="0007">5560104526</cbc:CompanyID>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</CreditNote>
XML;

    public function testCanBeCreatedFromFullContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject      = BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(BuyerPartyLegalEntity::class, $ublObject);
        $this->assertInstanceOf(LegalRegistrationIdentifier::class, $ublObject->getIdentifier());
        $this->assertEquals('Buyer Full Name AS', $ublObject->getRegistrationName());
    }

    public function testCanBeCreatedFromMinimalContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_MINIMAL_CONTENT);
        $ublObject      = BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(BuyerPartyLegalEntity::class, $ublObject);
        $this->assertNull($ublObject->getIdentifier());
        $this->assertEquals('Buyer Full Name AS', $ublObject->getRegistrationName());
    }

    public function testCannotBeCreatedFromNoLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NO_LINE);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromNotEnoughData(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_NOT_ENOUGH_DATA);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromWrongContent(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_WRONG_DATA);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContents(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS_1);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyContentsAlt(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS_2);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testCannotBeCreatedFromManyEntries(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_LINES);
        BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_FULL_CONTENT);
        $ublObject       = BuyerPartyLegalEntity::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
