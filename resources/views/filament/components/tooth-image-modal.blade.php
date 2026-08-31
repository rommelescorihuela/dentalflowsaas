<div class="space-y-4">
    <img src="{{ Storage::url($image->file_path) }}" alt="{{ $image->file_name }}" class="w-full rounded-lg">
    <div class="space-y-2">
        <p><strong>Diente:</strong> {{ $image->tooth_number }}</p>
        <p><strong>Tipo:</strong> {{ ucfirst($image->image_type) }}</p>
        <p><strong>Fecha:</strong> {{ $image->image_date->format('d/m/Y') }}</p>
        @if($image->description)
            <p><strong>Descripción:</strong> {{ $image->description }}</p>
        @endif
    </div>
</div>
