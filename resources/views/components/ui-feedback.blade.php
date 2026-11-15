<div
    x-data="uiFeedback"
    @app-confirm.window="requestConfirmation($event.detail)"
    @app-toast.window="showToast($event.detail)"
    @app-copy.window="copyText($event.detail)"
>
    <div
        x-show="confirmation"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[100] flex items-center justify-center bg-[#171824]/65 p-4 backdrop-blur-sm"
        @click.self="cancelConfirmation()"
        @keydown.escape.window="cancelConfirmation()"
        @keydown.tab.prevent="cycleConfirmationFocus($event)"
    >
        <section
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="confirmation-title"
            aria-describedby="confirmation-message"
            class="w-full max-w-md overflow-hidden rounded-2xl border border-[#eaeaef] bg-white shadow-2xl"
        >
            <div class="flex gap-4 p-5 sm:p-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                    <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <h2 id="confirmation-title" class="text-base font-semibold text-[#32324d]" x-text="confirmation?.title"></h2>
                    <p id="confirmation-message" class="mt-2 text-sm leading-6 text-[#666687]" x-text="confirmation?.message"></p>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-[#eaeaef] bg-[#fafafc] p-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    x-ref="confirmCancel"
                    @click="cancelConfirmation()"
                    class="rounded-lg border border-[#dcdce4] bg-white px-4 py-2.5 text-sm font-medium text-[#49445f] hover:bg-[#f6f6f9] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4945ff] focus-visible:ring-offset-2"
                >
                    Batal
                </button>
                <button
                    type="button"
                    x-ref="confirmAction"
                    @click="confirmAction()"
                    class="rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:ring-offset-2"
                    x-text="confirmation?.action"
                ></button>
            </div>
        </section>
    </div>

    <div class="fixed bottom-4 right-4 z-[110] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2" aria-live="polite" aria-atomic="true">
        <div
            x-show="toast"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="translate-y-1 opacity-0"
            :role="toast?.type === 'error' ? 'alert' : 'status'"
            class="flex items-start gap-3 rounded-xl border bg-white p-4 shadow-xl"
            :class="toast?.type === 'error' ? 'border-rose-200 text-rose-800' : (toast?.type === 'success' ? 'border-emerald-200 text-emerald-800' : 'border-[#dcdce4] text-[#32324d]')"
        >
            <i class="mt-0.5" aria-hidden="true" :class="toast?.type === 'error' ? 'fas fa-circle-exclamation text-rose-600' : (toast?.type === 'success' ? 'fas fa-circle-check text-emerald-600' : 'fas fa-circle-info text-[#4945ff]')"></i>
            <p class="min-w-0 flex-1 text-sm leading-5" x-text="toast?.message"></p>
            <button type="button" @click="dismissToast()" class="shrink-0 rounded p-1 text-[#8e8ea9] hover:text-[#32324d] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4945ff]" aria-label="Tutup notifikasi">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
