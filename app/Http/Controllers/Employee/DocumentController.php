<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Auth::user()->documents()->latest()->get();
        return view('employee.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);
        $file = $request->file('file');
        $path = $file->store('documents/' . Auth::id(), 'public');
        Document::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]);
        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->user_id !== Auth::id()) abort(403);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
