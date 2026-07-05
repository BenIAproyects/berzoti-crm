<div>
    <form wire:submit="guardar" class="space-y-6">

        {{-- Datos de la empresa --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Datos de la empresa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón social <span class="text-red-500">*</span></label>
                    <input wire:model="razon_social" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('razon_social') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre comercial</label>
                    <input wire:model="nombre_comercial" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                    <input wire:model="ruc" type="text" inputmode="numeric" maxlength="11"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('ruc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de cliente <span class="text-red-500">*</span></label>
                    <select wire:model="tipo_cliente"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                    <input wire:model="sector" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Origen</label>
                    <input wire:model="origen" type="text" placeholder="Referido, feria, web..."
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fuente</label>
                    <select wire:model="fuente"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Sin definir —</option>
                        @foreach($fuentes as $f)
                            <option value="{{ $f->value }}">{{ $f->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zona</label>
                    <input wire:model="zona" type="text" placeholder="Ej: Lima Norte, Arequipa..."
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Segmento</label>
                    <select wire:model="segmento"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Sin definir —</option>
                        @foreach($segmentos as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Contacto --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Contacto principal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input wire:model="contacto_principal" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <input wire:model="cargo_contacto" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input wire:model="telefono" type="text" inputmode="numeric" maxlength="9"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input wire:model="whatsapp" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo principal</label>
                    <input wire:model="correo" type="email"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('correo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo secundario</label>
                    <input wire:model="correo_secundario" type="email"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('correo_secundario') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Ubicación --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Ubicación</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                    <input wire:model="pais" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                    <input wire:model="departamento" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provincia</label>
                    <input wire:model="provincia" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distrito</label>
                    <input wire:model="distrito" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input wire:model="direccion" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                    <input wire:model="referencia" type="text"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

            </div>
        </div>

        {{-- Comercial --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Datos comerciales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado comercial</label>
                    <select wire:model="estado_comercial"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($estados as $estado)
                            <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                    <select wire:model="prioridad"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>

                @can('clientes.asignar')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendedor asignado</label>
                    <select wire:model="vendedor_asignado_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Sin asignar</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endcan

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Próximo contacto</label>
                    <input wire:model="fecha_proximo_contacto" type="date"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad habitual de compra <span class="text-gray-400 font-normal">(unid.)</span></label>
                    <input wire:model="cantidad_compra" type="number" min="0" step="1"
                           placeholder="Ej: 3000"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('cantidad_compra') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio año anterior <span class="text-gray-400 font-normal">(S/. por unid.)</span></label>
                    <input wire:model="precio_ano_anterior" type="number" min="0" step="0.01"
                           placeholder="Ej: 9.50"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('precio_ano_anterior') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes de contacto</label>
                    <select wire:model="mes_contacto"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Sin definir —</option>
                        <option value="enero">Enero</option>
                        <option value="febrero">Febrero</option>
                        <option value="marzo">Marzo</option>
                        <option value="abril">Abril</option>
                        <option value="mayo">Mayo</option>
                        <option value="junio">Junio</option>
                        <option value="julio">Julio</option>
                        <option value="agosto">Agosto</option>
                        <option value="septiembre">Septiembre</option>
                        <option value="octubre">Octubre</option>
                        <option value="noviembre">Noviembre</option>
                        <option value="diciembre">Diciembre</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="observaciones" rows="3"
                              class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('clientes.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span wire:loading.remove>{{ $modoEdicion ? 'Guardar cambios' : 'Crear cliente' }}</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>

    </form>
</div>
