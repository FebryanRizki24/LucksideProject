@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-oswald text-lg text-black']) }}>
    {{ $value ?? $slot }}
</label>
