<div>
    <form method="post" wire:submit.prevent="saveStock">
        <div class="row">

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Stock Name</label>
                    <input name="name" class="form-control" placeholder="Product Name" wire:model.defer="product_data.name" type="text">
                    @error('product_data.name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Location</label>
                    <input name="location" class="form-control" placeholder="Location" wire:model.defer="product_data.location" type="text">
                    @error('product_data.location') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Code</label>
                    <input name="code" class="form-control" placeholder="Code" wire:model.defer="product_data.code" type="text">
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Brand</label>
                    <select class="form-control"  wire:model.defer="product_data.brand_id">
                        <option value="">Choose Brand</option>
                        @foreach($this->brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Category</label>
                    <select class="form-control"  wire:model.defer="product_data.category_id">
                        <option value="">Choose Category</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Manufacturers</label>
                    <select class="form-control" name="category_id" wire:model.defer="product_data.manufacturer_id">
                        <option value="">Choose Manufacturer</option>
                        @foreach($this->manufacturers as $manufacturer)
                            <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Classification</label>
                    <select class="form-control" name="category_id" wire:model.defer="product_data.classification_id">
                        <option value="">Choose Classification</option>
                        @foreach($this->classifications as $classification)
                            <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Stock Group</label>
                    <select class="form-control" name="category_id" wire:model.defer="product_data.stockgroup_id">
                        <option value="">Choose Stock Group</option>
                        @foreach($this->stockgroups as $stockgroup)
                            <option value="{{ $stockgroup->id }}">{{ $stockgroup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12" >
                <div class="mb-3" wire:ignore>
                    <label>Can Product Expiry ?</label>
                    <select class="form-control" name="expiry" wire:model.defer="product_data.expiry">
                        <option value="">Select One</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    @error('product_data.expiry') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Pieces</label>
                    <input  class="form-control" placeholder="Pieces" wire:model.defer="product_data.piece" type="text">
                    @error('product_data.piece') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Box</label>
                    <input  class="form-control" placeholder="Cost Price" wire:model.defer="product_data.box"  type="text">
                    @error('product_data.box') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Carton Content</label>
                    <input  placeholder="Carton Content" wire:model.defer="product_data.carton" class="form-control" type="number">
                    @error('product_data.carton') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Barcode</label>
                    <div class="input-group col-md-12">
                        <input readonly=""  id="text_barcode" type="text" wire:model.defer="product_data.barcode" name="barcode" class="form-control">
                        <div class="input-group-btn">
                            <button data-toggle="modal" data-target="#myModal" type="button" class="btn btn-primary">Capture Barcode</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12" >
                <div class="mb-3" wire:ignore>
                    <label>Sachet Product ?</label>
                    <select class="form-control" name="sarchet" wire:model.defer="product_data.sachet">
                        <option value="">Select One</option>
                        <option value="1">Yes</option>
                        <option selected value="0">No</option>
                    </select>
                    @error('expiry') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-12">
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Description" wire:model.defer="product_data.description"></textarea>
                </div>
            </div>

            @if(userCanView('product.changeSellingPrice'))
                <div class="col-lg-12">
                    <h4>Product Price Settings</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="mb-3">
                                <label>Bulk Price <span style="color:red;">*</span></label>
                                <input type="number" wire:model.defer="product_data.bulk_price" step="0.00001" value=""   class="form-control" name="bulk_price" placeholder="Bulk Price">
                                @error('product_data.bulk_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="mb-3">
                                <label>Wholesales Price <span style="color:red;">*</span></label>
                                <input type="number" wire:model.defer="product_data.whole_price" step="0.00001" value=""   class="form-control" name="whole_price" placeholder="Wholesales Price">
                                @error('product_data.whole_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="mb-3">
                                <label>Retail Sales Price<span style="color:red;">*</span></label>
                                <input type="number"  wire:model.defer="product_data.retail_price" step="0.00001" value=""   class="form-control" name="retail_price" placeholder="Retail Sales Price">
                                @error('product_data.retail_price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-lg-12">
            <div class="col-lg-12 mt-4">
                <button type="submit" class="btn btn-primary btn-lg me-2" wire:loading.attr="disabled">Save

                    <i wire:loading.remove wire:target="saveStock" class="fa fa-check"></i>

                    <span wire:loading wire:target="saveStock" class="spinner-border spinner-border-sm me-2" role="status"></span>
                </button>
                <a href="{{ route('product.index') }}" class="btn btn-danger btn-lg">Cancel</a>
            </div>

        </div>
    </form>
</div>
