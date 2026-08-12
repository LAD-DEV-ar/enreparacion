@if (session('success'))
    <div
        x-data="{ show: false }"
        x-init="
            requestAnimationFrame(() => show = true);
            setTimeout(() => show = false, 3300);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-5"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-5"
        x-cloak
        class="fixed top-5 right-5 z-50 rounded-lg bg-success px-4 py-3 text-white shadow-lg"
    >
        <div class="flex items-center gap-3">
            <span>{{ session('success') }}</span>

            <button
                type="button"
                @click="show = false"
                class="font-bold cursor-pointer"
            >
                ×
            </button>
        </div>
    </div>
@endif

@if (session('error'))
    <div
        x-data="{ show: false }"
        x-init="
            requestAnimationFrame(() => show = true);
            setTimeout(() => show = false, 3300);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-5"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-5"
        x-cloak
        class="fixed top-5 right-5 z-50 rounded-lg bg-danger px-4 py-3 text-white shadow-lg"
    >
        <div class="flex items-center gap-3">
            <span>{{ session('error') }}</span>

            <button
                type="button"
                @click="show = false"
                class="font-bold cursor-pointer"
            >
                ×
            </button>
        </div>
    </div>
@endif

@if (session('warning'))
        <div
        x-data="{ show: false }"
        x-init="
            requestAnimationFrame(() => show = true);
            setTimeout(() => show = false, 3300);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-5"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-5"
        x-cloak
        class="fixed top-5 right-5 z-50 rounded-lg bg-warning px-4 py-3 text-white shadow-lg"
    >
        <div class="flex items-center gap-3">
            <span>{{ session('warning') }}</span>

            <button
                type="button"
                @click="show = false"
                class="font-bold cursor-pointer"
            >
                ×
            </button>
        </div>
    </div>
@endif

@if (session('info'))
        <div
        x-data="{ show: false }"
        x-init="
            requestAnimationFrame(() => show = true);
            setTimeout(() => show = false, 3300);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-5"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-5"
        x-cloak
        class="fixed top-5 right-5 z-50 rounded-lg bg-info px-4 py-3 text-white shadow-lg"
    >
        <div class="flex items-center gap-3">
            <span>{{ session('info') }}</span>

            <button
                type="button"
                @click="show = false"
                class="font-bold cursor-pointer"
            >
                ×
            </button>
        </div>
    </div>
@endif