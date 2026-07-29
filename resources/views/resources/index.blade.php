<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course Resources - Smart Discussion Forum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { background-color: #f8fafc; min-height: 100vh; color: #334155; padding: 40px 20px; }
        
        .container { max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

        /* Top Navigation Header bar */
        .top-navbar { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0; }
        .brand-title { font-size: 22px; font-weight: 700; color: #0b2265; }
        .back-link { font-size: 14px; font-weight: 600; color: #2563eb; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .back-link:hover { text-decoration: underline; }

        /* Alert Banners */
        .alert-banner { padding: 14px 18px; background-color: #d1fae5; border: 1px solid #10b981; color: #065f46; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .alert-danger { background-color: #fee2e2; border-color: #ef4444; color: #991b1b; }

        /* Main Workspace Grid Split */
        .workspace-grid { display: grid; grid-template-columns: 420px 1fr; gap: 30px; align-items: flex-start; }
        @media (max-width: 900px) { .workspace-grid { grid-template-columns: 1fr; } }

        /* Cards */
        .card { background: white; border-radius: 12px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05); }
        .card-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        .card-desc { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }

        /* Form Controls */
        .form-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        .form-label { font-size: 13px; font-weight: 600; color: #475569; }
        .form-input { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: border-color 0.2s; }
        .form-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .file-input-wrapper { border: 2px dashed #cbd5e1; padding: 16px; border-radius: 8px; text-align: center; background-color: #f1f5f9; cursor: pointer; }
        
        /* Submit Button */
        .btn-submit { width: 100%; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background-color 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background-color: #1d4ed8; }

        /* History Table Styling */
        .table-scroll { width: 100%; overflow-x: auto; }
        .resource-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; min-width: 480px; }
        .resource-table th { background-color: #f8fafc; padding: 14px 16px; font-weight: 600; color: #64748b; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; tracking: 0.05em; }
        .resource-table td { padding: 16px; border-bottom: 1px solid #e2e8f0; color: #334155; vertical-align: middle; }
        
        .badge { display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border-radius: 6px; background-color: #e2e8f0; color: #475569; }
        .badge-pdf { background-color: #fee2e2; color: #991b1b; }
        .badge-doc { background-color: #dbeafe; color: #1e40af; }

        .btn-delete { background: none; border: none; color: #dc2626; font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: none; }
        .btn-delete:hover { text-decoration: underline; }
        .empty-state { text-align: center; padding: 40px 0; color: #94a3b8; font-style: italic; }

        @media (max-width: 640px) {
            body { padding: 20px 14px; }
            .card { padding: 18px; }
            .top-navbar { flex-wrap: wrap; gap: 10px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Top Navbar Header instead of Sidebar -->
        <header class="top-navbar">
            <div class="brand-title">Smart Discussion Forum</div>
            <a href="{{ route('lecturer.dashboard') }}" class="back-link">⬅ Back to Main Dashboard</a>
        </header>

        <!-- Status Notifications -->
        @if(session('success'))
            <div class="alert-banner">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-banner alert-danger">❌ {{ session('error') }}</div>
        @endif
        {{-- 🔧 FIX: validation failures (missing/blank courseCode, disallowed file type,
             file over 20MB) were being silently swallowed — the form just reloaded with
             no feedback because nothing ever read Laravel's $errors bag. This surfaces
             them so a rejected upload is visible instead of looking like nothing happened. --}}
        @if($errors->any())
            <div class="alert-banner alert-danger">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="workspace-grid">
            <!-- Left Side: Interactive Upload Panel -->
            <section class="card">
                <h2 class="card-title">📁 Upload Resource</h2>
                <p class="card-desc">Publish reference books, syllabus guidelines, or lecture slides directly to specific courses.</p>
                
                <form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Document Title</label>
                        <input type="text" name="title" class="form-input" placeholder="e.g., Chapter 1: Introduction to OOP" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="courseCode" class="form-input" placeholder="e.g., BIT 2204" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select File (PDF, Slides, Docs, Images, Zip)</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="file" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">📤 Publish Course Resource</button>
                </form>
            </section>

            <!-- Right Side: Content List Panel Matrix -->
            <section class="card">
                <h2 class="card-title">📋 Uploaded Resources History</h2>
                <p class="card-desc">Track, audit, and manage your currently active course reference uploads.</p>

                @if($resources->isEmpty())
                    <p class="empty-state">No shared references uploaded yet.</p>
                @else
                    <div class="table-scroll">
                    <table class="resource-table">
                        <thead>
                            <tr>
                                <th>Resource Title</th>
                                <th>Course</th>
                                <th>Type</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resources as $resource)
                                <tr>
                                    <td style="font-weight: 600; color: #0f172a;">{{ $resource->title }}</td>
                                    <td><span class="badge">{{ $resource->courseCode }}</span></td>
                                    <td>
                                        <span class="badge {{ in_array(strtolower($resource->file_type), ['pdf']) ? 'badge-pdf' : (in_array(strtolower($resource->file_type), ['doc','docx']) ? 'badge-doc' : '') }}">
                                            {{ $resource->file_type ?? 'File' }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('resources.destroy', $resource->resourceID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resource?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @endif
            </section>
        </div>
    </div>

</body>
</html>