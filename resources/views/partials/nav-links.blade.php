<a href="{{ url('/') }}#beranda" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Beranda</a>
<a href="{{ url('/') }}#ekstrakurikuler" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Ekstrakurikuler</a>
<a href="{{ url('/') }}#tenaga-pengajar" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Tenaga Pengajar</a>

@auth
    @if(auth()->user()->pendaftaran)
        <a href="{{ route('pendaftaran.index') }}" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Status Pendaftaran</a>
    @else
        <a href="{{ route('pendaftaran.create') }}" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Formulir Daftar</a>
    @endif
@else
    <a href="{{ url('/') }}#ppdb" class="custom-nav-link text-[#002060] font-medium text-sm lg:text-base hover:text-blue-800">Pendaftaran</a>
@endauth