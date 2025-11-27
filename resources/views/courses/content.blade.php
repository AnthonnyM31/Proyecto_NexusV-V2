<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contenido del Curso: ') . $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                
                {{-- Mensajes de éxito/error (Mostrados después de marcar una lección) --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                @endif

                {{-- INICIO: BOTÓN DE CERTIFICADO (Se muestra solo al 100%) --}}
                {{-- CORRECCIÓN: Usamos '==' en lugar de '===' para manejar float/int --}}
                @if ($progressPercent == 100)
                    <div class="bg-indigo-100 border-l-4 border-indigo-500 text-indigo-700 p-4 mb-8 flex justify-between items-center" role="alert">
                        <div>
                            <p class="font-bold text-lg">{{ __('¡Felicidades! Curso Completado.') }}</p>
                            <p>{{ __('Has completado todas las lecciones. Obtén tu certificado ahora mismo.') }}</p>
                        </div>
                        
                        <a href="{{ route('courses.certify', $course) }}">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Generar Certificado') }}
                            </x-primary-button>
                        </a>
                    </div>
                @endif
                {{-- FIN BOTÓN DE CERTIFICADO --}}

                <h3 class="text-2xl font-bold mb-4">Progreso General</h3>
                <div class="mb-8">
                    <p class="text-3xl font-extrabold text-indigo-600 mb-2">{{ $progressPercent }}% Completado</p>
                    <p class="text-sm text-gray-500">{{ $completedModules }} de {{ $totalModules }} lecciones vistas.</p>
                </div>

                <h3 class="text-2xl font-bold mb-4 border-b pb-2">Temario del Curso</h3>
                
                <div class="space-y-4">
                    @forelse ($modules as $module)
                        @php
                            $isCompleted = $module->progress->isNotEmpty() && $module->progress->first()->is_completed;
                        @endphp
                        <div class="p-4 border rounded-lg flex items-center justify-between {{ $isCompleted ? 'bg-green-50' : 'bg-gray-50' }}">
                            
                            <div class="flex items-center space-x-3">
                                <span>{{ $module->sequence_order }}.</span>
                                <a href="{{ $module->content_url }}" target="_blank" class="font-medium {{ $isCompleted ? 'text-green-700' : 'text-gray-900' }} hover:underline">
                                    {{ $module->title }}
                                </a>
                                @if ($isCompleted)
                                    <span class="text-sm text-green-600">✅ Visto</span>
                                @endif
                            </div>

                            {{-- Botón de Marcar como Completado --}}
                            @if (!$isCompleted)
                                <form method="POST" action="{{ route('progress.store', $module) }}">
                                    @csrf
                                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-xs py-1 px-3">
                                        {{ __('Marcar como Completado') }}
                                    </x-primary-button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">Este curso aún no tiene módulos cargados.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>