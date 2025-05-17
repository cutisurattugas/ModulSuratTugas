<div class="form-group row">
    <div class="col-md-6">
        <label for="nomor_surat">Nomor Surat</label>
        <input type="text" class="form-control" name="nomor_surat" value="{{ $perjalanan->nomor_surat }}" required>
    </div>
    <div class="col-md-6">
        <label for="pejabat_id">Pejabat Pembuat Komitmen</label>
        <select name="pejabat_id" class="tomselect-edit" required>
            @foreach ($pejabat as $p)
                <option value="{{ $p->id }}" {{ $p->id == $perjalanan->pejabat_id ? 'selected' : '' }}>
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
        <label for="pegawai_id">Pegawai</label>
        <select name="pegawai_id" class="tomselect-edit" required>
            @foreach ($pegawai as $pe)
                <option value="{{ $pe->id }}" {{ $pe->id == optional($perjalanan->individu)->pegawai_id ? 'selected' : '' }}>
                    {{ $pe->gelar_dpn ? $pe->gelar_dpn . ' ' : '' }}
                    {{ $pe->nama }}
                    {{ $pe->gelar_blk ? ', ' . $pe->gelar_blk : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="kegiatan">Nama Kegiatan</label>
        <input type="text" name="kegiatan" class="form-control"
            value="{{ optional($perjalanan->individu)->kegiatan }}" required>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <label for="tanggal">Tanggal</label>
        <input type="text" name="tanggal" class="form-control flatpickr"
            value="{{ optional($perjalanan->individu)->tanggal_mulai }} to {{ optional($perjalanan->individu)->tanggal_selesai }}"
            required>
    </div>

    <div class="col-md-6">
        <label for="tempat">Tempat Kegiatan</label>
        <input type="text" name="tempat" class="form-control"
            value="{{ optional($perjalanan->individu)->tempat }}" required>
    </div>
</div>