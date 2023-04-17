<?php

namespace App\Http\Livewire\StockTransfer\Show;

use App\Models\Stocktransfer;
use App\Repositories\StockTransferRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ShowStockTransfer extends Component
{
    use LivewireAlert;

    public string $title;

    public string $subtitle;

    public array $errors = [];

    public Stocktransfer $stocktransfer;

    public function boot()
    {

    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.stock-transfer.show.show-stock-transfer');
    }


    public function complete()
    {
        $complete = (new StockTransferRepository())->complete($this->stocktransfer);

        if(is_array($complete))
        {
            $this->errors = $complete;

            $this->alert(
                "error",
                "Stock Transfer",
                [
                    'position' => 'center',
                    'timer' => 12000,
                    'toast' => false,
                    'text' =>  "An error occurred while completing this transfer, please check and adjust need items",
                ]
            );

            return false;
        }

        $this->alert(
            "success",
            "Stock Transfer",
            [
                'position' => 'center',
                'timer' => 12000,
                'toast' => false,
                'text' =>  "Stock Transfer has been completed successfully!.",
            ]
        );

        return redirect()->route('transfer.show', $this->stocktransfer->id);
    }


    public function delete()
    {
        (new StockTransferRepository())->delete($this->stocktransfer);

        $this->alert(
            "success",
            "Stock Transfer",
            [
                'position' => 'center',
                'timer' => 12000,
                'toast' => false,
                'text' =>  "Stock Transfer has been deleted successfully!.",
            ]
        );

        return redirect()->route('transfer.index');
    }

}
