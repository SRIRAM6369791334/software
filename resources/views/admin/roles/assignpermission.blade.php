@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Assign Permissions to {{ $role->name }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.assignPermission') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                        
                        <div class="row">
                            @foreach($permissionGroups as $group)
                            <div class="col-md-4 mb-4">
                                <div class="card bg-gray-100 border h-100">
                                    <div class="card-header pb-0 bg-transparent">
                                        <h6 class="mb-0 text-primary">{{ $group->name }}</h6>
                                    </div>
                                    <div class="card-body pt-2">
                                        @foreach($group->permissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" 
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label custom-control-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Permissions</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
