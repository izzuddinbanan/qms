<form action="{{ route('importing.defect') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <select name="client_id">
    	<option value="">Select Client</option>
    	@foreach($clients as $client)
    	<option value="{{ $client->id }}">{{ $client->name }}</option>
    	@endforeach
    </select>
	<input type="file" name="file">
	<input type="submit">
</form>