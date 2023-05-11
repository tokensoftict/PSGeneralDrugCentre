<?php

namespace App\Http\Livewire\StockTransfer;

use App\Models\Stocktransfer;
use App\Repositories\StockTransferRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class StockTransferComponent extends Component
{
    use LivewireAlert;

    public Stocktransfer $stocktransfer;

    public $data;

    public  $from = NULL;

    public  $to = NULL;

    private StockTransferRepository  $stockTransferRepository;

    public function mount()
    {

    }

    public function boot(StockTransferRepository $stockTransferRepository)
    {
        $this->stockTransferRepository = $stockTransferRepository;
    }

    public function booted()
    {
        $this->stocktransfer->from = $this->from;
        $this->stocktransfer->to = $this->to;

        $this->data = StockTransferRepository::stockTransfer($this->stocktransfer);

    }


    public function render()
    {
        return view('livewire.stock-transfer.stock-transfer-component');
    }


    public function draftTransfer()
    {
        $draft = $this->stockTransferRepository->saveTransfer($this->stocktransfer, $this->data);

        if(is_array($draft))
        {
            $this->alert(
                "error",
                "Stock Transfer",
                [
                    'position' => 'center',
                    'timer' => 12000,
                    'toast' => false,
                    'text' =>  "An error occurred while drafting this transfer, please check and adjust need items",
                ]
            );

            return ['errors' =>  $draft, 'status'=>false];

        }else {
            $this->alert(
                "success",
                "Stock Transfer",
                [
                    'position' => 'center',
                    'timer' => 6000,
                    'toast' => false,
                    'text' => "Stock Transfer has been drafted successfully!.",
                ]
            );

            return ['status' => true];
        }

    }


    public function completeTransfer()
    {

        $completed = $this->stockTransferRepository->saveTransfer($this->stocktransfer, $this->data);
        $this->stocktransfer =  $completed;
        if(is_array($completed))
        {

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

            return ['errors' =>  $completed, 'status'=>false];

        }else {

            $completed = $this->stockTransferRepository->complete($this->stocktransfer);

            $this->stocktransfer = $completed;

            $this->alert(
                "success",
                "Stock Transfer",
                [
                    'position' => 'center',
                    'timer' => 6000,
                    'toast' => false,
                    'text' =>  "Stock Transfer has been completed successfully!.",
                ]
            );


            return ['status'=>true];
        }

    }

}
