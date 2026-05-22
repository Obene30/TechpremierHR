@extends('layouts.app')
@section('title', 'My Documents')
@section('content')
<div class="space-y-6" x-data="{ uploadOpen: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">My Documents</h1>
            <p class="text-sm text-gray-500">Upload and manage your HR documents</p>
        </div>
        <button @click="uploadOpen = true" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium">
            <i class="fas fa-upload"></i> Upload Document
        </button>
    </div>

    @if($documents->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-gray-700 font-semibold mb-2">No documents yet</h3>
        <p class="text-gray-500 text-sm mb-4">Upload your HR documents like offer letters, contracts, certificates, etc.</p>
        <button @click="uploadOpen = true" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-indigo-700">
            <i class="fas fa-upload"></i> Upload Your First Document
        </button>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($documents as $doc)
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start justify-between">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ in_array($doc->file_type, ['pdf']) ? 'bg-red-50' : (in_array($doc->file_type, ['doc','docx']) ? 'bg-blue-50' : 'bg-green-50') }}">
                    <i class="fas {{ $doc->file_type === 'pdf' ? 'fa-file-pdf text-red-500' : (in_array($doc->file_type,['doc','docx']) ? 'fa-file-word text-blue-500' : 'fa-file-image text-green-500') }}"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-800 text-sm">{{ $doc->name }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ strtoupper($doc->file_type) }} · {{ $doc->sizeFormatted() }}</div>
                    <div class="text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center hover:bg-indigo-100 text-xs">
                    <i class="fas fa-download"></i>
                </a>
                <form method="POST" action="{{ route('employee.documents.destroy', $doc) }}">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this document?')" class="w-7 h-7 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Upload Modal --}}
<div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.4);">
    <div @click.stop class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Upload Document</h3>
            <button @click="uploadOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('employee.documents.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document Name *</label>
                <input type="text" name="name" required placeholder="e.g. Offer Letter, NIN, Certificate"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div x-data="{ fileName: '' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                <label class="block cursor-pointer">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-indigo-300 transition-colors">
                        <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                        <div class="text-sm text-gray-600" x-text="fileName || 'Click to select file'"></div>
                        <div class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG · Max 10MB</div>
                    </div>
                    <input type="file" name="file" required class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        @change="fileName = $event.target.files[0]?.name || ''">
                </label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="uploadOpen = false" class="flex-1 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700">Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection
