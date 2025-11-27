<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nuevo Módulo para: ') . $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('seller.modules.store', $course) }}">
                    @csrf

                    <div>
                        <x-input-label for="title" :value="__('Título de la Lección/Módulo')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="content_url" :value="__('URL del Contenido (Video/Documento)')" />
                        <x-text-input id="content_url" class="block mt-1 w-full" type="url" name="content_url" :value="old('content_url')" required placeholder="https://youtube.com/link-a-video" />
                        <x-input-error :messages="$errors->get('content_url')" class="mt-2" />
                    </div>
                    
                    <div class="mt-4">
                        <x-input-label for="sequence_order" :value="__('Orden de Secuencia')" />
                        <x-text-input id="sequence_order" class="block mt-1 w-full" type="number" name="sequence_order" :value="old('sequence_order', $course->modules->max('sequence_order') + 1)" required min="1" />
                        <x-input-error :messages="$errors->get('sequence_order')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('seller.courses.edit', $course) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Módulo') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>