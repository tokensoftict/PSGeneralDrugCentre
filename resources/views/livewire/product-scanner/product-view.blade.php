<div x-data="invoice()" x-init="getInputFromBarcode()">
    <section id="fullscreen">
        <div class="container flex">
            <div class="left">
                <div class="main_image">
                    <center>
                        <img src="{{ asset($this->product->image_path) }}" onclick="enterFullScreen(document.documentElement)" style="width: 75%; margin: 0px auto" class="slide">
                    </center>
                </div>
                @if(isset($this->product->classification))
                    <div class="option flex">
                        @foreach($this->product?->classification->stocks()->where('retail_price', '>', 0)->whereNotNull('image_path')->limit(10)->get() as $stock)
                            <img wire:click="getProductByID('{{ $stock->id }}')" src="{{ asset($stock->image_path) }}" onclick="{{ asset($stock->image_path) }}">
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="right">
                <h3>{{ $this->product->name }}</h3>
                <h4 id="price"> <small>&#8358;</small> {{ money($this->product->retail_price) }} </h4>
                <h4 id="quantity" style="margin-top: 10px"> Available Quantity : {{ $this->product->retail }} </h4>
                <p>
                    @if(!empty($this->product->description))
                    {{ $this->product->description }}
                    @else

                    @endif
                </p>

            </div>
        </div>
    </section>

    <script>
        function invoice()
        {
            return {

                async requestProductWithBarcode(barcode)
                {
                    @this.getProductByBarcode(barcode);
                },

                getInputFromBarcode()
                {
                    var obj = this;
                    $(document).ready(function(){
                        $(document).scannerDetection({
                            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                            endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
                            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms// turn off scanner detection if an input has focus
                            startChar: [16], // Prefix character for the cabled scanner (OPL6845R)
                            endChar: [40],
                            ignoreIfFocusOn : ['customer-search-text', 'searchText'],
                            onComplete: function(barcode){
                                //window.focus();
                                obj.requestProductWithBarcode(barcode);
                            }, // main callback function
                            scanButtonKeyCode: 116, // the hardware scan button acts as key 116 (F5)
                            scanButtonLongPressThreshold: 5, // assume a long press if 5 or more events come in sequence
                            onScanButtonLongPressed: function(){
                                alert('key pressed');
                            }, // callback for long pressing the scan button
                            onError: function(string){}
                        });
                    });
                }


            }
        }

       window.onload = function()
       {

       }

        function enterFullScreen(element) {
            if(element.requestFullscreen) {
                element.requestFullscreen();
            }else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();     // Firefox
            }else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();  // Safari
            }else if(element.msRequestFullscreen) {
                element.msRequestFullscreen();      // IE/Edge
            }
        };
    </script>
</div>
