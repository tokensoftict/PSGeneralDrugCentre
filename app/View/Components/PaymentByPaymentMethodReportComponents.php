<?php

namespace App\View\Components;

use App\Models\Paymentmethod;
use Illuminate\View\Component;

class PaymentByPaymentMethodReportComponents extends Component
{
    public array $filters;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if(isset($this->filters['user_id'])) {
            $data['payments'] =  Paymentmethod::with(['paymentmethoditems'=>function($query){
                $query->with(['invoice','payment','user','customer'])->where('user_id', $this->filters['user_id'])->where("payment_date",$this->filters['payment_date']);
            }])->where('id','<>',6)->where('id','<>',4)->get();
        }else{
        $data['payments'] =  Paymentmethod::with(['paymentmethoditems'=>function($query){
            $query->with(['invoice','payment','user','customer'])->where("payment_date",$this->filters['payment_date']);
        }])->where('id','<>',6)->where('id','<>',4)->get();
        }
        return view('components.payment-by-payment-method-report-components', $data);
    }
}
