<?php

declare(strict_types=1);

namespace Tiime\UniversalBusinessLanguage\PeppolBIS\CreditNote\DataType;

/**
 * UNTDID 1001 - Document type (BT-3)
 * Published by France (31/07/2023).
 */
enum CreditNoteTypeCode: string
{
    case CREDIT_NOTE_RELATED_TO_GOODS_OR_SERVICES     = '81';
    case CREDIT_NOTE_RELATED_TO_FINANCIAL_ADJUSTMENTS = '83';
    case CREDIT_NOTE                                  = '381';
    case FACTORED_CREDIT_NOTE                         = '396';
    case FORWARDER_CREDIT_NOTE                        = '532';
}
