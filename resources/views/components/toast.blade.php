@if (session('success'))
    <div
        id="toast"
        class="fixed top-5 right-5 rounded-lg bg-success px-4 py-3 text-text-primary font-bold shadow-lg"
    >
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div
        id="toast"
        class="fixed top-5 right-5 rounded-lg bg-danger px-4 py-3 text-text-primary font-bold shadow-lg"
    >
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div
        id="toast"
        class="fixed top-5 right-5 rounded-lg bg-warning px-4 py-3 text-text-primary font-bold shadow-lg"
    >
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div
        id="toast"
        class="fixed top-5 right-5 rounded-lg bg-info px-4 py-3 text-text-primary font-bold shadow-lg"
    >
        {{ session('info') }}
    </div>
@endif