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
        <label for="pegawai_id">Pegawai Utama</label>
        <select name="pegawai_id_tim" class="tomselect-edit" required>
            @foreach ($pegawai as $pe)
                <option value="{{ $pe->id }}"
                    {{ $pe->id == optional($perjalanan->tim)->pegawai_id ? 'selected' : '' }}>
                    {{ $pe->gelar_dpn ? $pe->gelar_dpn . ' ' : '' }}
                    {{ $pe->nama }}
                    {{ $pe->gelar_blk ? ', ' . $pe->gelar_blk : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="maksud">Maksud Perjalanan</label>
        <input type="text" name="maksud" class="form-control"
            value="{{ optional($perjalanan->tim)->maksud }}" required>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <label for="alat_angkutan">Alat Angkutan</label>
        <select class="form-control" name="alat_angkutan" required>
            <option value="">-- Pilih Angkutan --</option>
            <option value="Bis" {{ optional($perjalanan->tim)->alat_angkutan == 'Bis' ? 'selected' : '' }}>Bis</option>
            <option value="Kereta" {{ optional($perjalanan->tim)->alat_angkutan == 'Kereta' ? 'selected' : '' }}>Kereta</option>
            <option value="Pesawat" {{ optional($perjalanan->tim)->alat_angkutan == 'Pesawat' ? 'selected' : '' }}>Pesawat</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="tanggal">Tanggal Perjalanan</label>
        <input type="text" name="tanggal_tim" class="form-control flatpickr"
            value="{{ optional(optional($perjalanan->tim)->tanggal_berangkat) ? \Carbon\Carbon::parse(optional($perjalanan->tim)->tanggal_berangkat)->format('Y-m-d') : '' }} to {{ optional(optional($perjalanan->tim)->tanggal_kembali) ? \Carbon\Carbon::parse(optional($perjalanan->tim)->tanggal_kembali)->format('Y-m-d') : '' }}"
            required>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <label for="lama_perjalanan">Lama Perjalanan (hari)</label>
        <input type="number" name="lama_perjalanan" class="form-control" min="1"
            value="{{ optional($perjalanan->tim)->lama_perjalanan }}" required>
    </div>

    <div class="col-md-6">
        <label for="pengikut_ids">Pengikut</label>
        <select name="pengikut_ids[]" class="tomselect-edit" multiple>
            @foreach ($pegawai as $pe)
                <option value="{{ $pe->id }}"
                    @php
                        $pengikutIds = null;
                        if (!empty($perjalanan->tim) && !empty($perjalanan->tim->pengikut)) {
                            $pengikutIds = $perjalanan->tim->pengikut->pluck('pegawai_id')->toArray();
                        }
                        $pengikutIds = is_array($pengikutIds) ? $pengikutIds : [];
                    @endphp
                    {{ in_array($pe->id, $pengikutIds) ? 'selected' : '' }}>
                    {{ $pe->gelar_dpn ? $pe->gelar_dpn . ' ' : '' }}
                    {{ $pe->nama }}
                    {{ $pe->gelar_blk ? ', ' . $pe->gelar_blk : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>