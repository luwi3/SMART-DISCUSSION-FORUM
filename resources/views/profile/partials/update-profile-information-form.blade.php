<section>
    <header>
        <h2 class="text-sm font-bold text-sky-400 tracking-wide uppercase">
            {{ __('Profile Information') }}
        </h2>

        <p class="text-xs text-slate-400 mt-0.5">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @path('patch')

        <div class="space-y-1.5">
            <x-input-label for="name" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-0.5 block w-full bg-[#172033]/40 border border-slate-700/60 text-sky-100 rounded-xl px-3 py-2.5 text-xs focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/50 transition-all" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1 text-xs text-rose-400" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-1.5">
            <x-input-label for="email" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-0.5 block w-full bg-[#172033]/40 border border-slate-700/60 text-sky-100 rounded-xl px-3 py-2.5 text-xs focus:border-sky-500/50 focus:ring-1 focus:ring-sky-500/50 transition-all" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1 text-xs text-rose-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                    <p class="text-xs text-amber-300 flex items-center justify-between">
                        <span>{{ __('Your email address is unverified.') }}</span>

                        <button form="send-verification" class="underline font-bold text-amber-400 hover:text-amber-200 transition-colors focus:outline-none">
                            {{ __('Re-send Link') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 font-semibold text-xs text-cyan-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-[0_0_15px_rgba(56,189,248,0.2)] transition-all duration-200 transform active:scale-95">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-medium text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 px-3 py-1.5 rounded-lg flex items-center"
                >
                    <svg class="w-3.5 h-3.5 mr-1.5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>