@extends('layouts.admin_master')

@section('content')
<div class="container mt-4" style="max-width: 800px;">
    <h2 class="mb-4">🤖 Trace Chat</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form id="trace-form">
        @csrf
        <div class="input-group mb-3">
            <input type="text" id="command" name="command" class="form-control" placeholder="Type a command (e.g. fix materials page)">
            <button class="btn btn-dark" type="submit">Send to Trace</button>
        </div>
    </form>

    <div id="trace-response" class="mt-3 alert" style="display:none;"></div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('trace-form');
    const input = document.getElementById('command');
    const responseBox = document.getElementById('trace-response');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const command = input.value;

        responseBox.style.display = 'block';
        responseBox.className = 'alert alert-info';
        responseBox.innerHTML = 'Trace is thinking... 🤖';

        fetch("{{ route('trace.command') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ command: command })
        })
        .then(res => res.json())
        .then(data => {
            responseBox.className = 'alert alert-success';
            responseBox.innerHTML = data.message || '✅ Command completed.';
        })
        .catch(err => {
            responseBox.className = 'alert alert-danger';
            responseBox.innerHTML = 'Error: ' + err.message;
        });
    });
});
</script>
@endsection