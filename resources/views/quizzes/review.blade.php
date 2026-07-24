<x-app-layout>
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Review Uploaded Quiz</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Quiz Title: <span class="font-semibold text-slate-700">{{ $quizData['title'] }}</span> 
                    | Questions Found: <span class="font-semibold text-blue-600">{{ count($quizData['questions']) }}</span>
                </p>
            </div>
            <form action="{{ route('quizzes.confirm') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    <i class="fa-solid fa-check-double mr-2"></i>Confirm & Publish Quiz
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @foreach($quizData['questions'] as $index => $q)
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md">
                            Q{{ $index + 1 }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">{{ $q['points'] }} Point(s)</span>
                    </div>

                    <p class="text-sm font-semibold text-slate-800 mt-2 mb-3">{{ $q['question_text'] }}</p>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 rounded-lg border {{ $q['correct_option'] === 'a' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-bold' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                            A) {{ $q['option_a'] }}
                        </div>
                        <div class="p-2 rounded-lg border {{ $q['correct_option'] === 'b' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-bold' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                            B) {{ $q['option_b'] }}
                        </div>
                        <div class="p-2 rounded-lg border {{ $q['correct_option'] === 'c' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-bold' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                            C) {{ $q['option_c'] }}
                        </div>
                        <div class="p-2 rounded-lg border {{ $q['correct_option'] === 'd' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-bold' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                            D) {{ $q['option_d'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>