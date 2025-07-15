<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\CreditNote\Aggregate;

use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\CreditNote\DataType\Aggregate\PayeePartyName;

class CreditNotePayeePartyNameTest extends BaseXMLNodeTestWithHelpers
{
    protected const XML_VALID_CONTENT = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
  <cac:PartyName>
    <cbc:Name>Payee Name Ltd</cbc:Name>
  </cac:PartyName>
</CreditNote>
XML;

    protected const XML_INVALID_MANY_CONTENTS = <<<XML
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
<cac:PartyName>
  <cbc:Name>Payee Name Ltd 1</cbc:Name>
  <cbc:Name>Payee Name Ltd 2</cbc:Name>
</cac:PartyName>
</CreditNote>
XML;

    public function testCanBeCreatedFromContent(): void
    {
        $currentElement = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject      = PayeePartyName::fromXML($this->xpath, $currentElement);
        $this->assertInstanceOf(PayeePartyName::class, $ublObject);
        $this->assertEquals('Payee Name Ltd', $ublObject->getName());
    }

    public function testCannotBeCreatedFromOmittedLine(): void
    {
        $this->expectException(\Exception::class);
        $currentElement = $this->loadXMLDocument(self::XML_INVALID_MANY_CONTENTS);
        PayeePartyName::fromXML($this->xpath, $currentElement);
    }

    public function testGenerateXml(): void
    {
        $currentElement  = $this->loadXMLDocument(self::XML_VALID_CONTENT);
        $ublObject       = PayeePartyName::fromXML($this->xpath, $currentElement);
        $rootDestination = $this->generateEmptyCreditNoteRootDocument();
        $rootDestination->appendChild($ublObject->toXML($this->document));
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings(self::XML_VALID_CONTENT, $generatedOutput);
    }
}
