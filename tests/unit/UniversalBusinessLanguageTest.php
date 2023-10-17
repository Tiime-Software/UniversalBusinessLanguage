<?php

namespace Tiime\UniversalBusinessLanguage\Tests\unit;

use Tiime\EN16931\DataType\CurrencyCode;
use Tiime\EN16931\DataType\Identifier\InvoiceIdentifier;
use Tiime\EN16931\DataType\Identifier\SpecificationIdentifier;
use Tiime\EN16931\DataType\InvoiceTypeCode;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AccountingCustomerParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AccountingSupplierParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\AdditionalDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Allowance;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\BillingReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ContractDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\Delivery;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\DespatchDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoiceLine;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\InvoicePeriod;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\LegalMonetaryTotal;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OrderReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\OriginatorDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PayeeParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentMeans;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\PaymentTerms;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ProjectReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\ReceiptDocumentReference;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxRepresentativeParty;
use Tiime\UniversalBusinessLanguage\DataType\Aggregate\TaxTotal;
use Tiime\UniversalBusinessLanguage\DataType\Basic\DueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\IssueDate;
use Tiime\UniversalBusinessLanguage\DataType\Basic\TaxPointDate;
use Tiime\UniversalBusinessLanguage\Tests\helpers\BaseXMLNodeTestWithHelpers;
use Tiime\UniversalBusinessLanguage\UniversalBusinessLanguage;

class UniversalBusinessLanguageTest extends BaseXMLNodeTestWithHelpers
{
    protected string $xmlValidContent = '';
    protected string $xmlValidNoDefaultNamespace = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->xmlValidContent = file_get_contents(__DIR__ . '/../sample/ubl_fullcontent.xml');

        if ('' === $this->xmlValidContent) {
            $this->fail('cant load valid full sample');
        }

        $this->xmlValidNoDefaultNamespace = file_get_contents(__DIR__ . '/../sample/ubl_no_default_namespace.xml');

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
        $this->assertEquals('Please note our new phone number 33 44 55 66', $ublObject->getNote());
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
        $this->assertEquals($this->xmlValidContent, $generatedOutput);
    }
}
