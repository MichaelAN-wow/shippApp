<form method="POST" action="{{ route('trace.command') }}">
    @csrf
    <input type="text" name="command" placeholder="Try: fix materials">
    <button type="submit">Run Trace</button>
</form>