@php
    $editing = $car->exists;
    $action = $editing ? route('admin.cars.update', $car) : route('admin.cars.store');
    $selectedFeatures = old('features', $car->features->pluck('id')->all());
    $selectedFeatureIds = array_map('intval', (array) $selectedFeatures);

    $priceValue = (string) old('price', $car->price ?? '');
    $priceDigits = preg_replace('/\D+/', '', $priceValue);
    $priceDisplay = $priceDigits !== '' ? number_format((int) $priceDigits, 0, ',', '.') : '';

    $primaryPhoto = $car->photos->sortByDesc('created_at')->first() ?? $car->photos->firstWhere('is_primary', true);
    $photoCount = $car->photos->count();
@endphp

@push('scripts')
    <script>
        // Format harga Rupiah saat mengetik dan kirim angka murni.
        const priceInput = document.getElementById('price');

        if (priceInput) {
            const formatPrice = (value) => {
                const digits = value.replace(/\D+/g, '').replace(/^0+(?=\d)/, '');

                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            priceInput.addEventListener('input', () => {
                priceInput.value = formatPrice(priceInput.value);
            });

            priceInput.closest('form')?.addEventListener('submit', () => {
                priceInput.value = priceInput.value.replace(/\D+/g, '');
            });
        }

        // Zona unggah foto: klik, seret, dan hitung berkas terpilih.
        document.querySelectorAll('[data-photo-input]').forEach((input) => {
            const counter = document.getElementById(input.dataset.countTarget);
            const zone = input.closest('[data-photo-dropzone]');

            const updateCounter = () => {
                if (!counter) {
                    return;
                }

                const total = input.files ? input.files.length : 0;
                counter.textContent = total > 0
                    ? `${total} foto dipilih`
                    : 'Belum ada foto dipilih';
            };

            input.addEventListener('change', updateCounter);
            updateCounter();

            if (!zone) {
                return;
            }

            ['dragenter', 'dragover'].forEach((eventName) => {
                zone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    zone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                zone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    zone.classList.remove('is-dragover');
                });
            });

            zone.addEventListener('drop', (event) => {
                const files = event.dataTransfer?.files;

                if (!files || files.length === 0) {
                    return;
                }

                const transfer = new DataTransfer();

                Array.from(files).forEach((file) => {
                    if (file.type.startsWith('image/')) {
                        transfer.items.add(file);
                    }
                });

                if (transfer.files.length > 0) {
                    input.files = transfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        // Pratinjau foto pada form tambah mobil.
        document.getElementById('photos')?.addEventListener('change', function (event) {
            const preview = document.getElementById('photo-preview');

            if (!preview) {
                return;
            }

            preview.innerHTML = '';

            Array.from(event.target.files).forEach((file) => {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'photo-preview-item';

                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = file.name;

                wrapper.appendChild(image);
                preview.appendChild(wrapper);
            });
        });

        // Hitung fitur terpilih dan aksi pilih/kosongkan.
        const featureGrid = document.getElementById('feature-grid');
        const featureCounter = document.getElementById('feature-selected-count');

        if (featureGrid) {
            const featureInputs = () => Array.from(featureGrid.querySelectorAll('input[name="features[]"]'));

            const updateFeatureCounter = () => {
                if (!featureCounter) {
                    return;
                }

                const total = featureInputs().filter((input) => input.checked).length;
                featureCounter.textContent = `${total} dipilih`;
            };

            featureGrid.addEventListener('change', updateFeatureCounter);

            document.getElementById('features-select-all')?.addEventListener('click', () => {
                featureInputs().forEach((input) => {
                    input.checked = true;
                });
                updateFeatureCounter();
            });

            document.getElementById('features-clear-all')?.addEventListener('click', () => {
                featureInputs().forEach((input) => {
                    input.checked = false;
                });
                updateFeatureCounter();
            });

            updateFeatureCounter();
        }

        // Urutkan ulang foto dengan seret dan lepas.
        const photoList = document.getElementById('photo-list');
        const photoOrderForm = document.getElementById('photo-order-form');
        let draggedPhoto = null;

        if (photoList && photoOrderForm) {
            photoList.querySelectorAll('.photo-manager-item').forEach((item) => {
                item.addEventListener('dragstart', () => {
                    draggedPhoto = item;
                    item.classList.add('is-dragging');
                });

                item.addEventListener('dragend', () => {
                    item.classList.remove('is-dragging');
                    draggedPhoto = null;
                    photoOrderForm.querySelectorAll('input[name="photo_ids[]"]').forEach((input) => input.remove());

                    photoList.querySelectorAll('.photo-manager-item').forEach((photo) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'photo_ids[]';
                        input.value = photo.dataset.id;
                        photoOrderForm.appendChild(input);
                    });

                    photoOrderForm.submit();
                });

                item.addEventListener('dragover', (event) => {
                    event.preventDefault();

                    if (!draggedPhoto || draggedPhoto === item) {
                        return;
                    }

                    const items = Array.from(photoList.children);
                    const draggedIndex = items.indexOf(draggedPhoto);
                    const targetIndex = items.indexOf(item);

                    if (draggedIndex < targetIndex) {
                        item.after(draggedPhoto);
                    } else {
                        item.before(draggedPhoto);
                    }
                });
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Tombol mengikuti palet tema */
        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--bs-primary);
            --bs-btn-border-color: var(--bs-primary);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #164934;
            --bs-btn-hover-border-color: #164934;
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #164934;
            --bs-btn-active-border-color: #164934;
            --bs-btn-disabled-bg: var(--bs-primary);
            --bs-btn-disabled-border-color: var(--bs-primary);
        }

        .btn-secondary {
            --bs-btn-color: var(--text-primary);
            --bs-btn-bg: var(--bg-tertiary);
            --bs-btn-border-color: var(--border-color);
            --bs-btn-hover-color: var(--text-primary);
            --bs-btn-hover-bg: var(--hover-bg);
            --bs-btn-hover-border-color: var(--border-color);
            --bs-btn-focus-shadow-rgb: 29, 91, 66;
            --bs-btn-active-color: var(--text-primary);
            --bs-btn-active-bg: var(--hover-bg);
            --bs-btn-active-border-color: var(--border-color);
            --bs-btn-disabled-color: var(--text-muted);
            --bs-btn-disabled-bg: var(--bg-tertiary);
            --bs-btn-disabled-border-color: var(--border-color);
        }

        /* Kartu form */
        .car-form-card,
        .photo-manager-card {
            padding: 1.5rem;
        }

        .car-form-card:hover,
        .photo-manager-card:hover,
        .car-form-tips:hover {
            transform: none;
            box-shadow: var(--card-shadow);
        }

        .form-section-head {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            margin-bottom: 1.35rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .form-section-head-split {
            justify-content: space-between;
        }

        .form-section-head h5 {
            margin: 0 0 0.2rem;
            color: var(--text-primary);
            font-size: 1.02rem;
            font-weight: 700;
        }

        .form-section-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.78rem;
        }

        .form-section-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: rgba(29, 91, 66, 0.1);
            color: var(--bs-primary);
            font-size: 0.92rem;
        }

        .form-subsection-head {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 1.5rem 0 1rem;
            padding-top: 1.1rem;
            border-top: 1px dashed var(--border-color);
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .form-subsection-head i {
            color: var(--bs-primary);
        }

        .required-mark {
            color: var(--bs-danger);
        }

        #car-form .form-control,
        #car-form .form-select {
            border-radius: 8px;
        }

        #car-form .form-label {
            text-wrap: balance;
        }

        #car-form .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Input harga */
        .price-field {
            position: relative;
            display: flex;
            align-items: center;
        }

        .price-field-prefix {
            position: absolute;
            left: 0.95rem;
            z-index: 2;
            padding-right: 0.65rem;
            border-right: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 700;
            line-height: 1;
            pointer-events: none;
        }

        .price-field .form-control {
            padding-left: 3.35rem;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .price-field.is-invalid .form-control {
            border-color: var(--bs-danger);
        }

        /* Opsi unggulan */
        .featured-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            padding: 0.85rem;
            border: 1px solid var(--border-light);
            border-radius: 9px;
            background: var(--bg-tertiary);
        }

        .featured-option-copy {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-width: 0;
        }

        .featured-option-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: rgba(189, 138, 56, 0.14);
            color: var(--bs-warning);
            font-size: 0.82rem;
        }

        .featured-option-copy strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
        }

        .featured-option-copy span {
            display: block;
            color: var(--text-muted);
            font-size: 0.7rem;
            line-height: 1.35;
        }

        /* Metadata edit */
        .form-meta {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-light);
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .form-meta-row strong,
        .form-meta-row code {
            color: var(--text-secondary);
            font-size: 0.72rem;
            font-weight: 600;
            text-align: right;
            word-break: break-all;
        }

        .form-meta-row code {
            max-width: 65%;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            background: var(--bg-tertiary);
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        /* Fitur */
        .feature-counter,
        .photo-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.28rem 0.6rem;
            border-radius: 999px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .feature-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }

        .feature-toolbar-label {
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .feature-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .feature-tool-button {
            padding: 0.2rem 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: transparent;
            color: var(--text-secondary);
            font-size: 0.68rem;
            font-weight: 600;
            line-height: 1.4;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .feature-tool-button:hover {
            border-color: var(--bs-primary);
            color: var(--bs-primary);
            background: var(--hover-bg);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 0.65rem;
        }

        .feature-grid .custom-checkbox {
            padding: 0.6rem 0.7rem 0.6rem 2.5rem;
            border: 1px solid var(--border-light);
            border-radius: 7px;
            background: var(--bg-secondary);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .feature-grid .custom-checkbox:hover {
            border-color: var(--border-color);
            background: var(--hover-bg);
        }

        .feature-grid .form-check-input {
            margin-left: -1.8rem;
            margin-top: 0.1rem;
        }

        .form-empty-note {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            padding: 0.85rem 0.95rem;
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            font-size: 0.78rem;
            line-height: 1.45;
        }

        .form-empty-note i {
            color: var(--bs-primary);
            margin-top: 0.15rem;
        }

        .form-empty-note a {
            color: var(--bs-primary);
            font-weight: 600;
        }

        /* Zona foto */
        .photo-dropzone {
            position: relative;
            border: 1.5px dashed var(--border-color);
            border-radius: 10px;
            background: var(--bg-tertiary);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .photo-dropzone.is-dragover {
            border-color: var(--bs-primary);
            background: var(--hover-bg);
        }

        .photo-dropzone-input:focus-visible + .photo-dropzone-inner {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
            border-radius: 8px;
        }

        .photo-dropzone-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .photo-dropzone-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
        }

        .photo-dropzone-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            margin-bottom: 0.35rem;
            border-radius: 50%;
            background: var(--bg-secondary);
            color: var(--bs-primary);
            font-size: 1.05rem;
            box-shadow: 0 2px 8px rgba(23, 53, 43, 0.08);
        }

        .photo-dropzone-inner strong {
            color: var(--text-primary);
            font-size: 0.82rem;
        }

        .photo-dropzone-inner > span:not(.photo-dropzone-icon):not(.photo-dropzone-button) {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .photo-dropzone-button {
            margin-top: 0.5rem;
            padding: 0.35rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.72rem;
            font-weight: 600;
        }

        .photo-selection-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.2rem;
            margin-top: 0.6rem;
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .photo-selection-meta i {
            color: var(--bs-warning);
        }

        .photo-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(74px, 1fr));
            gap: 0.45rem;
            margin-top: 0.75rem;
        }

        .photo-preview-item {
            aspect-ratio: 4 / 3;
            overflow: hidden;
            border-radius: 7px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-light);
        }

        .photo-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Ringkasan foto edit */
        .photo-summary {
            display: flex;
            gap: 0.85rem;
            align-items: center;
        }

        .photo-summary-main {
            position: relative;
            width: 104px;
            height: 78px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: var(--bg-tertiary);
            color: var(--text-muted);
            border: 1px solid var(--border-light);
        }

        .photo-summary-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-summary-badge {
            position: absolute;
            top: 0.3rem;
            left: 0.3rem;
            padding: 0.12rem 0.35rem;
            border-radius: 4px;
            background: var(--bs-warning);
            color: #1f2937;
            font-size: 0.58rem;
            font-weight: 700;
        }

        .photo-summary-copy {
            min-width: 0;
        }

        .photo-summary-copy strong {
            display: block;
            color: var(--text-primary);
            font-size: 0.82rem;
        }

        .photo-summary-copy > span {
            display: block;
            margin-top: 0.12rem;
            color: var(--text-muted);
            font-size: 0.7rem;
            line-height: 1.4;
        }

        .photo-summary-copy a {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-top: 0.45rem;
            color: var(--bs-primary);
            font-size: 0.72rem;
            font-weight: 700;
            text-decoration: none;
        }

        .photo-summary-copy a:hover {
            text-decoration: underline;
        }

        /* Tips */
        .car-form-tips {
            padding: 1.1rem 1.25rem;
            border-left: 3px solid var(--bs-warning);
        }

        .tips-head {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-primary);
            font-size: 0.8rem;
        }

        .tips-head i {
            color: var(--bs-warning);
        }

        .car-form-tips ul {
            margin: 0.6rem 0 0;
            padding-left: 1.1rem;
            color: var(--text-muted);
            font-size: 0.72rem;
            line-height: 1.6;
        }

        /* Footer aksi */
        .car-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: 0.9rem 1.1rem;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            background: var(--bg-secondary);
            box-shadow: var(--card-shadow);
        }

        .car-form-footer-note {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-muted);
            font-size: 0.72rem;
        }

        .car-form-footer-note > i {
            color: var(--bs-primary);
        }

        .car-form-footer-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        /* Kelola foto */
        .photo-manager-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .photo-manager-head h5 {
            margin: 0 0 0.2rem;
            color: var(--text-primary);
            font-size: 1.02rem;
            font-weight: 700;
        }

        .photo-manager-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.78rem;
        }

        .photo-upload-form {
            display: flex;
            align-items: stretch;
            gap: 0.65rem;
        }

        .photo-upload-form .photo-dropzone {
            flex: 1;
            min-width: 0;
        }

        .photo-dropzone-compact .photo-dropzone-inner {
            flex-direction: row;
            justify-content: flex-start;
            gap: 0.75rem;
            padding: 0.7rem 0.9rem;
            text-align: left;
        }

        .photo-dropzone-compact .photo-dropzone-icon {
            width: 34px;
            height: 34px;
            margin: 0;
            font-size: 0.85rem;
        }

        .photo-dropzone-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .photo-dropzone-text strong {
            font-size: 0.78rem;
        }

        .photo-dropzone-text span {
            color: var(--text-muted);
            font-size: 0.68rem;
        }

        .photo-upload-form .btn {
            align-self: center;
            white-space: nowrap;
        }

        .photo-upload-hint {
            margin: 0.55rem 0 1.1rem;
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .photo-upload-hint i {
            margin-right: 0.15rem;
            color: var(--bs-primary);
        }

        .photo-manager-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 0.85rem;
        }

        .photo-manager-item {
            position: relative;
            aspect-ratio: 4 / 3;
            border-radius: 9px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            cursor: grab;
            background: var(--bg-tertiary);
            transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .photo-manager-item:hover {
            border-color: var(--bs-primary);
            box-shadow: 0 4px 14px rgba(23, 53, 43, 0.14);
        }

        .photo-manager-item.is-primary {
            border-color: var(--bs-warning);
        }

        .photo-manager-item.is-dragging {
            opacity: 0.45;
            transform: scale(0.98);
        }

        .photo-manager-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
        }

        .photo-primary-badge {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            padding: 0.22rem 0.45rem;
            border-radius: 5px;
            background: var(--bs-warning);
            color: #1f2937;
            font-size: 0.64rem;
            font-weight: 700;
        }

        .photo-drag-handle {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border-radius: 5px;
            background: rgba(15, 23, 20, 0.6);
            color: #fff;
            font-size: 0.65rem;
            cursor: grab;
        }

        .photo-actions {
            position: absolute;
            inset: auto 0 0 0;
            display: flex;
            justify-content: flex-end;
            gap: 0.35rem;
            padding: 0.5rem;
            background: linear-gradient(transparent, rgba(15, 23, 20, 0.65));
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .photo-manager-item:hover .photo-actions,
        .photo-manager-item:focus-within .photo-actions {
            opacity: 1;
        }

        @media (hover: none) {
            .photo-actions {
                opacity: 1;
            }
        }

        .photo-action {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.92);
            color: #1f2937;
            font-size: 0.68rem;
            transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
        }

        .photo-action:hover {
            background: #fff;
            color: var(--bs-warning);
            transform: translateY(-1px);
        }

        .photo-action-danger:hover {
            background: var(--bs-danger);
            color: #fff;
        }

        @media (max-width: 767.98px) {
            .car-form-card,
            .photo-manager-card {
                padding: 1.1rem;
            }

            .car-form-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .car-form-footer-actions {
                justify-content: flex-end;
            }

            .photo-upload-form {
                flex-direction: column;
            }

            .photo-upload-form .btn {
                align-self: stretch;
            }

            .photo-selection-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
            }
        }

        @media (max-width: 575.98px) {
            .form-section-head-split {
                flex-direction: column;
                gap: 0.65rem;
            }

            .car-form-footer-actions {
                flex-direction: column-reverse;
            }

            .car-form-footer-actions .btn {
                width: 100%;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

<x-layouts.app :title="($editing ? 'Edit Mobil' : 'Tambah Mobil').' - RodaTa Admin'">
    <x-layout.page-header
        :title="$editing ? 'Edit Mobil' : 'Tambah Mobil'"
        :subtitle="$editing ? 'Perbarui data, foto, dan status unit' : 'Lengkapi data unit baru untuk ditampilkan di katalog'"
    >
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                <x-ui.button variant="secondary" icon="fas fa-arrow-left" :href="route('admin.cars')">
                    Kembali
                </x-ui.button>
                <x-ui.button type="submit" form="car-form" variant="primary" icon="fas fa-floppy-disk">
                    {{ $editing ? 'Simpan Perubahan' : 'Simpan Mobil' }}
                </x-ui.button>
            </div>
        </x-slot:actions>
    </x-layout.page-header>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="car-form">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                {{-- Data utama --}}
                <x-layout.modern-card class="car-form-card mb-4">
                    <div class="form-section-head">
                        <span class="form-section-icon"><i class="fas fa-car-side"></i></span>
                        <div>
                            <h5>Data Utama</h5>
                            <p>Identitas unit dan harga jual yang tampil di katalog.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <x-form.select
                                name="car_model_id"
                                label="Merek & Model"
                                :options="$carModels"
                                :selected="old('car_model_id', $car->car_model_id)"
                                placeholder="Pilih merek dan model"
                                required
                                :error="$errors->first('car_model_id')"
                            />
                        </div>

                        <div class="col-12 col-md-4">
                            <x-form.input
                                name="variant"
                                label="Varian"
                                :value="old('variant', $car->variant)"
                                placeholder="G, V, Sport..."
                                :error="$errors->first('variant')"
                            />
                        </div>

                        <div class="col-6 col-md-3">
                            <x-form.input
                                type="number"
                                name="year"
                                label="Tahun"
                                :value="old('year', $car->year)"
                                min="1990"
                                max="{{ date('Y') + 1 }}"
                                placeholder="{{ date('Y') }}"
                                required
                                :error="$errors->first('year')"
                            />
                        </div>

                        <div class="col-12 col-md-5">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    Harga Jual <span class="required-mark">*</span>
                                </label>
                                <div class="price-field {{ $errors->has('price') ? 'is-invalid' : '' }}">
                                    <span class="price-field-prefix">Rp</span>
                                    <input type="text"
                                           id="price"
                                           name="price"
                                           value="{{ $priceDisplay }}"
                                           class="form-control"
                                           inputmode="numeric"
                                           autocomplete="off"
                                           placeholder="150.000.000"
                                           required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @else
                                    <div class="form-text">Tulis angka saja, pemisah ribuan otomatis ditambahkan.</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.input
                                type="number"
                                name="mileage"
                                label="Kilometer"
                                :value="old('mileage', $car->mileage)"
                                min="0"
                                placeholder="45000"
                                required
                                hint="Tanpa pemisah, contoh: 45000"
                                :error="$errors->first('mileage')"
                            />
                        </div>
                    </div>
                </x-layout.modern-card>

                {{-- Spesifikasi --}}
                <x-layout.modern-card class="car-form-card mb-4">
                    <div class="form-section-head">
                        <span class="form-section-icon"><i class="fas fa-sliders"></i></span>
                        <div>
                            <h5>Spesifikasi</h5>
                            <p>Detail teknis kendaraan untuk membantu pembeli membandingkan unit.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <x-form.select
                                name="transmission"
                                label="Transmisi"
                                :options="collect($transmissions)->mapWithKeys(fn ($item) => [$item->value => $item->label()])->all()"
                                :selected="old('transmission', $car->transmission?->value)"
                                required
                                :error="$errors->first('transmission')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.select
                                name="fuel_type"
                                label="Bahan Bakar"
                                :options="collect($fuelTypes)->mapWithKeys(fn ($item) => [$item->value => $item->label()])->all()"
                                :selected="old('fuel_type', $car->fuel_type?->value)"
                                required
                                :error="$errors->first('fuel_type')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.select
                                name="body_type"
                                label="Tipe Bodi"
                                :options="collect($bodyTypes)->mapWithKeys(fn ($item) => [$item->value => $item->label()])->all()"
                                :selected="old('body_type', $car->body_type?->value)"
                                required
                                :error="$errors->first('body_type')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.input
                                type="number"
                                name="engine_capacity"
                                label="Kapasitas Mesin (cc)"
                                :value="old('engine_capacity', $car->engine_capacity)"
                                min="500"
                                placeholder="1500"
                                :error="$errors->first('engine_capacity')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.input
                                name="color"
                                label="Warna"
                                :value="old('color', $car->color)"
                                placeholder="Putih"
                                :error="$errors->first('color')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.input
                                type="number"
                                name="owner_count"
                                label="Jumlah Pemilik"
                                :value="old('owner_count', $car->owner_count ?? 1)"
                                min="1"
                                max="20"
                                required
                                :error="$errors->first('owner_count')"
                            />
                        </div>
                    </div>

                    <div class="form-subsection-head">
                        <i class="fas fa-file-lines"></i>
                        Dokumen & Legalitas
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <x-form.input
                                name="plate_area"
                                label="Wilayah Plat"
                                :value="old('plate_area', $car->plate_area)"
                                placeholder="KT"
                                maxlength="10"
                                :error="$errors->first('plate_area')"
                            />
                        </div>

                        <div class="col-6 col-md-4">
                            <x-form.date-picker
                                name="tax_expiry"
                                label="Masa Pajak"
                                :value="old('tax_expiry', $car->tax_expiry?->format('Y-m-d'))"
                                :error="$errors->first('tax_expiry')"
                            />
                        </div>

                        <div class="col-12 col-md-4">
                            <x-form.input
                                name="document_completeness"
                                label="Kelengkapan Surat"
                                :value="old('document_completeness', $car->document_completeness)"
                                placeholder="Lengkap"
                                maxlength="50"
                                :error="$errors->first('document_completeness')"
                            />
                        </div>
                    </div>
                </x-layout.modern-card>

                {{-- Kondisi & deskripsi --}}
                <x-layout.modern-card class="car-form-card mb-4">
                    <div class="form-section-head">
                        <span class="form-section-icon"><i class="fas fa-clipboard-check"></i></span>
                        <div>
                            <h5>Kondisi & Deskripsi</h5>
                            <p>Ceritakan kondisi nyata unit agar calon pembeli lebih percaya.</p>
                        </div>
                    </div>

                    <x-form.textarea
                        name="condition"
                        label="Kondisi"
                        :value="old('condition', $car->condition)"
                        placeholder="Terawat, bebas banjir, mesin halus, ban baru..."
                        :rows="3"
                        maxlength="2000"
                        :error="$errors->first('condition')"
                    />

                    <x-form.textarea
                        name="description"
                        label="Deskripsi"
                        :value="old('description', $car->description)"
                        placeholder="Ceritakan kondisi, riwayat perawatan, dan keunggulan unit..."
                        :rows="6"
                        maxlength="10000"
                        :error="$errors->first('description')"
                    />
                </x-layout.modern-card>

                {{-- Fitur --}}
                <x-layout.modern-card class="car-form-card">
                    <div class="form-section-head form-section-head-split">
                        <div class="d-flex gap-3">
                            <span class="form-section-icon"><i class="fas fa-list-check"></i></span>
                            <div>
                                <h5>Fitur</h5>
                                <p>Pilih fitur yang dimiliki unit ini.</p>
                            </div>
                        </div>
                        <span class="feature-counter" id="feature-selected-count">
                            {{ count($selectedFeatureIds) }} dipilih
                        </span>
                    </div>

                    @if ($features->isEmpty())
                        <div class="form-empty-note">
                            <i class="fas fa-circle-info"></i>
                            <span>
                                Belum ada data fitur.
                                <a href="{{ route('admin.features') }}">Tambahkan fitur</a> terlebih dahulu.
                            </span>
                        </div>
                    @else
                        <div class="feature-toolbar">
                            <span class="feature-toolbar-label">Fitur tersedia</span>
                            <div class="feature-toolbar-actions">
                                <button type="button" class="feature-tool-button" id="features-select-all">
                                    Pilih semua
                                </button>
                                <button type="button" class="feature-tool-button" id="features-clear-all">
                                    Kosongkan
                                </button>
                            </div>
                        </div>

                        <div class="feature-grid" id="feature-grid">
                            @foreach ($features as $feature)
                                <x-form.checkbox
                                    :id="'feature-'.$feature->id"
                                    name="features[]"
                                    :value="$feature->id"
                                    :checked="in_array($feature->id, $selectedFeatureIds, true)"
                                    :label="$feature->name"
                                />
                            @endforeach
                        </div>
                    @endif

                    @error('features')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </x-layout.modern-card>
            </div>

            <div class="col-12 col-xl-4">
                {{-- Publikasi --}}
                <x-layout.modern-card class="car-form-card mb-4">
                    <div class="form-section-head">
                        <span class="form-section-icon"><i class="fas fa-bullhorn"></i></span>
                        <div>
                            <h5>Publikasi</h5>
                            <p>Atur status dan lokasi unit di katalog.</p>
                        </div>
                    </div>

                    <x-form.select
                        name="status"
                        label="Status"
                        :options="collect($statuses)->mapWithKeys(fn ($item) => [$item->value => $item->label()])->all()"
                        :selected="old('status', $car->status?->value ?? 'available')"
                        required
                        :error="$errors->first('status')"
                    />

                    <div class="mb-3">
                        <label class="form-label" for="city">
                            Kota <span class="required-mark">*</span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city', $car->city) }}"
                               class="form-control @error('city') is-invalid @enderror"
                               list="city-options" placeholder="Samarinda" required>
                        <datalist id="city-options">
                            @foreach ($cities as $cityOption)
                                <option value="{{ $cityOption }}"></option>
                            @endforeach
                        </datalist>
                        @error('city')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="featured-option">
                        <div class="featured-option-copy">
                            <span class="featured-option-icon"><i class="fas fa-star"></i></span>
                            <div>
                                <strong>Unit Unggulan</strong>
                                <span>Tampil lebih menonjol di halaman utama.</span>
                            </div>
                        </div>
                        <x-form.toggle
                            name="is_featured"
                            :checked="old('is_featured', $car->is_featured)"
                            aria-label="Tandai sebagai unit unggulan"
                        />
                    </div>

                    @if ($editing)
                        <div class="form-meta">
                            <div class="form-meta-row">
                                <span>Slug</span>
                                <code>{{ $car->slug }}</code>
                            </div>
                            <div class="form-meta-row">
                                <span>Dilihat</span>
                                <strong>{{ number_format($car->views_count, 0, ',', '.') }} kali</strong>
                            </div>
                            <div class="form-meta-row">
                                <span>Dibuat</span>
                                <strong>{{ $car->created_at?->format('d M Y H:i') }}</strong>
                            </div>
                        </div>
                    @endif
                </x-layout.modern-card>

                {{-- Foto --}}
                <x-layout.modern-card class="car-form-card mb-4">
                    <div class="form-section-head form-section-head-split">
                        <div class="d-flex gap-3">
                            <span class="form-section-icon"><i class="fas fa-camera"></i></span>
                            <div>
                                <h5>Foto</h5>
                                <p>Foto pertama menjadi sampul unit.</p>
                            </div>
                        </div>
                        @if ($editing)
                            <span class="photo-count-badge">{{ $photoCount }} foto</span>
                        @endif
                    </div>

                    @unless ($editing)
                        <div class="photo-dropzone" data-photo-dropzone>
                            <input type="file" name="photos[]" id="photos" multiple
                                   accept="image/jpeg,image/png,image/webp"
                                   data-photo-input
                                   data-count-target="photo-selection-count"
                                   class="photo-dropzone-input @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror">
                            <label for="photos" class="photo-dropzone-inner">
                                <span class="photo-dropzone-icon"><i class="fas fa-cloud-arrow-up"></i></span>
                                <strong>Pilih foto atau seret ke sini</strong>
                                <span>JPG, PNG, atau WebP · maks. 4 MB per foto · maks. 12 foto</span>
                                <span class="photo-dropzone-button">Pilih Foto</span>
                            </label>
                        </div>

                        <div class="photo-selection-meta">
                            <span id="photo-selection-count">Belum ada foto dipilih</span>
                            <span><i class="fas fa-star"></i> Foto pertama jadi foto utama</span>
                        </div>

                        @error('photos')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('photos.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div id="photo-preview" class="photo-preview-grid"></div>
                    @else
                        @if ($photoCount > 0)
                            <div class="photo-summary">
                                <div class="photo-summary-main">
                                    @if ($primaryPhoto)
                                        <img src="{{ $primaryPhoto->thumbnailUrl() }}" alt="Foto utama {{ $car->slug }}">
                                        <span class="photo-summary-badge"><i class="fas fa-star"></i> Utama</span>
                                    @else
                                        <i class="fas fa-image"></i>
                                    @endif
                                </div>
                                <div class="photo-summary-copy">
                                    <strong>{{ $photoCount }} foto tersimpan</strong>
                                    <span>Atur urutan, pilih foto utama, atau hapus foto di panel Kelola Foto.</span>
                                    <a href="#kelola-foto">
                                        <i class="fas fa-arrow-down"></i>
                                        Kelola Foto
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="form-empty-note">
                                <i class="fas fa-circle-info"></i>
                                <span>Belum ada foto. Unggah lewat panel <strong>Kelola Foto</strong> di bawah.</span>
                            </div>
                        @endif
                    @endif
                </x-layout.modern-card>

                <x-layout.modern-card class="car-form-tips">
                    <div class="tips-head">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tips unit cepat terjual</strong>
                    </div>
                    <ul>
                        <li>Gunakan minimal 5 foto dari berbagai sudut.</li>
                        <li>Isi kondisi dan riwayat servis secara jujur.</li>
                        <li>Pastikan harga dan kilometer sudah sesuai.</li>
                    </ul>
                </x-layout.modern-card>
            </div>
        </div>

        {{-- Aksi bawah --}}
        <div class="car-form-footer">
            <div class="car-form-footer-note">
                <i class="fas fa-shield-halved"></i>
                <span>Periksa kembali data sebelum menyimpan. Field bertanda <span class="required-mark">*</span> wajib diisi.</span>
            </div>
            <div class="car-form-footer-actions">
                <x-ui.button variant="secondary" :href="route('admin.cars')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="fas fa-floppy-disk">
                    {{ $editing ? 'Simpan Perubahan' : 'Simpan Mobil' }}
                </x-ui.button>
            </div>
        </div>
    </form>

    @if ($editing)
        <x-layout.modern-card class="photo-manager-card mt-4" id="kelola-foto">
            <div class="photo-manager-head">
                <div class="d-flex gap-3">
                    <span class="form-section-icon"><i class="fas fa-images"></i></span>
                    <div>
                        <h5>Kelola Foto</h5>
                        <p>Seret kartu untuk mengubah urutan. Perubahan urutan langsung disimpan.</p>
                    </div>
                </div>
                <span class="photo-count-badge">{{ $photoCount }} foto</span>
            </div>

            <form method="POST" action="{{ route('admin.cars.photos.store', $car) }}"
                  enctype="multipart/form-data" class="photo-upload-form">
                @csrf
                <div class="photo-dropzone photo-dropzone-compact" data-photo-dropzone>
                    <input type="file" name="photos[]" id="photos-manager" multiple
                           accept="image/jpeg,image/png,image/webp"
                           data-photo-input
                           data-count-target="photo-manager-count"
                           class="photo-dropzone-input @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror">
                    <label for="photos-manager" class="photo-dropzone-inner">
                        <span class="photo-dropzone-icon"><i class="fas fa-cloud-arrow-up"></i></span>
                        <span class="photo-dropzone-text">
                            <strong>Pilih foto atau seret ke sini</strong>
                            <span id="photo-manager-count">Belum ada foto dipilih</span>
                        </span>
                    </label>
                </div>
                <x-ui.button type="submit" variant="secondary" icon="fas fa-cloud-upload-alt">
                    Unggah Foto
                </x-ui.button>
            </form>

            <div class="photo-upload-hint">
                <span><i class="fas fa-circle-info"></i> JPG, PNG, atau WebP · maks. 4 MB per foto · maks. 12 foto per unggahan.</span>
            </div>

            @error('photos')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
            @enderror
            @error('photos.*')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
            @enderror

            @if ($car->photos->isEmpty())
                <x-ui.empty-state
                    icon="fas fa-image"
                    title="Belum ada foto"
                    description="Unggah foto unit agar pembeli dapat melihat kondisi mobil."
                    size="sm"
                />
            @else
                <div id="photo-list" class="photo-manager-grid">
                    @foreach ($car->photos as $photo)
                        <div class="photo-manager-item {{ $photo->is_primary ? 'is-primary' : '' }}"
                             draggable="true" data-id="{{ $photo->id }}">
                            <img src="{{ $photo->url() }}" alt="Foto {{ $car->slug }}">
                            @if ($photo->is_primary)
                                <span class="photo-primary-badge"><i class="fas fa-star"></i> Utama</span>
                            @endif
                            <span class="photo-drag-handle" title="Seret untuk mengubah urutan">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                            <div class="photo-actions">
                                @unless ($photo->is_primary)
                                    <form method="POST" action="{{ route('admin.cars.photos.primary', [$car, $photo]) }}">
                                        @csrf
                                        <button type="submit" class="photo-action" title="Jadikan foto utama">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.cars.photos.destroy', [$car, $photo]) }}"
                                      onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="photo-action photo-action-danger" title="Hapus foto">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.cars.photos.reorder', $car) }}" id="photo-order-form" class="d-none">
                    @csrf
                </form>
            @endif
        </x-layout.modern-card>
    @endif
</x-layouts.app>
