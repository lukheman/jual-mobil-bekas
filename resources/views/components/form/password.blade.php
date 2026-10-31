@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'hint' => null,
    'autocomplete' => 'current-password',
    'strength' => false,
])

@php
    $inputId = $id ?? $name ?? 'password-'.uniqid();
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span style="color: var(--bs-danger);">*</span>
            @endif
        </label>
    @endif

    <div class="password-field"
         x-data="{
             visible: false,
             value: '',
             get score() {
                 const value = this.value || '';
                 if (! value) return 0;

                 let score = 0;
                 if (value.length >= 8) score++;
                 if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
                 if (/\d/.test(value)) score++;
                 if (/[^A-Za-z0-9]/.test(value)) score++;

                 return Math.max(1, score);
             },
             get scoreLabel() {
                 return ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat'][this.score];
             }
         }">
        <div class="password-control">
            <input
                type="password"
                x-model="value"
                x-bind:type="visible ? 'text' : 'password'"
                id="{{ $inputId }}"
                @if ($name) name="{{ $name }}" @endif
                class="form-control password-control-input {{ $error ? 'is-invalid' : '' }}"
                @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                @if ($required) required @endif
                autocomplete="{{ $autocomplete }}"
                {{ $attributes->except('class') }}
            >

            <button type="button"
                    class="password-visibility"
                    x-on:click="visible = !visible"
                    x-bind:aria-label="visible ? 'Sembunyikan password' : 'Tampilkan password'"
                    aria-label="Tampilkan password">
                <i class="fas fa-eye" x-bind:class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>

        @if ($strength)
            <div class="password-strength" x-show="value.length > 0" x-cloak>
                <div class="password-strength-track">
                    <span x-bind:class="'level-' + score" x-bind:style="'width: ' + (score * 25) + '%'"></span>
                </div>
                <small x-bind:class="'level-' + score" x-text="scoreLabel"></small>
            </div>
        @endif
    </div>

    @if ($error)
        <div class="invalid-feedback d-block">{{ $error }}</div>
    @elseif ($hint)
        <div class="form-text" style="color: var(--text-muted);">{{ $hint }}</div>
    @endif
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .password-control {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-control-input {
        padding-right: 2.9rem;
    }

    .password-visibility {
        position: absolute;
        right: 0.35rem;
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 7px;
        background: transparent;
        color: var(--text-muted);
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .password-visibility:hover {
        background: var(--hover-bg);
        color: var(--bs-primary);
    }

    .password-visibility:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: 1px;
    }

    .password-strength {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-top: 0.45rem;
    }

    .password-strength-track {
        flex: 1;
        height: 5px;
        border-radius: 999px;
        background: var(--bg-tertiary);
        overflow: hidden;
    }

    .password-strength-track span {
        display: block;
        height: 100%;
        border-radius: 999px;
        transition: width 0.25s ease, background 0.25s ease;
    }

    .password-strength small {
        min-width: 78px;
        text-align: right;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .password-strength-track span.level-1 { background: var(--bs-danger); }
    .password-strength-track span.level-2 { background: var(--bs-warning); }
    .password-strength-track span.level-3 { background: var(--bs-info); }
    .password-strength-track span.level-4 { background: var(--bs-success); }

    .password-strength small.level-1 { color: var(--bs-danger); }
    .password-strength small.level-2 { color: var(--bs-warning); }
    .password-strength small.level-3 { color: var(--bs-info); }
    .password-strength small.level-4 { color: var(--bs-success); }
</style>
