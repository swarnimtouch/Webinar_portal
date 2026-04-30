
@extends('admin.layout.app')

@section('content')

<form method="POST" action="{{ route('category.store') }}">
@csrf

<input type="text" name="name" class="form-control" placeholder="Category Name">

<button class="btn btn-success mt-2">Save</button>

</form>

@endsection
