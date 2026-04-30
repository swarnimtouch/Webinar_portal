
@extends('admin.layout.app')

@section('content')

<a href="{{ route('category.create') }}" class="btn btn-primary">Add Category</a>

<table class="table mt-3">
<tr>
<th>ID</th>
<th>Name</th>
<th>Action</th>
</tr>

@foreach($categories as $cat)
<tr>
<td>{{ $cat->id }}</td>
<td>{{ $cat->name }}</td>
<td>
<a href="{{ route('category.edit',$cat->id) }}" class="btn btn-info">Edit</a>

<form action="{{ route('category.destroy',$cat->id) }}" method="POST" style="display:inline;">
@csrf
@method('DELETE')
<button class="btn btn-danger">Delete</button>
</form>

</td>
</tr>
@endforeach

</table>

@endsection
