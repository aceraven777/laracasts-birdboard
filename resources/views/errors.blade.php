@php
    $bag = $bag ?? 'default';    
@endphp

@if ($errors->$bag->any())
    <ul class="field mt-6 list-reset">
        @foreach ($errors->$bag->all() as $error)
            <li class="text-sm text-red">{{ $error }}</li>
        @endforeach
    </ul>
@endif