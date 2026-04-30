
@extends('admin.layout.app')

@section('content')

<form method="POST" action="{{ route('category.update',$category->id) }}">
@csrf
@method('PUT')

<input type="text" name="name" value="{{ $category->name }}" class="form-control">

<button class="btn btn-success mt-2">Update</button>

</form>

@endsection
