<div>
    <form method="post" wire:submit.prevent="savePayment">
        <div class="col-md-7 offset-2">
            <div class="mb-3">
                <label>Payment Date</label>
                <input type="text" wire:model.defer="payment_data.payment_date" placeholder="Payment Date"  class="form-control datepicker-basic" >
                @error('payment_data.payment_date') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label>Supplier</label>
                <select class="form-control" wire:model.defer="payment_data.supplier_id">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('payment_data.supplier_id') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label>Amount</label>
                <input type="text" placeholder="Amount Paid"  wire:model.defer="payment_data.amount" class="form-control">
                @error('payment_data.amount') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label>Payment Method</label>
                <select class="form-control" wire:model.defer="payment_data.paymentmethod_id">
                    <option value="">Select Payment Method</option>
                    @foreach($paymentMethods as $paymentMethod)
                        <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                    @endforeach
                </select>
                @error('payment_data.paymentmethod_id') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-12">
                <div class="col-lg-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-2" wire:loading.attr="disabled">Save Payment

                        <i wire:loading.remove wire:target="savePayment" class="fa fa-check"></i>

                        <span wire:loading wire:target="savePayment" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    </button>
                    <a href="{{ route('supplier.payment.index') }}" class="btn btn-danger btn-lg">Cancel</a>
                </div>

            </div>
        </div>
    </form>
</div>
