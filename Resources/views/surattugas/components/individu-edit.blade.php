<div class="form-group row">
    <div class="col-md-6">
        <label for="nomor_surat">Nomor Surat</label>
        <input type="text" class="form-control" name="nomor_surat" value="{{ $suratTugas->nomor_surat }}" required>
    </div>
    <div class="col-md-6">
        <label for="pejabat_id">Pejabat Pembuat Komitmen</label>
        <select name="pejabat_id" class="tomselect-edit" required>
            @foreach ($pejabat as $p)
                <option value="{{ $p->id }}" {{ $p->id == $suratTugas->pejabat_id ? 'selected' : '' }}>
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
        <select name="jarak" class="form-control" required>
            <option value="dalam_kota" {{ $suratTugas->jarak == 'dalam_kota' ? 'selected' : '' }}>Dalam Kota</option>
            <option value="luar_kota" {{ $suratTugas->jarak == 'luar_kota' ? 'selected' : '' }}>Luar Kota</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="pegawai_id">Pegawai</label>
        <select name="pegawai_id" class="tomselect-edit" required>
            @foreach ($pegawai as $pe)
                <option value="{{ $pe->id }}" {{ $pe->id == $suratTugas->detail->pegawai_id ? 'selected' : '' }}>
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
        <input type="text" name="kegiatan_maksud" class="form-control"
            value="{{ $suratTugas->detail->kegiatan_maksud }}" required>
    </div>
    <div class="col-md-6">
        <label for="tempat">Tempat Kegiatan</label>
        <input type="text" name="tempat" class="form-control"
            value="{{ $suratTugas->detail->tempat }}" required>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <label for="tanggal">Tanggal</label>
        <input type="text" name="tanggal" class="form-control flatpickr"
            value="{{ $suratTugas->detail->tanggal_mulai }} to {{ $suratTugas->detail->tanggal_selesai }}"
            required>
    </div>
    <div class="col-md-6" id="alat_angkutan_container" style="{{ $suratTugas->jarak == 'luar_kota' ? 'display: block;' : 'display: none;' }}">
        <label for="alat_angkutan">Alat Angkutan</label>
        <select class="form-control" name="alat_angkutan" id="alat_angkutan">
            <option value="">-- Pilih Angkutan --</option>
            <option value="Bis" {{ $suratTugas->detail->alat_angkutan == 'Bis' ? 'selected' : '' }}>Bis</option>
            <option value="Kereta" {{ $suratTugas->detail->alat_angkutan == 'Kereta' ? 'selected' : '' }}>Kereta</option>
            <option value="Pesawat" {{ $suratTugas->detail->alat_angkutan == 'Pesawat' ? 'selected' : '' }}>Pesawat</option>
        </select>
    </div>
</div>

<div class="form-group row" id="kota_fields_individu" style="{{ $suratTugas->jarak == 'luar_kota' ? 'display: flex;' : 'display: none;' }}">
    <!-- Kota Keberangkatan -->
    <div class="col-md-6">
        <label for="kota_keberangkatan">Kota Keberangkatan</label>
        <input type="text" name="kota_keberangkatan" class="form-control"
               value="{{ old('kota_keberangkatan', optional($suratTugas->detail)->kota_keberangkatan) }}"
               placeholder="Contoh: Surabaya">
    </div>

    <!-- Kota Tujuan -->
    <div class="col-md-6">
        <label for="kota_tujuan">Kota Tujuan</label>
        <input type="text" name="kota_tujuan" class="form-control"
               value="{{ old('kota_tujuan', optional($suratTugas->detail)->kota_tujuan) }}"
               placeholder="Contoh: Banyuwangi">
    </div>
</div>