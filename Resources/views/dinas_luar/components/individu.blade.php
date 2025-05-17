<form action="{{ route('perjadin.store') }}" method="POST">
    @csrf

    <!-- Input Jenis (hidden karena ini form individu) -->
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
        <div class="col-md-6">
            <label for="kegiatan">Nama Kegiatan</label>
            <input type="text" name="kegiatan" class="form-control" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <label for="tanggal">Tanggal</label>
            <input type="text" name="tanggal" id="tanggal" class="form-control flatpickr" required>
        </div>

        <div class="col-md-6">
            <label for="tempat">Tempat Kegiatan</label>
            <input type="text" name="tempat" class="form-control" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
