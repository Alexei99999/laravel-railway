<div x-show="Alpine.store('app').showEditModal"
     class="modal-backdrop"
     :class="{ 'active': Alpine.store('app').showEditModal }"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="Alpine.store('app').closeEditModal">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Editar Trabajador</h3>
                <button @click="Alpine.store('app').closeEditModal" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <template x-if="Alpine.store('app').currentTrabajador">
                <form :action="'/trabajadores/' + Alpine.store('app').currentTrabajador.id" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label class="form-label">Cédula*</label>
                                <input type="text" name="cedula" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.cedula" required maxlength="8">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Primer Nombre*</label>
                                <input type="text" name="nombre1" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.nombre1" required maxlength="15">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" name="nombre2" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.nombre2" maxlength="15">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email*</label>
                                <input type="email" name="e_mail" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.e_mail" required maxlength="50">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Primer Apellido*</label>
                                <input type="text" name="apellido1" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.apellido1" required maxlength="20">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" name="apellido2" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.apellido2" maxlength="20">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.telefono" maxlength="11">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Rol*</label>
                                <input type="text" name="rol" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.rol" required maxlength="21">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Estado Registro*</label>
                                <input type="text" name="e_registro" class="form-input"
                                       x-model="Alpine.store('app').currentTrabajador.e_registro" required maxlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <button type="submit" class="btn-primary w-full">
                            Actualizar
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>