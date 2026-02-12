<x-filament::page>
    <x-filament::card>
        <div class="flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div class="p-4 bg-gray-100 rounded-full dark:bg-gray-800">
                <x-heroicon-o-qr-code class="w-16 h-16 text-primary-500" />
            </div>
            
            <h2 class="text-2xl font-bold tracking-tight">مسح الباركود</h2>
            <p class="text-gray-500">قم بمسح الباركود باستخدام الجهاز أو أدخله يدوياً</p>

            <div class="w-full max-w-md">
                <form wire:submit.prevent="scan">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="text"
                            wire:model="barcode"
                            placeholder="Scan barcode here..."
                            class="text-center text-lg h-12"
                            autofocus
                        />
                    </x-filament::input.wrapper>

                    <x-filament::button type="submit" class="w-full mt-4" size="lg">
                        بحث / Scan
                    </x-filament::button>
                </form>
            </div>
        </div>
    </x-filament::card>

    <div class="mt-8">
        {{ $this->table }}
    </div>
</x-filament::page>
