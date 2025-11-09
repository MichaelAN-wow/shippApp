<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StickyNote;
use App\Models\ProductionBatch;
use Illuminate\Support\Facades\Auth;

class StickyNoteController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id');
        $folder = $request->input('folder', 'General');
        $isTrash = $request->has('trash');

        $notes = StickyNote::where('company_id', $companyId)
            ->where('folder', $folder)
            ->where('trashed', $isTrash)
            ->get();

        $folders = StickyNote::where('company_id', $companyId)
            ->distinct()
            ->pluck('folder');

        return view('Admin.sticky_notes', compact('notes', 'folders', 'folder', 'isTrash'));
    }

    public function store(Request $request)
    {
        try {
            $note = StickyNote::create([
                'company_id' => session('company_id'),
                'folder' => $request->input('folder', 'General'),
                'tags' => $request->input('tags', []),
                'content' => $request->input('content', ''),
                'color' => $request->input('color', '#FFD700'),
                'x' => $request->input('x', 100),
                'y' => $request->input('y', 100),
                'width' => $request->input('width', 200),
                'height' => $request->input('height', 200),
                'linked_to' => $request->input('linked_to'),
                'author' => strtoupper(substr(Auth::user()?->name ?? 'U', 0, )),
                'trashed' => false,
            ]);

            return response()->json($note);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $note = StickyNote::findOrFail($id);

        $note->update([
            'content' => $request->input('content'),
            'x' => $request->input('x'),
            'y' => $request->input('y'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'tags' => $request->input('tags'),
            'color' => $request->input('color'), // ✅ this ensures color is preserved
            'linked_to' => $request->input('linked_to'),
        ]);

        return response()->json(['status' => 'success']);
    }

    public function softDelete($id)
    {
        $note = StickyNote::findOrFail($id);
        $note->trashed = true;
        $note->save();

        return response()->json(['status' => 'soft-deleted']);
    }

    public function restore($id)
    {
        $note = StickyNote::findOrFail($id);
        $note->trashed = false;
        $note->save();

        return response()->json(['status' => 'restored']);
    }

    public function destroy($id)
    {
        StickyNote::destroy($id);
        return response()->json(['status' => 'deleted']);
    }

    public function getTrashed()
    {
        $companyId = session('company_id');

        $notes = StickyNote::where('company_id', $companyId)
            ->where('trashed', true)
            ->get(['id', 'content']); // Just return essentials for modal

        return response()->json($notes);
    }

    public function restoreSelected(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No IDs provided'], 400);
        }

        StickyNote::whereIn('id', $ids)->update(['trashed' => false]);

        return response()->json(['status' => 'restored']);
    }

    public function emptyTrash()
    {
        $companyId = session('company_id');

        StickyNote::where('company_id', $companyId)
            ->where('trashed', true)
            ->delete();

        return response()->json(['status' => 'trash emptied']);
    }

    public function saveNote(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        $batch = ProductionBatch::findOrFail($request->batch_id);
        $batch->notes = $request->notes;
        $batch->last_edited_by = Auth::user()?->name ?? 'Unknown';
        $batch->last_edited_at = now();
        $batch->save();

        return response()->json(['status' => 'success']);
    }

    public function linkNote(Request $request, $id)
    {
        $note = StickyNote::findOrFail($id);
        $note->linked_to = $request->linked_to;
        $note->save();

        return response()->json(['status' => 'linked']);
    }
}
