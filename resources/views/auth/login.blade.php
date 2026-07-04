<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-[#f4f7fc] py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 space-y-6">
            
            <div class="text-center">
                <div class="flex items-center justify-center gap-2 text-xl font-extrabold text-[#001845] tracking-wide">
                    <i class="fas fa-graduation-cap text-[#0a58ca] text-2xl"></i>
                    <span>SMART <span class="text-[#1d2d44] font-medium">DISCUSSION FORUM</span></span>
                </div>
                <h2 class="text-3xl font-bold text-[#001845] tracking-tight mt-5">Welcome Back!</h2>
                <p class="text-sm text-gray-500 mt-1">Login to continue to your account</p>
            </div>

            <x-auth-session-status class="mb-2" :status="session('status')" />
            <x-input-error :messages="$errors->get('login')" class="text-xs text-red-500" />
            <x-input-error :messages="$errors->get('password')" class="text-xs text-red-500" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-bold text-[#1d2d44] uppercase tracking-wide mb-1.5">Email / Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class="far fa-user text-gray-400 text-sm"></i>
                        </span>
                        <input id="login" type="text" name="login" :value="old('login')" required autofocus 
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0a58ca]/20 focus:border-[#0a58ca] text-gray-700 transition" 
                            placeholder="Enter your email or username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-[#1d2d44] uppercase tracking-wide mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="current-password" 
                            class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0a58ca]/20 focus:border-[#0a58ca] text-gray-700 transition" 
                            placeholder="Enter your password">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 cursor-pointer text-gray-400 hover:text-gray-600">
                            <i class="far fa-eye text-sm"></i>
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#0a58ca] focus:ring-[#0a58ca]">
                        <span class="ml-2 text-xs font-semibold text-gray-600 select-none">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs font-semibold text-[#0a58ca] hover:underline" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-[#0a58ca] hover:bg-[#084cdf] shadow-md transition duration-150 uppercase tracking-wider">
                        <i class="fas fa-user-plus text-xs"></i> LOGIN
                    </button>
                </div>
            </form>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-4 text-xs text-gray-400 font-medium lowercase">or</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <div class="space-y-3">
                <p class="text-center text-xs font-semibold text-gray-500">Don't have an account?</p>
                <a href="{{ route('register') }}" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-[#198754] bg-white border border-[#198754] hover:bg-[#198754]/5 transition uppercase tracking-wider">
                    <i class="fas fa-user-plus text-xs"></i> REGISTER
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>