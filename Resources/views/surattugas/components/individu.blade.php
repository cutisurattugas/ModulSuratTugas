<form action="{{ route('surattugas.store') }}" method="POST">
    @csrf
    <input type="hidden" name="jenis" value="individu">

    <div class="form-group row">
        <div class="col-md-6">
            <label for="nomor_surat">Nomor Surat</label>
            <input type="text" class="form-control" name="nomor_surat" required>
        </div>
        <div class="col-md-6">
            <label for="pejabat_id">Pejabat Pembuat Komitmen</label>
            <select name="pejabat_id" class="tomselect" required>
                <option value="">-- Pilih Pejabat --</option>
                @foreach ($pejabat as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->pegawai->gelar_dpn ? $p->pegawai->gelar_dpn . ' ' : '' }}
                        {{ $p->pegawai->nama }}
                        {{ $p->pegawai->gelar_blk ? ', ' . $p->pegawai->gelar_blk : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="jarak">Jenis Perjalanan</label>
            <select name="jarak" class="form-control" id="jarak_individu" required>
                <option value="dalam_kota">Dalam Kota</option>
                <option value="luar_kota">Luar Kota</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="pegawai_id">Pegawai</label>
            <select name="pegawai_id" class="tomselect" required>
                <option value="">-- Pilih Pegawai --</option>
                @foreach ($pegawai as $pe)
                    <option value="{{ $pe->id }}">
                        {{ $pe->gelar_dpn ? $pe->gelar_dpn . ' ' : '' }}
                        {{ $pe->nama }}
                        {{ $pe->gelar_blk ? ', ' . $pe->gelar_blk : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="kegiatan_maksud">Nama Kegiatan</label>
            <input type="text" name="kegiatan_maksud" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="tempat">Tempat Kegiatan</label>
            <input type="text" name="tempat" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="tanggal">Tanggal</label>
            <input type="text" name="tanggal" id="tanggal" class="form-control flatpickr" required>
        </div>
        <div class="col-md-6" id="alat_angkutan_container_individu" style="display: none;">
            <label for="alat_angkutan">Alat Angkutan</label>
            <select class="form-control" name="alat_angkutan" id="alat_angkutan_individu">
                <option value="">-- Pilih Angkutan --</option>
                <option value="Bis">Bis</option>
                <option value="Kereta">Kereta</option>
                <option value="Pesawat">Pesawat</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('surattugas.index') }}" class="btn btn-default">Kembali</a>
</form>
