{{--
    Shared nav links partial — dipakai oleh:
    - components/admin/sidebar.blade.php (desktop, lg:flex)
    - layouts/admin.blade.php → mobile drawer (lg:hidden)

    Required vars (caller wajib pass):
    - $active: string|null (active key)

    Optional vars:
    - $linkClickHandler: extra Alpine attribute string (e.g. "@click=\"open = false\"")
                        untuk drawer auto-close on link click. Default kosong.

    Source data: config/admin-nav.php (config('admin-nav.primary')).
--}}
@php
    $linkClickHandler ??= '';
    $primaryNav = config('admin-nav.primary', []);
@endphp

@foreach ($primaryNav as $item)
    @php $isActive = $active === $item['key'] || request()->routeIs($item['route']); @endphp
    <a href="{{ route($item['route']) }}"
       {!! $linkClickHandler !!}
       class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium transition
              {{ $isActive
                  ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12] dark:text-brand-400'
                  : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' }}">
        <x-admin.icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
