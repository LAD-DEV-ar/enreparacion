@extends('layout')

@section('main')

    <main class="flex justify-center">
        <article
            class="w-full max-w-5xl m-6 rounded-3xl border border-white/10 bg-white/5 p-6 md:p-10 backdrop-blur-md shadow-2xl text-[#F5F7FA]"
        >

            {{-- Encabezado --}}
            <header class="flex justify-between mb-10 border-b border-white/10 pb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight">
                        Política de Privacidad
                    </h1>

                    <p class="mt-3 text-sm text-[#AAB6C4]">
                        Última actualización: 17/09/2026
                    </p>
                    <p class="text-sm text-[#AAB6C4]">
                        Versión: 1.0
                    </p>
                </div>
                <img src="{{ asset('favicon.svg') }}" alt="Logo enReparacion" class="w-18 h-18 mb-4">
            </header>


            {{-- 1. Responsable del tratamiento --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    1. Responsable del tratamiento
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    La presente Política de Privacidad explica cómo
                    <strong class="text-[#F5F7FA]">enReparacion</strong>
                    recopila, utiliza, almacena y protege información personal
                    relacionada con sus usuarios y con las personas cuyos datos
                    sean incorporados a la plataforma.
                </p>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ul class="space-y-2 text-[#AAB6C4]">
                        <li>
                            <span class="font-medium text-[#F5F7FA]">
                                Responsable:
                            </span>
                            Elías Arroyo
                        </li>

                        <li>
                            <span class="font-medium text-[#F5F7FA]">
                                Teléfono:
                            </span>
                            22244550676
                        </li>
                    </ul>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El tratamiento de datos personales se realiza de acuerdo
                    con la legislación argentina aplicable en materia de
                    protección de datos personales.
                </p>
            </section>


            {{-- 2. Información recopilada --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    2. ¿Qué información recopilamos?
                </h2>

                <p class="leading-7 text-[#AAB6C4]">
                    La información recopilada depende de la utilización que
                    cada persona haga del servicio.
                </p>
            </section>


            {{-- 3. Datos proporcionados al registrarse --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    3. Datos proporcionados al registrarse
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Podemos recopilar información proporcionada por el usuario
                    durante el registro y utilización de la cuenta, como:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Nombre.</li>
                    <li>Correo electrónico.</li>
                    <li>Número de teléfono.</li>
                    <li>Información relacionada con el negocio o taller.</li>
                    <li>Credenciales de acceso.</li>
                    <li>Información relacionada con la suscripción.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La información solicitada será utilizada para permitir
                    el funcionamiento de la cuenta y prestar el servicio.
                </p>
            </section>


            {{-- 4. Datos de clientes --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    4. Datos de clientes incorporados por los talleres
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion permite a los usuarios registrar información
                    relacionada con sus propios clientes y reparaciones.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Esta información puede incluir:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Nombre.</li>
                    <li>Correo electrónico.</li>
                    <li>Teléfono.</li>
                    <li>Información del dispositivo.</li>
                    <li>Información técnica.</li>
                    <li>Estado de reparación.</li>
                    <li>Observaciones.</li>
                    <li>Notas introducidas por el usuario.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El taller o usuario que incorpora estos datos es responsable
                    de asegurarse de que su recopilación y utilización sea
                    legítima y adecuada para su actividad.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    enReparacion trata esta información en la medida necesaria
                    para proporcionar las funcionalidades contratadas por el
                    taller.
                </p>
            </section>


            {{-- 5. Información que no es necesaria --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    5. Información que no es necesaria
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El usuario deberá evitar incorporar datos personales que
                    no sean necesarios para la prestación de sus servicios.
                </p>

                <div class="rounded-2xl border border-[#0081CC]/20 bg-[#0081CC]/5 p-5">
                    <p class="leading-7 text-[#AAB6C4]">
                        En particular, se recomienda no incorporar información
                        sensible o excesiva en notas u otros campos de texto
                        libre cuando dicha información no resulte necesaria.
                    </p>
                </div>
            </section>


            {{-- 6. Finalidades --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    6. Finalidades del tratamiento
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    La información recopilada podrá utilizarse para:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Crear y administrar cuentas.</li>
                    <li>Proporcionar las funcionalidades de enReparacion.</li>
                    <li>Gestionar reparaciones.</li>
                    <li>Almacenar información ingresada por los talleres.</li>
                    <li>Enviar notificaciones relacionadas con las reparaciones.</li>
                    <li>Enviar comunicaciones relacionadas con la cuenta.</li>
                    <li>Gestionar suscripciones.</li>
                    <li>Verificar pagos.</li>
                    <li>Prevenir usos fraudulentos o abusivos.</li>
                    <li>Mantener la seguridad de la plataforma.</li>
                    <li>Solucionar problemas técnicos.</li>
                    <li>Mejorar el funcionamiento del servicio.</li>
                    <li>Cumplir obligaciones legales.</li>
                    <li>Generar estadísticas agregadas y anonimizadas.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Los datos no serán utilizados para finalidades incompatibles
                    con aquellas informadas en esta Política de Privacidad,
                    salvo que exista una base legal que lo permita.
                </p>
            </section>


            {{-- 7. Correo electrónico --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    7. Comunicaciones por correo electrónico
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion puede utilizar direcciones de correo electrónico
                    proporcionadas por los usuarios o incorporadas por los
                    talleres para enviar comunicaciones necesarias para el
                    funcionamiento de la plataforma.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Estas comunicaciones pueden incluir:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Recuperación de contraseña.</li>
                    <li>Confirmaciones.</li>
                    <li>Avisos relacionados con la cuenta.</li>
                    <li>Notificaciones sobre reparaciones.</li>
                    <li>Cambios de estado.</li>
                    <li>Comunicaciones relacionadas con la suscripción.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Para realizar estos envíos pueden utilizarse proveedores
                    tecnológicos especializados en correo electrónico.
                </p>
            </section>


            {{-- 8. Mercado Pago --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    8. Mercado Pago
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Los pagos y suscripciones podrán gestionarse mediante
                    <strong class="text-[#F5F7FA]">Mercado Pago</strong>.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Los datos necesarios para procesar el pago podrán ser
                    tratados directamente por Mercado Pago conforme a sus
                    propias políticas y condiciones.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    enReparacion podrá recibir información necesaria para
                    determinar:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Si una suscripción fue creada.</li>
                    <li>Si un pago fue aprobado.</li>
                    <li>Si un pago fue rechazado.</li>
                    <li>El estado de una suscripción.</li>
                    <li>Información necesaria para identificar la operación.</li>
                </ul>

                <div class="mt-5 rounded-2xl border border-white/10 bg-black/10 p-5">
                    <p class="leading-7 text-[#AAB6C4]">
                        enReparacion no necesita almacenar los datos completos
                        de tarjetas de pago para proporcionar la funcionalidad
                        de suscripción.
                    </p>
                </div>
            </section>


            {{-- 9. Proveedores tecnológicos --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    9. Proveedores tecnológicos
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Para proporcionar el servicio pueden utilizarse proveedores
                    externos de infraestructura, alojamiento, almacenamiento,
                    correo electrónico, pagos, seguridad u otros servicios
                    tecnológicos.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Estos proveedores podrán acceder a información únicamente
                    en la medida necesaria para proporcionar los servicios
                    contratados por enReparacion.
                </p>
            </section>


            {{-- 10. Datos técnicos --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    10. Datos técnicos
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Durante el uso de la plataforma pueden generarse determinados
                    datos técnicos necesarios para su funcionamiento y seguridad,
                    como:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Dirección IP.</li>
                    <li>Información del navegador.</li>
                    <li>Sistema operativo.</li>
                    <li>Registros técnicos.</li>
                    <li>Información relacionada con sesiones.</li>
                    <li>Información sobre errores y eventos de seguridad.</li>
                </ul>

                <p class="mt-4 mb-3 leading-7 text-[#AAB6C4]">
                    Estos datos podrán utilizarse para:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Mantener la seguridad.</li>
                    <li>Detectar comportamientos abusivos.</li>
                    <li>Solucionar errores.</li>
                    <li>Mantener la estabilidad.</li>
                    <li>Investigar incidentes.</li>
                </ul>
            </section>


            {{-- 11. Cookies --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    11. Cookies
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion puede utilizar cookies o tecnologías similares
                    necesarias para mantener sesiones, autenticar usuarios,
                    proteger formularios y proporcionar funcionalidades
                    esenciales.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Las cookies estrictamente necesarias para el funcionamiento
                    de la plataforma pueden ser utilizadas para proporcionar
                    las funcionalidades esenciales del servicio.
                </p>
            </section>


            {{-- 12. Seguridad --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    12. Seguridad
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion adopta medidas técnicas y organizativas
                    razonables destinadas a proteger la información frente a:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Acceso no autorizado.</li>
                    <li>Pérdida.</li>
                    <li>Alteración.</li>
                    <li>Divulgación indebida.</li>
                    <li>Destrucción.</li>
                    <li>Utilización fraudulenta.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    No obstante, ningún sistema informático puede garantizar
                    seguridad absoluta.
                </p>
            </section>


            {{-- 13. Confidencialidad --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    13. Confidencialidad
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    La información almacenada en enReparacion no será divulgada
                    a terceros salvo cuando:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Sea necesario para proporcionar el servicio.</li>
                    <li>Exista autorización del usuario.</li>
                    <li>Resulte necesario para cumplir una obligación legal.</li>
                    <li>Sea necesario para proteger la seguridad de la plataforma.</li>
                    <li>Sea necesario para prevenir fraude o abuso.</li>
                    <li>Exista otra base legal que permita dicha divulgación.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    No comercializamos datos personales identificables de los
                    usuarios o clientes de los talleres con fines publicitarios
                    de terceros.
                </p>
            </section>


            {{-- 14. Estadísticas --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    14. Estadísticas anonimizadas
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion podrá utilizar información estadística
                    agregada o anonimizada para analizar el funcionamiento
                    del servicio.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Por ejemplo, podrán analizarse indicadores generales como:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Cantidad de reparaciones.</li>
                    <li>Cantidad de usuarios.</li>
                    <li>Utilización de determinadas funcionalidades.</li>
                    <li>Tendencias generales de utilización.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La información utilizada con este propósito deberá
                    encontrarse agregada o anonimizada de manera que no permita
                    identificar directamente a una persona, taller o cliente
                    determinado.
                </p>
            </section>


            {{-- 15. Conservación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    15. Conservación de los datos
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Mientras una cuenta permanezca activa, los datos se
                    conservarán durante el tiempo necesario para proporcionar
                    el servicio.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    La cancelación de una suscripción no implica la eliminación
                    inmediata de los datos.
                </p>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <p class="leading-7 text-[#AAB6C4]">
                        Cuando el usuario solicite voluntariamente la eliminación
                        permanente de su cuenta, los datos asociados podrán
                        conservarse durante un período de hasta
                        <strong class="text-[#F5F7FA]">30 días</strong>.
                    </p>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Finalizado dicho período, serán eliminados de forma
                    permanente, salvo que exista una obligación legal que
                    requiera conservar determinada información durante un
                    período superior.
                </p>
            </section>


            {{-- 16. Derechos --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    16. Derechos de los titulares de datos
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Las personas titulares de datos personales podrán ejercer
                    los derechos reconocidos por la legislación aplicable,
                    incluyendo:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Acceso.</li>
                    <li>Rectificación.</li>
                    <li>Actualización.</li>
                    <li>Supresión.</li>
                    <li>Información sobre el tratamiento de sus datos.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Las solicitudes deberán realizarse mediante los canales
                    de contacto indicados en esta Política.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Cuando corresponda, podrá solicitarse información adicional
                    para verificar la identidad de la persona solicitante.
                </p>
            </section>


            {{-- 17. Datos de clientes de talleres --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    17. Datos de clientes de los talleres
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Cuando una persona considere que sus datos fueron
                    incorporados a enReparacion por un taller, deberá,
                    en principio, dirigir su solicitud al taller que recopiló
                    y utiliza dichos datos para su actividad.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    No obstante, también podrá comunicarse con enReparacion
                    para realizar una consulta o solicitud relacionada con
                    sus datos.
                </p>
            </section>


            {{-- 18. Menores --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    18. Datos de menores
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El servicio está destinado a personas mayores de 18 años
                    que actúen en representación de un taller, servicio técnico
                    o actividad profesional o comercial.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    enReparacion no busca recopilar deliberadamente información
                    personal de menores como usuarios del servicio.
                </p>
            </section>


            {{-- 19. Procesamiento por terceros --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    19. Transferencia y procesamiento por terceros
                </h2>

                <p class="leading-7 text-[#AAB6C4]">
                    Algunos proveedores tecnológicos utilizados para operar
                    enReparacion podrían encontrarse ubicados fuera de la
                    República Argentina.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    En esos casos, la información podrá ser procesada por
                    dichos proveedores en los lugares donde operen, respetando
                    las condiciones y garantías que correspondan conforme a
                    la legislación aplicable.
                </p>
            </section>


            {{-- 20. Eliminación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    20. Eliminación de la cuenta
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El usuario puede solicitar la eliminación permanente
                    de su cuenta.
                </p>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ol class="ml-5 list-decimal space-y-2 text-[#AAB6C4]">
                        <li>La cuenta podrá ser deshabilitada.</li>
                        <li>Los datos podrán mantenerse durante un período de hasta 30 días.</li>
                        <li>Posteriormente serán eliminados de forma permanente, salvo las excepciones legalmente aplicables.</li>
                    </ol>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La eliminación de la cuenta es independiente de la
                    cancelación de una suscripción.
                </p>
            </section>


            {{-- 21. Cambios --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    21. Cambios en esta Política de Privacidad
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Esta Política de Privacidad podrá ser modificada cuando
                    resulte necesario para reflejar:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Cambios en el funcionamiento de enReparacion.</li>
                    <li>Nuevas funcionalidades.</li>
                    <li>Cambios en proveedores tecnológicos.</li>
                    <li>Cambios en las prácticas de tratamiento de datos.</li>
                    <li>Modificaciones legales o regulatorias.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La versión vigente estará disponible en el sitio web
                    de enReparacion.
                </p>
            </section>


            {{-- 22. Contacto --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    22. Contacto y ejercicio de derechos
                </h2>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ul class="space-y-2 text-[#AAB6C4]">
                        <li>
                            <span class="font-medium text-[#F5F7FA]">
                                Responsable:
                            </span>
                            Elías Arroyo
                        </li>

                        <li>
                            <span class="font-medium text-[#F5F7FA]">
                                Teléfono:
                            </span>
                            22244550676
                        </li>
                    </ul>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Las solicitudes deberán indicar claramente qué derecho
                    se desea ejercer y aportar la información necesaria para
                    identificar al solicitante y localizar los datos
                    correspondientes.
                </p>
            </section>


            {{-- 23. Aceptación --}}
            <section>
                <h2 class="mb-4 text-2xl font-semibold">
                    23. Aceptación
                </h2>

                <p class="leading-7 text-[#AAB6C4]">
                    El registro y utilización de enReparacion implica que
                    el usuario ha tenido acceso a esta Política de Privacidad
                    y ha podido conocer cómo se tratarán sus datos personales.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Cuando la legislación requiera un consentimiento específico
                    para determinado tratamiento, este será solicitado mediante
                    el mecanismo correspondiente.
                </p>
            </section>

        </article>
    </main>
    <x-footer></x-footer>
@endsection