@props([
    'brands',
    'bodyTypes',
    'filters' => [],
    'formClass' => 'rk-search-card',
    'id' => null,
    'submitLabel' => 'Cari Mobil',
])

<form @if ($id) id="{{ $id }}" @endif class="{{ $formClass }}" action="{{ route('home') }}#stok" method="GET">
    <div class="rk-search-title"><span class="rk-search-icon"><i class="fas fa-sliders"></i></span><div><strong>Cari mobil impianmu</strong><small>Pilih sesuai kebutuhan dan budget</small></div></div>
    <label><span>Merek</span><select name="brand"><option value="">Semua merek</option>@foreach($brands as $brand)<option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? '') === $brand->slug)>{{ $brand->name }}</option>@endforeach</select></label>
    <label><span>Tipe bodi</span><select name="body_type"><option value="">Semua tipe</option>@foreach($bodyTypes as $type)<option value="{{ $type->value }}" @selected(($filters['body_type'] ?? '') === $type->value)>{{ $type->label() }}</option>@endforeach</select></label>
    <label><span>Budget maksimal</span><select name="max_price"><option value="">Semua harga</option><option value="150000000" @selected(($filters['max_price'] ?? '') === '150000000')>Rp 150 juta</option><option value="250000000" @selected(($filters['max_price'] ?? '') === '250000000')>Rp 250 juta</option><option value="400000000" @selected(($filters['max_price'] ?? '') === '400000000')>Rp 400 juta</option></select></label>
    <label><span>Urutkan</span><select name="sort"><option value="">Terbaru</option><option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Harga termurah</option><option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Harga termahal</option></select></label>
    <button class="rk-search-submit" type="submit"><i class="fas fa-search"></i> {{ $submitLabel }}</button>
</form>
