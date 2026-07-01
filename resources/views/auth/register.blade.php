<x-guest-layout>
    <div class="min-h-screen flex w-screen bg-gray-100 overflow-x-hidden">
        
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 flex-col justify-center p-12 text-white relative">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 to-slate-950 opacity-95"></div>
            
            <div class="relative z-10 max-w-md mx-auto space-y-6">
                <div class="flex items-center gap-3 text-2xl font-black text-blue-400 border-b border-slate-800 pb-4 uppercase tracking-wider">
                    <i class="fas fa-shield-halved text-3xl"></i> Forum Guidelines
                </div>
                
                <div class="space-y-4 text-sm text-slate-300">
                    <div class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-xs font-bold text-blue-400 border border-blue-500/20">1</span>
                        <p><strong class="text-white block font-semibold">Respect Professional Boundaries</strong> Maintain academic courtesy across all active student and lecturer panels.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-xs font-bold text-blue-400 border border-blue-500/20">2</span>
                        <p><strong class="text-white block font-semibold">No Defamatory Content</strong> Zero tolerance for offensive discourse, explicit material, or spam links.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-xs font-bold text-blue-400 border border-blue-500/20">3</span>
                        <p><strong class="text-white block font-semibold">Keep Threads Categorized</strong> Ensure updates align directly with system computing modules or open threads.</p>
                    </div>
                </div>

                <div class="p-4 bg-slate-800/40 border border-slate-800 rounded-xl text-xs text-slate-400 leading-relaxed">
                    <i class="fas fa-triangle-exclamation text-amber-500 mr-1.5"></i> Failure to match standard rules can flag account states for administrative blacklisting.
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100 space-y-5">
                
                <div class="text-center space-y-1">
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Create Account</h2>
                    <p class="text-sm text-gray-500">Join the smart discussion platform</p>
                </div>

                <x-input-error :messages="$errors->get('name')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('username')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('email')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('phone')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('password')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('role')" class="text-xs text-red-500" />
                <x-input-error :messages="$errors->get('agreed_to_rules')" class="text-xs text-red-500" />

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Full Name</label>
        <div class="relative mt-1 shadow-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="fas fa-id-card text-gray-400 text-sm"></i>
            </span>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Enter your full name">
        </div>

    <div>
        <label for="username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Username</label>
        <div class="relative mt-1 shadow-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="fas fa-user text-gray-400 text-sm"></i>
            </span>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Choose a unique username">
        </div>
    </div>

    <div>
        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Email Address</label>
        <div class="relative mt-1 shadow-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="fas fa-envelope text-gray-400 text-sm"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Enter university email">
        </div>
    </div>

    <div>
        <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Contact Number</label>
        <div class="relative mt-1 shadow-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="fas fa-phone text-gray-400 text-sm"></i>
            </span>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="e.g. +256700000000">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Password</label>
            <div class="relative mt-1 shadow-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </span>
                <input id="password" type="password" name="password" required 
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Min 8 chars">
            </div>
        </div>
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Confirm</label>
            <div class="relative mt-1 shadow-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fas fa-shield text-gray-400 text-sm"></i>
                </span>
                <input id="password_confirmation" type="password" name="password_confirmation" required 
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Confirm">
            </div>
        </div>
    </div>

    <div class="space-y-4 pt-2 border-t border-gray-100 mt-4">
        <div>
            <label for="course_code" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Course Code</label>
            <div class="relative mt-1 shadow-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fas fa-graduation-cap text-gray-400 text-sm"></i>
                </span>
                <input id="course_code" type="text" name="course_code" value="{{ old('course_code') }}" required
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="e.g. BCS">
            </div>
        </div>
        <div>
            <label for="reg_no" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Registration Number</label>
            <div class="relative mt-1 shadow-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="fas fa-address-card text-gray-400 text-sm"></i>
                </span>
                <input id="reg_no" type="text" name="reg_no" value="{{ old('reg_no') }}" required
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="e.g. 26/U/1234/PS">
            </div>
        </div>
    </div>

    <div class="pt-1">
        <label class="flex items-center cursor-pointer">
            <input type="checkbox" name="agreed_to_rules" value="1" required class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-xs font-medium text-gray-600 select-none">I agree to the community regulations</span>
        </label>
    </div>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="/" class="w-1/2 text-center px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition font-bold text-xs uppercase tracking-wider">
                            DECLINE
                        </a>
                        <button type="submit" class="w-1/2 px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 shadow-md transition font-bold text-xs uppercase tracking-wider">
                            REGISTER
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-xl">
                            <div class="text-xs font-bold text-red-700 uppercase mb-1">Registration Blocked:</div>
                            <ul class="list-disc pl-4 text-xs text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>

                <div class="text-center pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500">Already have an account? 
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 ml-1 transition duration-150">Login here</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
   
</x-guest-layout>
