<?php

namespace App\Imports;

use App\Models\Promotion;
use App\Models\PromotionItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PromotionStocksImport implements ToModel, WithHeadingRow
{

    private Promotion $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new PromotionItem([
            'promotion_id' => $this->promotion->id,
            'user_id' => auth()->id(),
            'status_id' => status('Pending'),
            'from_date' => $this->promotion->from_date,
            'end_date' => $this->promotion->end_date,
            'created' => $this->promotion->created,
            'stock_id' => $row['stock_id'],
            'whole_price' => $row['wholesales_price'],
            'retail_price' => $row['retail_price'],
            'bulk_price' => $row['bulk_price'],
        ]);
    }
}
