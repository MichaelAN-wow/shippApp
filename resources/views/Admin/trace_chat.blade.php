@extends('layouts.admin_master')

@section('content')
<div class="container mt-4" style="max-width: 800px;">
    <h2 class="mb-4">🤖 Trace Command Center</h2>
    <form id="trace-form">
        @csrf
        <input type="text" name="command" id="command" class="form-control mb-3"
    placeholder="Type a command (e.g. create feature Smart Purchase)" required>
        <button type="submit" class="btn btn-primary">Run Command</button>
    </form>
    <div class="mt-3" id="trace-output" style="font-weight: bold;"></div>
</div>
@endsection

@section('script')
<script>
    document.getElementById('trace-form').addEventListener('submit', function(e) {
        e.preventDefault();
        let command = document.getElementById('command').value;
        let token = document.querySelector('input[name="_token"]').value;

        fetch("{{ route('trace.execute') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ command: command })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('trace-output').innerText = data.message;
        })
        .catch(error => {
            document.getElementById('trace-output').innerText = '❌ Trace encountered an error.';
        });
    });
</script>
@endsection