<div class="batch-note-wrapper" style="margin-bottom: 20px;">
    <label for="batch_notes">Batch Notes</label>
    <textarea id="batch_notes" name="batch_notes" class="batch-note"
        onblur="saveBatchNote(this)"
        style="background:#f5f5f5; border-radius:8px; padding:12px; font-size:14px; width:100%; resize:vertical; min-height:80px;">{{ $batch->notes ?? '' }}</textarea>

    <div id="save-confirmation" style="display:none; color: #4CAF50; font-size: 12px; margin-top: 5px;">
        ✓ Saved
    </div>

    @if($batch->last_edited_by)
        <small style="color:#666;">🕘 Edited by {{ $batch->last_edited_by }} at {{ \Carbon\Carbon::parse($batch->last_edited_at)->format('m/d/y g:i A') }}</small>
    @endif
</div>

<script>
function saveBatchNote(element) {
    const note = element.value;
    fetch('/batch/save-note', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            batch_id: '{{ $batch->id }}',
            notes: note
        })
    }).then(res => {
        if(res.ok) {
            document.getElementById('save-confirmation').style.display = 'block';
            setTimeout(() => {
                document.getElementById('save-confirmation').style.display = 'none';
            }, 2000);
        }
    });
}
</script>
