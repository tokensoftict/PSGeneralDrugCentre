@section('pageHeaderTitle','User Management')
@section('pageHeaderDescription','Manage User using the system')

@section('pageHeaderAction')
    @if(userCanView('user.create '))
        <div class="row">
        <div class="col-sm">
            <div class="mb-4">
                <button  wire:click="new" wire:target="new" wire:loading.attr="disabled" type="button" class="btn btn-primary waves-effect waves-light">
                    <i wire:loading.remove wire:target="new" class="bx bx-plus me-1"></i>
                    <span wire:loading wire:target="new" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    New User
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
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Full name</th>
                        <th>Username</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Department</th>
                        <th>Group</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->get() as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->department->name ?? ""}}</td>
                            <td>{{ $user->usergroup->name }}</td>
                            <td class="text-end">
                                @if(userCanView('user.update') || userCanView('user.destroy'))
                                    @if(userCanView('user.update'))
                                        <a class="btn btn-outline-primary btn-sm edit" wire:click="edit({{ $user->id }})" href="javascript:void(0);" >

                                            <span wire:loading wire:target="edit({{ $user->id }})" class="spinner-border spinner-border-sm me-2" role="status"></span>

                                            <i wire:loading.remove wire:target="edit({{ $user->id }})" class="fas fa-pencil-alt"></i>

                                        </a>
                                    @endif
                                    @if(userCanView('user.destroy'))
                                        <a class="btn btn-outline-primary btn-sm delete confirm-text"  wire:click="destroy({{ $user->id }})" href="javascript:void(0);">

                                            <span wire:loading wire:target="destroy({{ $user->id }})" class="spinner-border spinner-border-sm me-2" role="status"></span>

                                            <i wire:loading.remove wire:target="destroy({{ $user->id }})" class="fas fa-trash"></i>

                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $this->get()->links() }}
            </div>
        </div>
    </div>

    @include('component.include.modal')

</div>
