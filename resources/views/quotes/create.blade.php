{{-- resources/views/quotes/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 mb-0">Crea preventivo</h2>
                <div class="text-muted small">Configura opzioni e genera il preventivo</div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary btn-sm">
                    ← Templates
                </a>
                <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary btn-sm">
                    Preventivi
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $fmt = fn(int $cents) => '€ ' . number_format($cents / 100, 2, ',', '.');
    @endphp

    <div class="py-4">
        <div class="container">

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Se non ho template, chiedo di sceglierlo --}}
            @if (!isset($template) || !$template)
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <p class="text-muted mb-3">
                            Prima scegli un template (Bronze/Silver/Gold), poi configuri le opzioni.
                        </p>

                        <a href="{{ route('templates.index') }}" class="btn btn-primary">
                            Scegli template
                        </a>
                    </div>
                </div>
            @else
                @php
                    $base = (int) $template->base_price;

                    // opzioni già filtrate dal controller
                    $boolOptions = $options->where('type', 'bool');
                    $numberOptions = $options->where('type', 'number');
                @endphp

                <form method="POST" action="{{ route('quotes.store') }}">
                    @csrf

                    {{-- IMPORTANT: serve al controller store() --}}
                    <input type="hidden" name="template_id" value="{{ (int) $template->id }}">

                    {{-- total_gross in EURO (es 1875.00) --}}
                    <input type="hidden" name="total_gross" id="js-total-gross" value="{{ number_format($base / 100, 2, '.', '') }}">

                    {{-- Metadati template dentro config_json --}}
                    <input type="hidden" name="config_json[template_id]" value="{{ (int) $template->id }}">
                    <input type="hidden" name="config_json[template_slug]" value="{{ $template->slug }}">
                    <input type="hidden" name="config_json[template_name]" value="{{ $template->name }}">
                    <input type="hidden" name="config_json[pricing][base_cents]" value="{{ (int) $base }}">
                    <input type="hidden" name="config_json[pricing][total_cents]" id="js-total-cents" value="{{ (int) $base }}">

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-md-5">

                            {{-- TEMPLATE HEADER --}}
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                <div>
                                    <div class="text-muted small">Template selezionato</div>
                                    <div class="h5 mb-0">{{ $template->name }}</div>
                                    @if($template->description)
                                        <div class="text-muted small mt-2">{!! nl2br(e($template->description)) !!}</div>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <div class="text-muted small">Base</div>
                                    <div class="fw-semibold">{{ $fmt($base) }}</div>
                                </div>
                            </div>

                            <div class="row g-4">
                                {{-- Colonna sx --}}
                                <div class="col-12 col-lg-6">

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">Titolo (opzionale)</label>
                                        <input
                                            type="text"
                                            name="title"
                                            value="{{ old('title') }}"
                                            class="form-control"
                                            placeholder="Es. Sito vetrina 5 pagine"
                                        >
                                    </div>

                                    <div class="mb-3">
                                        <div class="fw-semibold mb-2">Opzioni</div>

                                        {{-- BOOL --}}
                                        @foreach($boolOptions as $opt)
                                            <div class="card border mb-2">
                                                <div class="card-body py-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $opt->label }}</div>
                                                            <div class="text-muted small">+ {{ $fmt((int)$opt->price_delta) }}</div>
                                                        </div>

                                                        <div class="form-check form-switch m-0">
                                                            <input
                                                                class="form-check-input js-opt"
                                                                type="checkbox"
                                                                role="switch"
                                                                name="options[{{ $opt->key }}]"
                                                                value="1"
                                                                data-key="{{ e($opt->key) }}"
                                                                data-label="{{ e($opt->label) }}"
                                                                data-type="bool"
                                                                data-price="{{ (int)$opt->price_delta }}"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- salviamo anche in config_json (server-side leggerà questi campi) --}}
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][type]" value="bool">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][label]" value="{{ $opt->label }}">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][price_delta_cents]" value="{{ (int)$opt->price_delta }}">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][value]" value="0" class="js-opt-hidden" data-key="{{ e($opt->key) }}">
                                        @endforeach

                                        {{-- NUMBER --}}
                                        @foreach($numberOptions as $opt)
                                            <div class="card border mb-2">
                                                <div class="card-body py-3">
                                                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                        <div>
                                                            <div class="fw-medium">{{ $opt->label }}</div>
                                                            <div class="text-muted small">+ {{ $fmt((int)$opt->price_delta) }} / unità</div>
                                                        </div>
                                                    </div>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="1"
                                                        name="options[{{ $opt->key }}]"
                                                        value="0"
                                                        class="form-control js-opt"
                                                        data-key="{{ e($opt->key) }}"
                                                        data-label="{{ e($opt->label) }}"
                                                        data-type="number"
                                                        data-price="{{ (int)$opt->price_delta }}"
                                                    >
                                                </div>
                                            </div>

                                            {{-- salviamo anche in config_json --}}
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][type]" value="number">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][label]" value="{{ $opt->label }}">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][price_delta_cents]" value="{{ (int)$opt->price_delta }}">
                                            <input type="hidden" name="config_json[options][{{ $opt->key }}][value]" value="0" class="js-opt-hidden" data-key="{{ e($opt->key) }}">
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Colonna dx --}}
                                <div class="col-12 col-lg-6">
                                    <div class="fw-semibold mb-2">Riepilogo costi</div>

                                    <div class="card border">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center justify-content-between small">
                                                <span>Base</span>
                                                <span class="fw-semibold" id="js-base">{{ $fmt($base) }}</span>
                                            </div>

                                            <div class="my-3" id="js-breakdown"></div>

                                            <hr>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-semibold">Totale compenso (lordo)</span>
                                                <span class="fw-bold fs-5" id="js-total">{{ $fmt($base) }}</span>
                                            </div>

                                            <div class="text-muted small mt-2">
                                                Include base + opzioni selezionate. (Ritenuta + bollo vengono calcolati nel dettaglio preventivo.)
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between gap-2 mt-4">
                                        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
                                            Cambia template
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            Crea preventivo
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

                <script>
                    (function () {
                        const base = {{ (int)$base }};

                        const fmt = (cents) => {
                            const euro = (cents / 100).toFixed(2).replace('.', ',');
                            const parts = euro.split(',');
                            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return '€ ' + parts.join(',');
                        };

                        const esc = (s) => String(s ?? '').replace(/[&<>"]+/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c] || c));

                        const opts = Array.from(document.querySelectorAll('.js-opt'));
                        const hiddenByKey = {};
                        Array.from(document.querySelectorAll('.js-opt-hidden')).forEach(h => {
                            hiddenByKey[h.dataset.key] = h;
                        });

                        const breakdownEl = document.getElementById('js-breakdown');
                        const totalEl = document.getElementById('js-total');

                        const totalGrossInput = document.getElementById('js-total-gross'); // euro
                        const totalCentsInput = document.getElementById('js-total-cents'); // cents

                        function recalc() {
                            let total = base;
                            const lines = [];

                            opts.forEach(el => {
                                const type = el.dataset.type;
                                const key = el.dataset.key;
                                const label = el.dataset.label || 'Opzione';
                                const price = parseInt(el.dataset.price || '0', 10);

                                if (!key) return;

                                if (type === 'bool') {
                                    const v = el.checked ? 1 : 0;
                                    if (hiddenByKey[key]) hiddenByKey[key].value = String(v);

                                    if (v === 1) {
                                        total += price;
                                        lines.push({ label, amount: price });
                                    }
                                } else if (type === 'number') {
                                    const qty = Math.max(0, parseInt(el.value || '0', 10));
                                    if (hiddenByKey[key]) hiddenByKey[key].value = String(qty);

                                    if (qty > 0) {
                                        const amount = qty * price;
                                        total += amount;
                                        lines.push({ label: label + ' x ' + qty, amount });
                                    }
                                }
                            });

                            breakdownEl.innerHTML = lines.length
                                ? lines
                                    .map(l => `
                                        <div class="d-flex align-items-center justify-content-between small text-muted mb-2">
                                            <span>${esc(l.label)}</span>
                                            <span>+ ${fmt(l.amount)}</span>
                                        </div>
                                    `)
                                    .join('')
                                : '<div class="text-muted small">Nessuna opzione selezionata.</div>';

                            totalEl.textContent = fmt(total);

                            // controller vuole EURO (es: 1875.00)
                            totalGrossInput.value = (total / 100).toFixed(2);

                            // per debug/storico in config_json
                            if (totalCentsInput) totalCentsInput.value = String(total);
                        }

                        opts.forEach(el => el.addEventListener('change', recalc));
                        opts.forEach(el => el.addEventListener('input', recalc));
                        recalc();
                    })();
                </script>
            @endif

        </div>
    </div>
</x-app-layout>