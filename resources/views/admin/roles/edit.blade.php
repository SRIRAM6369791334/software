@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Edit Role: {{ $role->name }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="input-group input-group-outline mb-3 is-filled">
                            <label class="form-label">Role Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                        </div>
                        <div class="input-group input-group-outline mb-3 is-filled">
                            <label class="form-label">Guard Name</label>
                            <input type="text" name="guard_name" class="form-control" value="{{ $role->guard_name }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
