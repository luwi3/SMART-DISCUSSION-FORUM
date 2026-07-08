<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
        <!-- Main Card Wrapper -->
        <div class="w-full sm:max-w-xl bg-white px-10 py-12 shadow-md rounded-xl border border-slate-100">
            
            <!-- Header Logo & Title Section -->
            <div class="flex flex-col items-center mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <!-- Graduation Cap Icon -->
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.8 9L12 4.53 20.2 9 12 13.47 3.8 9zM6 13.11V17c0 1.66 2.69 3 6 3s6-1.34 6-3v-3.89l-6 3.27-6-3.27z"/>
                    </svg>
                    <span class="text-xl font-black tracking-wide text-slate-800">
                        <span class="text-blue-600">SMART</span> DISCUSSION FORUM
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 mb-1">Welcome Back!</h2>
                <p class="text-sm text-slate-500">Login to continue to your account</p>
            </div>

            <!-- Session Status Errors -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email / Username Input -->
                <div class="mb-5">
                    <label for="login" class="block text-sm font-bold text-slate-800 mb-2">Email / Username</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <!-- User Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="login" type="text" name="login" :value="old('login')" required autofocus 
                            placeholder="Enter your email or username"
                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                    </div>
                    <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Password Input -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-bold text-slate-800 mb-2">Password</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <!-- Lock Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                            placeholder="Enter your password"
                            class="block w-full pl-11 pr-11 py-3 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900 placeholder-slate-400 text-sm transition-all" />
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 hover:text-slate-600">
                            <!-- Eye/Visibility Icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password Layout -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                        <span class="ms-2 text-sm font-medium text-slate-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-semibold text-blue-600 hover:underline" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <!-- Main Login Button -->
                <div class="mb-6">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-sm tracking-wide">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        LOGIN
                    </button>
                </div>
            </form>

            <!-- Divider Line from image_f4125f.jpg -->
            <div class="relative flex py-4 items-center mb-4">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-xs font-semibold text-slate-400 uppercase">or</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <!-- Registration Footer Option -->
            <div class="text-center">
                <p class="text-sm font-medium text-slate-600 mb-4">Don't have an account?</p>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center gap-2 border-2 border-emerald-500 hover:bg-emerald-50 text-emerald-600 font-bold py-3 px-4 rounded-lg transition-colors tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    REGISTER
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
