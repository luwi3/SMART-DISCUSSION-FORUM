<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
        <div class="w-full sm:max-w-4xl bg-white px-10 py-10 shadow-md rounded-xl border border-slate-100 my-6">
            
            <div class="flex flex-col items-center mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.8 9L12 4.53 20.2 9 12 13.47 3.8 9zM6 13.11V17c0 1.66 2.69 3 6 3s6-1.34 6-3v-3.89l-6 3.27-6-3.27z"/>
                    </svg>
                    <span class="text-xl font-black tracking-wide text-slate-800">
                        <span class="text-blue-600">SMART</span> DISCUSSION FORUM
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 mb-1">Create New Account</h2>
                <p class="text-sm text-slate-500">Fill in your details to get started</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Full Name</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Enter your full name"
                                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Email Address</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <input id="email" type="email" name="email" :value="old('email')" required placeholder="Enter your email address"
                                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <input type="hidden" name="username" id="username_hidden" value="">

                        <div class="mb-4">
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Contact</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <input id="phone" type="text" name="phone" :value="old('phone')" required placeholder="Enter your contact number"
                                    class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                            </div>
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Password</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                                <input id="password" type="password" name="password" required placeholder="Create a password"
                                    class="block w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Confirm Password</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm your password"
                                    class="block w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Select Role</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <select id="role" name="role" required 
                                    class="block w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-600 text-sm appearance-none transition-all">
                                    <option value="" disabled selected>-- Select Role --</option>
                                    <option value="student">Student</option>
                                    <option value="lecturer">Lecturer</option>
                                    <option value="administrator">Administrator</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                        </div>
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-6 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-blue-600 font-bold mb-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <span class="text-sm tracking-wide">Forum Rules & Regulations</span>
                            </div>
                            <ol class="text-sm text-slate-700 space-y-3 list-decimal list-inside pl-1 leading-relaxed font-medium">
                                <li>Respect all members</li>
                                <li>No abusive or offensive language</li>
                                <li>Participate actively</li>
                                <li>Keep discussions relevant</li>
                                <li>Do not spam or post irrelevant content</li>
                            </ol>
                        </div>

                        <div class="mt-8 pt-4 border-t border-blue-100/60">
                            <label for="agreed_to_rules" class="inline-flex items-start cursor-pointer">
                                <input id="agreed_to_rules" type="checkbox" name="agreed_to_rules" value="1" required class="mt-0.5 rounded border-blue-300 text-blue-600 focus:ring-blue-400 shadow-sm">
                                <span class="ms-2.5 text-xs font-bold text-slate-700 select-none">
                                    I have read and understood the rules
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('agreed_to_rules')" class="mt-1" />
                        </div>
                    </div>

                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 border border-red-500 hover:bg-red-50 text-red-500 font-bold py-2.5 px-4 rounded-lg transition-colors text-sm tracking-wide uppercase">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        Decline
                    </a>
                    
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors shadow-sm text-sm tracking-wide uppercase">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Agree & Continue
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm font-medium text-slate-500">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline ms-1">Login</a>
                </p>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('email').addEventListener('input', function() {
            let emailVal = this.value;
            let usernameStub = emailVal.split('@')[0];
            document.getElementById('username_hidden').value = usernameStub;
        });
    </script>
</x-guest-layout>