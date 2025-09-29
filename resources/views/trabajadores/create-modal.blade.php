<div x-show="showCreateModal"
             class="modal-backdrop"
             :class="{ 'active': showCreateModal }"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="closeCreateModal">
            <div class="modal-container">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Nuevo Trabajador</h3>
                        <button @click="closeCreateModal" class="modal-close-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('trabajadores.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="modal-body">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Cédula*</label>
                                    <input type="text" name="cedula" class="form-input" required maxlength="8">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Primer Nombre*</label>
                                    <input type="text" name="nombre1" class="form-input" required maxlength="15">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Segundo Nombre</label>
                                    <input type="text" name="nombre2" class="form-input" maxlength="15">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email*</label>
                                    <input type="email" name="e_mail" class="form-input" required maxlength="50">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Primer Apellido*</label>
                                    <input type="text" name="apellido1" class="form-input" required maxlength="20">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Segundo Apellido</label>
                                    <input type="text" name="apellido2" class="form-input" maxlength="20">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-input" maxlength="11">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Rol*</label>
                                    <input type="text" name="rol" class="form-input" required maxlength="21">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Estado Registro*</label>
                                    <input type="text" name="e_registro" class="form-input" required maxlength="8">
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-0">
                            <button type="submit" class="modal-submit-btn">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>