<?php

namespace App\Http\Livewire\ProductModule;

use App\Models\Category;
use App\Models\Stock;
use App\Repositories\ProductRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ProductComponent extends Component
{

    use LivewireAlert;

    public $categories;

    public $brands;

    public $stockgroups;

    public $classifications;

    public $manufacturers;

    public Stock $product;

    public array $product_data = [];

    private ProductRepository $productRepository;

    public function boot(ProductRepository $productRepository)
    {

        $this->productRepository = $productRepository;

    }

    public function booted()
    {
        $this->categories = categories(true);
        $this->brands = brands(true);
        $this->manufacturers = manufacturers(true);
        $this->stockgroups = stockgroups(true);
        $this->classifications = classifications(true);
    }

    public function mount()
    {

        if(isset($this->product->id))
        {
            $this->product_data = Arr::only( $this->product->toArray(), array_keys(ProductRepository::$productFields));
        }
        else {
            $this->product_data = ProductRepository::$productFields;
        }

    }

    public function render()
    {

        return view('livewire.product-module.product-component');
    }

    public function saveStock()
    {
        $this->validate([
            "product_data.name"=>"bail|required|max:255",
            "product_data.piece"=>"bail|required",
            "product_data.carton"=>"bail|required",
            "product_data.box"=>"bail|required",
            "product_data.whole_price"=>"required",
            "product_data.bulk_price"=>"required",
            "product_data.retail_price"=>"required",
            "product_data.location"=>"required"
        ]);



        if(isset($this->product->id))
        {
            $message = "updated";

            $this->product_data['user_id'] = auth()->id();

            $this->product =  $this->productRepository->update($this->product->id, $this->product_data);
        }
        else {
            $message = "created";

            $this->product_data['user_id'] = auth()->id();

            $this->product =  $this->productRepository->create($this->product_data);

            $this->product_data = ProductRepository::$productFields;

        }

        $this->alert(
            "success",
            "Product",
            [
                'position' => 'center',
                'timer' => 12000,
                'toast' => false,
                'text' =>  "Product has been ".$message." successfully!.",
            ]
        );

        return redirect()->route('product.index');

    }
}
