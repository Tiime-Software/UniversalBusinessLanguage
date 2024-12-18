<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21\Invoice\Aggregate;

use Tiime\EN16931\DataType\Identifier\LegalRegistrationIdentifier;
use Tiime\UniversalBusinessLanguage\Ubl21\Invoice\DataType\Aggregate\BuyerPartyLegalEntity;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;

class BuyerPartyLegalEntityTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_FULL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_VALID_MINIMAL_CONTENT = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_NO_LINE = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
</Invoice>
XML;

    protected const XML_INVALID_NOT_ENOUGH_DATA = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_WRONG_DATA = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="WTF42">5560104525</cbc:CompanyID>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS_1 = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_MANY_CONTENTS_2 = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
    <cbc:CompanyID schemeID="0007">5560104525</cbc:CompanyID>
    <cbc:CompanyID schemeID="0007">5560104526</cbc:CompanyID>
  </cac:PartyLegalEntity>
</Invoice>
XML;

    protected const XML_INVALID_MANY_LINES = <<<XML
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
  <cac:PartyLegalEntity>
    <cbc:RegistrationName>Buyer Full Name AS</cbc:RegistrationName>
  </cac:PartyLegalEntity>
</Invoice>
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
        $rootDestination = $this->generateEmptyInvoiceRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_FULL_CONTENT, $generatedOutput);
    }
}
