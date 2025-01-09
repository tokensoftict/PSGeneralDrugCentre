<div>
    <form method="post" wire:submit.prevent="saveStock">
        <div class="row">

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Stock Name</label>
                    <input name="name" required class="form-control" placeholder="Product Name" wire:model.defer="product_data.name" type="text">
                    @error('product_data.name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Location</label>
                    <input name="location"  class="form-control" placeholder="Location" wire:model.defer="product_data.location" type="text">
                    @error('product_data.location') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Code</label>
                    <input name="code"  class="form-control" placeholder="Code" wire:model.defer="product_data.code" type="text">
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3" wire:ignore>
                    <label>Brand</label>
                    <select class="form-control" {{ count($this->brands) > 0 ? 'required' : '' }}  wire:model.defer="product_data.brand_id">
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
                    <select class="form-control" {{ count($this->categories) > 0 ? 'required' : '' }}  wire:model.defer="product_data.category_id">
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
                    <select class="form-control" {{ count($this->manufacturers) > 0 ? 'required' : '' }} name="category_id" wire:model.defer="product_data.manufacturer_id">
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
                    <select class="form-control" {{ count($this->classifications) > 0 ? 'required' : '' }} name="category_id" wire:model.defer="product_data.classification_id">
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
                    <select class="form-control" {{ count($this->stockgroups) > 0 ? 'required' : '' }} name="category_id" wire:model.defer="product_data.stockgroup_id">
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
                    <select class="form-control" required name="expiry" wire:model.defer="product_data.expiry">
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
                    <input  class="form-control" required placeholder="Pieces" wire:model.defer="product_data.piece" type="text">
                    @error('product_data.piece') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Box</label>
                    <input  class="form-control" required placeholder="Box" wire:model.defer="product_data.box"  type="text">
                    @error('product_data.box') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Carton Content</label>
                    <input  placeholder="Carton Content" required wire:model.defer="product_data.carton" class="form-control" type="number">
                    @error('product_data.carton') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="mb-3">
                    <label>Barcode</label>
                    <div class="input-group col-md-12">
                        <input readonly=""  id="text_barcode" type="text"  name="barcode" class="form-control">
                        <div class="input-group-btn">
                            <button id="barcode" type="button" class="btn btn-primary">Capture Barcode</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12" >
                <div class="mb-3" wire:ignore>
                    <label>Sachet Product ?</label>
                    <select class="form-control" required name="sachet" wire:model.defer="product_data.sachet">
                        <option value="">Select One</option>
                        <option value="1">Yes</option>
                        <option selected value="0">No</option>
                    </select>
                    @error('expiry') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12" >
                <div class="mb-3" wire:ignore>
                    <label>Minimum Quantity</label>
                    <input  placeholder="Minimum Quantity" required wire:model.defer="product_data.minimum_quantity" class="form-control" type="number">
                    @error('product_data.minimum_quantity') <span class="text-danger">{{ $message }}</span> @enderror
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
                        @if(department_by_quantity_column('bulksales', false)->status)
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3">
                                    <label>Bulk Price <span style="color:red;">*</span></label>
                                    <input type="number" required wire:model.defer="product_data.bulk_price" step="0.00001" value=""   class="form-control" name="bulk_price" placeholder="Bulk Price">
                                    @error('product_data.bulk_price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                        @if(department_by_quantity_column('wholesales', false)->status)
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3">
                                    <label>Wholesales Price <span style="color:red;">*</span></label>
                                    <input type="number" required wire:model.defer="product_data.whole_price" step="0.00001" value=""   class="form-control" name="whole_price" placeholder="Wholesales Price">
                                    @error('product_data.whole_price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        @if(department_by_quantity_column('retail', false)->status)
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="mb-3">
                                    <label>Retail Sales Price<span style="color:red;">*</span></label>
                                    <input type="number" required  wire:model.defer="product_data.retail_price" step="0.00001" value=""   class="form-control" name="retail_price" placeholder="Retail Sales Price">
                                    @error('product_data.retail_price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif


            <div class="col-lg-12 mt-3">
                <h4>Product Image</h4>
                <hr/>
                <div class="mb-3">
                    <label>Product Image</label>
                    <input type="file" id="formFile"  name="logo" wire:model.defer="product_data.image_path" style="width: 0;height: 0;padding: 0; margin: 0" >
                    <div class="form-control">
                        <br/>
                        <img src="{{$this->product_data['image_path'] !== NULL ? (is_string($this->product_data['image_path']) ? asset($this->product_data['image_path']) : $this->product_data['image_path']->temporaryUrl()) : asset('images/brands/placholder.jpg') }}"   class="img-responsive" style="width:15%; margin: auto; display: block;"/>
                        <br/>
                        <div wire:loading wire:target="product_data.image">Uploading...</div>
                        <button type="button" onclick="formFile.click()" class="btn btn-sm btn-success">Select Image and Upload</button>
                    </div>
                    @error('product_data.image_path') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

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
    <div  class="modal fade" wire:ignore.self id="simpleBarcodeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Barcode Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="modal-title">Product Barcode List(s)</h5>
                            <table class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bar Code</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($this->product->id))
                                    @foreach($this->barcodes as $barcode)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{!! $barcode !!}</td>
                                            <td><button href="#" onclick="deleteBarcode('{{ $barcode }}')" class="btn btn-danger btn-sm">Delete</button></td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveBarcode" wire:target="saveBarcode" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading wire:target="saveBarcode" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let barCodeOpen = false;
        window.onload = function (){
            $(document).ready(function(){
                let myModal = "";
                myModal = new bootstrap.Modal(document.getElementById("simpleBarcodeModal"), {});

                document.getElementById("simpleBarcodeModal").addEventListener('shown.bs.modal', function () {
                    barCodeOpen = true
                })

                document.getElementById("simpleBarcodeModal").addEventListener('hidden.bs.modal', function () {
                    barCodeOpen = false
                })

                $('#barcode').on('click', function (){
                    myModal.show();
                });
            });

            $(document).scannerDetection({
                timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
                avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
                ignoreIfFocusOn: 'input', // turn off scanner detection if an input has focus
                startChar: [16], // Prefix character for the cabled scanner (OPL6845R)
                endChar: [40],
                onComplete: function(barcode){
                    captureBarcode(barcode);
                }, // main callback function
                scanButtonKeyCode: 116, // the hardware scan button acts as key 116 (F5)
                scanButtonLongPressThreshold: 5, // assume a long press if 5 or more events come in sequence
                onScanButtonLongPressed: function(){
                    alert('key pressed');
                }, // callback for long pressing the scan button
                onError: function(string){}
            });

            $('#saveBarcode').on('click', function(){
                @this.saveBarcode().then(function(response){
                    setTimeout(function(){
                        window.location.reload();
                    },2000)
                });
            });

        }

        function deleteBarcode(code){
            @this.barcodes = @this.barcodes.filter(item => item !== code)
            console.log(@this.barcodes);
        }

        function captureBarcode(barcode)
        {
            if(barCodeOpen === false)
            {
                alert('Click on capture barcode scanner to capture barcode');
            }else{
                @if(!isset($this->product->id))
                alert('Please save this product before, creating barcode')
                @else
                @this.validateBarcode(barcode).then(function(resp){
                    if(resp.status == false){

                    }
                });
                @endif
            }
        }


    </script>

</div>
