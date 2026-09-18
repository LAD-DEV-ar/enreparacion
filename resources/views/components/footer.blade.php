<footer class="border-t border-[#364252] bg-[#131A22] mt-8">
    <div class="mx-auto max-w-7xl px-6 py-10">

        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            {{-- Logo / marca --}}
            <div>
                <span class="text-lg font-semibold text-[#F5F7FA]">
                    EnReparacion
                </span>

                <p class="mt-1 text-sm text-[#AAB6C4]">
                    Sistema de gestión para servicios técnicos.
                </p>
            </div>

            {{-- Links --}}
            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <a href="{{ route('legales') }}"
                   class="text-[#AAB6C4] transition hover:text-[#33B4FF]">
                    Términos y condiciones
                </a>

                <a href="{{ route('privacidad') }}"
                   class="text-[#AAB6C4] transition hover:text-[#33B4FF]">
                    Política de privacidad
                </a>
            </nav>

        </div>

        <div class="mt-8 border-t border-[#364252] pt-6 text-center text-sm text-[#6B7280]">
            © {{ date('Y') }} enReparacion. Todos los derechos reservados.
        </div>

    </div>
</footer>