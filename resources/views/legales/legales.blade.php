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
                        Términos y Condiciones
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


            {{-- 1. Identificación del titular --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    1. Identificación del titular
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El presente documento establece los términos y condiciones
                    aplicables al uso de <strong class="text-[#F5F7FA]">enReparacion</strong>,
                    una plataforma de gestión destinada a servicios técnicos y
                    talleres de reparación.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    El servicio es desarrollado y administrado por:
                </p>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ul class="space-y-2 text-[#AAB6C4]">
                        <li>
                            <span class="font-medium text-[#F5F7FA]">Titular:</span>
                            Elías Arroyo
                        </li>

                        <li>
                            <span class="font-medium text-[#F5F7FA]">Teléfono:</span>
                            22244550676
                        </li>
                    </ul>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    En adelante, el titular será denominado
                    <strong class="text-[#F5F7FA]">“enReparacion”</strong>,
                    <strong class="text-[#F5F7FA]">“el servicio”</strong> o
                    <strong class="text-[#F5F7FA]">“el titular”</strong>.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Al registrarse, acceder o utilizar enReparacion, la persona
                    usuaria declara haber leído y aceptado estos Términos y
                    Condiciones y la Política de Privacidad.
                </p>
            </section>


            {{-- 2. Descripción del servicio --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    2. Descripción del servicio
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion es una aplicación web orientada a la gestión y
                    organización de servicios técnicos y talleres de reparación.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Entre sus funcionalidades se encuentran, según la versión
                    disponible del servicio:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Gestión de clientes.</li>
                    <li>Registro y seguimiento de reparaciones.</li>
                    <li>Almacenamiento de información técnica relacionada con dispositivos.</li>
                    <li>Seguimiento del estado de las reparaciones.</li>
                    <li>Envío de notificaciones por correo electrónico.</li>
                    <li>Generación e impresión de comprobantes.</li>
                    <li>Organización de información relacionada con la actividad del taller.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Las funcionalidades pueden modificarse, ampliarse,
                    reemplazarse o eliminarse con el tiempo.
                </p>
            </section>


            {{-- 3. Alcance del servicio --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    3. Alcance del servicio
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion proporciona una herramienta tecnológica de gestión.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    El servicio no interviene en las reparaciones realizadas por
                    los talleres, ni garantiza la calidad, legalidad, seguridad,
                    resultado o cumplimiento de los servicios que cada usuario
                    presta a sus propios clientes.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La relación comercial entre un taller y sus clientes es
                    independiente de la relación entre el usuario y enReparacion.
                </p>
            </section>


            {{-- 4. Requisitos para utilizar el servicio --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    4. Requisitos para utilizar el servicio
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Para registrarse y contratar el servicio, la persona usuaria deberá:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Ser mayor de 18 años.</li>
                    <li>Proporcionar información verdadera, completa y actualizada.</li>
                    <li>Utilizar la cuenta para una actividad legítima.</li>
                    <li>Aceptar estos Términos y Condiciones.</li>
                    <li>Aceptar la Política de Privacidad cuando corresponda.</li>
                    <li>Mantener bajo su responsabilidad las credenciales de acceso.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El usuario se compromete a no proporcionar información falsa,
                    engañosa o perteneciente a terceros sin autorización.
                </p>
            </section>


            {{-- 5. Registro y cuenta --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    5. Registro y cuenta
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Para utilizar determinadas funcionalidades será necesario
                    crear una cuenta.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    El usuario es responsable de:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Mantener la confidencialidad de su contraseña.</li>
                    <li>Evitar compartir sus credenciales con personas no autorizadas.</li>
                    <li>Informar cualquier acceso no autorizado que detecte.</li>
                    <li>Mantener actualizados sus datos de contacto.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Todas las acciones realizadas mediante una cuenta podrán
                    considerarse realizadas por el titular de dicha cuenta,
                    salvo que se demuestre lo contrario.
                </p>
            </section>


            {{-- 6. Uso compartido de la cuenta --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    6. Uso compartido de la cuenta
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Actualmente enReparacion no cuenta con funcionalidades
                    específicas de gestión multiusuario o asignación individual
                    de roles.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Por este motivo, el titular de una cuenta podrá permitir el
                    acceso a otras personas vinculadas a su taller o actividad.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    En estos casos, el titular de la cuenta será responsable de
                    las acciones realizadas mediante dicha cuenta y deberá
                    asegurarse de que las personas a quienes otorgue acceso
                    conozcan y respeten estos Términos y Condiciones.
                </p>
            </section>


            {{-- 7. Período gratuito --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    7. Período gratuito
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El servicio ofrece un período inicial gratuito de
                    <strong class="text-[#F5F7FA]">30 días</strong>.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El período gratuito comienza a partir del registro o de la
                    aceptación de la suscripción, según corresponda al flujo
                    vigente de contratación.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Durante este período no será necesario ingresar un medio de
                    pago para utilizar el servicio.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Finalizado el período gratuito, el usuario deberá contratar
                    y abonar la suscripción correspondiente para continuar
                    utilizando el servicio.
                </p>
            </section>


            {{-- 8. Suscripción y contratación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    8. Suscripción y contratación
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion ofrece actualmente un plan de suscripción mensual.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El precio vigente será informado al usuario antes de realizar
                    la contratación y podrá consultarse en la sección de precios
                    del sitio web.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    La contratación podrá realizarse mediante los mecanismos de
                    pago habilitados, actualmente Mercado Pago.
                </p>
            </section>


            {{-- 9. Pagos y renovación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    9. Pagos y renovación
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Una vez establecida la suscripción, los pagos podrán
                    realizarse de manera recurrente mediante Mercado Pago,
                    de acuerdo con las condiciones informadas durante la
                    contratación.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Si un pago no pudiera realizarse, fuera rechazado, cancelado
                    o no pudiera ser correctamente corroborado por el sistema,
                    enReparacion podrá suspender el acceso a las funcionalidades
                    que requieran una suscripción activa.
                </p>
            </section>


            {{-- 10. Cancelación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    10. Cancelación de la suscripción
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El usuario podrá cancelar su suscripción en cualquier momento
                    mediante los mecanismos habilitados por enReparacion.
                </p>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    La cancelación evitará futuras renovaciones o cargos
                    correspondientes a períodos posteriores, de acuerdo con las
                    condiciones del medio de pago utilizado.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    La cancelación de la suscripción
                    <strong class="text-[#F5F7FA]">
                        no implica automáticamente la eliminación de la cuenta
                        ni de los datos almacenados.
                    </strong>
                </p>
            </section>


            {{-- 11. Derecho de arrepentimiento --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    11. Derecho de arrepentimiento
                </h2>

                <p class="leading-7 text-[#AAB6C4]">
                    Cuando resulte aplicable conforme a la legislación argentina
                    vigente, el usuario podrá ejercer el derecho de arrepentimiento
                    respecto de una contratación realizada a distancia.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    enReparacion dispondrá del mecanismo correspondiente para
                    ejercer este derecho, incluyendo el Botón de Arrepentimiento,
                    cuando resulte legalmente exigible.
                </p>
            </section>


            {{-- 12. Reembolsos --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    12. Política de reembolsos
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Los pagos correspondientes a períodos de suscripción ya
                    iniciados no serán reembolsados de manera automática por
                    el mero hecho de cancelar una suscripción.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Esta disposición no limita los derechos que correspondan
                    al usuario en virtud de la legislación aplicable.
                </p>
            </section>


            {{-- 13. Datos ingresados por el usuario --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    13. Datos ingresados por el usuario
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El usuario podrá incorporar información relacionada con su
                    actividad comercial y con los clientes a quienes presta
                    servicios.
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Nombre del cliente.</li>
                    <li>Correo electrónico.</li>
                    <li>Número de teléfono.</li>
                    <li>Información del dispositivo.</li>
                    <li>Información técnica de la reparación.</li>
                    <li>Observaciones y notas.</li>
                    <li>Información relacionada con el estado de una reparación.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El usuario deberá asegurarse de que la recopilación,
                    almacenamiento y utilización de dichos datos se realice
                    de acuerdo con la legislación aplicable.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El usuario es responsable de la información que incorpore
                    a la plataforma y de contar con las bases legales,
                    autorizaciones o consentimientos que correspondan para
                    su tratamiento.
                </p>
            </section>


            {{-- 14. Información de clientes --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    14. Información de clientes del taller
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Los datos de los clientes incorporados por un taller se
                    encuentran sujetos a los derechos que correspondan a sus
                    respectivos titulares.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    El usuario deberá evitar incorporar información innecesaria
                    o excesiva y deberá adoptar medidas razonables para proteger
                    la información de sus propios clientes.
                </p>
            </section>


            {{-- 15. Información estadística --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    15. Uso de información estadística
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion podrá, actualmente o en el futuro, elaborar
                    estadísticas generales y agregadas sobre el funcionamiento
                    y utilización del servicio.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Cuando se utilice información con fines estadísticos,
                    se procurará que se encuentre anonimizada o agregada de
                    manera que no permita identificar directamente a un usuario,
                    taller o cliente particular.
                </p>
            </section>


            {{-- 16. Disponibilidad --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    16. Disponibilidad del servicio
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion procura mantener el servicio disponible de
                    manera continua, pero no garantiza que la plataforma se
                    encuentre disponible en todo momento y sin interrupciones.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    El servicio puede experimentar:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Interrupciones temporales.</li>
                    <li>Mantenimiento programado.</li>
                    <li>Errores funcionales o técnicos.</li>
                    <li>Problemas de infraestructura.</li>
                    <li>Problemas de proveedores externos.</li>
                    <li>Actualizaciones.</li>
                    <li>Fallas de conectividad.</li>
                    <li>Situaciones de fuerza mayor.</li>
                </ul>
            </section>


            {{-- 17. Servicios de terceros --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    17. Servicios de terceros
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El funcionamiento de determinadas características puede
                    depender de servicios proporcionados por terceros.
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Procesamiento de pagos.</li>
                    <li>Envío de correos electrónicos.</li>
                    <li>Alojamiento e infraestructura.</li>
                    <li>Almacenamiento.</li>
                    <li>Servicios tecnológicos.</li>
                    <li>Seguridad y conectividad.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El funcionamiento de dichos servicios depende de sus
                    respectivos proveedores y términos de servicio.
                </p>
            </section>


            {{-- 18. Propiedad intelectual --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    18. Propiedad intelectual
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El software, código fuente, diseño, estructura, interfaz,
                    identidad visual, logotipo, nombre comercial, elementos
                    gráficos, textos y demás componentes desarrollados
                    específicamente para enReparacion pertenecen a su titular
                    o son utilizados legítimamente por este.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Salvo autorización expresa, el usuario no podrá:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Copiar el software.</li>
                    <li>Reproducir su código.</li>
                    <li>Redistribuirlo.</li>
                    <li>Comercializarlo.</li>
                    <li>Modificarlo para su explotación independiente.</li>
                    <li>Utilizar sus elementos visuales o identidad para crear un servicio sustancialmente similar.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    El acceso al servicio no implica la transferencia de derechos
                    de propiedad intelectual sobre la plataforma.
                </p>
            </section>


            {{-- 19. Usos prohibidos --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    19. Usos prohibidos
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    Está prohibido utilizar enReparacion para:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Actividades ilegales.</li>
                    <li>Vulnerar derechos de terceros.</li>
                    <li>Intentar acceder a cuentas ajenas.</li>
                    <li>Realizar ataques contra la infraestructura.</li>
                    <li>Introducir malware o código malicioso.</li>
                    <li>Intentar alterar, inutilizar o sobrecargar el servicio.</li>
                    <li>Obtener acceso no autorizado a sistemas.</li>
                    <li>Realizar actividades fraudulentas.</li>
                    <li>Interferir deliberadamente con el funcionamiento de la plataforma.</li>
                </ul>
            </section>


            {{-- 20. Suspensión --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    20. Suspensión o cancelación de cuentas
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    enReparacion podrá suspender temporalmente o cancelar una
                    cuenta cuando existan motivos razonables relacionados con:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Incumplimiento de estos Términos y Condiciones.</li>
                    <li>Utilización ilegal del servicio.</li>
                    <li>Fraude.</li>
                    <li>Abuso de la plataforma.</li>
                    <li>Ataques o intentos de comprometer la seguridad.</li>
                    <li>Falta de pago de la suscripción.</li>
                </ul>
            </section>


            {{-- 21. Eliminación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    21. Eliminación de la cuenta
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    El usuario podrá solicitar voluntariamente la eliminación
                    permanente de su cuenta.
                </p>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ol class="ml-5 list-decimal space-y-2 text-[#AAB6C4]">
                        <li>La cuenta podrá dejar de estar disponible.</li>
                        <li>Los datos asociados podrán mantenerse durante un período de hasta 30 días.</li>
                        <li>Transcurrido dicho período, los datos serán eliminados permanentemente, sujeto a las obligaciones legales aplicables.</li>
                    </ol>
                </div>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La eliminación permanente de la cuenta es diferente de la
                    cancelación de la suscripción.
                </p>
            </section>


            {{-- 22. Modificaciones --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    22. Modificaciones del servicio
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion podrá modificar, actualizar, incorporar o
                    eliminar funcionalidades del servicio.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Cuando una modificación afecte sustancialmente las condiciones
                    de contratación o el funcionamiento esencial del servicio,
                    se procurará informar al usuario de manera adecuada.
                </p>
            </section>


            {{-- 23. Limitación de responsabilidad --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    23. Limitación de responsabilidad
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    enReparacion proporciona una herramienta tecnológica para
                    facilitar la gestión de los talleres.
                </p>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    El titular no será responsable por:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>La calidad de las reparaciones realizadas por un usuario.</li>
                    <li>Conflictos entre un taller y sus clientes.</li>
                    <li>Información falsa ingresada por un usuario.</li>
                    <li>Interrupciones originadas exclusivamente en proveedores externos.</li>
                    <li>Fallas de conectividad del usuario.</li>
                    <li>Utilización indebida de las credenciales.</li>
                </ul>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    Esta disposición no limita las responsabilidades que
                    legalmente no puedan ser excluidas o limitadas.
                </p>
            </section>


            {{-- 24. Comunicaciones --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    24. Comunicaciones
                </h2>

                <p class="mb-3 leading-7 text-[#AAB6C4]">
                    El usuario acepta recibir comunicaciones necesarias para
                    la prestación del servicio, incluyendo:
                </p>

                <ul class="ml-5 list-disc space-y-2 text-[#AAB6C4] marker:text-[#0081CC]">
                    <li>Confirmaciones de cuenta.</li>
                    <li>Información relacionada con la suscripción.</li>
                    <li>Avisos de seguridad.</li>
                    <li>Cambios relevantes del servicio.</li>
                    <li>Comunicaciones técnicas.</li>
                    <li>Recuperación de acceso.</li>
                </ul>
            </section>


            {{-- 25. Legislación --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    25. Legislación aplicable
                </h2>

                <p class="mb-4 leading-7 text-[#AAB6C4]">
                    Estos Términos y Condiciones se regirán por las leyes de
                    la República Argentina.
                </p>

                <p class="leading-7 text-[#AAB6C4]">
                    Nada de lo establecido en estos términos pretende privar al
                    consumidor de los derechos que le correspondan conforme a
                    normas de carácter obligatorio.
                </p>
            </section>


            {{-- 26. Contacto --}}
            <section class="mb-10">
                <h2 class="mb-4 text-2xl font-semibold">
                    26. Contacto
                </h2>

                <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                    <ul class="space-y-2 text-[#AAB6C4]">
                        <li>
                            <span class="font-medium text-[#F5F7FA]">Titular:</span>
                            Elías Arroyo
                        </li>

                        <li>
                            <span class="font-medium text-[#F5F7FA]">Teléfono:</span>
                            22244550676
                        </li>
                    </ul>
                </div>
            </section>


            {{-- 27. Aceptación --}}
            <section>
                <h2 class="mb-4 text-2xl font-semibold">
                    27. Aceptación
                </h2>

                <p class="leading-7 text-[#AAB6C4]">
                    Al registrarse y utilizar enReparacion, el usuario declara
                    haber leído, comprendido y aceptado estos Términos y
                    Condiciones.
                </p>

                <p class="mt-4 leading-7 text-[#AAB6C4]">
                    La aceptación deberá realizarse mediante el mecanismo
                    dispuesto en el sitio web antes de completar el registro
                    o contratación correspondiente.
                </p>
            </section>
        </article>
    </main>
    <x-footer></x-foote>
@endsection