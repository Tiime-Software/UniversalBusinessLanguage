<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit\Ubl21Standard\Invoice;

use Tiime\EN16931\Codelist\CurrencyCodeISO4217 as CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\AccountingCustomerParty;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\AccountingSupplierParty;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\AdditionalDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\Allowance;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\Charge;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\ContractDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\Delivery;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\DespatchDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\InvoiceLine;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\OrderReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\OriginatorDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\PayeeParty;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\PaymentMeans;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\PaymentTerms;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\ProjectReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\ReceiptDocumentReference;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\TaxRepresentativeParty;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\Note;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\Basic\TaxPointDate;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\UniversalBusinessLanguage;
use Tiime\UniversalBusinessLanguage\Ubl21Standard\Invoice\Utils\UniversalBusinessLanguageUtils;

class UniversalBusinessLanguageTest extends BaseXMLNodeTestWithHelpers
{
    protected string $xmlValidContent            = '';
    protected string $xmlValidNoDefaultNamespace = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->xmlValidContent = file_get_contents(__DIR__ . '/../sample/ubl21_fullcontent.xml');

        if ('' === $this->xmlValidContent) {
            $this->fail('cant load valid full sample');
        }

        $this->xmlValidNoDefaultNamespace = file_get_contents(__DIR__ . '/../sample/ubl21_no_default_namespace.xml');

        if ('' === $this->xmlValidNoDefaultNamespace) {
            $this->fail('cant load valid no default namespace sample');
        }
    }

    public function testCanBeCreatedFromContent(): void
    {
        $this->loadXMLDocument($this->xmlValidContent);
        $ublObject = UniversalBusinessLanguage::fromXML($this->document);
        $this->assertInstanceOf(UniversalBusinessLanguage::class, $ublObject);
        $this->assertInstanceOf(SpecificationIdentifier::class, $ublObject->getCustomizationID());
        $this->assertEquals('urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0', $ublObject->getCustomizationID()->value);
        $this->assertEquals('urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', $ublObject->getProfileIdentifier());
        $this->assertInstanceOf(InvoiceIdentifier::class, $ublObject->getIdentifier());
        $this->assertInstanceOf(IssueDate::class, $ublObject->getIssueDate());
        $this->assertInstanceOf(DueDate::class, $ublObject->getDueDate());
        $this->assertInstanceOf(InvoiceTypeCode::class, $ublObject->getInvoiceTypeCode());
        $this->assertIsArray($ublObject->getNotes());
        $this->assertCount(2, $ublObject->getNotes());

        foreach ($ublObject->getNotes() as $note) {
            $this->assertInstanceOf(Note::class, $note);
        }

        $this->assertInstanceOf(TaxPointDate::class, $ublObject->getTaxPointDate());
        $this->assertInstanceOf(CurrencyCode::class, $ublObject->getDocumentCurrencyCode());
        $this->assertInstanceOf(CurrencyCode::class, $ublObject->getTaxCurrencyCode());
        $this->assertEquals('4217:2323:2323', $ublObject->getAccountingCost());
        $this->assertEquals('abs1234', $ublObject->getBuyerReference());
        $this->assertInstanceOf(InvoicePeriod::class, $ublObject->getInvoicePeriod());
        $this->assertInstanceOf(OrderReference::class, $ublObject->getOrderReference());
        $this->assertIsArray($ublObject->getBillingReferences());
        $this->assertCount(1, $ublObject->getBillingReferences());

        foreach ($ublObject->getBillingReferences() as $billingReference) {
            $this->assertInstanceOf(BillingReference::class, $billingReference);
        }

        $this->assertInstanceOf(DespatchDocumentReference::class, $ublObject->getDespatchDocumentReference());
        $this->assertInstanceOf(ReceiptDocumentReference::class, $ublObject->getReceiptDocumentReference());
        $this->assertInstanceOf(OriginatorDocumentReference::class, $ublObject->getOriginatorDocumentReference());
        $this->assertInstanceOf(ContractDocumentReference::class, $ublObject->getContractDocumentReference());
        $this->assertIsArray($ublObject->getBillingReferences());
        $this->assertCount(1, $ublObject->getAdditionalDocumentReferences());

        foreach ($ublObject->getAdditionalDocumentReferences() as $additionalDocumentReference) {
            $this->assertInstanceOf(AdditionalDocumentReference::class, $additionalDocumentReference);
        }
        $this->assertInstanceOf(ProjectReference::class, $ublObject->getProjectReference());
        $this->assertInstanceOf(AccountingSupplierParty::class, $ublObject->getAccountingSupplierParty());
        $this->assertInstanceOf(AccountingCustomerParty::class, $ublObject->getAccountingCustomerParty());
        $this->assertInstanceOf(PayeeParty::class, $ublObject->getPayeeParty());
        $this->assertInstanceOf(TaxRepresentativeParty::class, $ublObject->getTaxRepresentativeParty());
        $this->assertInstanceOf(Delivery::class, $ublObject->getDelivery());
        $this->assertIsArray($ublObject->getPaymentMeans());
        $this->assertCount(1, $ublObject->getPaymentMeans());

        foreach ($ublObject->getPaymentMeans() as $paymentMean) {
            $this->assertInstanceOf(PaymentMeans::class, $paymentMean);
        }
        $this->assertInstanceOf(PaymentTerms::class, $ublObject->getPaymentTerms());
        $this->assertIsArray($ublObject->getAllowances());
        $this->assertCount(1, $ublObject->getAllowances());

        foreach ($ublObject->getAllowances() as $allowance) {
            $this->assertInstanceOf(Allowance::class, $allowance);
        }
        $this->assertIsArray($ublObject->getCharges());
        $this->assertCount(1, $ublObject->getCharges());

        foreach ($ublObject->getCharges() as $charge) {
            $this->assertInstanceOf(Charge::class, $charge);
        }
        $this->assertIsArray($ublObject->getTaxTotals());
        $this->assertCount(1, $ublObject->getTaxTotals());

        foreach ($ublObject->getTaxTotals() as $taxTotal) {
            $this->assertInstanceOf(TaxTotal::class, $taxTotal);
        }
        $this->assertInstanceOf(LegalMonetaryTotal::class, $ublObject->getLegalMonetaryTotal());
        $this->assertIsArray($ublObject->getInvoiceLines());
        $this->assertCount(1, $ublObject->getInvoiceLines());

        foreach ($ublObject->getInvoiceLines() as $invoiceLine) {
            $this->assertInstanceOf(InvoiceLine::class, $invoiceLine);
        }
    }

    public function testCanBeCreatedFromNoDefaultNamespace(): void
    {
        $this->loadXMLDocument($this->xmlValidNoDefaultNamespace);
        $ublObject = UniversalBusinessLanguage::fromXML($this->document);
        $this->assertInstanceOf(UniversalBusinessLanguage::class, $ublObject);
    }

    public function testGenerateXml(): void
    {
        $this->loadXMLDocument($this->xmlValidContent);
        $ublObject       = UniversalBusinessLanguage::fromXML($this->document);
        $this->document  = $ublObject->toXML();
        $generatedOutput = $this->formatXMLOutput();
        $this->assertStringEqualsStringIgnoringLineEndings($this->xmlValidContent, $generatedOutput);
    }

    public function testXmlXsd(): void
    {
        $this->loadXMLDocument($this->xmlValidContent);
        $xsdErrors = UniversalBusinessLanguageUtils::validateXSD($this->document);

        $this->assertCount(0, $xsdErrors);
    }
}
