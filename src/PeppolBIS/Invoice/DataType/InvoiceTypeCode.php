<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\Invoice\DataType;

/**
 * UNTDID 1001 - inv - Document type (BT-3)
 * Published by France (31/07/2023).
 */
enum InvoiceTypeCode: string
{
    case DEBIT_NOTE_RELATED_TO_GOODS_OR_SERVICES       = '80';
    case METERED_SERVICES_INVOICE                      = '82';
    case DEBIT_NOTE_RELATED_TO_FINANCIAL_ADJUSTMENTS   = '84';
    case INVOICING_DATA_SHEET                          = '130';
    case DIRECT_PAYMENT_VALUATION                      = '202';
    case PROVISIONAL_PAYMENT_VALUATION                 = '203';
    case PAYMENT_VALUATION                             = '204';
    case INTERIM_APPLICATION_FOR_PAYMENT               = '211';
    case SELF_BILLED_CREDIT_NOTE                       = '261';
    case CONSOLIDATED_CREDIT_NOTE_GOODS_AND_SERVICES   = '262';
    case PRICE_VARIATION_INVOICE                       = '295';
    case CREDIT_NOTE_FOR_PRICE_VARIATION               = '296';
    case DELCREDERE_CREDIT_NOTE                        = '308';
    case PROFORMA_INVOICE                              = '325';
    case PARTIAL_INVOICE                               = '326';
    case COMMERCIAL_INVOICE                            = '380';
    case DEBIT_NOTE                                    = '383';
    case CORRECTED_INVOICE                             = '384';
    case CONSOLIDATED_INVOICE                          = '385';
    case PREPAYMENT_INVOICE                            = '386';
    case HIRE_INVOICE                                  = '387';
    case TAX_INVOICE                                   = '388';
    case SELF_BILLED_INVOICE                           = '389';
    case DELCREDERE_INVOICE                            = '390';
    case FACTORED_INVOICE                              = '393';
    case LEASE_INVOICE                                 = '394';
    case CONSIGNMENT_INVOICE                           = '395';
    case OPTICAL_CHARACTER_READING_PAYMENT_CREDIT_NOTE = '420';
    case DEBIT_ADVICE                                  = '456';
    case REVERSAL_OF_DEBIT                             = '457';
    case REVERSAL_OF_CREDIT                            = '458';
    case SELF_BILLED_PREPAYMENT_INVOICE                = '500';
    case SELF_BILLED_FACTORED_INVOICE                  = '501';
    case SELF_BILLED_FACTORED_CREDIT_NOTE              = '502';
    case PREPAYMENT_CREDIT_NOTE                        = '503';
    case SELF_BILLED_DEBIT_NOTE                        = '527';
    case INSURER_INVOICE                               = '575';
    case FORWARDER_INVOICE                             = '623';
    case PORT_CHARGES_DOCUMENTS                        = '633';
    case INVOICE_INFORMATION_FOR_ACCOUNTING_PURPOSES   = '751';
    case FREIGHT_INVOICE                               = '780';
    case CUSTOMS_INVOICE                               = '935';
}
