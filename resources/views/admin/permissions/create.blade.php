@extends('layouts.app')

@section('title', 'Create Permission')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Create Permission</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Permission Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="input-group input-group-static mb-3">
                            <label for="permission_group_id" class="ms-0">Permission Group</label>
                            <select name="permission_group_id" id="permission_group_id" class="form-control" required>
                                <option value="" disabled selected>Select a group</option>
                                @foreach($permissionGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
