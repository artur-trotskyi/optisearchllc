<?php

namespace App\Enums\Order;

use App\Traits\EnumTrait;

enum OrderFilterEnum: string
{
    use EnumTrait;

    // ITEMS_PER_PAGE
    case MIN_ITEMS_PER_PAGE = '1';
    case MAX_ITEMS_PER_PAGE = '20';

    // SORTABLE_FIELDS
    case PRODUCT_NAME = 'product_name';
    case STATUS = 'status';
    case AMOUNT = 'amount';

    // SORT_ORDER_OPTIONS
    case ASC = 'asc';
    case DESC = 'desc';

    // Getting all sortable fields
    public static function sortableFields(): array
    {
        return [
            self::PRODUCT_NAME->message(),
            self::STATUS->message(),
            self::AMOUNT->message(),
        ];
    }

    // Getting all sort order options
    public static function sortOrderOptions(): array
    {
        return [
            self::ASC->message(),
            self::DESC->message(),
        ];
    }

    // Getting min and max items per page
    public static function itemsPerPage(): array
    {
        return [
            'min' => (int) self::MIN_ITEMS_PER_PAGE->message(),
            'max' => (int) self::MAX_ITEMS_PER_PAGE->message(),
        ];
    }
}
