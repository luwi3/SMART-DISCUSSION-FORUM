<x-app-layout>
    <!-- Premium Header Area with glowing soft blue accent -->
    <x-slot name="header">
        <div class="flex items-center justify-between border-b border-sky-500/20 pb-4 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black tracking-tight text-white drop-shadow-[0_2px_10px_rgba(56,189,248,0.15)]">
                {{ __('Account Settings') }}
            </h2>
            <p class="text-xs font-bold uppercase tracking-widest text-sky-400">
                {{ Auth::user()->role }} Workspace
            </p>
        </div>
    </x-slot>

    <!-- Canvas Layout Background: Deep Matte Obsidian Midnight -->
    <div class="py-8 bg-[#090d16] min-h-screen text-slate-100 antialiased">
        <!-- Compact centered layout container -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- 1. PRIMARY PROFILE INFORMATION CARD -->
            <!-- Blended Style: Sleek Deep Charcoal, Glass-like borders, neon blue headers -->
            <div class="bg-[#111827] shadow-[0_4px_20px_-2px_rgba(0,0,0,0.5)] rounded-2xl border border-sky-500/10 overflow-hidden backdrop-blur-md transition-all duration-300 hover:border-sky-500/20">
                <!-- Inner Blended Light/Dark Blue Header Accent -->
                <div class="bg-gradient-to-r from-sky-950/40 via-transparent to-transparent px-6 py-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-sky-400 tracking-wide uppercase">
                        {{ __('Profile Information') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ __('Update your account details and email address.') }}
                    </p>
                </div>
                <!-- Shrunk layout container wrapper -->
                <div class="p-6 max-w-md">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @php
                $studentData = DB::table('students')->where('user_id', Auth::id())->first();
            @endphp

            <!-- 2. ADDITIONAL REGISTRATION FIELDS CARD -->
            <div class="bg-[#111827] shadow-[0_4px_20px_-2px_rgba(0,0,0,0.5)] rounded-2xl border border-sky-500/10 overflow-hidden backdrop-blur-md transition-all duration-300 hover:border-sky-500/20">
                <!-- Inner Blended Header Accent -->
                <div class="bg-gradient-to-r from-sky-950/40 via-transparent to-transparent px-6 py-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-sky-400 tracking-wide uppercase">
                        {{ __('Additional Registration Details') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ __('View your system assigned attributes and custom account criteria.') }}
                    </p>
                </div>

                <div class="p-6">
                    <section class="max-w-2xl">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            <!-- Registration Number Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="registrationNumber" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Registration Number')" />
                                <x-text-input id="registrationNumber" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl px-3 py-2.5 cursor-not-allowed opacity-80 font-mono text-xs tracking-wide focus:ring-0" :value="$studentData->regNo ?? Auth::user()->student->regNo ?? 'N/A'" disabled />
                            </div>

                            <!-- Course Code Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="courseCode" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Course Code')" />
                                <x-text-input id="courseCode" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl px-3 py-2.5 cursor-not-allowed opacity-80 font-mono text-xs tracking-wide focus:ring-0" :value="$studentData->courseCode ?? Auth::user()->student->courseCode ?? 'N/A'" disabled />
                            </div>

                            <!-- Username Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="username" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Username')" />
                                <x-text-input id="username" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl px-3 py-2.5 cursor-not-allowed opacity-80 text-xs focus:ring-0" :value="Auth::user()->username" disabled />
                            </div>

                            <!-- Phone Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="phone" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Phone Number')" />
                                <x-text-input id="phone" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl px-3 py-2.5 cursor-not-allowed opacity-80 text-xs focus:ring-0" :value="Auth::user()->phone ?? 'N/A'" disabled />
                            </div>

                            <!-- Student Status Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="studentCategory" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Student Status')" />
                                <div class="relative">
                                    <x-text-input id="studentCategory" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl pl-3 pr-8 py-2.5 cursor-not-allowed opacity-80 text-xs focus:ring-0" :value="ucfirst($studentData->status ?? Auth::user()->student->status ?? 'N/A')" disabled />
                                    <!-- Electric Eye-Safe Status Neon Dot -->
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full {{ ($studentData->status ?? Auth::user()->student->status ?? '') === 'active' ? 'bg-cyan-400 shadow-[0_0_8px_rgba(34,211,238,0.7)]' : 'bg-amber-400' }}"></span>
                                </div>
                            </div>

                            <!-- Role Info -->
                            <div class="space-y-1.5">
                                <x-input-label for="role" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider" :value="__('Account Role')" />
                                <x-text-input id="role" type="text" class="mt-0.5 block w-full bg-[#172033]/60 border border-slate-700/60 text-sky-200 rounded-xl px-3 py-2.5 cursor-not-allowed opacity-80 text-xs focus:ring-0" :value="ucfirst(Auth::user()->role)" disabled />
                            </div>

                        </div>
                    </section>
                </div>
            </div>

            <!-- 3. SECURITY / PASSWORD CHANGE CARD -->
            <div class="bg-[#111827] shadow-[0_4px_20px_-2px_rgba(0,0,0,0.5)] rounded-2xl border border-sky-500/10 overflow-hidden backdrop-blur-md transition-all duration-300 hover:border-sky-500/20">
                <div class="bg-gradient-to-r from-sky-950/40 via-transparent to-transparent px-6 py-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-sky-400 tracking-wide uppercase">
                        {{ __('Security Settings') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                </div>
                <div class="p-6 max-w-md">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- 4. DANGER ZONE CARD -->
            <div class="bg-[#111827] shadow-[0_4px_20px_-2px_rgba(0,0,0,0.5)] rounded-2xl border border-rose-500/10 overflow-hidden backdrop-blur-md transition-all duration-300 hover:border-rose-500/20">
                <div class="bg-gradient-to-r from-rose-950/30 via-transparent to-transparent px-6 py-4 border-b border-slate-800">
                    <h3 class="text-sm font-bold text-rose-400 tracking-wide uppercase">
                        {{ __('Danger Zone') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ __('Permanently remove your account data and configurations from the system.') }}
                    </p>
                </div>
                <div class="p-6 max-w-md">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>