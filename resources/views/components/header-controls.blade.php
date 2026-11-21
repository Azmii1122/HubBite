@props(['store', 'greeting' => 'Good morning', 'subtitle' => "Here's what's happening at your shop today."])

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6" x-data="{
    crowdOpen: false,
    currentCrowd: '{{ $store->crowd_status }}',
    storeOpen: {{ $store->store_status === 'open' ? 'true' : 'false' }},
    async setCrowd(val) {
        this.currentCrowd = val;
        this.crowdOpen = false;
        try {
            const res = await fetch('{{ route('store.updateCrowd') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ crowd_status: val })
            });
            const data = await res.json();
            if(data.success) {
                Alpine.store('toast').success(data.message);
            }
        } catch(e) {
            Alpine.store('toast').error('Gagal memperbarui crowd status');
        }
    },
    async toggleStore() {
        this.storeOpen = !this.storeOpen;
        const newStatus = this.storeOpen ? 'open' : 'closed';
        try {
            const res = await fetch('{{ route('store.updateStatus') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ store_status: newStatus })
            });
            const data = await res.json();
            if(data.success) {
                Alpine.store('toast').success(data.message);
            }
        } catch(e) {
            Alpine.store('toast').error('Gagal memperbarui status toko');
        }
    }
}">
    {{-- Left Greeting --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-text-primary">
            {{ $greeting }}, <span class="text-hubbite">{{ $store->store_name }}</span>
        </h1>
        <p class="text-text-secondary text-sm mt-1">{{ $subtitle }}</p>
    </div>

    {{-- Right Controls: Crowd Status + Store Status --}}
    <div class="flex items-center gap-4 bg-white p-2.5 rounded-2xl border border-border shadow-xs">
        {{-- Crowd Status Dropdown / Popover --}}
        <div class="relative">
            <div class="flex items-center gap-2 cursor-pointer select-none px-2 py-1 rounded-xl hover:bg-gray-50 transition-colors"
                 @click="crowdOpen = !crowdOpen">
                <span class="text-xs font-semibold text-text-secondary">Crowd Status</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                      :class="{
                          'bg-emerald-100 text-emerald-800': currentCrowd === 'low',
                          'bg-amber-100 text-amber-800': currentCrowd === 'medium',
                          'bg-rose-100 text-rose-800': currentCrowd === 'high'
                      }">
                    <span class="w-2 h-2 rounded-full"
                          :class="{
                              'bg-emerald-500': currentCrowd === 'low',
                              'bg-amber-500': currentCrowd === 'medium',
                              'bg-rose-500': currentCrowd === 'high'
                          }"></span>
                    <span x-text="currentCrowd.toUpperCase()"></span>
                </span>
                <svg class="w-4 h-4 text-text-secondary transition-transform" :class="crowdOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

            {{-- Dropdown Menu --}}
            <div x-show="crowdOpen" @click.outside="crowdOpen = false" x-transition.scale.95 x-cloak
                 class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-border py-1.5 z-30">
                <button type="button" @click="setCrowd('low')"
                        class="w-full text-left px-3 py-2 text-xs font-semibold flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Low (Sepi)
                    </span>
                    <span x-show="currentCrowd === 'low'" class="text-hubbite">✓</span>
                </button>
                <button type="button" @click="setCrowd('medium')"
                        class="w-full text-left px-3 py-2 text-xs font-semibold flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Medium (Sedang)
                    </span>
                    <span x-show="currentCrowd === 'medium'" class="text-hubbite">✓</span>
                </button>
                <button type="button" @click="setCrowd('high')"
                        class="w-full text-left px-3 py-2 text-xs font-semibold flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        High (Ramai)
                    </span>
                    <span x-show="currentCrowd === 'high'" class="text-hubbite">✓</span>
                </button>
            </div>
        </div>

        <div class="h-6 w-px bg-border"></div>

        {{-- Store Status Toggle (Open / Closed) --}}
        <div class="flex items-center gap-2.5">
            <span class="text-xs font-semibold text-text-secondary">Store Status</span>
            <div class="flex items-center gap-2">
                <button type="button" @click="toggleStore()"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="storeOpen ? 'bg-emerald-500' : 'bg-gray-300'">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                          :class="storeOpen ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
                <span class="text-xs font-bold w-12" :class="storeOpen ? 'text-emerald-600' : 'text-gray-500'" x-text="storeOpen ? 'Open' : 'Closed'"></span>
            </div>
        </div>
    </div>
</div>
