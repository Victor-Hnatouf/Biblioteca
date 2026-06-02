@props([
    'icon',
    'title',
    'href',
    'description',
])

<div class="p-5 rounded-lg relative dashboard-card">
    <div class="flex items-center">
        <span class="dashboard-card-icon">{{ $icon }}</span>
        <h2 class="ms-3 text-xl font-semibold" style="font-family: 'Cinzel', serif; color: #d4af37;">
            <a href="{{ $href }}">{{ $title }}</a>
        </h2>
    </div>

    <p class="mt-4 text-sm leading-relaxed dashboard-card-text">
        {{ $description }}
    </p>

    <p class="mt-4">
        <a href="{{ $href }}" class="btn btn-primary btn-sm font-cinzel">
            Aceder
        </a>
    </p>
</div>
