<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TopicExportController extends Controller
{
    public function export($id)
    {
        // Fetch the topic along with its messages and the users who sent them
        $topic = Topic::with(['messages.user'])->findOrFail($id);

        // Pass data to an isolated PDF blade view
        $pdf = Pdf::loadView('pdf.topic-messages', compact('topic'));

        // Force a download with a clean filename
        return $pdf->download("topic-{$topic->id}-chats.pdf");
    }
}