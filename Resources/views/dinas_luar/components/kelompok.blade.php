<form action="{{ route('perjadin.store') }}" method="POST">
    @csrf

    <!-- Hidden jenis -->
    <input type="hidden" name="jenis" value="tim">

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
            <label for="pegawai_id">Pegawai Utama</label>
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
        <div class="col-md-6">
            <label for="maksud">Maksud Perjalanan</label>
            <input type="text" name="maksud" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="alat_angkutan">Alat Angkutan</label>
            <select class="form-control" name="alat_angkutan" id="alat_angkutan">
                <option value="">-- Pilih Angkutan --</option>
                <option value="Bis">Bis</option>
                <option value="Kereta">Kereta</option>
                <option value="Pesawat">Pesawat</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="tanggal">Tanggal Perjalanan</label>
            <input type="text" name="tanggal" id="tanggal" class="form-control flatpickr" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="lama_perjalanan">Lama Perjalanan (hari)</label>
            <input type="number" name="lama_perjalanan" class="form-control" min="1" required>
        </div>

        <div class="col-md-6">
            <label for="pengikut_ids">Pengikut</label>
            <select name="pengikut_ids[]" class="tomselect" multiple>
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

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
