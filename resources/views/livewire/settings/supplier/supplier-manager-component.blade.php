@section('pageHeaderTitle','Supplier Manager')
@section('pageHeaderDescription','Manage All Suppliers')

@section('pageHeaderAction')
    @if(userCanView('supplier.create'))
        <div class="row">
            <div class="col-sm">
                <div class="mb-4">
                    <button  wire:click="new" wire:target="new" wire:loading.attr="disabled" type="button" class="btn btn-primary waves-effect waves-light">
                        <i wire:loading.remove wire:target="new" class="bx bx-plus me-1"></i>
                        <span wire:loading wire:target="new" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        New Supplier
                    </button>
                </div>
            </div>
            <div class="col-sm-auto">

            </div>
        </div>
    @endif
@endsection

<div>
    @if(View::hasSection('pageHeaderTitle'))
        @include('shared.pageheader')
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone Numbers</th>
                            <th>Email Address</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->get() as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phonenumber }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td>
                                    @if(userCanView('supplier.toggle'))
                                        <div class="form-check form-switch mb-3" dir="ltr">
                                            <input wire:change="toggle({{ $supplier->id }})" id="user{{ $supplier->id }}" type="checkbox" class="form-check-input" id="customSwitch1" {{ $supplier->status ? 'checked' : '' }}>
                                            <label class="form-check-label" for="customSwitch1">{{ $supplier->status ? 'Active' : 'Inactive' }}</label>
                                        </div>
                                    @else
                                        <label class="form-check-label">{{ $supplier->status ? 'Active' : 'Inactive' }}</label>
                                    @endif
                                </td>
                                <td>
                                    @if(userCanView('supplier.update'))
                                        <a class="btn btn-outline-primary btn-sm edit" wire:click="edit({{ $supplier->id }})" href="javascript:void(0);" >

                                            <span wire:loading wire:target="edit({{ $supplier->id }})" class="spinner-border spinner-border-sm me-2" role="status"></span>

                                            <i wire:loading.remove wire:target="edit({{ $supplier->id }})" class="fas fa-pencil-alt"></i>

                                        </a>
                                    @endif
                                    @if(userCanView('supplier.destroy'))
                                        <a class="btn btn-outline-primary btn-sm delete confirm-text"  wire:click="destroy({{ $supplier->id }})" href="javascript:void(0);">

                                            <span wire:loading wire:target="destroy({{ $supplier->id }})" class="spinner-border spinner-border-sm me-2" role="status"></span>

                                            <i wire:loading.remove wire:target="destroy({{ $supplier->id }})" class="fas fa-trash"></i>

                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('component.include.modal')
</div>
