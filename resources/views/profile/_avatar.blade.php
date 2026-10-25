{{-- Profile photo if one is uploaded, otherwise the user's initials. --}}
@php($size = $size ?? 36)
@if ($user->hasAvatar())
    <img src="{{ route('profile.avatar', ['user' => $user, 'v' => $user->updated_at?->timestamp]) }}"
         alt="{{ $user->name }}"
         class="rounded-2 flex-shrink-0"
         style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;">
@else
    <span class="racts-avatar" style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.38) }}px;">{{ $user->initials() }}</span>
@endif
