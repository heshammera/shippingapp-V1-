<div x-data="{
    signaturePad: null,
    data: @entangle($getStatePath()),
    init() {
        this.signaturePad = new SignaturePad(this.$refs.canvas);
        this.signaturePad.addEventListener('endStroke', () => {
           this.data = this.signaturePad.toDataURL();
        });
    },
    clear() {
        this.signaturePad.clear();
        this.data = null;
    }
}" class="w-full">
    <label class="fi-fo-field-label mb-2 block text-sm font-medium text-gray-950 dark:text-white">توقيع المستلم</label>
    <div class="relative bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg overflow-hidden">
        <canvas x-ref="canvas" class="w-full h-40 touch-none"></canvas>
        <button type="button" @click="clear" class="absolute top-2 right-2 p-1 bg-white dark:bg-gray-800 rounded shadow-sm text-xs text-gray-500 hover:text-danger-600">
            مسح
        </button>
    </div>
    <input type="hidden" x-model="data">
    
    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        @endpush
    @endonce
</div>
