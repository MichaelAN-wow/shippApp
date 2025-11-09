@extends('layouts.admin_master')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Shadows+Into+Light&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    overflow: hidden;
}
.note-board {
    position: relative;
    width: 100vw;
    height: 100vh;
    background-image: linear-gradient(#eee 1px, transparent 1px), linear-gradient(to right, #eee 1px, transparent 1px);
    background-size: 20px 20px;
    overflow: hidden;
}
.note {
    position: absolute;
    width: 200px;
    min-height: 200px;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 2px 2px 8px rgba(0,0,0,0.15);
    cursor: move;
    font-size: 24px;
    font-family: 'Shadows Into Light', cursive;
    font-weight: bold;
    resize: both;
    overflow: auto;
    transition: box-shadow 0.2s ease;
    color: #111;
    z-index: 10;
}
.note.hover-target {
    box-shadow: 0 0 12px 4px #FFD700 !important;
    z-index: 999;
}
.note .delete-btn {
    position: absolute;
    top: 4px;
    right: 6px;
    cursor: pointer;
    font-weight: bold;
    color: #c00;
    background: none;
    border: none;
}
.trash-icon {
    position: fixed;
    bottom: 30px;
    right: 40px;
    width: 60px;
    height: 60px;
    background-image: url('/icons/trashcan.svg');
    background-size: contain;
    background-repeat: no-repeat;
    background-color: transparent;
    border: none;
    border-radius: 8px;
    z-index: 1000;
    transition: box-shadow 0.2s ease;
}
.trash-icon.glow {
    box-shadow: 0 0 12px 4px #FFD700;
    .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    max-width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
}
}
.top-bar {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 999;
}
.top-bar h2 {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 12px;
}
.color-popup {
    display: none;
    margin-top: 10px;
}
.color-popup div {
    margin-right: 6px;
    display: inline-block;
    cursor: pointer;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: 1px solid #000;
}
.color-popup div:hover {
    transform: scale(1.2);
}

.neon-yellow { background-color: #FFC700; }
.neon-red    { background-color: #FF1900; }
.neon-orange { background-color: #FF8800; }
.neon-pink   { background-color: #FF008C; }
.neon-green  { background-color: #D8FF00; }
.lime-green  { background-color: #00FF2E; }
.neon-blue   { background-color: #057DFF; }
.neon-purple { background-color: #9800FF; }
.black       {
    background-color: #000000;
    color: #ffffff !important;
}
.black * {
    color: #ffffff !important;
}
</style>

<div class="note-board" id="noteBoard">

    <div class="top-bar">
        <h2>Sticky Notes</h2>
        <button onclick="toggleColorPopup()" class="btn btn-sm btn-dark">+ Add Sticky</button>
        <div class="color-popup" id="colorPopup">
            <div class="neon-yellow" onclick="addNewNote('#FFC700')"></div>
            <div class="neon-red" onclick="addNewNote('#FF1900')"></div>
            <div class="neon-orange" onclick="addNewNote('#FF8800')"></div>
            <div class="neon-pink" onclick="addNewNote('#FF008C')"></div>
            <div class="neon-green" onclick="addNewNote('#D8FF00')"></div>
	        <div class="lime-green" onclick="addNewNote('#00FF2E')"></div>
            <div class="neon-blue" onclick="addNewNote('#057DFF')"></div>
            <div class="neon-purple" onclick="addNewNote('#9800FF')"></div>
            <div class="black" onclick="addNewNote('#000000')"></div>
        </div>
    </div>
    
@foreach($notes as $note)
    <div class="note {{ $note->color == '#000000' ? 'black' : '' }}"
        style="left: {{ $note->x ?? 50 }}px; top: {{ $note->y ?? 50 }}px;
        background-color: {{ $note->color ?? '#FFD700' }};"
        data-id="{{ $note->id }}">

      {{-- Delete button (not editable) --}}
    <span class="delete-btn" onclick="deleteNote({{ $note->id }})">
    <i class="fas fa-times" style="font-size: 11px;"></i>
    </span>

    {{-- Content area ONLY is editable --}}
    <div class="note-content" contenteditable="true" onblur="updateNote(this)"
     style="outline: none; border: none; background: transparent; padding: 0; margin-top: 10px; white-space: pre-wrap; word-wrap: break-word;">
    {!! nl2br(e($note->content)) !!}
    </div>

        {{-- Author Circle (Bottom Right) --}}
        <div style="
            position: absolute;
            bottom: 5px;
            right: 5px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            pointer-events: none;">
            <div style="
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background-color: #333;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;">
                {{ strtoupper(substr($note->author, 0, 1)) }}
            </div>
        </div>
    </div>
@endforeach

<!-- Trash Icon -->
<div class="trash-icon" id="trashDropZone" title="Drag here to delete or click to open trash view" onclick="openTrashModal()"></div>

<!-- Trash Modal -->
<div id="trashModal" class="modal" style="display: none;">
  <div class="modal-content">
    <h3>Trashed Sticky Notes</h3>
    <div id="trashedNotesContainer"></div>
    <button onclick="restoreSelectedNotes()">♻️ Restore Selected</button>
    <button onclick="emptyTrash()">🔥 Empty Trash</button>
    <button onclick="closeTrashModal()">Close</button>
  </div>
</div>

</div>

<script>
function toggleColorPopup() {
    let popup = document.getElementById('colorPopup');
    popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
}

function addNewNote(color) {
    fetch('/sticky-notes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
        content: "",
         x: 100,
         y: 100,
        color: color
})
    }).then(() => {
        document.getElementById('colorPopup').style.display = 'none';
        location.reload();
    });
}
{{-- Insert this JS inside your existing <script> tag at the bottom, after updateNote() function --}}

function updateNote(el) {
    const noteDiv = el.closest('.note');
    const id = noteDiv.getAttribute('data-id');
    const rawContent = el.innerText || '';
    const content = rawContent.trim();

    if (!id) return console.error('Missing note ID');
    if (!content) return; // Don’t save if content is empty

    const x = parseInt(noteDiv.style.left, 10);
    const y = parseInt(noteDiv.style.top, 10);
    const width = parseInt(noteDiv.offsetWidth, 10);
    const height = parseInt(noteDiv.offsetHeight, 10);
    const color = noteDiv.style.backgroundColor;

    fetch(`/sticky-notes/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ content, x, y, width, height, color })
    }).then(response => {
        if (!response.ok) {
            console.error('Failed to save note');
        }
    });
}
function deleteNote(id) {
    fetch(`/sticky-notes/soft-delete/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload());
}

document.querySelectorAll('.note').forEach(note => {
    note.onmousedown = function (event) {
        const shiftX = event.clientX - note.offsetLeft;
        const shiftY = event.clientY - note.offsetTop;

        function moveAt(pageX, pageY) {
            const board = document.getElementById('noteBoard');
            const maxX = board.offsetWidth - note.offsetWidth;
            const maxY = board.offsetHeight - note.offsetHeight;
            let newX = pageX - shiftX;
            let newY = pageY - shiftY;

            newX = Math.max(0, Math.min(newX, maxX));
            newY = Math.max(0, Math.min(newY, maxY));

            note.style.left = newX + 'px';
            note.style.top = newY + 'px';
        }

        function onMouseMove(event) {
            moveAt(event.pageX, event.pageY);
            const trash = document.getElementById('trashDropZone');
            const trashRect = trash.getBoundingClientRect();
            const noteRect = note.getBoundingClientRect();

            const isOverlapping = !(
                noteRect.right < trashRect.left ||
                noteRect.left > trashRect.right ||
                noteRect.bottom < trashRect.top ||
                noteRect.top > trashRect.bottom
            );

            if (isOverlapping) {
                trash.classList.add('glow');
            } else {
                trash.classList.remove('glow');
            }
        }

        document.addEventListener('mousemove', onMouseMove);

        note.onmouseup = function () {
            document.removeEventListener('mousemove', onMouseMove);
            updateNote(note);
            const trash = document.getElementById('trashDropZone');
            trash.classList.remove('glow');

            const trashRect = trash.getBoundingClientRect();
            const noteRect = note.getBoundingClientRect();

            const droppedInTrash = !(
                noteRect.right < trashRect.left ||
                noteRect.left > trashRect.right ||
                noteRect.bottom < trashRect.top ||
                noteRect.top > trashRect.bottom
            );

            if (droppedInTrash) {
                deleteNote(note.dataset.id);
            }

            note.onmouseup = null;
        };
    };
    note.ondragstart = () => false;
});
</script>

<script>
function openTrashModal() {
    fetch('/sticky-notes/trashed')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('trashedNotesContainer');
            container.innerHTML = '';
            data.forEach(note => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = note.id;
                checkbox.style.marginRight = '8px';

                const label = document.createElement('label');
                label.innerText = note.content.length > 30 ? note.content.substring(0, 30) + '...' : note.content;
                label.style.display = 'inline-block';

                const wrapper = document.createElement('div');
                wrapper.style.marginBottom = '6px';
                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);
                container.appendChild(wrapper);
            });

            document.getElementById('trashModal').style.display = 'block';
        });
}

function closeTrashModal() {
    document.getElementById('trashModal').style.display = 'none';
}

function restoreSelectedNotes() {
    const checked = Array.from(document.querySelectorAll('#trashedNotesContainer input[type="checkbox"]:checked'))
        .map(cb => cb.value);

    if (checked.length === 0) return;

    fetch('/sticky-notes/restore', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: checked })
    }).then(() => {
        closeTrashModal();
        location.reload();
    });
}

function emptyTrash() {
    if (!confirm('Permanently delete all trashed notes? This cannot be undone.')) return;

    fetch('/sticky-notes/empty-trash', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => {
        closeTrashModal();
        location.reload();
    });
}
</script>

@endsection
