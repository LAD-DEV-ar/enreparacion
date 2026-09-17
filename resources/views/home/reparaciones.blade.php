@extends('layout')

@section('main')
    <main
        class="ml-56 min-h-screen pb-16"
        x-data="{
            search: '',
            filtroEstado: 'todas',
            reparaciones: {{ Js::from($reparaciones) }},

            params: new URLSearchParams(window.location.search),
            get selectedReparacionId() {
                const id = this.params.get('selectedReparacion');
                return id !== null ? Number(id) : null;
            },

            init() {
                if (this.selectedReparacionId) {
                    let rep = this.reparaciones.find( reparacion => {
                        return reparacion.id === this.selectedReparacionId;
                    });

                    setTimeout( () => {
                        this.abrirSlideOver(rep);
                    }, 100);
                    const url = new URL(window.location.href);
                    url.searchParams.delete('selectedReparacion');
                    window.history.replaceState({}, '', url);
                }
                return;
            },
            
            
            // Estados de interfaz y modales
            openSlideOver: false,
            openNewModal: {{ $errors->any() ? 'true' : 'false' }},
            openPrintModal: false,
            selectedReparacion: null,

            // Modal de confirmación unificado (Cambio de estado / Entrega)
            confirmModal: {
                open: false,
                loading: false,
                item: null,
                from: null,
                to: null,
            },

            // Notificaciones por Email
            enviarEmail: true,
            mostrarPersonalizarMensaje: false,
            mensajePersonalizado: '',

            // Acciones asíncronas
            isUpdatingStatus: false,
            isSavingNotas: false,
            notasEditadas: '',
            copiadoCodigo: false,
            copiadoImei: false,

            rateLimiting: false,
            alertRateLimiting(){
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'No puedes cambiar tan rapido de estado', type: 'warning' }
                }));
            },

            // Control de edición por cards en el Slide-Over
            editCard: {
                cliente: false,
                dispositivo: false,
                falla: false,
                financiero: false,
            },

            // Control de Clave de Acceso y Sub-modales de Patrón
            openClaveSubModal: false,
            tipoClaveSeleccionada: 'Sin clave',
            tipoClaveConfirmada: 'Sin clave',
            claveAccesoValor: '',

            // PIN / Contraseña
            tempPinValor: '',
            mostrarPinTexto: true,

            // Patrón 3x3 Interactivo
            tempPatronSecuencia: [],
            isDrawingPattern: false,
            patternCoords: { x: 0, y: 0 },

            // Visor de Patrón en Detalle
            openPatternViewerModal: false,
            visorPatronSecuencia: [],
            visorPatronTitulo: '',
            isSavingCard: {
                cliente: false,
                dispositivo: false,
                falla: false,
                financiero: false,
            },
            formCard: {
                cliente: { nombre: '', telefono: '', email: '' },
                dispositivo: { marca_y_modelo: '', imei_o_serie: '', clave_de_acceso: '' },
                falla: { falla_reportada: '' },
                financiero: { costo_estimado: 0, sena: 0 }
            },

            // Filtrado reactivo instantáneo (Buscador híbrido + Píldoras de estado)
            filtradas() {
                return this.reparaciones.filter(item => {
                    // 1. Filtro por píldora
                    let matchEstado = true;
                    if (this.filtroEstado === 'entregadas') {
                        matchEstado = item.estado_slug === 'entregado';
                    } else if (this.filtroEstado === 'en_reparacion') {
                        matchEstado = item.estado_slug === 'en_reparacion';
                    } else if (this.filtroEstado === 'recibidas') {
                        matchEstado = item.estado_slug === 'recibido';
                    } else if (this.filtroEstado === 'listas') {
                        matchEstado = item.estado_slug === 'listo';
                    } else if (this.filtroEstado === 'canceladas') {
                        matchEstado = item.estado_slug === 'cancelado';
                    }

                    if (!matchEstado) return false;

                    // 2. Filtro por barra de búsqueda híbrida
                    if (!this.search || !this.search.trim()) return true;
                    const q = this.search.toLowerCase().trim();
                    return item.search_target && item.search_target.includes(q);
                });
            },

            // Conteo por estados para las píldoras
            totalPorEstado(slug) {
                if (slug === 'todas') return this.reparaciones.length;
                if (slug === 'entregadas') return this.reparaciones.filter(r => r.estado_slug === 'entregado').length;
                if (slug === 'en_reparacion') return this.reparaciones.filter(r => r.estado_slug === 'en_reparacion').length;
                if (slug === 'recibidas') return this.reparaciones.filter(r => r.estado_slug === 'recibido').length;
                if (slug === 'listas') return this.reparaciones.filter(r => r.estado_slug === 'listo').length;
                if (slug === 'canceladas') return this.reparaciones.filter(r => r.estado_slug === 'cancelado').length;
                return 0;
            },

            // Métodos del Slide-Over
            abrirSlideOver(rep) {
                this.selectedReparacion = rep;
                this.notasEditadas = rep.notas_internas || '';
                this.editCard.cliente = false;
                this.editCard.dispositivo = false;
                this.editCard.falla = false;
                this.editCard.financiero = false;
                this.openSlideOver = true;
            },

            cerrarSlideOver() {
                this.openSlideOver = false;
            },

            // Métodos del Modal Nueva Reparación
            abrirModalNuevaReparacion() {
                this.tipoClaveSeleccionada = 'Sin clave';
                this.tipoClaveConfirmada = 'Sin clave';
                this.claveAccesoValor = 'Sin clave';
                this.tempPinValor = '';
                this.tempPatronSecuencia = [];
                if (this.formCard && this.formCard.dispositivo) {
                    this.formCard.dispositivo.clave_de_acceso = 'Sin clave';
                }
                this.openNewModal = true;
            },

            cerrarModalNuevaReparacion() {
                this.openNewModal = false;
            },

            // Métodos de Impresión
            imprimirComprobante(rep) {
                this.selectedReparacion = rep;
                this.openPrintModal = true;
            },

            ejecutarImpresion() {
                window.print();
            },

            limpiarBusqueda() {
                this.search = '';
            },

            // Copiar al portapapeles
            copiarTexto(texto, tipo) {
                if (!navigator.clipboard) return;
                navigator.clipboard.writeText(texto).then(() => {
                    if (tipo === 'codigo') {
                        this.copiadoCodigo = true;
                        setTimeout(() => this.copiadoCodigo = false, 2000);
                    } else if (tipo === 'imei') {
                        this.copiadoImei = true;
                        setTimeout(() => this.copiadoImei = false, 2000);
                    }
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Copiado al portapapeles.', type: 'success' }
                    }));
                });
            },

            setRateLimiting() {
                this.rateLimiting = true;
                setTimeout( () => {
                    this.rateLimiting = false;
                }, 3000);
            },

            clienteTieneEmail(item) {
                if (!item || !item.cliente_email) return false;
                const email = String(item.cliente_email).trim().toLowerCase();
                return email !== '' && email !== 'sin correo' && email !== 'no especificado' && email.includes('@');
            },

            columnaLabel(key) {
                if (key === 'recibido') return 'Recibido';
                if (key === 'en_reparacion') return 'En reparación';
                if (key === 'listo') return 'Listo';
                if (key === 'entregado') return 'Entregado';
                if (key === 'cancelado') return 'Cancelado';
                return key || '';
            },

            // Abre el modal de confirmación con opciones de notificación por email
            cambiarEstado(nuevoEstado) {
                if (!this.selectedReparacion) return;
                if (this.rateLimiting) return this.alertRateLimiting();
                if (this.selectedReparacion.estado_slug === nuevoEstado) return;
                this.abrirModalConfirmacion(this.selectedReparacion, this.selectedReparacion.estado_slug, nuevoEstado);
            },

            abrirModalConfirmacion(item, from, to) {
                this.confirmModal = {
                    open: true,
                    loading: false,
                    item: item,
                    from: from,
                    to: to,
                };
                this.enviarEmail = this.clienteTieneEmail(item);
                this.mostrarPersonalizarMensaje = false;
                this.mensajePersonalizado = '';
            },

            cerrarModalConfirmacion() {
                this.confirmModal.open = false;
                this.confirmModal.item = null;
                this.confirmModal.from = null;
                this.confirmModal.to = null;
                this.mostrarPersonalizarMensaje = false;
                this.mensajePersonalizado = '';
            },

            async confirmarCambioEstado() {
                if (this.rateLimiting) {
                    return this.alertRateLimiting();
                }
                if (!this.selectedReparacion || this.isUpdatingStatus || this.rateLimiting) return;
                this.setRateLimiting();

                this.confirmModal.loading = true;
                const { item, from, to } = this.confirmModal;
                const isEntrega = to === 'entregado';

                try {
                    const url = `{{ url('/reparaciones') }}/${item.id}/estado`;
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            estado: to,
                            enviar_email: this.enviarEmail && this.clienteTieneEmail(item),
                            mensaje_personalizado: this.mensajePersonalizado ? this.mensajePersonalizado.trim() : null
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const idx = this.reparaciones.findIndex(r => r.id === item.id);
                        if (idx !== -1) {
                            this.reparaciones[idx].estado = data.estado;
                            this.reparaciones[idx].estado_slug = data.estado_slug;
                            this.reparaciones[idx].dot_color = data.dot_color;
                            if (this.selectedReparacion && this.selectedReparacion.id === item.id) {
                                this.selectedReparacion = { ...this.reparaciones[idx] };
                            }
                        }

                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                message: data.message || (isEntrega ? 'Reparación marcada como entregada.' : `Estado actualizado a ${this.columnaLabel(to)}`),
                                type: 'success'
                            }
                        }));

                        this.cerrarModalConfirmacion();
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                message: data.message || 'Error al actualizar el estado de la reparación.',
                                type: 'error'
                            }
                        }));
                    }
                } catch (e) {
                    console.error('Error al actualizar estado:', e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Hubo un error de conexión al actualizar el estado.', type: 'error' }
                    }));
                } finally {
                    this.confirmModal.loading = false;
                }
            },

            // Guardar notas internas del técnico en vivo
            async guardarNotas() {
                if (!this.selectedReparacion || this.isSavingNotas) return;
                this.isSavingNotas = true;
                const repId = this.selectedReparacion.id;

                try {
                    const url = `{{ url('/reparaciones') }}/${repId}/notas`;
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ notas_internas: this.notasEditadas })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        const idx = this.reparaciones.findIndex(r => r.id === repId);
                        if (idx !== -1) {
                            this.reparaciones[idx].notas_internas = this.notasEditadas;
                            this.selectedReparacion.notas_internas = this.notasEditadas;
                        }

                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Notas técnicas guardadas con éxito.', type: 'success' }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: data.message || 'Error al guardar notas.', type: 'error' }
                        }));
                    }
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Hubo un error de conexión al guardar notas.', type: 'error' }
                    }));
                } finally {
                    this.isSavingNotas = false;
                }
            },

            // Construir enlace de WhatsApp con mensaje personalizado
            mensajeWhatsapp(rep) {
                if (!rep) return '#';
                const telefono = (rep.cliente_telefono || '').replace(/[^0-9]/g, '');
                if (!telefono) return '#';

                let raw = telefono;
                if (raw.startsWith('0')) raw = raw.substring(1);
                if (raw.length === 10) raw = '549' + raw;
                else if (raw.length === 11 && raw.startsWith('15')) raw = '549' + raw.substring(2);
                else if (raw.startsWith('54') && !raw.startsWith('549') && raw.length === 12) raw = '549' + raw.substring(2);

                const texto = `Hola ${rep.cliente_nombre}, te escribimos de ${rep.negocio_nombre} sobre tu equipo ${rep.dispositivo_marca_modelo} (Orden ${rep.codigo_seguimiento}). Estado actual: ${rep.estado}. ¡Saludos!`;
                return `https://wa.me/${raw}?text=${encodeURIComponent(texto)}`;
            },

            // Acciones de edición por Cards en el Slide-Over
            iniciarEdicion(card) {
                if (!this.selectedReparacion) return;
                if (card === 'cliente') {
                    this.formCard.cliente = {
                        nombre: this.selectedReparacion.cliente_nombre || '',
                        telefono: this.selectedReparacion.cliente_telefono || '',
                        email: (this.selectedReparacion.cliente_email && this.selectedReparacion.cliente_email !== 'Sin correo') ? this.selectedReparacion.cliente_email : ''
                    };
                } else if (card === 'dispositivo') {
                    const clave = (this.selectedReparacion.clave_de_acceso && this.selectedReparacion.clave_de_acceso !== 'Sin clave') ? this.selectedReparacion.clave_de_acceso : '';
                    this.formCard.dispositivo = {
                        marca_y_modelo: this.selectedReparacion.dispositivo_marca_modelo || '',
                        imei_o_serie: (this.selectedReparacion.imei_o_serie && this.selectedReparacion.imei_o_serie !== 'No especificado') ? this.selectedReparacion.imei_o_serie : '',
                        clave_de_acceso: clave
                    };

                    const esPat = this.esPatron(clave);
                    const esPin = clave.startsWith('PIN:') || (clave !== 'Sin clave' && clave !== 'Huella / Face ID' && !esPat && clave.trim().length > 0);
                    const esHuella = clave === 'Huella / Face ID';
                    const tipo = esPat ? 'Patrón de desbloqueo' : (esPin ? 'PIN / Contraseña' : (esHuella ? 'Huella / Face ID' : 'Sin clave'));

                    this.tipoClaveSeleccionada = tipo;
                    this.tipoClaveConfirmada = tipo;
                    this.claveAccesoValor = clave;
                    this.tempPatronSecuencia = esPat ? this.extraerSecuenciaPatron(clave) : [];
                    this.tempPinValor = esPin ? this.extraerPin(clave) : '';
                } else if (card === 'falla') {
                    this.formCard.falla = {
                        falla_reportada: (this.selectedReparacion.falla_reportada && this.selectedReparacion.falla_reportada !== 'No especificada') ? this.selectedReparacion.falla_reportada : ''
                    };
                } else if (card === 'financiero') {
                    this.formCard.financiero = {
                        costo_estimado: this.selectedReparacion.costo_estimado_num || 0,
                        sena: this.selectedReparacion.sena_num || 0
                    };
                }
                this.editCard[card] = true;
            },

            cancelarEdicion(card) {
                this.editCard[card] = false;
            },

            // Métodos para Gestión de Claves y Patrón 3x3
            onTipoClaveChange(val) {
                if (val === 'PIN / Contraseña') {
                    this.tempPinValor = this.extraerPin(this.formCard.dispositivo.clave_de_acceso || this.claveAccesoValor);
                    this.openClaveSubModal = true;
                } else if (val === 'Patrón de desbloqueo') {
                    this.tempPatronSecuencia = this.extraerSecuenciaPatron(this.formCard.dispositivo.clave_de_acceso || this.claveAccesoValor);
                    this.openClaveSubModal = true;
                } else if (val === 'Huella / Face ID') {
                    this.claveAccesoValor = 'Huella / Face ID';
                    this.formCard.dispositivo.clave_de_acceso = 'Huella / Face ID';
                    this.tipoClaveConfirmada = 'Huella / Face ID';
                    this.openClaveSubModal = false;
                } else {
                    this.claveAccesoValor = 'Sin clave';
                    this.formCard.dispositivo.clave_de_acceso = 'Sin clave';
                    this.tipoClaveConfirmada = 'Sin clave';
                    this.tempPinValor = '';
                    this.tempPatronSecuencia = [];
                    this.openClaveSubModal = false;
                }
            },

            cancelarSubModalClave() {
                this.openClaveSubModal = false;
                this.isDrawingPattern = false;
                if (!this.tipoClaveConfirmada || this.tipoClaveConfirmada === 'Sin clave') {
                    this.tipoClaveSeleccionada = 'Sin clave';
                    this.claveAccesoValor = 'Sin clave';
                    this.formCard.dispositivo.clave_de_acceso = 'Sin clave';
                } else {
                    this.tipoClaveSeleccionada = this.tipoClaveConfirmada;
                }
            },

            guardarSubModalClave() {
                if (this.tipoClaveSeleccionada === 'PIN / Contraseña') {
                    const pin = (this.tempPinValor || '').trim();
                    if (!pin) {
                        this.tipoClaveSeleccionada = 'Sin clave';
                        this.tipoClaveConfirmada = 'Sin clave';
                        this.claveAccesoValor = 'Sin clave';
                        this.formCard.dispositivo.clave_de_acceso = 'Sin clave';
                        this.openClaveSubModal = false;
                        return;
                    }
                    this.claveAccesoValor = pin.startsWith('PIN:') || pin.startsWith('Contraseña:') ? pin : `PIN: ${pin}`;
                    this.formCard.dispositivo.clave_de_acceso = this.claveAccesoValor;
                    this.tipoClaveConfirmada = 'PIN / Contraseña';
                    this.openClaveSubModal = false;
                } else if (this.tipoClaveSeleccionada === 'Patrón de desbloqueo') {
                    if (this.tempPatronSecuencia.length < 2) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                message: 'Debes conectar al menos 2 puntos para formar un patrón.',
                                type: 'error'
                            }
                        }));
                        return;
                    }
                    this.claveAccesoValor = `Patrón: ${this.tempPatronSecuencia.join('-')}`;
                    this.formCard.dispositivo.clave_de_acceso = this.claveAccesoValor;
                    this.tipoClaveConfirmada = 'Patrón de desbloqueo';
                    this.openClaveSubModal = false;
                }
            },

            verOEditarClave() {
                if (this.tipoClaveConfirmada === 'PIN / Contraseña' || this.tipoClaveSeleccionada === 'PIN / Contraseña') {
                    this.tempPinValor = this.extraerPin(this.formCard.dispositivo.clave_de_acceso || this.claveAccesoValor);
                    this.tipoClaveSeleccionada = 'PIN / Contraseña';
                    this.openClaveSubModal = true;
                } else if (this.tipoClaveConfirmada === 'Patrón de desbloqueo' || this.tipoClaveSeleccionada === 'Patrón de desbloqueo') {
                    this.tempPatronSecuencia = this.extraerSecuenciaPatron(this.formCard.dispositivo.clave_de_acceso || this.claveAccesoValor);
                    this.tipoClaveSeleccionada = 'Patrón de desbloqueo';
                    this.openClaveSubModal = true;
                }
            },

            extraerPin(texto) {
                if (!texto || texto === 'Sin clave' || texto === 'Huella / Face ID') return '';
                return texto.replace(/^PIN:\s*/i, '').replace(/^Contraseña:\s*/i, '');
            },

            extraerSecuenciaPatron(texto) {
                if (!texto) return [];
                const match = texto.match(/Patr[oó]n:\s*([0-9\-]+)/i);
                if (match) {
                    return match[1].split('-').map(Number).filter(n => n >= 1 && n <= 9);
                }
                if (/^[1-9](\-[1-9])+$/.test(texto.trim())) {
                    return texto.trim().split('-').map(Number);
                }
                return [];
            },

            esPatron(texto) {
                if (!texto) return false;
                return texto.toLowerCase().includes('patrón') || texto.toLowerCase().includes('patron') || /^[1-9](\-[1-9])+$/.test(texto.trim());
            },

            obtenerPosPunto(num) {
                const mapa = {
                    1: { x: 50, y: 50 },
                    2: { x: 150, y: 50 },
                    3: { x: 250, y: 50 },
                    4: { x: 50, y: 150 },
                    5: { x: 150, y: 150 },
                    6: { x: 250, y: 150 },
                    7: { x: 50, y: 250 },
                    8: { x: 150, y: 250 },
                    9: { x: 250, y: 250 }
                };
                return mapa[num] || { x: 150, y: 150 };
            },

            generarPuntosPolyline(secuencia) {
                if (!secuencia || !secuencia.length) return '';
                return secuencia.map(n => {
                    const p = this.obtenerPosPunto(n);
                    return `${p.x},${p.y}`;
                }).join(' ');
            },

            obtenerPuntoCercano(x, y) {
                for (let i = 1; i <= 9; i++) {
                    const p = this.obtenerPosPunto(i);
                    const dist = Math.hypot(x - p.x, y - p.y);
                    if (dist <= 36) {
                        return i;
                    }
                }
                return null;
            },

            actualizarPunteroDesdeEvento(e, container) {
                if (!container) return;
                const rect = container.getBoundingClientRect();
                const clientX = e.touches && e.touches.length > 0 ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches && e.touches.length > 0 ? e.touches[0].clientY : e.clientY;
                const scaleX = 300 / rect.width;
                const scaleY = 300 / rect.height;
                const x = Math.max(0, Math.min(300, (clientX - rect.left) * scaleX));
                const y = Math.max(0, Math.min(300, (clientY - rect.top) * scaleY));
                this.patternCoords = { x, y };

                const punto = this.obtenerPuntoCercano(x, y);
                if (punto && !this.tempPatronSecuencia.includes(punto)) {
                    this.tempPatronSecuencia.push(punto);
                }
            },

            iniciarTrazo(e, container) {
                this.isDrawingPattern = true;
                this.actualizarPunteroDesdeEvento(e, container);
            },

            moverTrazo(e, container) {
                if (!this.isDrawingPattern) return;
                this.actualizarPunteroDesdeEvento(e, container);
            },

            finalizarTrazo() {
                this.isDrawingPattern = false;
            },

            hacerClicEnPunto(num) {
                if (!this.tempPatronSecuencia.includes(num)) {
                    this.tempPatronSecuencia.push(num);
                }
            },

            limpiarPatron() {
                this.tempPatronSecuencia = [];
                this.isDrawingPattern = false;
            },

            verPatronEnDetalle(item) {
                if (!item) return;
                this.visorPatronSecuencia = this.extraerSecuenciaPatron(item.clave_de_acceso);
                this.visorPatronTitulo = `${item.codigo_seguimiento || ''} - ${item.dispositivo_marca_modelo || ''}`;
                this.openPatternViewerModal = true;
            },

            actualizarSearchTarget(r) {
                r.search_target = String(
                    (r.cliente_nombre || '') + ' ' +
                    (r.codigo_seguimiento || '') + ' ' +
                    (r.codigo_limpio || '') + ' ' +
                    (r.dispositivo_marca_modelo || '') + ' ' +
                    (r.imei_o_serie || '') + ' ' +
                    (r.cliente_telefono || '') + ' ' +
                    (r.falla_reportada || '') + ' ' +
                    (r.notas_internas || '') + ' ' +
                    (r.estado || '') + ' ' +
                    (r.tecnico || '')
                ).toLowerCase();
            },

            saldoPreviewForm() {
                const costo = Math.max(0, Number(this.formCard.financiero.costo_estimado) || 0);
                const sena = Math.max(0, Number(this.formCard.financiero.sena) || 0);
                const saldo = Math.max(0, costo - sena);
                return '$' + new Intl.NumberFormat('es-AR').format(saldo);
            },

            get totalPendienteCobroActual() {
                const total = this.reparaciones.reduce((acc, r) => acc + (Number(r.saldo_pendiente_num) || 0), 0);
                return '$' + new Intl.NumberFormat('es-AR').format(total);
            },

            async guardarCliente() {
                if (!this.selectedReparacion || this.isSavingCard.cliente) return;
                const nombre = (this.formCard.cliente.nombre || '').trim();
                const telefono = (this.formCard.cliente.telefono || '').trim();
                const email = (this.formCard.cliente.email || '').trim();

                if (!nombre) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'El nombre del cliente es obligatorio.', type: 'error' }
                    }));
                    return;
                }
                if (!telefono) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'El teléfono del cliente es obligatorio.', type: 'error' }
                    }));
                    return;
                }

                this.isSavingCard.cliente = true;
                try {
                    const url = `{{ url('/clientes') }}/${this.selectedReparacion.cliente_id}`;
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ nombre, telefono, email: email || null })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        const c = data.cliente;
                        this.selectedReparacion.cliente_nombre = c.nombre;
                        this.selectedReparacion.cliente_telefono = c.telefono;
                        this.selectedReparacion.cliente_email = c.email;
                        this.selectedReparacion.cliente_iniciales = c.iniciales;
                        this.selectedReparacion.whatsapp_url = c.whatsapp_url;

                        this.reparaciones.forEach(r => {
                            if (r.cliente_id === c.id) {
                                r.cliente_nombre = c.nombre;
                                r.cliente_telefono = c.telefono;
                                r.cliente_email = c.email;
                                r.cliente_iniciales = c.iniciales;
                                r.whatsapp_url = c.whatsapp_url;
                                this.actualizarSearchTarget(r);
                            }
                        });

                        this.editCard.cliente = false;
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: data.message || 'Cliente actualizado con éxito.', type: 'success' }
                        }));
                    } else {
                        const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Error al actualizar cliente.');
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: 'error' } }));
                    }
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Error de conexión al actualizar cliente.', type: 'error' }
                    }));
                } finally {
                    this.isSavingCard.cliente = false;
                }
            },

            async guardarDispositivo() {
                if (!this.selectedReparacion || this.isSavingCard.dispositivo) return;
                const marca_y_modelo = (this.formCard.dispositivo.marca_y_modelo || '').trim();
                const imei_o_serie = (this.formCard.dispositivo.imei_o_serie || '').trim();
                const clave_de_acceso = (this.formCard.dispositivo.clave_de_acceso || '').trim();

                if (!marca_y_modelo) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'La marca y modelo son obligatorios.', type: 'error' }
                    }));
                    return;
                }

                this.isSavingCard.dispositivo = true;
                try {
                    const urlDisp = `{{ url('/dispositivos') }}/${this.selectedReparacion.dispositivo_id}`;
                    const resDisp = await fetch(urlDisp, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ marca_y_modelo, imei_o_serie: imei_o_serie || null })
                    });

                    const dataDisp = await resDisp.json();
                    if (!resDisp.ok || !dataDisp.success) {
                        const msg = dataDisp.errors ? Object.values(dataDisp.errors).flat().join(' ') : (dataDisp.message || 'Error al actualizar dispositivo.');
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: 'error' } }));
                        return;
                    }

                    // Si se modificó la clave de acceso, actualizar también la reparación
                    const claveActual = (this.selectedReparacion.clave_de_acceso === 'Sin clave') ? '' : (this.selectedReparacion.clave_de_acceso || '');
                    if (clave_de_acceso !== claveActual) {
                        const urlRep = `{{ url('/reparaciones') }}/${this.selectedReparacion.id}`;
                        const resRep = await fetch(urlRep, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ clave_de_acceso: clave_de_acceso || null })
                        });
                        const dataRep = await resRep.json();
                        if (resRep.ok && dataRep.success) {
                            this.selectedReparacion.clave_de_acceso = dataRep.reparacion.clave_de_acceso;
                        }
                    }

                    const d = dataDisp.dispositivo;
                    this.selectedReparacion.dispositivo_marca_modelo = d.marca_y_modelo;
                    this.selectedReparacion.imei_o_serie = d.imei_o_serie;

                    const idx = this.reparaciones.findIndex(r => r.id === this.selectedReparacion.id);
                    if (idx !== -1) {
                        this.reparaciones[idx].dispositivo_marca_modelo = d.marca_y_modelo;
                        this.reparaciones[idx].imei_o_serie = d.imei_o_serie;
                        this.reparaciones[idx].clave_de_acceso = this.selectedReparacion.clave_de_acceso;
                        this.actualizarSearchTarget(this.reparaciones[idx]);
                    }

                    this.editCard.dispositivo = false;
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Dispositivo y seguridad actualizados correctamente.', type: 'success' }
                    }));
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Error de conexión al actualizar dispositivo.', type: 'error' }
                    }));
                } finally {
                    this.isSavingCard.dispositivo = false;
                }
            },

            async guardarFalla() {
                if (!this.selectedReparacion || this.isSavingCard.falla) return;
                const falla = (this.formCard.falla.falla_reportada || '').trim();

                if (!falla) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'La falla reportada no puede estar vacía.', type: 'error' }
                    }));
                    return;
                }

                this.isSavingCard.falla = true;
                try {
                    const url = `{{ url('/reparaciones') }}/${this.selectedReparacion.id}`;
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ falla_reportada: falla })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        const rep = data.reparacion;
                        this.selectedReparacion.falla_reportada = rep.falla_reportada;

                        const idx = this.reparaciones.findIndex(r => r.id === this.selectedReparacion.id);
                        if (idx !== -1) {
                            this.reparaciones[idx].falla_reportada = rep.falla_reportada;
                            this.actualizarSearchTarget(this.reparaciones[idx]);
                        }

                        this.editCard.falla = false;
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Diagnóstico actualizado con éxito.', type: 'success' }
                        }));
                    } else {
                        const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Error al actualizar falla.');
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: 'error' } }));
                    }
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Error de conexión al actualizar diagnóstico.', type: 'error' }
                    }));
                } finally {
                    this.isSavingCard.falla = false;
                }
            },

            async guardarFinanciero() {
                if (!this.selectedReparacion || this.isSavingCard.financiero) return;
                const costo = Math.max(0, Number(this.formCard.financiero.costo_estimado) || 0);
                const sena = Math.max(0, Number(this.formCard.financiero.sena) || 0);

                this.isSavingCard.financiero = true;
                try {
                    const url = `{{ url('/reparaciones') }}/${this.selectedReparacion.id}`;
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ costo_estimado: costo, sena: sena })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        const rep = data.reparacion;
                        this.selectedReparacion.costo_estimado = rep.costo_estimado;
                        this.selectedReparacion.costo_estimado_num = rep.costo_estimado_num;
                        this.selectedReparacion.sena = rep.sena;
                        this.selectedReparacion.sena_num = rep.sena_num;
                        this.selectedReparacion.saldo_pendiente = rep.saldo_pendiente;
                        this.selectedReparacion.saldo_pendiente_num = rep.saldo_pendiente_num;
                        this.selectedReparacion.esta_saldado = rep.esta_saldado;

                        const idx = this.reparaciones.findIndex(r => r.id === this.selectedReparacion.id);
                        if (idx !== -1) {
                            this.reparaciones[idx].costo_estimado = rep.costo_estimado;
                            this.reparaciones[idx].costo_estimado_num = rep.costo_estimado_num;
                            this.reparaciones[idx].sena = rep.sena;
                            this.reparaciones[idx].sena_num = rep.sena_num;
                            this.reparaciones[idx].saldo_pendiente = rep.saldo_pendiente;
                            this.reparaciones[idx].saldo_pendiente_num = rep.saldo_pendiente_num;
                            this.reparaciones[idx].esta_saldado = rep.esta_saldado;
                        }

                        this.editCard.financiero = false;
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Estado financiero actualizado correctamente.', type: 'success' }
                        }));
                    } else {
                        const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Error al actualizar balance.');
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: msg, type: 'error' } }));
                    }
                } catch (e) {
                    console.error(e);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'Error de conexión al actualizar balance.', type: 'error' }
                    }));
                } finally {
                    this.isSavingCard.financiero = false;
                }
            },

            // Modal Nueva Reparación
            abrirModalNuevaReparacion() {
                this.tipoClaveSeleccionada = 'Sin clave';
                this.tipoClaveConfirmada = 'Sin clave';
                this.claveAccesoValor = 'Sin clave';
                this.tempPinValor = '';
                this.tempPatronSecuencia = [];
                this.openNewModal = true;
            },

            cerrarModalNuevaReparacion() {
                this.openNewModal = false;
            }
        }"
    >
        @include('components.sidebar')

        <div class="px-8 sm:px-12 py-10">

            {{-- =========================================================
                1. EL ENCABEZADO: BÚSQUEDA HÍBRIDA Y PÍLDORAS DE ESTADO (CERO DROPDOWNS)
            ========================================================== --}}
            <div class="flex flex-col gap-5">

                {{-- Fila Principal: Buscador Grande Híbrido + Botón Nueva Reparación --}}
                <div class="flex items-center justify-between gap-6">

                    {{-- La Barra Híbrida --}}
                    <div class="relative flex-1">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="absolute left-6 top-1/2 h-7 w-7 -translate-y-1/2 text-text-disabled pointer-events-none"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"
                            />
                        </svg>

                        <input
                            type="search"
                            x-model="search"
                            placeholder="Buscar por Cliente, Código (ej. JA-4829), Modelo o IMEI..."
                            class="h-16 w-full rounded-full border-0 bg-surface-hover pl-20 pr-14 text-base font-semibold text-text-primary placeholder:text-text-disabled outline-none focus:ring-2 focus:ring-primary shadow-sm transition-all"
                        >

                        {{-- Botón para limpiar búsqueda --}}
                        <button
                            x-show="search.length > 0"
                            x-cloak
                            @click="limpiarBusqueda()"
                            type="button"
                            class="absolute right-5 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-surface text-text-disabled hover:text-white hover:bg-border/60 transition-colors cursor-pointer"
                            title="Limpiar búsqueda"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Botón Nueva Reparación --}}
                    <x-btn-nueva-reparacion />

                </div>

                {{-- Píldoras de Estado (Pills): Filtro Rápido con 1 Clic --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
                    
                    <div class="flex flex-wrap items-center gap-2.5">
                        
                        {{-- Pill: Todas --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'todas'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'todas'
                                ? 'bg-surface-hover text-white border-primary/60 shadow-sm ring-1 ring-primary/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span>Todas</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'todas' ? 'bg-primary text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('todas')"
                            ></span>
                        </button>

                        {{-- Pill: Entregadas --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'entregadas'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'entregadas'
                                ? 'bg-surface-hover text-white border-emerald-500/60 shadow-sm ring-1 ring-emerald-500/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            <span>Entregadas</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'entregadas' ? 'bg-emerald-500 text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('entregadas')"
                            ></span>
                        </button>

                        {{-- Pill: En reparación --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'en_reparacion'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'en_reparacion'
                                ? 'bg-surface-hover text-white border-amber-500/60 shadow-sm ring-1 ring-amber-500/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            <span>En reparación</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'en_reparacion' ? 'bg-amber-500 text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('en_reparacion')"
                            ></span>
                        </button>

                        {{-- Pill: Recibidas --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'recibidas'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'recibidas'
                                ? 'bg-surface-hover text-white border-primary/60 shadow-sm ring-1 ring-primary/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span class="h-2 w-2 rounded-full bg-[#0081cc]"></span>
                            <span>Recibidas</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'recibidas' ? 'bg-primary text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('recibidas')"
                            ></span>
                        </button>

                        {{-- Pill: Listas (Para entregar) --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'listas'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'listas'
                                ? 'bg-surface-hover text-white border-teal-400/60 shadow-sm ring-1 ring-teal-400/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                            <span>Listas</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'listas' ? 'bg-teal-500 text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('listas')"
                            ></span>
                        </button>

                        {{-- Pill: Canceladas --}}
                        <button
                            type="button"
                            @click="filtroEstado = 'canceladas'"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-200 cursor-pointer border select-none"
                            :class="filtroEstado === 'canceladas'
                                ? 'bg-surface-hover text-white border-rose-500/60 shadow-sm ring-1 ring-rose-500/30'
                                : 'bg-[#1c2530]/70 text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover/70'"
                        >
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            <span>Canceladas</span>
                            <span
                                class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold"
                                :class="filtroEstado === 'canceladas' ? 'bg-rose-500 text-white' : 'bg-surface text-text-disabled'"
                                x-text="totalPorEstado('canceladas')"
                            ></span>
                        </button>

                    </div>

                    {{-- Resumen Rápido / Contador Activo --}}
                    <div class="flex items-center gap-4 text-xs text-text-disabled font-medium px-2">
                        <span class="text-text-secondary">
                            Mostrando: <strong class="text-white font-bold" x-text="filtradas().length"></strong> de <strong class="text-white font-bold">{{ $totalTodas }}</strong>
                        </span>
                        <span>•</span>
                        <span>
                            Por cobrar: <strong class="text-amber-400 font-bold" x-text="totalPendienteCobroActual"></strong>
                        </span>
                    </div>

                </div>

            </div>


            {{-- =========================================================
                2. LA FILA INTELIGENTE DE REPARACIÓN (3 BLOQUES)
            ========================================================== --}}
            <div class="mt-8 flex flex-col gap-3.5">

                {{-- Template Iterativo de Filas Horizontales --}}
                <template x-for="item in filtradas()" :key="item.id">
                    <div
                        @click="abrirSlideOver(item)"
                        class="group relative flex flex-col md:flex-row md:items-center justify-between gap-5 rounded-[22px] bg-[#1c2530] border border-[#2b3a4d] px-6 py-5 shadow-md hover:bg-[#232f3e] hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition-all duration-200 cursor-pointer select-none"
                    >
                        {{-- Resplandor sutil hover --}}
                        <div class="absolute inset-0 rounded-[22px] bg-gradient-to-r from-primary/0 via-primary/5 to-transparent opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300"></div>

                        {{-- =========================================
                            BLOQUE 1: LA IDENTIDAD TÉCNICA (Izquierda)
                        ========================================== --}}
                        <div class="relative z-10 flex flex-col min-w-0 md:w-5/12 lg:w-4/12 gap-1.5">
                            
                            {{-- Código de Seguimiento Destacado --}}
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="text-base sm:text-lg font-bold text-primary-light tracking-wide bg-[#141c25] px-3 py-0.5 rounded-xl border border-border/40 group-hover:border-primary/50 transition-colors inline-flex items-center gap-1.5 shrink-0"
                                    x-text="item.codigo_seguimiento"
                                ></span>

                                <template x-if="item.imei_o_serie && item.imei_o_serie !== 'No especificado'">
                                    <span class="text-[11px] font-mono text-text-disabled bg-surface px-2 py-0.5 rounded-md truncate max-w-[140px]" :title="'IMEI / Serie: ' + item.imei_o_serie" x-text="item.imei_o_serie"></span>
                                </template>
                            </div>

                            {{-- Dispositivo y Cliente (Secundario) --}}
                            <div class="flex items-center gap-2 text-sm font-medium text-text-secondary truncate mt-0.5">
                                <span class="text-white font-semibold truncate group-hover:text-primary-light transition-colors" :title="item.dispositivo_marca_modelo" x-text="item.dispositivo_marca_modelo"></span>
                                <span class="text-text-disabled">•</span>
                                <span class="text-text-secondary truncate" :title="item.cliente_nombre" x-text="item.cliente_nombre"></span>
                            </div>

                            {{-- Falla Reportada sutil --}}
                            <p class="text-xs text-text-disabled truncate max-w-sm" :title="item.falla_reportada" x-text="item.falla_reportada"></p>
                        </div>


                        {{-- =========================================
                            BLOQUE 2: EL TIEMPO Y ESTADO (Centro)
                        ========================================== --}}
                        <div class="relative z-10 flex flex-col justify-center min-w-0 md:w-3/12 lg:w-4/12 gap-1">
                            
                            {{-- Etiqueta Minimalista (Badge con diseño Dot) --}}
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full shrink-0 animate-pulse" :class="item.dot_color"></span>
                                <span class="text-sm font-semibold text-text-primary tracking-tight" x-text="item.estado"></span>
                            </div>

                            {{-- Fecha de Ingreso y Tiempo Relativo --}}
                            <div class="flex items-center gap-2 text-xs text-text-disabled">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-text-disabled">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                </svg>
                                <span x-text="item.fecha_corta"></span>
                                <span>•</span>
                                <span class="italic" x-text="item.tiempo_relativo"></span>
                            </div>
                        </div>


                        {{-- =========================================
                            BLOQUE 3: DINERO Y ACCIONES (Derecha)
                        ========================================== --}}
                        <div class="relative z-10 flex items-center justify-between md:justify-end gap-6 md:w-4/12 lg:w-4/12 shrink-0">
                            
                            {{-- Costo Total y Balance --}}
                            <div class="flex flex-col items-start md:items-end">
                                <span class="text-lg sm:text-xl font-bold text-white tracking-tight" x-text="item.costo_estimado"></span>
                                
                                <template x-if="item.saldo_pendiente_num > 0">
                                    <span class="text-xs font-semibold text-amber-400 flex items-center gap-1">
                                        <span>Pendiente:</span>
                                        <span class="font-bold" x-text="item.saldo_pendiente"></span>
                                    </span>
                                </template>

                                <template x-if="item.esta_saldado">
                                    <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>Saldado</span>
                                    </span>
                                </template>
                            </div>

                            {{-- Acciones (Íconos Limpios con Hover) --}}
                            <div class="flex items-center gap-2">
                                
                                {{-- 1. Ícono de Impresora --}}
                                <button
                                    type="button"
                                    @click.stop="imprimirComprobante(item)"
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#141c25] border border-border/40 text-text-disabled hover:text-white hover:border-primary/60 hover:bg-primary/20 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer shadow-sm"
                                    title="Imprimir comprobante térmico o PDF"
                                    aria-label="Imprimir comprobante"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                    </svg>
                                </button>

                                {{-- 2. Ícono de Flecha (>) / Detalle --}}
                                <button
                                    type="button"
                                    @click.stop="abrirSlideOver(item)"
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/15 text-primary-light border border-primary/30 hover:bg-primary hover:text-white hover:border-primary hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer shadow-sm"
                                    title="Abrir panel de detalles"
                                    aria-label="Ver detalles"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 group-hover:translate-x-0.5 transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>

                            </div>

                        </div>

                    </div>
                </template>


                {{-- =========================================
                    ESTADO VACÍO: BÚSQUEDA SIN RESULTADOS
                ========================================== --}}
                <div
                    x-show="filtradas().length === 0 && reparaciones.length > 0"
                    x-cloak
                    class="flex flex-col items-center justify-center rounded-[26px] bg-[#1c2530]/50 border border-border/40 py-16 px-6 text-center"
                >
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-hover text-text-disabled mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">No se encontraron reparaciones</h3>
                    <p class="mt-1 text-sm text-text-secondary max-w-sm">
                        No hay coincidencias para los filtros aplicados. Prueba buscando con otro código, cliente o cambiando la píldora de estado.
                    </p>
                    <button
                        type="button"
                        @click="limpiarBusqueda(); filtroEstado = 'todas'"
                        class="mt-5 rounded-xl bg-surface-hover hover:bg-border/60 px-5 py-2 text-xs font-bold text-text-primary transition-colors cursor-pointer"
                    >
                        Restablecer filtros
                    </button>
                </div>


                {{-- =========================================
                    ESTADO VACÍO: SIN REPARACIONES REGISTRADAS
                ========================================== --}}
                <div
                    x-show="reparaciones.length === 0"
                    x-cloak
                    class="flex flex-col items-center justify-center rounded-[30px] bg-[#1c2530]/40 border-2 border-dashed border-border/60 py-20 px-6 text-center"
                >
                    <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-primary/10 border border-primary/20 text-primary mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-10 h-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10h3V7L6.5 3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1-3 3l-6-6a6 6 0 0 1-8-8L7 10Z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Comienza a gestionar tus reparaciones</h2>
                    <p class="mt-2 text-sm text-text-secondary max-w-md">
                        Registra órdenes de servicio técnico, controla estados en tiempo real, emite comprobantes térmicos y administra el historial técnico.
                    </p>
                    <button
                        type="button"
                        @click="abrirModalNuevaReparacion()"
                        class="mt-6 rounded-2xl bg-primary hover:bg-primary-hover px-8 py-3.5 text-sm font-bold text-white transition-all shadow-lg active:scale-95 cursor-pointer"
                    >
                        + Registrar Primer Servicio
                    </button>
                </div>

            </div>

        </div>


        {{-- =========================================================
            3. EL COMPORTAMIENTO UX: EL "SLIDE-OVER" (PANEL LATERAL)
        ========================================================== --}}
        <div
            x-show="openSlideOver"
            x-cloak
            @keydown.escape.window="cerrarSlideOver()"
            class="fixed inset-0 z-50 overflow-hidden"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop con Desenfoque Oscuro --}}
            <div
                x-show="openSlideOver"
                x-transition:enter="ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="cerrarSlideOver()"
                class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"
            ></div>

            {{-- Panel Lateral Deslizable (Slide-Over) --}}
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div
                    x-show="openSlideOver"
                    x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-xl bg-[#141c25] border-l border-border/40 shadow-2xl flex flex-col justify-between"
                >
                    <template x-if="selectedReparacion">
                        <div class="flex h-full flex-col">

                            {{-- ── Encabezado Sticky del Slide-Over ── --}}
                            <div class="flex items-center justify-between border-b border-border/30 px-6 py-5 bg-[#141c25]/90 backdrop-blur-md shrink-0">
                                
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/20 text-primary border border-primary/30">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10h3V7L6.5 3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1-3 3l-6-6a6 6 0 0 1-8-8L7 10Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h2 class="text-xl font-extrabold text-white tracking-tight" x-text="selectedReparacion.codigo_seguimiento"></h2>
                                            <button
                                                type="button"
                                                @click="copiarTexto(selectedReparacion.codigo_limpio, 'codigo')"
                                                class="text-text-disabled hover:text-primary-light transition-colors p-1 rounded-md cursor-pointer"
                                                title="Copiar código"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-text-disabled" x-text="selectedReparacion.tiempo_relativo"></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- Badge Estado Minimalista --}}
                                    <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface border border-border/40">
                                        <span class="h-2 w-2 rounded-full" :class="selectedReparacion.dot_color"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider" x-text="selectedReparacion.estado"></span>
                                    </div>

                                    {{-- Botón Cerrar X --}}
                                    <button
                                        type="button"
                                        @click="cerrarSlideOver()"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-surface-hover text-text-disabled hover:text-white hover:bg-border/60 transition-colors cursor-pointer"
                                        title="Cerrar panel (Esc)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                            </div>

                            {{-- ── Cuerpo del Slide-Over con Scroll ── --}}
                            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">

                                {{-- SECCIÓN 1: SELECTOR RÁPIDO DE CAMBIO DE ESTADO --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30">
                                    <span class="block text-xs font-bold text-text-disabled uppercase tracking-wider mb-2.5">
                                        Cambiar Estado de la Orden
                                    </span>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        {{-- Estado Recibido --}}
                                        <button
                                            type="button"
                                            :disabled="isUpdatingStatus"
                                            @click="cambiarEstado('recibido')"
                                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all border cursor-pointer disabled:opacity-50"
                                            :class="selectedReparacion.estado_slug === 'recibido'
                                                ? 'bg-[#0081cc] text-white border-[#0081cc] shadow-md ring-2 ring-[#0081cc]/30'
                                                : 'bg-[#141c25] text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover'"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-[#0081cc]"></span>
                                            <span>Recibido</span>
                                        </button>

                                        {{-- Estado En Reparación --}}
                                        <button
                                            type="button"
                                            :disabled="isUpdatingStatus"
                                            @click="cambiarEstado('en_reparacion')"
                                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all border cursor-pointer disabled:opacity-50"
                                            :class="selectedReparacion.estado_slug === 'en_reparacion'
                                                ? 'bg-amber-500 text-white border-amber-500 shadow-md ring-2 ring-amber-500/30'
                                                : 'bg-[#141c25] text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover'"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                            <span>En Reparación</span>
                                        </button>

                                        {{-- Estado Listo --}}
                                        <button
                                            type="button"
                                            :disabled="isUpdatingStatus"
                                            @click="cambiarEstado('listo')"
                                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all border cursor-pointer disabled:opacity-50"
                                            :class="selectedReparacion.estado_slug === 'listo'
                                                ? 'bg-teal-500 text-white border-teal-500 shadow-md ring-2 ring-teal-500/30'
                                                : 'bg-[#141c25] text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover'"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                                            <span>Listo</span>
                                        </button>

                                        {{-- Estado Entregado --}}
                                        <button
                                            type="button"
                                            :disabled="isUpdatingStatus"
                                            @click="cambiarEstado('entregado')"
                                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all border cursor-pointer disabled:opacity-50"
                                            :class="selectedReparacion.estado_slug === 'entregado'
                                                ? 'bg-emerald-500 text-white border-emerald-500 shadow-md ring-2 ring-emerald-500/30'
                                                : 'bg-[#141c25] text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover'"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                            <span>Entregado</span>
                                        </button>

                                        {{-- Estado Cancelado --}}
                                        <button
                                            type="button"
                                            :disabled="isUpdatingStatus"
                                            @click="cambiarEstado('cancelado')"
                                            class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all border cursor-pointer disabled:opacity-50 col-span-2 sm:col-span-1"
                                            :class="selectedReparacion.estado_slug === 'cancelado'
                                                ? 'bg-rose-500 text-white border-rose-500 shadow-md ring-2 ring-rose-500/30'
                                                : 'bg-[#141c25] text-text-secondary border-border/40 hover:text-white hover:bg-surface-hover'"
                                        >
                                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                            <span>Cancelado</span>
                                        </button>
                                    </div>
                                </div>


                                {{-- SECCIÓN 2: DATOS DEL CLIENTE Y CONTACTO --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30 flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-border/20 pb-2.5">
                                        <span class="text-xs font-bold text-text-disabled uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-primary">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                            Cliente
                                        </span>

                                        <div class="flex items-center gap-2">
                                            {{-- Botón Editar Cliente --}}
                                            <button
                                                type="button"
                                                x-show="!editCard.cliente"
                                                @click="iniciarEdicion('cliente')"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary-light hover:text-white bg-primary/15 hover:bg-primary/30 px-2 py-0.5 rounded-lg border border-primary/30 transition-all cursor-pointer"
                                                title="Editar datos del cliente"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                <span>Editar</span>
                                            </button>

                                            {{-- Botón rápido WhatsApp --}}
                                            <a
                                                :href="mensajeWhatsapp(selectedReparacion)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 px-2.5 py-1 rounded-lg border border-emerald-500/30 transition-all"
                                            >
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/></svg>
                                                <span>WhatsApp</span>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Vista Normal --}}
                                    <div x-show="!editCard.cliente" class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/20 border border-primary/30 text-primary-light font-bold text-lg" x-text="selectedReparacion.cliente_iniciales"></div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-base font-bold text-white truncate" x-text="selectedReparacion.cliente_nombre"></span>
                                            <div class="flex items-center gap-3 text-xs text-text-secondary mt-0.5">
                                                <span class="font-medium" x-text="selectedReparacion.cliente_telefono"></span>
                                                <template x-if="selectedReparacion.cliente_email && selectedReparacion.cliente_email !== 'Sin correo'">
                                                    <span class="truncate text-text-disabled" x-text="'• ' + selectedReparacion.cliente_email"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modo Edición Formulario --}}
                                    <div x-show="editCard.cliente" x-cloak class="flex flex-col gap-2.5 pt-1">
                                        <div>
                                            <label class="text-[11px] font-semibold text-text-disabled block mb-1">Nombre Completo *</label>
                                            <input type="text" x-model="formCard.cliente.nombre" class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-medium text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all" placeholder="Ej: Juan Pérez">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">Teléfono *</label>
                                                <input type="text" x-model="formCard.cliente.telefono" class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-medium text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all" placeholder="Ej: 1123456789">
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">Email</label>
                                                <input type="email" x-model="formCard.cliente.email" class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-medium text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all" placeholder="cliente@correo.com">
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 pt-1.5 border-t border-border/20 mt-1">
                                            <button type="button" @click="cancelarEdicion('cliente')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-text-disabled hover:text-white hover:bg-surface transition-colors cursor-pointer">Cancelar</button>
                                            <button type="button" :disabled="isSavingCard.cliente" @click="guardarCliente()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-xs font-bold text-white transition-all shadow-sm cursor-pointer disabled:opacity-50">
                                                <svg x-show="isSavingCard.cliente" class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                <span x-text="isSavingCard.cliente ? 'Guardando...' : 'Guardar'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                {{-- SECCIÓN 3: FICHA TÉCNICA DEL DISPOSITIVO --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30 flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-border/20 pb-2.5">
                                        <span class="text-xs font-bold text-text-disabled uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-primary">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                            </svg>
                                            Dispositivo & Seguridad
                                        </span>

                                        <button
                                            type="button"
                                            x-show="!editCard.dispositivo"
                                            @click="iniciarEdicion('dispositivo')"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-primary-light hover:text-white bg-primary/15 hover:bg-primary/30 px-2 py-0.5 rounded-lg border border-primary/30 transition-all cursor-pointer"
                                            title="Editar dispositivo y clave"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Editar</span>
                                        </button>
                                    </div>

                                    {{-- Vista Normal --}}
                                    <div x-show="!editCard.dispositivo" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20">
                                            <span class="text-text-disabled font-semibold block">Modelo</span>
                                            <span class="text-white font-bold text-sm mt-0.5 block" x-text="selectedReparacion.dispositivo_marca_modelo"></span>
                                        </div>

                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20 flex flex-col justify-between gap-1.5">
                                            <span class="text-text-disabled font-semibold block">Clave de Acceso</span>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-amber-300 font-mono font-bold text-sm truncate" x-text="selectedReparacion.clave_de_acceso"></span>
                                                <template x-if="esPatron(selectedReparacion?.clave_de_acceso)">
                                                    <button
                                                        type="button"
                                                        @click="verPatronEnDetalle(selectedReparacion)"
                                                        class="px-2.5 py-1 rounded-lg bg-[#0081cc]/20 hover:bg-[#0081cc] text-[#33b4ff] hover:text-white border border-[#0081cc]/40 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer shrink-0 shadow-sm"
                                                        title="Ver patrón gráfico en 3x3"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        </svg>
                                                        <span>Ver Patrón</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20 sm:col-span-2 flex items-center justify-between">
                                            <div>
                                                <span class="text-text-disabled font-semibold block">IMEI / Número de Serie</span>
                                                <span class="text-white font-mono font-medium text-xs mt-0.5 block" x-text="selectedReparacion.imei_o_serie"></span>
                                            </div>
                                            <template x-if="selectedReparacion.imei_o_serie && selectedReparacion.imei_o_serie !== 'No especificado'">
                                                <button
                                                    type="button"
                                                    @click="copiarTexto(selectedReparacion.imei_o_serie, 'imei')"
                                                    class="text-xs text-primary-light hover:text-white bg-primary/20 hover:bg-primary px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                                                >
                                                    Copiar
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Modo Edición Formulario --}}
                                    <div x-show="editCard.dispositivo" x-cloak class="flex flex-col gap-2.5 pt-1">
                                        <div>
                                            <label class="text-[11px] font-semibold text-text-disabled block mb-1">Marca y Modelo *</label>
                                            <input type="text" x-model="formCard.dispositivo.marca_y_modelo" class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-medium text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all" placeholder="Ej: iPhone 13 Pro">
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">IMEI / Número de Serie</label>
                                                <input type="text" x-model="formCard.dispositivo.imei_o_serie" class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-medium text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all" placeholder="Ej: 354892019283741">
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">Tipo de Clave / Seguridad</label>
                                                <div class="flex items-center gap-1.5">
                                                    <div class="relative flex-1">
                                                        <select
                                                            x-model="tipoClaveSeleccionada"
                                                            @change="onTipoClaveChange($event.target.value)"
                                                            class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-semibold text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all appearance-none cursor-pointer pr-8"
                                                        >
                                                            <option value="Sin clave">Sin clave</option>
                                                            <option value="PIN / Contraseña">PIN / Contraseña</option>
                                                            <option value="Patrón de desbloqueo">Patrón de desbloqueo</option>
                                                            <option value="Huella / Face ID">Huella / Face ID</option>
                                                        </select>
                                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                                                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 20 20">
                                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    {{-- Botón Configurar / Dibujar Clave --}}
                                                    <template x-if="tipoClaveSeleccionada === 'Patrón de desbloqueo' || tipoClaveSeleccionada === 'PIN / Contraseña'">
                                                        <button
                                                            type="button"
                                                            @click="verOEditarClave()"
                                                            class="px-2.5 py-2 rounded-xl bg-[#0081cc]/20 hover:bg-[#0081cc] text-[#33b4ff] hover:text-white border border-[#0081cc]/30 transition-all flex items-center gap-1 text-xs font-bold cursor-pointer shrink-0 shadow-sm"
                                                            :title="tipoClaveSeleccionada === 'Patrón de desbloqueo' ? 'Dibujar / Cambiar Patrón' : 'Editar PIN'"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                            </svg>
                                                            <span x-text="tipoClaveSeleccionada === 'Patrón de desbloqueo' ? 'Dibujar' : 'Editar'"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Badge resumen de clave configurada en edición --}}
                                        <div
                                            x-show="formCard.dispositivo.clave_de_acceso && formCard.dispositivo.clave_de_acceso !== 'Sin clave'"
                                            class="flex items-center justify-between px-3 py-1.5 rounded-xl bg-[#141c25] border border-border/30 text-xs"
                                        >
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="text-text-disabled">Configurada:</span>
                                                <span class="text-amber-300 font-mono font-bold truncate" x-text="formCard.dispositivo.clave_de_acceso"></span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="formCard.dispositivo.clave_de_acceso = 'Sin clave'; claveAccesoValor = 'Sin clave'; tipoClaveConfirmada = 'Sin clave'; tipoClaveSeleccionada = 'Sin clave'"
                                                class="text-danger hover:text-red-400 font-bold ml-2 cursor-pointer text-[11px] hover:underline shrink-0"
                                                title="Quitar clave"
                                            >
                                                Quitar
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-end gap-2 pt-1.5 border-t border-border/20 mt-1">
                                            <button type="button" @click="cancelarEdicion('dispositivo')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-text-disabled hover:text-white hover:bg-surface transition-colors cursor-pointer">Cancelar</button>
                                            <button type="button" :disabled="isSavingCard.dispositivo" @click="guardarDispositivo()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-xs font-bold text-white transition-all shadow-sm cursor-pointer disabled:opacity-50">
                                                <svg x-show="isSavingCard.dispositivo" class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                <span x-text="isSavingCard.dispositivo ? 'Guardando...' : 'Guardar'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                {{-- SECCIÓN 4: DIAGNÓSTICO Y FALLA REPORTADA --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30 flex flex-col gap-2">
                                    <div class="flex items-center justify-between border-b border-border/20 pb-2.5">
                                        <span class="text-xs font-bold text-text-disabled uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-warning">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                            </svg>
                                            Falla Reportada
                                        </span>

                                        <button
                                            type="button"
                                            x-show="!editCard.falla"
                                            @click="iniciarEdicion('falla')"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-warning hover:text-white bg-warning/15 hover:bg-warning/30 px-2 py-0.5 rounded-lg border border-warning/30 transition-all cursor-pointer"
                                            title="Editar falla reportada"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Editar</span>
                                        </button>
                                    </div>

                                    {{-- Vista Normal --}}
                                    <div x-show="!editCard.falla" class="rounded-xl bg-[#141c25] p-3.5 border border-border/20 text-sm text-white font-medium whitespace-pre-wrap leading-relaxed" x-text="selectedReparacion.falla_reportada"></div>

                                    {{-- Modo Edición Formulario --}}
                                    <div x-show="editCard.falla" x-cloak class="flex flex-col gap-2.5 pt-1">
                                        <textarea
                                            x-model="formCard.falla.falla_reportada"
                                            rows="3"
                                            class="w-full rounded-xl bg-[#141c25] p-3 text-xs sm:text-sm font-medium text-white placeholder:text-text-disabled outline-none border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all resize-none"
                                            placeholder="Detalla el problema o motivo de ingreso reportado por el cliente..."
                                        ></textarea>
                                        <div class="flex items-center justify-end gap-2 pt-1 border-t border-border/20">
                                            <button type="button" @click="cancelarEdicion('falla')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-text-disabled hover:text-white hover:bg-surface transition-colors cursor-pointer">Cancelar</button>
                                            <button type="button" :disabled="isSavingCard.falla" @click="guardarFalla()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-xs font-bold text-white transition-all shadow-sm cursor-pointer disabled:opacity-50">
                                                <svg x-show="isSavingCard.falla" class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                <span x-text="isSavingCard.falla ? 'Guardando...' : 'Guardar'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                {{-- SECCIÓN 5: NOTAS INTERNAS DEL TÉCNICO (Edición en Vivo) --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30 flex flex-col gap-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-text-disabled uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-primary">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Notas Técnicas y Repuestos
                                        </span>

                                        <button
                                            type="button"
                                            :disabled="isSavingNotas"
                                            @click="guardarNotas()"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-primary hover:bg-primary-hover px-3 py-1 rounded-lg transition-all shadow-sm active:scale-95 cursor-pointer disabled:opacity-50"
                                        >
                                            <svg x-show="isSavingNotas" class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span x-text="isSavingNotas ? 'Guardando...' : 'Guardar Notas'"></span>
                                        </button>
                                    </div>

                                    <textarea
                                        x-model="notasEditadas"
                                        rows="3"
                                        placeholder="Escribe aquí repuestos cambiados, número de lote, pruebas realizadas, garantía interna..."
                                        class="w-full rounded-xl bg-[#141c25] p-3 text-xs sm:text-sm font-medium text-white placeholder:text-text-disabled outline-none border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all resize-none"
                                    ></textarea>
                                </div>


                                {{-- SECCIÓN 6: RESUMEN ECONÓMICO Y BALANCE --}}
                                <div class="rounded-2xl bg-[#1c2530] p-4.5 border border-border/30 flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-border/20 pb-2.5">
                                        <span class="text-xs font-bold text-text-disabled uppercase tracking-wider flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-emerald-400">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Estado Financiero
                                        </span>

                                        <button
                                            type="button"
                                            x-show="!editCard.financiero"
                                            @click="iniciarEdicion('financiero')"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-400 hover:text-white bg-emerald-500/15 hover:bg-emerald-500/30 px-2 py-0.5 rounded-lg border border-emerald-500/30 transition-all cursor-pointer"
                                            title="Editar presupuesto y seña"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            <span>Editar</span>
                                        </button>
                                    </div>

                                    {{-- Vista Normal --}}
                                    <div x-show="!editCard.financiero" class="grid grid-cols-3 gap-3 text-center">
                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20">
                                            <span class="text-[11px] font-semibold text-text-disabled uppercase block">Costo Total</span>
                                            <span class="text-base font-extrabold text-white mt-1 block" x-text="selectedReparacion.costo_estimado"></span>
                                        </div>

                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20">
                                            <span class="text-[11px] font-semibold text-text-disabled uppercase block">Seña</span>
                                            <span class="text-base font-extrabold text-emerald-400 mt-1 block" x-text="selectedReparacion.sena"></span>
                                        </div>

                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20">
                                            <span class="text-[11px] font-semibold text-text-disabled uppercase block">Saldo</span>
                                            <span
                                                class="text-base font-extrabold mt-1 block"
                                                :class="selectedReparacion.saldo_pendiente_num > 0 ? 'text-amber-400' : 'text-emerald-400'"
                                                x-text="selectedReparacion.saldo_pendiente"
                                            ></span>
                                        </div>
                                    </div>

                                    {{-- Modo Edición Formulario --}}
                                    <div x-show="editCard.financiero" x-cloak class="flex flex-col gap-3 pt-1">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">Costo Total Estimado ($)</label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="1"
                                                    x-model.number="formCard.financiero.costo_estimado"
                                                    class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-bold text-white border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all"
                                                    placeholder="0"
                                                >
                                            </div>
                                            <div>
                                                <label class="text-[11px] font-semibold text-text-disabled block mb-1">Seña Entregada ($)</label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="1"
                                                    x-model.number="formCard.financiero.sena"
                                                    class="w-full rounded-xl bg-[#141c25] px-3 py-2 text-xs font-bold text-emerald-400 border border-border/30 focus:border-primary focus:ring-1 focus:ring-primary/30 outline-none transition-all"
                                                    placeholder="0"
                                                >
                                            </div>
                                        </div>

                                        {{-- Previsualización del Saldo Resultante --}}
                                        <div class="rounded-xl bg-[#141c25] p-3 border border-border/20 flex items-center justify-between text-xs">
                                            <span class="text-text-disabled font-semibold">Saldo resultante:</span>
                                            <span class="font-extrabold text-sm text-amber-400" x-text="saldoPreviewForm()"></span>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-1.5 border-t border-border/20">
                                            <button type="button" @click="cancelarEdicion('financiero')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-text-disabled hover:text-white hover:bg-surface transition-colors cursor-pointer">Cancelar</button>
                                            <button type="button" :disabled="isSavingCard.financiero" @click="guardarFinanciero()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-hover text-xs font-bold text-white transition-all shadow-sm cursor-pointer disabled:opacity-50">
                                                <svg x-show="isSavingCard.financiero" class="animate-spin -ml-0.5 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                <span x-text="isSavingCard.financiero ? 'Guardando...' : 'Guardar'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                {{-- SECCIÓN 7: METADATOS TÉCNICOS --}}
                                <div class="rounded-2xl bg-[#141c25]/60 p-3.5 border border-border/20 flex items-center justify-between text-xs text-text-secondary">
                                    <div>
                                        <span class="text-text-disabled">Fecha de Ingreso:</span>
                                        <span class="font-medium text-white ml-1" x-text="selectedReparacion.fecha_hora"></span>
                                    </div>
                                    <div>
                                        <span class="text-text-disabled">Técnico:</span>
                                        <span class="font-medium text-primary-light ml-1" x-text="selectedReparacion.tecnico"></span>
                                    </div>
                                </div>

                            </div>

                            {{-- ── Pie Fijo de Acciones del Slide-Over ── --}}
                            <div class="border-t border-border/30 p-5 bg-[#141c25]/95 backdrop-blur-md flex flex-wrap items-center justify-between gap-3 shrink-0">
                                
                                <div class="flex items-center gap-2.5">
                                    {{-- Botón Imprimir Comprobante --}}
                                    <button
                                        type="button"
                                        @click="imprimirComprobante(selectedReparacion)"
                                        class="flex items-center gap-2 rounded-xl bg-surface-hover hover:bg-border/60 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-sm active:scale-95 cursor-pointer"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                        </svg>
                                        <span>Imprimir Ticket</span>
                                    </button>

                                    {{-- Botón Enviar WhatsApp --}}
                                    <a
                                        :href="mensajeWhatsapp(selectedReparacion)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center gap-2 rounded-xl bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white px-4 py-2.5 text-xs font-bold transition-all shadow-sm active:scale-95"
                                    >
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/></svg>
                                        <span>WhatsApp</span>
                                    </a>
                                </div>

                                <button
                                    type="button"
                                    @click="cerrarSlideOver()"
                                    class="h-10 rounded-xl bg-surface-hover hover:bg-border/60 px-5 text-xs font-bold text-white transition-colors cursor-pointer"
                                >
                                    Cerrar
                                </button>

                            </div>

                        </div>
                    </template>
                </div>
            </div>
        </div>


        {{-- =========================================================
            4. MODAL COMPROBANTE TÉRMICO / TICKET DE SERVICIO
        ========================================================== --}}
        <div
            x-show="openPrintModal"
            x-cloak
            @keydown.escape.window="openPrintModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openPrintModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openPrintModal = false"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box con Vista Previa de Ticket 80mm --}}
            <div
                x-show="openPrintModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 flex w-full max-w-md max-h-[calc(100dvh-2rem)] flex-col rounded-3xl bg-[#141c25] p-6 shadow-2xl border border-border/40"
            >
                <div class="flex shrink-0 items-center justify-between border-b border-border/30 pb-4 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/20 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-white">Comprobante de Servicio</h3>
                    </div>

                    <button
                        type="button"
                        @click="openPrintModal = false"
                        class="text-text-disabled hover:text-white transition-colors cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Preview visual del Ticket Térmico --}}
                <x-scrollbar
                    :hover="false"
                >
                    <div class="min-h-0 flex-1 overflow-y-auto pr-3">    
                        <div id="ticket-imprimible" class="rounded-2xl bg-white text-black p-5 font-mono text-xs shadow-inner select-none">
                            <div class="text-center border-b border-dashed border-gray-400 pb-3 mb-3">
                                <h4 class="font-extrabold text-sm uppercase tracking-wider text-black" x-text="selectedReparacion?.negocio_nombre || 'EnReparación'"></h4>
                                <p class="text-[10px] text-gray-700" x-text="selectedReparacion?.negocio_direccion"></p>
                                <p class="text-[10px] text-gray-700" x-text="'Tel: ' + (selectedReparacion?.negocio_telefono || 'Sin teléfono')"></p>
                                <div class="mt-2 text-center">
                                    <span class="text-xs font-bold uppercase tracking-widest bg-black text-white px-2 py-0.5 rounded">ORDEN DE SERVICIO</span>
                                    <div class="text-base font-extrabold mt-1 tracking-wider text-black" x-text="selectedReparacion?.codigo_seguimiento"></div>
                                </div>
                            </div>

                            <div class="space-y-1.5 border-b border-dashed border-gray-400 pb-3 mb-3 text-[11px]">
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">Fecha:</span>
                                    <span class="font-semibold text-black" x-text="selectedReparacion?.fecha_hora"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">Cliente:</span>
                                    <span class="font-semibold text-black truncate max-w-[170px]" x-text="selectedReparacion?.cliente_nombre"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">Teléfono:</span>
                                    <span class="font-semibold text-black" x-text="selectedReparacion?.cliente_telefono"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">Equipo:</span>
                                    <span class="font-semibold text-black" x-text="selectedReparacion?.dispositivo_marca_modelo"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">IMEI/Serie:</span>
                                    <span class="font-semibold text-black truncate max-w-[170px]" x-text="selectedReparacion?.imei_o_serie"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-600">Clave/PIN:</span>
                                    <span class="font-semibold text-black" x-text="selectedReparacion?.clave_de_acceso"></span>
                                </div>
                            </div>

                            <div class="border-b border-dashed border-gray-400 pb-3 mb-3 text-[11px]">
                                <span class="font-bold text-gray-600 block mb-0.5">Falla Reportada:</span>
                                <p class="text-black italic leading-tight" x-text="selectedReparacion?.falla_reportada"></p>
                            </div>

                            <div class="space-y-1 border-b border-dashed border-gray-400 pb-3 mb-3 text-xs">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Presupuesto Estimado:</span>
                                    <span class="font-bold text-black" x-text="selectedReparacion?.costo_estimado"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-700">Seña Entregada:</span>
                                    <span class="font-bold text-black" x-text="selectedReparacion?.sena"></span>
                                </div>
                                <div class="flex justify-between text-sm font-extrabold border-t border-gray-300 pt-1 mt-1">
                                    <span>Saldo a Cancelar:</span>
                                    <span x-text="selectedReparacion?.saldo_pendiente"></span>
                                </div>
                            </div>

                            <div class="text-[9px] text-gray-600 text-center leading-tight space-y-1">
                                <p>Conserve este talón para retirar su equipo.</p>
                                <p>Pasados los 60 días corridos sin retiro, el equipo pasa a desarme o reciclaje.</p>
                            </div>
                        </div>
                    </div> 
                </x-scrollbar>    

                {{-- Botones de Acción del Modal --}}
                <div class="flex shrink-0 items-center justify-end gap-3 mt-5 pt-1">
                    <button
                        type="button"
                        @click="openPrintModal = false"
                        class="px-4 py-2.5 rounded-xl bg-surface-hover hover:bg-border/60 text-xs font-bold text-white transition-colors cursor-pointer"
                    >
                        Cerrar
                    </button>

                    <button
                        type="button"
                        @click="ejecutarImpresion()"
                        class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover active:scale-95 text-xs font-bold text-white transition-all shadow-md flex items-center gap-2 cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                        </svg>
                        <span>Imprimir Ticket</span>
                    </button>
                </div>
            </div>
        </div>


        {{-- =========================================================
            5. MODAL NUEVA REPARACIÓN (Componente Reutilizable)
        ========================================================== --}}
        <x-modal-nueva-reparacion :action="route('reparaciones.store')" />

        {{-- =========================================
            SUB-MODAL CLAVE DE ACCESO (PIN & Patrón 3x3)
        ========================================== --}}
        @php
            $puntosCoords = [
                1 => ['top' => '16.66%', 'left' => '16.66%'],
                2 => ['top' => '16.66%', 'left' => '50%'],
                3 => ['top' => '16.66%', 'left' => '83.33%'],
                4 => ['top' => '50%', 'left' => '16.66%'],
                5 => ['top' => '50%', 'left' => '50%'],
                6 => ['top' => '50%', 'left' => '83.33%'],
                7 => ['top' => '83.33%', 'left' => '16.66%'],
                8 => ['top' => '83.33%', 'left' => '50%'],
                9 => ['top' => '83.33%', 'left' => '83.33%'],
            ];
        @endphp

        <div
            x-show="openClaveSubModal"
            x-cloak
            @keydown.escape.window="if (openClaveSubModal) cancelarSubModalClave()"
            class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openClaveSubModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="cancelarSubModalClave()"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openClaveSubModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="relative z-10 w-full max-w-md rounded-3xl bg-[#141c25] p-5 sm:p-6 shadow-2xl border border-border/40 my-auto text-white"
            >
                {{-- Encabezado Sub-modal --}}
                <div class="flex items-center justify-between border-b border-border/30 pb-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/20 text-primary shrink-0">
                            <template x-if="tipoClaveSeleccionada === 'PIN / Contraseña'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                </svg>
                            </template>
                            <template x-if="tipoClaveSeleccionada === 'Patrón de desbloqueo'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-white tracking-wide" x-text="tipoClaveSeleccionada === 'PIN / Contraseña' ? 'PIN o Contraseña' : 'Patrón de Desbloqueo'"></h3>
                            <p class="text-xs text-text-disabled" x-text="tipoClaveSeleccionada === 'PIN / Contraseña' ? 'Ingresa la clave para desbloquear el equipo' : 'Desliza o haz clic para conectar los puntos'"></p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="cancelarSubModalClave()"
                        class="text-text-disabled hover:text-white transition-colors p-1.5 rounded-xl hover:bg-surface-hover cursor-pointer"
                        title="Cerrar sin guardar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- CONTENIDO 1: PIN / CONTRASEÑA --}}
                <div x-show="tipoClaveSeleccionada === 'PIN / Contraseña'" class="py-2 flex flex-col gap-3">
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider">
                        Contraseña o PIN numérico:
                    </label>
                    <div class="relative">
                        <input
                            :type="mostrarPinTexto ? 'text' : 'password'"
                            x-model="tempPinValor"
                            @keydown.enter.prevent="guardarSubModalClave()"
                            placeholder="Ej: 1234, ABCD, Clave123..."
                            class="h-12 w-full rounded-2xl bg-[#1c2530] px-4 pr-12 text-sm sm:text-base font-semibold text-white placeholder:text-gray-400 outline-none border border-border/40 focus:border-[#0081cc] focus:ring-2 focus:ring-[#0081cc]/25 transition-all shadow-inner"
                        >
                        <button
                            type="button"
                            @click="mostrarPinTexto = !mostrarPinTexto"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-white transition-colors cursor-pointer"
                            title="Ver u ocultar texto"
                        >
                            <svg x-show="!mostrarPinTexto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="mostrarPinTexto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- CONTENIDO 2: PATRÓN 3x3 INTERACTIVO --}}
                <div x-show="tipoClaveSeleccionada === 'Patrón de desbloqueo'" class="py-1 flex flex-col items-center gap-3">
                    
                    {{-- Lienzo Interactivo 3x3 --}}
                    <div
                        class="w-64 h-64 sm:w-72 sm:h-72 relative bg-[#1c2530] rounded-3xl border border-border/40 p-2 shadow-inner select-none touch-none overflow-hidden cursor-crosshair"
                        @mousedown="iniciarTrazo($event, $el)"
                        @mousemove="moverTrazo($event, $el)"
                        @mouseup="finalizarTrazo()"
                        @mouseleave="finalizarTrazo()"
                        @touchstart.prevent="iniciarTrazo($event, $el)"
                        @touchmove.prevent="moverTrazo($event, $el)"
                        @touchend="finalizarTrazo()"
                    >
                        {{-- Capa SVG de Trazos --}}
                        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 300">
                            {{-- Línea principal del patrón conectado --}}
                            <polyline
                                :points="generarPuntosPolyline(tempPatronSecuencia)"
                                fill="none"
                                stroke="#0081cc"
                                stroke-width="6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                            {{-- Línea dinámica que sigue al cursor al dibujar --}}
                            <template x-if="isDrawingPattern && tempPatronSecuencia.length > 0">
                                <line
                                    :x1="obtenerPosPunto(tempPatronSecuencia[tempPatronSecuencia.length - 1]).x"
                                    :y1="obtenerPosPunto(tempPatronSecuencia[tempPatronSecuencia.length - 1]).y"
                                    :x2="patternCoords.x"
                                    :y2="patternCoords.y"
                                    stroke="#33b4ff"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    stroke-dasharray="6 4"
                                    opacity="0.85"
                                />
                            </template>
                        </svg>

                        {{-- Los 9 Puntos Interactivos --}}
                        @foreach($puntosCoords as $num => $pos)
                            <div
                                class="absolute -translate-x-1/2 -translate-y-1/2 flex items-center justify-center cursor-pointer transition-transform duration-150 active:scale-90"
                                style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }}; width: 50px; height: 50px;"
                                @click.stop="hacerClicEnPunto({{ $num }})"
                            >
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200"
                                    :class="tempPatronSecuencia.includes({{ $num }})
                                        ? 'bg-[#0081cc]/25 border-2 border-[#0081cc] shadow-[0_0_12px_rgba(0,129,204,0.7)] scale-110'
                                        : 'bg-[#273343]/60 border border-border/40 hover:border-primary/50 hover:bg-surface-hover'"
                                >
                                    <template x-if="tempPatronSecuencia.includes({{ $num }})">
                                        <span
                                            class="w-5 h-5 rounded-full bg-[#0081cc] text-white text-[11px] font-bold flex items-center justify-center shadow"
                                            x-text="tempPatronSecuencia.indexOf({{ $num }}) + 1"
                                        ></span>
                                    </template>
                                    <template x-if="!tempPatronSecuencia.includes({{ $num }})">
                                        <span class="w-3 h-3 rounded-full bg-gray-400/60"></span>
                                    </template>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Indicador y botón limpiar --}}
                    <div class="w-full flex items-center justify-between px-1 text-xs">
                        <span class="text-text-secondary truncate">
                            <template x-if="tempPatronSecuencia.length > 0">
                                <span>Secuencia: <strong class="text-primary-light font-mono" x-text="tempPatronSecuencia.join(' → ')"></strong></span>
                            </template>
                            <template x-if="tempPatronSecuencia.length === 0">
                                <span class="italic text-text-disabled">Une al menos 2 puntos</span>
                            </template>
                        </span>

                        <button
                            type="button"
                            @click="limpiarPatron()"
                            class="px-2.5 py-1 rounded-lg bg-surface-hover hover:bg-border/60 text-xs font-bold text-text-secondary hover:text-white transition-all cursor-pointer flex items-center gap-1 shrink-0"
                            title="Reiniciar trazo"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span>Limpiar</span>
                        </button>
                    </div>

                </div>

                {{-- Pie del Sub-modal: Botones de Acción --}}
                <div class="flex items-center justify-end gap-3 pt-4 mt-2 border-t border-border/30">
                    <button
                        type="button"
                        @click="cancelarSubModalClave()"
                        class="px-4 py-2.5 rounded-xl bg-surface-hover hover:bg-[#364252] text-xs font-bold text-white transition-all cursor-pointer"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        @click="guardarSubModalClave()"
                        class="px-5 py-2.5 rounded-xl bg-[#0081cc] hover:bg-[#33b4ff] active:scale-95 text-xs font-bold text-white transition-all shadow-md cursor-pointer flex items-center gap-1.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <span>Confirmar Clave</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================
            MODAL VISOR DE PATRÓN EN DETALLE (3x3 Gráfico)
        ========================================== --}}
        <div
            x-show="openPatternViewerModal"
            x-cloak
            @keydown.escape.window="openPatternViewerModal = false"
            class="fixed inset-0 z-[80] flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div
                x-show="openPatternViewerModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="openPatternViewerModal = false"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm"
            ></div>

            {{-- Modal Box --}}
            <div
                x-show="openPatternViewerModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-sm rounded-3xl bg-[#141c25] p-5 sm:p-6 shadow-2xl border border-border/40 my-auto text-white flex flex-col items-center"
            >
                <div class="w-full flex items-center justify-between pb-3 border-b border-border/30 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/20 text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Patrón de Desbloqueo</h3>
                            <p class="text-[11px] text-text-disabled truncate max-w-[200px]" x-text="visorPatronTitulo"></p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="openPatternViewerModal = false"
                        class="p-1.5 rounded-xl text-text-disabled hover:text-white hover:bg-surface-hover transition-colors cursor-pointer"
                        title="Cerrar visor"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Canvas 3x3 Visor Reconstruido --}}
                <div class="w-64 h-64 relative bg-[#1c2530] rounded-2xl border border-border/40 p-2 shadow-inner select-none overflow-hidden my-2">
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 300">
                        <polyline
                            :points="generarPuntosPolyline(visorPatronSecuencia)"
                            fill="none"
                            stroke="#0081cc"
                            stroke-width="6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    @foreach($puntosCoords as $num => $pos)
                        <div
                            class="absolute -translate-x-1/2 -translate-y-1/2 flex items-center justify-center"
                            style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }}; width: 46px; height: 46px;"
                        >
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center transition-all"
                                :class="visorPatronSecuencia.includes({{ $num }})
                                    ? 'bg-[#0081cc]/25 border-2 border-[#0081cc] shadow-[0_0_10px_rgba(0,129,204,0.7)] scale-105'
                                    : 'bg-[#273343]/60 border border-border/40'"
                            >
                                <template x-if="visorPatronSecuencia.includes({{ $num }})">
                                    <span
                                        class="w-5 h-5 rounded-full bg-[#0081cc] text-white text-[11px] font-bold flex items-center justify-center shadow"
                                        x-text="visorPatronSecuencia.indexOf({{ $num }}) + 1"
                                    ></span>
                                </template>
                                <template x-if="!visorPatronSecuencia.includes({{ $num }})">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gray-500/50"></span>
                                </template>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Secuencia en texto --}}
                <div class="mt-2 w-full px-3 py-2 rounded-xl bg-[#1c2530] border border-border/30 text-xs font-mono text-center text-primary-light">
                    <span class="text-text-secondary font-sans mr-1">Secuencia:</span>
                    <strong x-text="visorPatronSecuencia.join(' → ')"></strong>
                </div>

                {{-- Botón Cerrar --}}
                <div class="w-full flex justify-end mt-4 pt-3 border-t border-border/30">
                    <button
                        type="button"
                        @click="openPatternViewerModal = false"
                        class="w-full h-10 rounded-xl bg-surface-hover hover:bg-[#364252] text-xs font-bold text-white transition-all cursor-pointer"
                    >
                        Cerrar Visor
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================
            MODAL REUTILIZABLE DE CAMBIO DE ESTADO Y ENTREGA
        ========================================== --}}
        <x-modal-cambio-estado />
    </main>

    {{-- Estilos para impresión térmica limpia en @media print --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #ticket-imprimible, #ticket-imprimible * {
                visibility: visible;
            }
            #ticket-imprimible {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 80mm;
                margin: 0 auto;
                padding: 10px;
                background: white !important;
                color: black !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
@endsection